class Chatbox {
    constructor() {
        this.chatboxVisible = false;
        this.isTyping = false;

        document.addEventListener("DOMContentLoaded", () => {
            this.loadChatFromCookies();
        });
    }

    toggleChatbox() {
        const chatbox = document.querySelector('.chat-container');
        chatbox.style.display = this.chatboxVisible ? 'none' : 'block';
        this.chatboxVisible = !this.chatboxVisible;
    }

    renewChat() {
        document.getElementById('chatBody').innerHTML = '';
        document.cookie = "chatHistory=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        this.helloMessage();
    }

    sendMessage(event) {
        if (event && event.key !== 'Enter') {
            return;
        }

        const inputElement = document.getElementById('messageInput');
        const message = inputElement.value.trim();

        if (message !== '') {
            this.appendMessage('user', message);
            inputElement.value = '';

            this.saveChatToCookies();

            this.appendTypingIndicator();
            this.isTyping = true;

            const chatBody = document.getElementById('chatBody');
            chatBody.scrollTop = chatBody.scrollHeight;

            setTimeout(() => {
                this.removeTypingIndicator();
                this.messageReceived(message);
                this.saveChatToCookies();
            }, 1500);
        }
    }

    messageReceived(message) {
        this.appendMessage('bot', message);
        this.saveChatToCookies();
        const chatBody = document.getElementById('chatBody');
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    appendTypingIndicator() {
        const chatBody = document.getElementById('chatBody');
        const messageBox = document.createElement('div');
        messageBox.className = 'bot-message';
        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'typing-indicator';
        typingIndicator.innerHTML = '<span>.</span><span>.</span><span>.</span>';
        messageBox.appendChild(typingIndicator);
        chatBody.appendChild(messageBox);
        const inputElement = document.getElementById('messageInput');
        inputElement.disabled = true;
        this.isTyping = true;
    }

    removeTypingIndicator() {
        const chatBody = document.getElementById('chatBody');
        const messages = chatBody.querySelectorAll('.bot-message');
        const lastMessage = messages[messages.length - 1];
        if (lastMessage.querySelector('.typing-indicator')) {
            lastMessage.remove();
        }
        const inputElement = document.getElementById('messageInput');
        inputElement.disabled = false;
        this.isTyping = false;
    }

    appendMessage(sender, message) {
        const chatBody = document.getElementById('chatBody');
        const messageBox = document.createElement('div');
        const messageElement = document.createElement('div');
        messageElement.textContent = message;

        if (sender === 'user') {
            messageBox.className = 'user-message';
        } else {
            messageBox.className = 'bot-message';
        }
        messageElement.className = 'message';

        messageBox.appendChild(messageElement);
        chatBody.appendChild(messageBox);
    }

    saveChatToCookies() {
        const chatBody = document.getElementById('chatBody');
        const messages = chatBody.querySelectorAll('.message');

        let chatHistory = [];

        messages.forEach((message) => {
            let from = message.parentElement.className;
            from = from === 'user-message' ? 'user' : 'bot';
            const content = message.textContent;
            chatHistory.push({
                from: from,
                content: content,
            });
        });

        const chatHistoryString = JSON.stringify(chatHistory);
        document.cookie = `chatHistory=${encodeURIComponent(chatHistoryString)}; path=/`;
    }

    loadChatFromCookies() {
        const chatHistoryString = this.getCookie('chatHistory');
        if (chatHistoryString) {
            const chatHistory = JSON.parse(decodeURIComponent(chatHistoryString));
            chatHistory.forEach((message) => {
                this.appendMessage(message.from, message.content);
            });
        }
        this.helloMessage();
        const chatBody = document.getElementById('chatBody');
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    helloMessage() {
        const chatBody = document.getElementById('chatBody');
        const messages = chatBody.querySelectorAll('.message');
        if (messages.length === 0) {
            this.appendMessage('bot', "Ciao, sono LeoGPT.  \r\nDescrivi il tuo problema e ti aiuterò a risolverlo...");
        }
    }
}

// Initialize the chatbox
const chatbox = new Chatbox();
