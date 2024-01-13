<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/af303d99ee.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css?v=1<?php echo time()?>">
    <title>Chatbox</title>
</head>
<body>
    <div class="chat-launcher" onclick="chatbox.toggleChatbox()">
        <span class="icon is-large has-text-primary">
        <i class="fa-brands fa-rocketchat"></i>
        </span>
    </div>
    
    <div class="chat-container">
        <div class="chat-header">
            <span class="chat-title">Chatta con LeoGPT</span>
            <div class="buttons-container">
                <button class="renew-btn" onclick="chatbox.renewChat()">⟳</button>
                <button class="close-btn" onclick="chatbox.toggleChatbox()">✕</button>
            </div>
        </div>
        <div class="chat-body" id="chatBody" aria-live="polite">
            <!-- Messages will be added here dynamically -->
        </div>
        <div class="chat-bottom">
            <input type="text" id="messageInput" placeholder="Type your message..." onkeydown="chatbox.sendMessage(event)">
            <button onclick="chatbox.sendMessage()">Send</button>
        </div>
    </div>
    <script src="script.js?v=1<?php echo time()?>"></script>
</body>
</html>
