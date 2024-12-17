<?php
require_once 'src/models/Notification.php';
require_once 'src/models/offices-d/DepOfficeModel.php';
require_once 'src/models/hold/HoldModel.php';

class DepOfficeController
{

    private $pdo;

    protected $table_name = "leave_requests";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Initialize necessary models
                $userModel = new User();
                $leaveRequestModel = new DepOfficeModel();

                // Retrieve session data
                $user_id = $_SESSION['user_id'];
                $user_email = $_SESSION['email'];
                $position = $_SESSION['position'];
                $office = $_SESSION['officeName'];
                $department = $_SESSION['departmentName'];
                $user_name = $_SESSION['user_khmer_name'];

                // Retrieve POST data
                $leave_type_id = $_POST['leave_type_id'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $remarks = $_POST['remarks'];

                // Additional messages
                $message = "$user_name បានស្នើសុំច្បាប់ឈប់សម្រាក។";
                $activity = "បានស្នើសុំច្បាប់ឈប់សម្រាក។";

                // Validate required fields
                $requiredFields = ['leave_type_id', 'start_date', 'end_date'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Missing required fields. Please fill out all fields.");
                    }
                }

                // Validate date range
                $datetime_start = new DateTime($start_date);
                $datetime_end = new DateTime($end_date);
                if ($datetime_end < $datetime_start) {
                    throw new Exception("ថ្ងៃបញ្ចប់មិនអាចតូចជាងថ្ងៃចាប់ផ្ដើម។ សូមពិនិត្យម្តងទៀត");
                }

                // Handle file upload
                $attachment_name = $leaveRequestModel->handleFileUpload(
                    $_FILES['attachment'],
                    ['docx', 'pdf'],
                    2097152,
                    'public/uploads/leave_attachments/'
                );
                if ($attachment_name === false) {
                    throw new Exception("មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត");
                }

                // Fetch leave type details
                $leaveTypeModel = new Leavetype();
                $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
                if (!$leaveType) {
                    throw new Exception("Invalid leave type selected.");
                }
                $leave_type_duration = $leaveType['duration'];

                // Calculate business days
                $duration_days = $leaveRequestModel->calculateBusinessDays($datetime_start, $datetime_end);
                if ($duration_days > $leave_type_duration) {
                    throw new Exception("ប្រភេទច្បាប់នេះមានរយៈពេល {$leave_type_duration} ថ្ងៃ។ សូមពិនិត្យប្រភេទច្បាប់ដែលបានជ្រើសរើសម្តងទៀត");
                }

                // Create leave request
                $leaveRequestId = $leaveRequestModel->create(
                    $user_id,
                    $user_email,
                    $leave_type_id,
                    $position,
                    $office,
                    $department,
                    $leaveType['name'],
                    $start_date,
                    $end_date,
                    $remarks,
                    $duration_days,
                    $attachment_name
                );

                if (!$leaveRequestId) {
                    throw new Exception("Failed to create leave request. Please try again.");
                }

                // Determine approving managers
                $managerApis = [
                    'getEmailLeaderHOApi' => ['column' => 'head_office', 'url' => 'https://leave.iauoffsa.us/elms/'],
                    'getEmailLeaderDDApi' => ['column' => 'dhead_department', 'url' => 'https://leave.iauoffsa.us/elms/'],
                    'getEmailLeaderHDApi' => ['column' => 'head_department', 'url' => 'https://leave.iauoffsa.us/elms/'],
                    'getEmailLeaderDHU1Api' => ['column' => 'dhead_unit', 'url' => 'https://leave.iauoffsa.us/elms/'],
                    'getEmailLeaderDHU2Api' => ['column' => 'dhead_unit', 'url' => 'https://leave.iauoffsa.us/elms/'],
                    'getEmailLeaderHUApi' => ['column' => 'head_unit', 'url' => 'https://leave.iauoffsa.us/elms/']
                ];

                $approvingManagerId = null;
                $approvingManagerEmail = null;
                $approvingManagerName = null;

                foreach ($managerApis as $apiMethod => $details) {
                    $columnToUpdate = $details['column'];
                    $dynamicUrl = $details['url'];

                    $managerDetails = $userModel->$apiMethod($user_id, $_SESSION['token']);
                    if (!$managerDetails || empty($managerDetails['ids'])) {
                        continue;
                    }

                    foreach ($managerDetails['ids'] as $index => $managerId) {
                        $managerEmail = $managerDetails['emails'][$index] ?? null;
                        $managerName = $managerDetails['lastNameKh'][$index] . ' ' . $managerDetails['firstNameKh'][$index];
                        $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                        $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                        if ($isManagerOnLeave || $isManagerOnMission) {
                            // Set appropriate status
                            $approvalStatus = $isManagerOnLeave ? "On Leave" : "Mission";

                            // Update table_approval with "On Leave" or "Mission"
                            $leaveApproval = new DepOfficeModel();
                            $leaveApproval->updateApproval($leaveRequestId, $managerId, $approvalStatus, $remarks);

                            // Update table_name to "Approved" in the relevant column
                            $stmt = $this->pdo->prepare(
                                "UPDATE {$this->table_name} SET $columnToUpdate = 'Approved' WHERE id = ?"
                            );
                            $stmt->execute([$leaveRequestId]);

                            // Notify manager via Telegram with dynamic URL
                            $userModel->sendTelegramNotification(
                                $userModel,
                                $managerId,
                                $start_date,
                                $end_date,
                                $duration_days,
                                $remarks,
                                $leaveRequestId,
                                $dynamicUrl
                            );
                        } else {
                            // Assign the first available manager
                            $approvingManagerId = $managerId;
                            $approvingManagerEmail = $managerEmail;
                            $approvingManagerName = $managerName;
                            break 2; // Exit loops if a valid manager is found
                        }
                    }
                }


                if (!$approvingManagerId) {
                    throw new Exception("No available managers for approval. Please contact support.");
                }

                if (
                    !
                    $leaveRequestModel->sendEmailNotification(
                        $approvingManagerEmail,
                        $message,
                        $leaveRequestId,
                        $start_date,
                        $end_date,
                        $duration_days,
                        $remarks,
                        $leaveType['name']
                    )
                ) {
                    throw new Exception("Notification email could not be sent. Please try again.");
                }

                // Create user activity log and notifications
                $notificationModel = new Notification();
                $notificationModel->createNotification($approvingManagerId, $user_id, $leaveRequestId, $message);
                $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ {$approvingManagerName}"
                ];
                header("Location: /elms/dofficeLeave");
                exit();

            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "Error",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/dofficeLeave");
                exit();
            }
        } else {
            // If request method is not POST, redirect to the dashboard
            header("Location: /elms/dofficeLeave");
            exit();
        }
    }

    public function viewRequests()
    {
        $leaveRequestModel = new DepOfficeModel();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/offices-d/myLeave.php';
    }

    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new DepOfficeModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/offices-d/myLeave.php';
    }

    public function pending()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Validate required POST fields
            // $requiredFields = [
            //     'request_id',
            //     'status',
            //     'uremarks',
            //     'uname',
            //     'uemail',
            //     'leaveType',
            //     'user_id',
            //     'start_date',
            //     'end_date',
            //     'duration'
            // ];

            // foreach ($requiredFields as $field) {
            //     if (empty($_POST[$field])) {
            //         $_SESSION['error'] = [
            //             'title' => "Invalid Input",
            //             'message' => "Missing required fields. Please try again."
            //         ];
            //         header("Location: /elms/pending");
            //         exit();
            //     }
            // }

            // Initialize variables from POST data
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'] ?? ''; // Optional field
            $uremarks = $_POST['uremarks'];
            $uname = $_POST['uname'];
            $uEmail = $_POST['uemail'];
            $leaveType = $_POST['leaveType'];
            $user_id = $_POST['user_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $duration_days = $_POST['duration'];
            $approver_id = $_SESSION['user_id'];

            // Prepare message and additional variables
            $message = $_SESSION['user_khmer_name'] . " បាន " . $status . " ច្បាប់ឈប់សម្រាក។";
            $username = $uname . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";
            $department = $_SESSION['departmentName'];
            $leaveRemarks = "ច្បាប់";
            $mission = "Mission";

            try {
                // Start database transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $leaveApproval = new DepOfficeModel();
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks);

                // Define manager APIs with corresponding columns to update
                $managerApis = [
                    'getEmailLeaderHOApi' => 'head_office',
                    'getEmailLeaderDDApi' => 'dhead_department',
                    'getEmailLeaderHDApi' => 'head_department',
                    'getEmailLeaderDHU1Api' => 'dhead_unit',
                    'getEmailLeaderDHU2Api' => 'dhead_unit',
                    'getEmailLeaderHUApi' => 'head_unit'
                ];

                // Determine the appropriate DHU API based on the department
                if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    unset($managerApis['getEmailLeaderDHU2Api']);
                } else {
                    unset($managerApis['getEmailLeaderDHU1Api']);
                }

                $link = "https://leave.iauoffsa.us/elms/";
                $approvingManagerId = $approvingManagerEmail = $approvingManagerName = null;

                $userModel = new User();

                foreach ($managerApis as $apiMethod => $columnToUpdate) {
                    $managerDetails = $userModel->$apiMethod($user_id, $_SESSION['token']);
                    if (!$managerDetails || empty($managerDetails['ids'])) {
                        continue; // Skip if no managers found
                    }

                    foreach ($managerDetails['ids'] as $index => $managerId) {
                        $managerEmail = $managerDetails['emails'][$index] ?? null;
                        $managerName = $managerDetails['lastNameKh'][$index] . ' ' . $managerDetails['firstNameKh'][$index];

                        // Check if the manager is available
                        $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                        $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                        if ($isManagerOnLeave || $isManagerOnMission) {
                            // Update approval status for unavailable manager in table_approval
                            $approvalStatus = $isManagerOnLeave ? "On Leave" : "Mission";
                            $approvalRemarks = $isManagerOnLeave ? $leaveRemarks : $mission;

                            $leaveApproval->updatePendingApproval($request_id, $managerId, $approvalStatus, $approvalRemarks);

                            // Update the specific column in table_name
                            $stmt = $this->pdo->prepare(
                                "UPDATE {$this->table_name} SET $columnToUpdate = 'Approved' WHERE id = ?"
                            );
                            $stmt->execute([$request_id]);

                            // Notify manager via Telegram
                            $userModel->sendTelegramNextManager(
                                $managerId,
                                $uname,
                                $start_date,
                                $end_date,
                                $duration_days,
                                $remarks,
                                $status,
                                $link
                            );
                        } else {
                            // Assign the first available manager
                            $approvingManagerId = $managerId;
                            $approvingManagerEmail = $managerEmail;
                            $approvingManagerName = $managerName;
                            break 2; // Exit both loops once a valid manager is found
                        }
                    }
                }

                if (!$approvingManagerId) {
                    throw new Exception("No available managers for approval. Please contact support.");
                }

                $userModel->sendBackToUser($user_id, $uname, $start_date, $end_date, $duration_days, $uremarks, $status);

                // Notify the head office via email
                if (!$leaveApproval->sendEmailNotificationToHOffice($approvingManagerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                    throw new Exception("Failed to send email to the office.");
                }

                // Send confirmation email to the user
                if (!$leaveApproval->sendEmailBackToUser($uEmail, $_SESSION['user_khmer_name'], $request_id, $status, $updatedAt, $remarks)) {
                    throw new Exception("Failed to send confirmation email to the user.");
                }

                // Create a notification for the user
                $notificationModel = new Notification();
                $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                // Log the activity
                $activity = "បាន " . $status . " ច្បាប់ឈប់សម្រាក " . $uname;
                $userModel->logUserActivity($approver_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Define notification details
                $notificationTitle = "ច្បាប់ឈប់សម្រាក";
                $notificationMessage = $_SESSION['user_khmer_name'] . " បានស្នើសុំច្បាប់ឈប់សម្រាកពី $start_date ដល់ $end_date ។";
                $notificationProfile = $_SESSION['user_profile'];
                $notificationLink = ($_SERVER['SERVER_NAME'] === '127.0.0.1') ? 'http://127.0.0.1/elms/pending' : 'https://leave.iauoffsa.us/elms/pending';

                // Create the in-app notification
                $notificationModel = new NotificationModel();
                $notificationModel->createNotification($approvingManagerId, $notificationTitle, $notificationMessage, $notificationLink, $notificationProfile);


                // Commit the transaction
                $this->pdo->commit();

                // Set success message and redirect
                $_SESSION['success'] = [
                    'title' => "សំណើច្បាប់",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $approvingManagerName
                ];
                header('Location: /elms/pending');
                exit();

            } catch (Exception $e) {
                // Rollback transaction and handle errors
                $this->pdo->rollBack();

                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "បញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/pending");
                exit();
            }
        } else {
            // Handle GET request to view pending leave requests
            $leaveApprovalModel = new DepOfficeModel();
            $requests = $leaveApprovalModel->getAllLeaveRequests();

            // Initialize the UserModel
            $userModel = new User();

            // Get approver based on role and department
            $approver = $userModel->getApproverByRoleWithoutAvailabilityCheck($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);

            // Initialize the HoldModel to retrieve any holds for the current user
            $holdsModel = new HoldModel();
            $holds = $holdsModel->getHoldByuserId($_SESSION['user_id']);

            // Initialize the TransferoutModel to retrieve transfer-out details for the current user
            $transferoutModel = new TransferoutModel();
            $transferouts = $transferoutModel->getTransferoutByUserId($_SESSION['user_id']);

            // Initialize the HoldModel to retrieve any holds for the current user
            $resignsModel = new ResignModel();
            $resigns = $resignsModel->getResignByuserId($_SESSION['user_id']);

            // Initialize the backWork to retrieve transfer-out details for the current user
            $backworkModel = new BackworkModel();
            $backworks = $backworkModel->getBackworkByUserId($_SESSION['user_id']);

            // Initialize the LeaveType model and retrieve all leave types
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            // Load the approval view
            require 'src/views/leave/offices-d/approvals.php';
        }
    }

    public function action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate required session and form values
            if (!isset($_SESSION['user_id'], $_SESSION['user_khmer_name'], $_SESSION['departmentName'], $_POST['holdId'], $_POST['approverId'], $_POST['uId'], $_POST['status'])) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "ទិន្នន័យមិនគ្រប់គ្រាន់សម្រាប់បញ្ជូនសំណើ។"
                ];
                header("Location: /elms/pending");
                exit();
            }

            // Extract session and form data
            $approverId = $_SESSION['user_id'];
            $approverName = $_SESSION['user_khmer_name'];
            $department = $_SESSION['departmentName'];

            $holdId = $_POST['holdId'];
            $nextApproverId = $_POST['approverId'];
            $uId = $_POST['uId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'] ?? '';

            // User request details
            $uName = $_POST['uName'] ?? '';
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';
            $duration = $_POST['duration'] ?? '';
            $reason = $_POST['reason'] ?? '';
            $actionAt = date('Y-m-d h:i:s A');

            $title = "លិខិតព្យួរការងារ";

            try {
                // Ensure a valid connection and start transaction
                if (!$this->pdo->inTransaction()) {
                    $this->pdo->beginTransaction();
                }

                // Models
                $leaveApproval = new DepOfficeModel();
                $userModel = new User();

                // Determine manager API based on department
                $managersApi = in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])
                    ? 'getEmailLeaderDHU1Api'
                    : 'getEmailLeaderDHU2Api';

                // Update hold approval
                $leaveApproval->updateHoldApproval($approverId, $holdId, $action, $comment);

                // Delegate to next manager
                $leaveApproval->delegateManager($leaveApproval, $userModel, $managersApi, $holdId, $approverId);

                // Send notifications
                $userModel->sendDocBackToUser($title, $uId, $approverName, $action, $actionAt, $comment);
                $userModel->sendDocToNextApprover(
                    $title,
                    $comment,
                    $actionAt,
                    $nextApproverId,
                    $approverName,
                    $uName,
                    $action,
                    $start_date,
                    $end_date,
                    $duration,
                    $reason
                );

                // Define notification details
                $notificationMessageToUser = $approverName . " បាន " . $action . "ស្នើលិខិតព្យួរការងារ";
                $notificationProfile = $_SESSION['user_profile'];
                $notificationLink = ($_SERVER['SERVER_NAME'] === '127.0.0.1')
                    ? 'http://127.0.0.1/elms/headofficepending'
                    : 'https://leave.iauoffsa.us/elms/pending';

                // Create the in-app notification
                $notificationModel = new NotificationModel();
                $notificationModel->createNotification(
                    $uId,            // Target user ID (requestor)
                    $title,
                    $notificationMessageToUser,
                    $notificationProfile
                );

                // Notify the next approver
                $notificationModel->createNotification(
                    $nextApproverId, // Target user ID (next approver)
                    $title,
                    $notificationMessageToUser,
                    $notificationLink,
                    $notificationProfile
                );

                // Commit transaction
                if ($this->pdo->inTransaction()) {
                    $this->pdo->commit();
                }

                // Success message
                $_SESSION['success'] = [
                    'title' => $title,
                    'message' => "អ្នកបាន " . htmlspecialchars($action) . " លើលិខិតព្យួរការងាររួចរាល់។"
                ];
                header("Location: /elms/pending");
                exit();
            } catch (Exception $e) {
                // Rollback transaction if active
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }

                // Log and show error
                error_log("Error in action: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "បញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/pending");
                exit();
            }
        } else {
            // Invalid request method
            $_SESSION['error'] = [
                'title' => "កំហុស",
                'message' => "វិធីសំណើមិនត្រឹមត្រូវ។"
            ];
            header("Location: /elms/pending");
            exit();
        }
    }

    public function actiontransferout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Get values from form and session
            $userId = $_SESSION['user_id'];
            $transferoutId = $_POST['transferoutId'];
            $approverId = $_POST['approverId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'];
            $department = $_SESSION['departmentName'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $transferoutapproval = new DepOfficeModel();
                $userModel = new User();

                if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $managers = 'getEmailLeaderDHU1Api';
                } else {
                    $managers = 'getEmailLeaderDHU2Api';
                }

                $transferoutapproval->updateTransferoutApproval($userId, $transferoutId, $action, $comment);
                // Recursive manager delegation
                $transferoutapproval->delegateManagerTransferout($transferoutapproval, $userModel, $managers, $transferoutId, $userId);

                if ($transferoutapproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតព្យួរការងារ",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតព្យួរការងាររួចរាល់។"
                    ];
                    header("Location: /elms/pending");
                    exit();
                }
                // Commit transaction after successful approval update
                $this->pdo->commit();
            } catch (Exception $e) {
                // Rollback transaction in case of error
                $this->pdo->rollBack();

                // Log the error and set error message
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "បញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/pending");
                exit();
            }
        }
    }

    public function actionResign()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Get values from form and session
            $userId = $_SESSION['user_id'];
            $resignId = $_POST['resignId'];
            $approverId = $_POST['approverId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'];
            $department = $_SESSION['departmentName'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $resignApproval = new DepOfficeModel();
                $userModel = new User();

                if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $managers = 'getEmailLeaderDHU1Api';
                } else {
                    $managers = 'getEmailLeaderDHU2Api';
                }

                $resignApproval->updateResignApproval($userId, $resignId, $action, $comment);

                // Recursive manager delegation
                $resignApproval->delegateResignManager($resignApproval, $userModel, $managers, $resignId, $userId);

                if ($resignApproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតលាឈប់",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតលាឈប់រួចរាល់។"
                    ];
                    header("Location: /elms/pending");
                    exit();
                }
                // Commit transaction after successful approval update
                $this->pdo->commit();
            } catch (Exception $e) {
                // Rollback transaction in case of error
                $this->pdo->rollBack();

                // Log the error and set error message
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "បញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/pending");
                exit();
            }
        }
    }

    public function actionback()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Get values from form and session
            $userId = $_SESSION['user_id'];
            $backworkId = $_POST['backworkId'];
            $approverId = $_POST['approverId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'];
            $department = $_SESSION['departmentName'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $backworkApproval = new DepOfficeModel();
                $userModel = new User();

                if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $managers = 'getEmailLeaderDHU1Api';
                } else {
                    $managers = 'getEmailLeaderDHU2Api';
                }

                $backworkApproval->updateTransBackworkApproval($userId, $backworkId, $action, $comment);
                // Recursive manager delegation
                $backworkApproval->delegateManagerBackwork($backworkApproval, $userModel, $managers, $backworkId, $userId);

                if ($backworkApproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតព្យួរការងារ",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតព្យួរការងាររួចរាល់។"
                    ];
                    header("Location: /elms/pending");
                    exit();
                }
                // Commit transaction after successful approval update
                $this->pdo->commit();
            } catch (Exception $e) {
                // Rollback transaction in case of error
                $this->pdo->rollBack();

                // Log the error and set error message
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "បញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/pending");
                exit();
            }
        }
    }

    public function approved()
    {
        $leaveRequestModel = new DepOfficeModel();
        $requests = $leaveRequestModel->getdhapproved($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/offices-d/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new DepOfficeModel();
        $requests = $leaveRequestModel->getdhrejected($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/offices-d/rejected.php';
    }

    public function viewDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new DepOfficeModel();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            if ($request) {
                require 'src/views/leave/offices-d/viewLeave.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }

    public function viewLeaveDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new DepOfficeModel();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            if ($request) {
                require 'src/views/leave/offices-d/viewLeaveDetail.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }

    public function delete($id)
    {
        $deleteLeaveRequest = new DepOfficeModel();
        if ($deleteLeaveRequest->deleteLeaveRequest($id)) {
            $_SESSION['success'] = [
                'title' => "លុបសំណើច្បាប់",
                'message' => "លុបសំណើច្បាប់បានជោគជ័យ។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "លុបសំណើច្បាប់",
                'message' => "មិនអាចលុបសំណើច្បាប់នេះបានទេ។"
            ];
        }
        header("Location: /elms/dofficeLeave");
        exit();
    }
}
