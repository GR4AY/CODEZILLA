document.addEventListener("DOMContentLoaded", function() {
    const conversationList = document.getElementById("conversation-list");
    const chatMessages = document.getElementById("chat-messages");
    const messageInput = document.getElementById("message-input");
    const sendButton = document.getElementById("send-button");

    // Fetch conversations from the server
    fetch("fetch_conversations.php")
        .then(response => response.json())
        .then(conversations => {
            conversations.forEach(conversation => {
                const listItem = document.createElement("li");
                listItem.textContent = conversation.username;
                listItem.addEventListener("click", () => displayMessages(conversation.id));
                conversationList.appendChild(listItem);
            });
        });

    // Display messages for a conversation
    function displayMessages(conversationID) {
        chatMessages.innerHTML = "";
        // Fetch messages for the selected conversation from the server
        fetch(`fetch_messages.php?conversationID=${conversationID}`)
            .then(response => response.json())
            .then(messages => {
                messages.forEach(message => {
                    const messageDiv = document.createElement("div");
                    messageDiv.className = "message";
                    messageDiv.textContent = message.content;
                    chatMessages.appendChild(messageDiv);
                });
            });
    }

    // Send button click event
    sendButton.addEventListener("click", () => {
        // Implement sending messages to the server and updating the UI
    });
});
