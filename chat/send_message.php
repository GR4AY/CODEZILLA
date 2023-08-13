<?php
require_once '../includes/db_connection.php'; // Include your database connection

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['user_id'];
$conversationID = $_POST['conversationID'];
$messageContent = $_POST['messageContent'];

$query = "INSERT INTO messages (conversation_id, user_id, content)
          VALUES (:conversationID, :userID, :messageContent)";
$stmt = $db->prepare($query);
$stmt->bindParam(':conversationID', $conversationID, PDO::PARAM_INT);
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->bindParam(':messageContent', $messageContent, PDO::PARAM_STR);
$stmt->execute();
?>
