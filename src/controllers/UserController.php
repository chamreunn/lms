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
}
