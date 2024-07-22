<?php
require_once 'src/controllers/LeavetypeController.php';

// Check session status
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

// Initialize base URL
$base_url = '/elms';

// Initialize LeavetypeController
$controller = new LeavetypeController();

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle form submission for creating a leave type
    if (isset($_POST['create'])) {
        // Prepare data from form
        $data = [
            'name' => $_POST['name'],
            'duration' => $_POST['duration'],
            'color' => $_POST['color'], // Adjust based on your form input for color
            'description' => $_POST['description'],
            'attachment_required' => isset($_POST['attachment_required']) ? 1 : 0
        ];

        // Call store method to create new leave type
        $result = $controller->store($data);

        // Redirect based on the result (adjust as per your application flow)
        if ($result) {
            $_SESSION['success'] = [
                'title' => "បង្កើតប្រភេទច្បាប់",
                'message' => "ប្រភេទច្បាប់ត្រូវបានបង្កើតដោយជោគជ័យ។"
            ];
            header("Location: $base_url/leavetype"); // Redirect to leave types page
            exit();
        }
    }

    // Handle form submission for deleting a leave type
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $result = $controller->delete($id);
        if ($result) {
            $_SESSION['success'] = [
                'title' => "លុបប្រភេទច្បាប់",
                'message' => "ប្រភេទច្បាប់ត្រូវបានលុបដោយជោគជ័យ។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "កំហុសក្នុងការលុបប្រភេទច្បាប់",
                'message' => "មានបញ្ហាក្នុងការលុបប្រភេទច្បាប់។ សូមព្យាយាមម្តងទៀតនៅពេលព្រមានសំណើម។"
            ];
        }
        header("Location: $base_url/leavetype");
        exit();
    }

    // Handle form submission for updating a leave type
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'duration' => $_POST['duration'],
            'color' => $_POST['color'],
            'description' => $_POST['description'],
            'attachment_required' => isset($_POST['attachment_required']) ? 1 : 0
        ];

        // Call update method to update the leave type
        $result = $controller->update($id, $data);

        // Redirect based on the result (adjust as per your application flow)
        if ($result) {
            $_SESSION['success'] = [
                'title' => "កែប្រែប្រភេទច្បាប់",
                'message' => "ប្រភេទច្បាប់ត្រូវបានកែប្រែដោយជោគជ័យ។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "កំហុសក្នុងការកែប្រែប្រភេទច្បាប់",
                'message' => "មានបញ្ហាក្នុងការកែប្រែប្រភេទច្បាប់។ សូមព្យាយាមម្តងទៀតនៅពេលព្រមានសំណើម។"
            ];
        }
        header("Location: $base_url/leavetype");
        exit();
    }
}
?>
