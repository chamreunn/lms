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

            // Retrieve the QR code data from the database
            $qrModel = new QrModel();
            $qrCodeData = $qrModel->getQRCodeByName($userId); // Fetch QR code data for the user

            // Ensure QR code contains valid latitude and longitude
            $qrLatitude = $qrCodeData['latitude'] ?? null;
            $qrLongitude = $qrCodeData['longitude'] ?? null;

            if (!is_numeric($qrLatitude) || !is_numeric($qrLongitude)) {
                die('QR Code location data is invalid.');
            }

            // Calculate the distance between user and QR code location
            $distance = $this->calculateDistance($latitude, $longitude, $qrLatitude, $qrLongitude);
            $maxDistance = 0.1; // 100 meters in kilometers

            // Check if the user is within the allowed range
            if ($distance <= $maxDistance) {
                // Success: User is within the allowed range
                $_SESSION['success'] = [
                    'title' => "Attendance Check",
                    'message' => "You are within the allowed range. Distance: {$distance} km"
                ];

                // Record attendance
                $attendanceModel = new AttendanceModel();
                if ($attendanceModel->recordAttendance($userId, $date, $check)) {

                    $userModel = new User();
                    $userModel->sendCheckToTelegram($userId, $date,  $check);

                    // Redirect to attendance page
                    header("Location: /elms/my-attendances");
                    exit();
                } else {
                    $_SESSION['error'] = [
                        'title' => "Attendance Check",
                        'message' => "Failed to record attendance. Please try again."
                    ];
                    header("Location: /elms/my-attendances");
                    exit();
                }
            } else {
                // Error: User is too far from the allowed location
                $_SESSION['error'] = [
                    'title' => "Attendance Check",
                    'message' => "You are too far from the allowed location. Distance: {$distance} km"
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
