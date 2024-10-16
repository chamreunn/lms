<?php

class TelegramModel
{
    private $pdo;

    private $telegramUser = "telegram_users";

    public function __construct($pdo)
    {
        $this->pdo = $pdo; // Store the PDO instance
    }

    // Method to get all chat_ids from the database
    public function getTelegramUserData($userId)
    {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->telegramUser} WHERE user_id = :user_id");

        // Execute the statement with the user_id from the session or passed argument
        $stmt->execute(['user_id' => $userId]);

        // Fetch all matching data (assuming one record per user)
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the user data if found, otherwise return null
        return $result ? $result : null;
    }

    public function disconnectTelegram($telegram_id)
    {
        // Prepare the SQL statement to delete the record associated with the telegram_id
        $stmt = $this->pdo->prepare("DELETE FROM {$this->telegramUser} WHERE telegram_id = :telegram_id");

        // Execute the statement
        return $stmt->execute(['telegram_id' => $telegram_id]);
    }

    public function sendTelegramNotification($telegramId, $message, $keyboard = null)
    {
        $token = "7893193205:AAGIuomS97k2eV_UJQzkBKYy4dhTLxE966Y"; // Replace with your correct bot token

        // Prepare the data array
        $data = [
            'chat_id' => $telegramId,
            'text' => $message,
            'parse_mode' => 'Markdown', // Optional: to use Markdown for formatting
        ];

        // Add keyboard to the data if it exists
        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard); // Use json_encode for reply_markup
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Properly format data
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Optional: Disable SSL host verification
        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            error_log("CURL Error: $error");
        }

        curl_close($ch);

        // Log the response for debugging
        error_log("Telegram Response: " . print_r($response, true));

        return $response;
    }
}
