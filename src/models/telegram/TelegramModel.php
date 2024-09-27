<?php

class TelegramModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo; // Store the PDO instance
    }

    // Method to save chat_id, first_name, and message to the database
    public function saveChatId($user_id, $chat_id, $first_name, $message)
    {
        $stmt = $this->pdo->prepare("INSERT INTO telegram_users (user_id, chat_id, first_name, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $chat_id, $first_name, $message]); // Use execute with an array of parameters
    }

    // Method to get all chat_ids from the database
    public function getChatIds()
    {
        $stmt = $this->pdo->query("SELECT chat_id FROM telegram_users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll with PDO
    }
}
