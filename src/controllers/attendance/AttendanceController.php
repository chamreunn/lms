<?php
require_once 'src/models/attendance/AttendanceModel.php';

class AttendanceController
{
    public function index()
    {
        require 'src/views/attendence/check.php';
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
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Retrieve QR code and device details from the database
            $qrModel = new QrModel();
            $qrCodeData = $qrModel->getQRCodeByName($userId);

            // Ensure QR code data exists
            if (!$qrCodeData) {
                $_SESSION['error'] = [
                    'title' => "Attendance Check",
                    'message' => "QR Code data not found for this user."
                ];
                header("Location: /elms/attendanceCheck");
                exit();
            }

            // Validate UUID
            $storedUuid = $qrCodeData['device_id'] ?? null;
            if ($uuid !== $storedUuid) {
                $_SESSION['error'] = [
                    'title' => "Attendance Check",
                    'message' => "Unauthorized device. UUID does not match."
                ];
                header("Location: /elms/attendanceCheck");
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
                    'title' => "Attendance Check",
                    'message' => "Unauthorized device. IP address does not match."
                ];
                header("Location: /elms/attendanceCheck");
                exit();
            }

            // Validate User-Agent
            $storedUserAgent = $qrCodeData['user_agent'] ?? null;
            if ($userAgent !== $storedUserAgent) {
                $_SESSION['error'] = [
                    'title' => "Attendance Check",
                    'message' => "Unauthorized device. User-Agent does not match."
                ];
                header("Location: /elms/attendanceCheck");
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
                    'title' => "Attendance Check",
                    'message' => "You are too far from the allowed location. Distance: {$distance} km"
                ];
                header("Location: /elms/attendanceCheck");
                exit();
            }

            // Record attendance
            $attendanceModel = new AttendanceModel();
            if ($attendanceModel->recordAttendance($userId, $date, $check)) {
                $_SESSION['success'] = [
                    'title' => "Attendance Check",
                    'message' => "Attendance recorded successfully."
                ];
                header("Location: /elms/attendanceCheck");
                exit();
            } else {
                $_SESSION['error'] = [
                    'title' => "Attendance Check",
                    'message' => "Failed to record attendance. Please try again."
                ];
                header("Location: /elms/attendanceCheck");
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
