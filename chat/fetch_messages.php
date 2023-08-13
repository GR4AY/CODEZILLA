<?php
require_once '../includes/db_connection.php'; // Include your database connection

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

if (!isset($_GET['conversationID'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

$conversationID = $_GET['conversationID'];

$query = "SELECT m.content, u.username FROM messages m
          JOIN users u ON m.user_id = u.id
          WHERE m.conversation_id = :conversationID";
$stmt = $db->prepare($query);
$stmt->bindParam(':conversationID', $conversationID);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);
?>
