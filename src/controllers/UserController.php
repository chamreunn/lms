<?php

require_once 'src/models/User.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        // Number of results per page
        $results_per_page = 10;

        // Check if a page number is passed in the GET request, otherwise default to page 1
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;

        // Ensure page number is valid
        if ($page < 1) {
            $page = 1;
        }

        // Call the User API to get all users
        $userController = new User();
        $users = $userController->getAllUserApi($_SESSION['token']);

        // Check if the API call was successful and users were fetched
        if ($users && isset($users['data']) && is_array($users['data'])) {
            // Calculate the total number of users
            $total_users = count($users['data']);

            // Calculate the total number of pages
            $total_pages = ceil($total_users / $results_per_page);

            // Ensure the current page doesn't exceed the total pages
            if ($page > $total_pages) {
                $page = $total_pages;
            }

            // Calculate the starting point for slicing the array
            $start_from = ($page - 1) * $results_per_page;

            // Slice the users array to get only the users for the current page
            $paginated_users = array_slice($users['data'], $start_from, $results_per_page);
        } else {
            // If no users are fetched, set paginated_users to an empty array
            $paginated_users = [];
            $total_pages = 1; // Default to 1 page if there are no users
        }

        // Pass paginated users and pagination details to the view
        require 'src/views/users/index.php';
    }

    public function create()
    {
        $data = [
            'email' => $_POST['email'],
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'role' => $_POST['role'],
            'khmer_name' => $_POST['khmer_name'],
            'english_name' => $_POST['english_name'],
            'gender' => $_POST['gender'],
            'phone' => $_POST['phonenumber'],
            'dob' => $_POST['dob'],
            'address' => $_POST['address'],
            'department' => $_POST['department'],
            'office' => $_POST['office'],
            'position' => $_POST['position'],
            'profile_image' => $this->uploadProfileImage()
        ];

        $this->userModel->create($data);
        $_SESSION['success'] = [
            'title' => "បង្កើតគណនី",
            'message' => "បង្កើតគណនីបានជោគជ័យ។"
        ];
        header('Location: /elms/user_index');
    }

    public function update()
    {
        // Initialize an array to store errors
        $errors = [];

        // Define the data to be updated
        $data = [
            'id' => $_POST['user_id'] ?? null,
            'email' => $_POST['eemail'] ?? null,
            'username' => $_POST['eusername'] ?? null,
            'role' => $_POST['erole'] ?? null,
            'khmer_name' => $_POST['ekhmer_name'] ?? null,
            'english_name' => $_POST['eenglish_name'] ?? null,
            'gender' => $_POST['egender'] ?? null,
            'phone_number' => $_POST['ephone'] ?? null,
            'dob' => $_POST['edob'] ?? null,
            'address' => $_POST['eaddress'] ?? null,
            'department' => $_POST['edepartment'] ?? null,
            'office' => $_POST['eoffice'] ?? null,
            'position' => $_POST['eposition'] ?? null,
            'status' => $_POST['estatus'] ?? null,
            'profile_image' => $this->uploadProfileImage()
        ];

        // Validate each field for presence and validity
        foreach ($data as $key => $value) {
            if ($value === null) {
                $errors[] = "Missing or invalid input for field: $key";
            }
        }

        // If there are errors, redirect to the error page
        if (!empty($errors)) {
            $_SESSION['error'] = implode("<br>", $errors); // Store errors in session
            header('Location: /elms/error_page');
            exit();
        }

        // Proceed with updating the user model if no errors
        $this->userModel->update($data);

        // Set success message if update was successful
        $_SESSION['success'] = [
            'title' => "កែប្រែគណនី",
            'message' => "កែប្រែគណនីបានជោគជ័យ។"
        ];
        header('Location: /elms/user_index');
    }

    public function uploadProfileImage()
    {
        if (isset($_FILES['eavatar-upload']) && $_FILES['eavatar-upload']['error'] == 0) {
            // Set the target directory for uploads
            $target_dir = "public/uploads/profiles/";

            // Create the target directory if it doesn't exist
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            // Generate a unique file name to avoid overwriting existing files
            $target_file = $target_dir . uniqid() . '_' . basename($_FILES['eavatar-upload']['name']);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES['eavatar-upload']['tmp_name']);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $_SESSION['error'] = "File is not an image.";
                $uploadOk = 0;
            }

            // Check file size (5MB maximum)
            if ($_FILES['eavatar-upload']['size'] > 5000000) {
                $_SESSION['error'] = "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedFormats)) {
                $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $_SESSION['error'] = "Sorry, your file was not uploaded.";
                return "public/uploads/profiles/default_image.svg";
                // If everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES['eavatar-upload']['tmp_name'], $target_file)) {
                    return $target_file;
                } else {
                    $_SESSION['error'] = "Sorry, there was an error uploading your file.";
                    return "public/uploads/profiles/default_image.svg";
                }
            }
        }
        return "public/uploads/profiles/default_image.svg";
    }

    public function delete($id)
    {
        if ($this->userModel->delete($id)) {
            $_SESSION['success'] = [
                'title' => "Delete User",
                'message' => "User deleted successfully."
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "Delete User",
                'message' => "Error deleting user."
            ];
        }
        header("Location: /elms/user_index");
        exit();
    }
}
