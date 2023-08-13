<?php
require_once '../includes/db_connection.php'; // Include your database connection

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

$userID = $_SESSION['user_id'];

$query = "SELECT c.id, u.username FROM conversations c
          JOIN participants p ON c.id = p.conversation_id
          JOIN users u ON p.user_id = u.id
          WHERE p.user_id = :userID";
$stmt = $db->prepare($query);
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($conversations);
?>
