<?php

define('BOT_TOKEN', '7893193205:AAGIuomS97k2eV_UJQzkBKYy4dhTLxE966Y');

class TelegramController
{
    private $pdo;
    private $telegramUser = "telegram_users";

    public function __construct()
    {
        global $pdo; // Import the global $pdo variable
        $this->pdo = $pdo; // Assign the global $pdo to the class property
    }

    public function telegramAuth($botUsername)
    {
        // Avoid redirect loop by checking if the user is already authenticated
        if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] === TRUE) {
            header('Location: /elms/dashboard');
            exit;
        }

        if (!isset($_GET['hash'])) {
            $this->redirectToLogin('Telegram hash not found');
        }

        // Process Telegram auth data
        $auth_data = $_GET;
        try {
            $auth_data = $this->checkTelegramAuthorization($auth_data);
            $this->userAuthentication($auth_data); // Authenticate or create/update user
        } catch (Exception $e) {
            $this->redirectToLogin($e->getMessage()); // Redirect to login page on error
        }

        // Redirect or handle after successful authentication
        echo 'User authenticated successfully!';
    }

    function checkTelegramAuthorization($auth_data)
    {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', BOT_TOKEN, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            throw new Exception('Data is NOT from Telegram');
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new Exception('Data is outdated');
        }
        return $auth_data;
    }

    public function userAuthentication($auth_data)
    {
        // Check if user exists by telegram_id
        if ($this->checkUserExists($auth_data['id'])) {
            // If user exists, update their information
            $this->updateExistedUser($auth_data);
        } else {
            // If user does not exist, create a new one
            $this->createNewUser($auth_data);
        }

        // Set session
        $_SESSION['logged-in'] = TRUE;
        $_SESSION['telegram_id'] = $auth_data['id'];

        // Redirect to dashboard after successful authentication
        header('Location: /elms/dashboard');
        exit;
    }

    public function createNewUser($auth_data)
    {
        $sql = "INSERT INTO {$this->telegramUser} 
                (first_name, last_name, user_id, telegram_id, telegram_username, profile_picture, auth_date) 
                VALUES 
                (:first_name, :last_name, :user_id, :telegram_id, :telegram_username, :profile_picture, :auth_date)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'first_name' => $auth_data['first_name'],
            'last_name' => $auth_data['last_name'],
            'user_id' => $_SESSION['user_id'],
            'telegram_id' => $auth_data['id'],
            'telegram_username' => $auth_data['username'],
            'profile_picture' => $auth_data['photo_url'],
            'auth_date' => $auth_data['auth_date']
        ]);

        $_SESSION['success'] = [
            'title' => "ចូលប្រព័ន្ធ",
            'message' => "ចូលប្រព័ន្ធបានជោគជ័យ។"
        ];
    }

    public function updateExistedUser($auth_data)
    {
        $sql = "UPDATE {$this->telegramUser} SET
                first_name = :first_name,
                last_name = :last_name,
                telegram_username = :telegram_username,
                profile_picture = :profile_picture,
                auth_date = :auth_date
                WHERE telegram_id = :telegram_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'first_name' => $auth_data['first_name'],
            'last_name' => $auth_data['last_name'],
            'telegram_username' => $auth_data['username'],
            'profile_picture' => $auth_data['photo_url'],
            'auth_date' => $auth_data['auth_date'],
            'telegram_id' => $auth_data['id']
        ]);

        $_SESSION['success'] = [
            'title' => "ចូលប្រព័ន្ធ",
            'message' => "ចូលប្រព័ន្ធបានជោគជ័យ។"
        ];
    }

    public function checkUserExists($telegram_id)
    {
        $stmt = $this->pdo->prepare("SELECT telegram_id FROM {$this->telegramUser} WHERE telegram_id = :telegram_id");
        $stmt->execute(['telegram_id' => $telegram_id]);
        $user = $stmt->fetch();

        return $user ? true : false;
    }

    // Helper function to redirect to the login page
    private function redirectToLogin($errorMessage = null)
    {
        if ($errorMessage) {
            header('Location: /elms/');
        } else {
            header('Location: /elms/');
        }
        exit;
    }
}
