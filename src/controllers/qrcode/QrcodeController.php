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
}