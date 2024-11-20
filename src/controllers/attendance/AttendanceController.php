<?php
require_once 'src/models/attendance/AttendanceModel.php';

class AttendanceController
{
    public function index()
    {
        // Retrieve token and userId from the query parameters
        $token = $_GET['token'] ?? null;
        $userId = $_GET['userId'] ?? null;

        // If token or userId is missing, redirect to an error page or display an error message
        if (!$token || !$userId) {
            $_SESSION['error'] = [
                'title' => "Invalid QR Code",
                'message' => "The scanned QR code is invalid or missing required data."
            ];
            header('Location: /elms/404');
            exit();
        }

        // Validate the QR code
        $qrModel = new QrModel();
        $result = $qrModel->validateQR($token, $userId);

        if ($result['status'] === 'valid') {
            // QR code is valid, load the attendance check view
            $_SESSION['success'] = [
                'title' => "QR Code Valid",
                'message' => "Attendance recorded successfully."
            ];

            // Optionally, you can log the scan details, like time and location
            $this->logScanDetails($result['data']);

            // Load the check view
            require 'src/views/attendence/check.php';
        } else {
            // QR code is invalid
            $_SESSION['error'] = [
                'title' => "Invalid QR Code",
                'message' => $result['message']
            ];
            header('Location: /elms/404');
            exit();
        }
    }

    private function logScanDetails($qrData)
    {
        $scanTime = date('Y-m-d H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

        // If behind a proxy, use the forwarded IP
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }

        // Log the scan to the database or a file
        $logData = [
            'user_id' => $qrData['user_id'],
            'qr_token' => $qrData['qr_token'],
            'scan_time' => $scanTime,
            'ip_address' => $ipAddress
        ];

        $qrModel = new QrModel();
        $qrModel->logScan($logData);
    }


    public function action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['userId'] ?? '';
            $date = $_POST['date'];
            $check = $_POST['check'];
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];
            $uuid = $_POST['device_id'] ?? '';
            $ipAddress = $_POST['ip_address'] ?? '';

            // Retrieve QR code and device details from the database
            $qrModel = new QrModel();
            $qrCodeData = $qrModel->getQRCodeByName($userId);

            // Ensure QR code data exists
            if (!$qrCodeData) {
                $_SESSION['error'] = [
                    'title' => "វត្តមានប្រចាំថ្ងៃ",
                    'message' => "ទិន្នន័យ QR Code មិនត្រឹមត្រូវ។"
                ];
                header("Location: /elms/qrcode");
                exit();
            }

            // Validate UUID
            $storedUuid = $qrCodeData['device_id'] ?? null;
            if ($uuid !== $storedUuid) {
                $_SESSION['error'] = [
                    'title' => "វត្តមានប្រចាំថ្ងៃ",
                    'message' => "ឧបករណ៍ស្កេនមិនត្រឹមត្រូវ។ សូមប្រើប្រាស់ឧបករណ៍ដែលបង្កើត QR Code ។"
                ];
                header("Location: /elms/qrcode");
                exit();
            }

            // Validate IP Address
            $storedIp = $qrCodeData['ip_address'] ?? null;
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

            // Handle proxies/load balancers
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $clientIp = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            }

            if ($ipAddress !== $storedIp || $clientIp !== $storedIp) {
                $_SESSION['error'] = [
                    'title' => "វត្តមានប្រចាំថ្ងៃ",
                    'message' => "ទីតាំងមិនត្រឹមត្រូវ។ សូមទៅកាន់ការិយាល័យដើម្បីស្កេន។"
                ];
                header("Location: /elms/qrcode");
                exit();
            }

            // Validate latitude/longitude
            $qrLatitude = $qrCodeData['latitude'] ?? null;
            $qrLongitude = $qrCodeData['longitude'] ?? null;

            if (!is_numeric($qrLatitude) || !is_numeric($qrLongitude)) {
                die('QR Code location data is invalid.');
            }

            $distance = $this->calculateDistance($latitude, $longitude, $qrLatitude, $qrLongitude);
            $maxDistance = 0.1; // 100 meters

            if ($distance > $maxDistance) {
                $_SESSION['error'] = [
                    'title' => "វត្តមានប្រចាំថ្ងៃ",
                    'message' => "ទីតាំងរបស់អ្នកនៅឆ្ងាយជាងកន្លែងធ្វើការ {$distance} គីឡូម៉ែត្រ"
                ];
                header("Location: /elms/my-attendances");
                exit();
            }

            $currentTime = strtotime($check);
            $morningStart = strtotime('07:30:00');
            $lateMorningStart = strtotime('09:00:00');
            $afternoonStart = strtotime('13:00:00');
            $endTime = strtotime('17:30:00');

            $isCheckIn = false; // Default to check-out
            $alertMessage = ""; // Initialize an empty alert message

            // Determine if it's checkIn or checkOut based on time
            if ($currentTime >= $morningStart && $currentTime <= $afternoonStart) {
                $isCheckIn = true; // Morning session
                if ($currentTime >= $lateMorningStart) {
                    $alertMessage = "ចូលយឺត"; // Late in
                }
            } elseif ($currentTime >= $afternoonStart) {
                $isCheckIn = false; // Afternoon session
                if ($currentTime < $endTime) {
                    $alertMessage = "ចេញមុន"; // Late out
                } elseif ($currentTime >= $endTime) {
                    $alertMessage = "ចេញយឺត"; // Leave early
                }
            }

            // Record attendance with the determined type
            $attendanceModel = new AttendanceModel();
            if ($attendanceModel->recordAttendance($userId, $date, $check, $isCheckIn)) {

                // Notify via Telegram
                $userModel = new User();
                $userModel->sendCheckToTelegram($userId, $date, $check, $alertMessage);

                // Display success message with alert (if any)
                $_SESSION['success'] = [
                    'title' => "វត្តមានប្រចាំថ្ងៃ",
                    'message' => "អបអរសាទរអ្នកបានស្កេនវត្តមានដោយជោគជ័យ" . ($alertMessage ? " ({$alertMessage})" : "")
                ];
                header("Location: /elms/my-attendances");
                exit();
            } else {
                $_SESSION['error'] = [
                    'title' => "វត្តមានប្រចាំថ្ងៃ",
                    'message' => "មានកំហុសកើតឡើងសូមធ្វើការស្កេនម្តងទៀត។"
                ];
                header("Location: /elms/my-attendances");
                exit();
            }
        }
    }

    // Helper function to calculate distance using Haversine formula
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance; // Distance in kilometers
    }
}
