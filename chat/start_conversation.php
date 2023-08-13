<?php
require_once '../includes/db_connection.php'; // Include your database connection

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if (!isset($_POST['userID']) || !isset($_POST['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

$userID = $_SESSION['user_id'];
$otherUsername = $_POST['username'];

// Check if the other user exists
$query = "SELECT id FROM users WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $otherUsername);
$stmt->execute();
$otherUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$otherUser) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

// Check if a conversation already exists between the users
$query = "SELECT c.id FROM conversations c
          JOIN participants p ON c.id = p.conversation_id
          WHERE c.type = 'private' AND (
              (p.user_id = :userID AND c.other_user_id = :otherUserID) OR
              (p.user_id = :otherUserID AND c.other_user_id = :userID)
          )";
$stmt = $db->prepare($query);
$stmt->bindParam(':userID', $userID);
$stmt->bindParam(':otherUserID', $otherUser['id']);
$stmt->execute();
$conversation = $stmt->fetch(PDO::FETCH_ASSOC);

if ($conversation) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Conversation already exists']);
    exit();
}

// Create a new conversation
$query = "INSERT INTO conversations (type, other_user_id) VALUES ('private', :otherUserID)";
$stmt = $db->prepare($query);
$stmt->bindParam(':otherUserID', $otherUser['id']);
$stmt->execute();
$conversationID = $db->lastInsertId();

// Add participants to the conversation
$query = "INSERT INTO participants (conversation_id, user_id) VALUES (:conversationID, :userID), (:conversationID, :otherUserID)";
$stmt = $db->prepare($query);
$stmt->bindParam(':conversationID', $conversationID);
$stmt->bindParam(':userID', $userID);
$stmt->bindParam(':otherUserID', $otherUser['id']);
$stmt->execute();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Conversation started', 'conversationID' => $conversationID]);
?>
