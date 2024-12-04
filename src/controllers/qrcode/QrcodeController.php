<?php
require_once 'src/models/qrcode/QrModel.php';

class QrcodeController
{
    public function index()
    {
        // Retrieve the QR code data from the database
        $qrModel = new QrModel();
        $getQRs = $qrModel->getAllUserQRcode();
        // Include the view to display the QR code
        require 'src/views/QRCode/allqr.php';
    }

    public function downloadQR()
    {
        // Check if `qr_id` is provided
        if (isset($_GET['qr_id'])) {
            $qr_id = intval($_GET['qr_id']);

            $qrModel = new QrModel();
            $qr = $qrModel->getQRById($qr_id);

            if ($qr) {
                $qrName = $qr['name'] ?: 'qr_code';
                $qrImage = 'data:image/png;base64,' . $qr['image'];

                // Validate and decode base64 image data
                $base64DataParts = explode(',', $qrImage, 2);
                if (count($base64DataParts) === 2) {
                    $imageData = base64_decode($base64DataParts[1]);

                    if ($imageData !== false) {
                        // Set headers to force file download
                        header('Content-Type: image/png');
                        header('Content-Disposition: attachment; filename="' . $qrName . '.png"');
                        echo $imageData;
                        exit;
                    } else {
                        http_response_code(500);
                        echo "Failed to decode the image data.";
                    }
                } else {
                    http_response_code(400);
                    echo "Invalid base64 image format.";
                }
            } else {
                // QR code not found
                http_response_code(404);
                echo "QR code not found.";
            }
        } else {
            // Invalid request
            http_response_code(400);
            echo "Invalid request.";
        }
    }
}