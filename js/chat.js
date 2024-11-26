// Chat functionality
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatHistory = document.querySelector('.chat-history ul');
    const captureBtn = document.getElementById('captureBtn');
    const uploadBtn = document.getElementById('uploadBtn');

    // Function to add a message to chat history
    function addMessageToChatHistory(content, direction = 'outgoing', type = 'text') {
        const li = document.createElement('li');
        li.style.display = 'flex';
        li.style.justifyContent = direction === 'outgoing' ? 'flex-end' : 'flex-start';

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${direction === 'outgoing' ? 'sent' : 'received'}`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';

        if (type === 'text') {
            contentDiv.textContent = content;
        } else if (type === 'image') {
            const img = document.createElement('img');
            img.src = content;
            img.style.maxWidth = '200px';
            img.style.borderRadius = '8px';
            contentDiv.appendChild(img);
        }

        messageDiv.appendChild(contentDiv);
        li.appendChild(messageDiv);
        chatHistory.appendChild(li);

        // Scroll to bottom
        chatHistory.parentElement.scrollTop = chatHistory.parentElement.scrollHeight;
    }

    // Send message function
    function sendMessage() {
        const message = messageInput.value.trim();
        if (message && CURRENT_CHAT_PARTNER_ID) {
            // Send message to server
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    sender_id: CURRENT_USER_ID,
                    sender_type: CURRENT_USER_TYPE,
                    receiver_id: CURRENT_CHAT_PARTNER_ID,
                    receiver_type: CURRENT_CHAT_PARTNER_TYPE,
                    message: message,
                    message_type: 'text'
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addMessageToChatHistory(message, 'outgoing', 'text');
                    messageInput.value = '';
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Event listeners for sending messages
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }

    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    // File upload functionality
    if (uploadBtn) {
        uploadBtn.addEventListener('click', function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = function(e) {
                const file = e.target.files[0];
                if (file && CURRENT_CHAT_PARTNER_ID) {
                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('sender_id', CURRENT_USER_ID);
                    formData.append('sender_type', CURRENT_USER_TYPE);
                    formData.append('receiver_id', CURRENT_CHAT_PARTNER_ID);
                    formData.append('receiver_type', CURRENT_CHAT_PARTNER_TYPE);

                    fetch('upload_chat_image.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            addMessageToChatHistory(data.image_path, 'outgoing', 'image');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            };
            input.click();
        });
    }

    // Camera capture functionality
    if (captureBtn) {
        captureBtn.addEventListener('click', function() {
            // Create video and canvas elements
            const video = document.createElement('video');
            const canvas = document.createElement('canvas');
            
            // Create camera modal
            const modal = document.createElement('div');
            modal.className = 'camera-modal';
            modal.innerHTML = `
                <div class="camera-container">
                    <video id="camera-preview" autoplay></video>
                    <div class="camera-controls">
                        <button id="capture-btn" class="btn btn-primary">Capture</button>
                        <button id="close-camera" class="btn btn-danger">Close</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // Get video stream
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    const videoElement = document.getElementById('camera-preview');
                    videoElement.srcObject = stream;
                    
                    // Capture button click handler
                    document.getElementById('capture-btn').onclick = function() {
                        canvas.width = videoElement.videoWidth;
                        canvas.height = videoElement.videoHeight;
                        canvas.getContext('2d').drawImage(videoElement, 0, 0);
                        
                        // Convert to blob and upload
                        canvas.toBlob(function(blob) {
                            const formData = new FormData();
                            formData.append('image', blob, 'capture.jpg');
                            formData.append('sender_id', CURRENT_USER_ID);
                            formData.append('sender_type', CURRENT_USER_TYPE);
                            formData.append('receiver_id', CURRENT_CHAT_PARTNER_ID);
                            formData.append('receiver_type', CURRENT_CHAT_PARTNER_TYPE);

                            fetch('upload_chat_image.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    addMessageToChatHistory(data.image_path, 'outgoing', 'image');
                                }
                            })
                            .catch(error => console.error('Error:', error));
                        }, 'image/jpeg', 0.8);

                        // Close modal and stop stream
                        stream.getTracks().forEach(track => track.stop());
                        modal.remove();
                    };

                    // Close button click handler
                    document.getElementById('close-camera').onclick = function() {
                        stream.getTracks().forEach(track => track.stop());
                        modal.remove();
                    };
                })
                .catch(error => {
                    console.error('Error accessing camera:', error);
                    modal.remove();
                    alert('Unable to access camera. Please make sure you have granted camera permissions.');
                });
        });
    }
});

// Add these styles to your CSS
const styles = `
.camera-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.camera-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    max-width: 90%;
    max-height: 90vh;
}

#camera-preview {
    max-width: 100%;
    max-height: 70vh;
    margin-bottom: 20px;
}

.camera-controls {
    display: flex;
    justify-content: center;
    gap: 10px;
}
`;

// Add styles to document
const styleSheet = document.createElement('style');
styleSheet.textContent = styles;
document.head.appendChild(styleSheet);