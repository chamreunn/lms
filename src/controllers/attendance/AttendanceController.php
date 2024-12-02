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
        $userModel = new User();
        $users = $userModel->getUserByIdApi($userId, $_SESSION['token']);

        if ($result['status'] === 'valid') {
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
            try {
                // Retrieve POST data
                $userId = $_POST['userId'] ?? '';  // From QR code
                $uId = $_POST['uid'];             // From session
                $date = $_POST['date'];
                $check = $_POST['check'];
                $latitude = $_POST['latitude'];
                $longitude = $_POST['longitude'];
                $uuid = $_POST['device_id'] ?? '';
                $ipAddress = $_POST['ip_address'] ?? '';
                $roleLeave = $_SESSION['role'];

                // Validate user ID matches session
                if ($userId !== $uId) {
                    throw new Exception("សូមប្រើប្រាស់ QR Code ឬគណនីរបស់អ្នកក្នុងការស្កេនវត្តមានប្រចាំថ្ងៃ។ សូមអរគុណ។");
                }

                // Retrieve QR code data
                $qrModel = new QrModel();
                $qrCodeData = $qrModel->getQRCodeByName($userId);
                if (!$qrCodeData) {
                    throw new Exception("ទិន្នន័យ QR Code មិនត្រឹមត្រូវ។");
                }

                // Validate device UUID
                $storedUuid = $qrCodeData['device_id'] ?? null;
                if ($uuid !== $storedUuid) {
                    throw new Exception("ឧបករណ៍ស្កេនមិនត្រឹមត្រូវ។ សូមប្រើឧបករណ៍ដែលបានចុះឈ្មោះ។");
                }

                // Validate IP address
                $ipModel = new AdminModel();
                $ipData = $ipModel->getIPByAddress($ipAddress);
                if ($ipData && $ipData['status'] === 1) {
                    $storedIp = $qrCodeData['ip_address'] ?? null;
                    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
                    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $clientIp = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
                    }
                    if ($ipAddress !== $storedIp || $clientIp !== $storedIp) {
                        throw new Exception("ទីតាំងមិនត្រឹមត្រូវ។ សូមទៅកាន់ការិយាល័យដើម្បីស្កេន។");
                    }
                }

                // Validate geographic coordinates
                $qrLatitude = $qrCodeData['latitude'] ?? null;
                $qrLongitude = $qrCodeData['longitude'] ?? null;
                if (!is_numeric($qrLatitude) || !is_numeric($qrLongitude)) {
                    throw new Exception("ទិន្នន័យទីតាំង QR Code មិនត្រឹមត្រូវ។");
                }
                $distance = $this->calculateDistance($latitude, $longitude, $qrLatitude, $qrLongitude);
                $maxDistance = 0.1; // 100 meters
                if ($distance > $maxDistance) {
                    throw new Exception("ទីតាំងឆ្ងាយពីកន្លែងធ្វើការជាង {$distance} គីឡូម៉ែត្រ។");
                }

                // Determine time period and status
                $time = new DateTime($check);
                $hour = $time->format('H:i:s');

                $morningStart = "07:30:00";
                $morningEnd = "12:59:59";
                $eveningStart = "13:00:00";
                $eveningEnd = "17:29:59";
                $lateEveningStart = "17:30:00";

                $period = null;
                $statusMessage = "ទាន់ពេល"; // Default status is "on time"

                if ($hour >= $morningStart && $hour <= $morningEnd) {
                    $period = "morning";
                    if ($hour > "09:00:00") {
                        $statusMessage = "ចូលយឺត"; // Late in
                    }
                } elseif ($hour >= $eveningStart && $hour < $lateEveningStart) {
                    $period = "evening";
                    if ($hour < "16:00:00") {
                        $statusMessage = "ចេញមុន"; // Leave early
                    }
                } elseif ($hour >= $lateEveningStart) {
                    $period = "evening";
                    $statusMessage = "ចេញយឺត"; // Late out
                } else {
                    throw new Exception("ម៉ោងមិនត្រឹមត្រូវសម្រាប់ការបញ្ចូលវត្តមាន។");
                }

                // Check for duplicate attendance based on checkIn/checkOut
                $attendanceModel = new AttendanceModel();
                $attendanceData = $attendanceModel->checkAttendanceByDateApi($userId, $date, $_SESSION['token']);

                if ($attendanceData) {
                    if ($period === "morning" && $attendanceData['checkIn']) {
                        throw new Exception("អ្នកបានស្កេនចូលនៅពេលព្រឹករួចរាល់ហើយ។");
                    }
                    if ($period === "evening" && $attendanceData['checkOut']) {
                        throw new Exception("អ្នកបានស្កេនចូលនៅពេលល្ងាចរួចរាល់ហើយ។");
                    }
                }

                // Record attendance via API
                $response = $attendanceModel->recordAttendanceApi($userId, $date, $check, $_SESSION['token']);

                if (!$response['success']) {
                    throw new Exception("មានកំហុសកើតឡើងសូមធ្វើការស្កេនម្តងទៀត។");
                }

                // Notify via Telegram with the status message (only if no duplicate)
                $userModel = new User();
                $userModel->sendCheckToTelegram($userId, $date, $check, $statusMessage);

                // Redirect to appropriate page
                $location = ($roleLeave === 'Admin') ? 'admin-attendances' : 'my-attendances';
                $_SESSION['success'] = [
                    'title' => "វត្តមានប្រចាំថ្ងៃ",
                    'message' => $response[0]['response']['message'] ?? "វត្តមានបានកត់ត្រាដោយជោគជ័យ។"
                ];
                header("Location: /elms/" . $location);
                exit();

            } catch (Exception $e) {
                // Handle exceptions
                $_SESSION['error'] = [
                    'title' => "វត្តមានប្រចាំថ្ងៃ",
                    'message' => $e->getMessage(),
                ];
                header("Location: /elms/qrcode");
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
