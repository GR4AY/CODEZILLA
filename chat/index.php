<?php
require_once '../includes/db_connection.php'; // Include your database connection

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Optional: Check cookies and populate session if needed
if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['username'] = $_COOKIE['username'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/index.css">
    <title>Chat Interface</title>
</head>
<body>
    <div class="chat-container">
        <div class="chat-list">
            <ul id="conversation-list">
                <?php foreach ($conversations as $conversation) { ?>
                    <li data-conversation-id="<?php echo $conversation['id']; ?>"><?php echo $conversation['username']; ?></li>
                <?php } ?>
            </ul>
        </div>
        <div class="chat-area">
            <div id="chat-messages" class="chat-messages">
                <!-- Messages will be added here -->
            </div>
            <div class="send-message">
                <input type="text" id="message-input" placeholder="Type a message...">
                <div class="send-button">
                    <button id="send-button">Send</button>
                </div>
            </div>
        </div>
    </div>
    <div class="start-conversation">
        <input type="text" id="start-conversation-input" placeholder="Search for users...">
        <button id="start-conversation-button">Start Conversation</button>
    </div>
    <script >
        document.addEventListener("DOMContentLoaded", function() {
    const userID = <?php echo $_SESSION['user_id']; ?>;
    const conversationList = document.getElementById("conversation-list");
    const chatMessages = document.getElementById("chat-messages");
    const messageInput = document.getElementById("message-input");
    const sendButton = document.getElementById("send-button");
    const startConversationInput = document.getElementById("start-conversation-input");
    const startConversationButton = document.getElementById("start-conversation-button");

    function fetchConversations() {
        fetch("../chat/fetch_conversations.php")
            .then(response => response.json())
            .then(conversations => {
                conversationList.innerHTML = "";
                conversations.forEach(conversation => {
                    const listItem = document.createElement("li");
                    listItem.textContent = conversation.username;
                    listItem.dataset.conversationId = conversation.id;
                    listItem.addEventListener("click", () => displayMessages(conversation.id));
                    conversationList.appendChild(listItem);
                });
            })
            .catch(error => console.error(error));
    }

    function displayMessages(conversationID) {
        chatMessages.innerHTML = "";
        fetch(`../chat/fetch_messages.php?conversationID=${conversationID}`)
            .then(response => response.json())
            .then(messages => {
                messages.forEach(message => {
                    const messageDiv = document.createElement("div");
                    messageDiv.className = "message";
                    messageDiv.textContent = `${message.username}: ${message.content}`;
                    chatMessages.appendChild(messageDiv);
                });
            })
            .catch(error => console.error(error));
    }

    function sendMessage(conversationID, content) {
        fetch("../chat/send_message.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `conversationID=${conversationID}&messageContent=${encodeURIComponent(content)}`
        })
        .then(response => response.text())
        .then(() => {
            displayMessages(conversationID);
        })
        .catch(error => console.error(error));
    }

    startConversationButton.addEventListener("click", () => {
        const username = startConversationInput.value.trim();
        if (username) {
            fetch("../chat/start_conversation.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `userID=${userID}&username=${encodeURIComponent(username)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchConversations();
                    startConversationInput.value = "";
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error(error));
        }
    });

    fetchConversations();

    sendButton.addEventListener("click", () => {
        const activeConversation = document.querySelector(".active-conversation");
        if (activeConversation) {
            const conversationID = activeConversation.dataset.conversationId;
            const newMessage = messageInput.value;
            if (newMessage) {
                sendMessage(conversationID, newMessage);
                messageInput.value = "";
            }
        }
    });
});

    </script>
</body>
</html>
