<?php
require_once 'src/models/routindoc/RoutinDocModel.php';
class RoutinDocController
{
    public function index()
    {
        $reportByUserId = new RoutinDocModel();
        $response = $reportByUserId->getUserReportById($_SESSION['user_id'], $_SESSION['token']);

        // Check if the response is valid and extract reports
        $reports = isset($response['userReports']) ? $response['userReports'] : [];

        require_once 'src/views/routindoc/index.php';
    }

    public function addUserReport()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method.");
            }

            // Validate required fields
            if (empty($_POST['reportStartDate']) || empty($_POST['reportDescription'])) {
                $_SESSION['error'] = [
                    'title' => "បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់",
                    'message' => "សូមបំពេញព័ត៌មានទាំងអស់។"
                ];
                header("Location: /elms/routinDocs");
                exit();
            }

            // Retrieve user session data and form input
            $userId = $_SESSION['user_id'];
            $token = $_SESSION['token'];
            $startDate = $_POST['reportStartDate'];
            $description = $_POST['reportDescription'];
            $note = $_POST['reportNote'] ?? null; // Optional field

            // Validate date format and ensure it is not in the future
            $date = DateTime::createFromFormat('Y-m-d', $startDate);
            if (!$date || $date->format('Y-m-d') !== $startDate) {
                throw new Exception("កាលបរិច្ឆេទមិនត្រឹមត្រូវ។ សូមបញ្ចូលកាលបរិច្ឆេទដោយប្រើទម្រង់ Y-m-d។");
            }
            $currentDateTime = new DateTime();
            if ($date > $currentDateTime) {
                throw new Exception("កាលបរិច្ឆេទមិនអាចនៅអនាគត។ សូមបញ្ចូលកាលបរិច្ឆេទមុនឬនៅបច្ចុប្បន្ន។");
            }

            // Initialize model and call method to insert report
            $reportByUserId = new RoutinDocModel();
            $reportByUserId->addUserReportById($userId, $startDate, $description, $note, $token);

            $_SESSION['success'] = [
                'title' => "របាយការណ៍ប្រចាំថ្ងៃ",
                'message' => "បង្កើតរបាយការណ៍ប្រចាំថ្ងៃបានជោគជ័យ។"
            ];
            header('Location: /elms/routinDocs');
            exit();
        } catch (Exception $e) {
            // Log error for debugging (optional, requires a logging mechanism)
            error_log("Error in addUserReport: " . $e->getMessage());

            // Set error session and redirect
            $_SESSION['error'] = [
                'title' => "បរាជ័យ",
                'message' => $e->getMessage() ?: "មិនអាចបង្កើតរបាយការណ៍ប្រចាំថ្ងៃបានទេ។"
            ];
            header("Location: /elms/routinDocs");
            exit();
        }
    }
}
