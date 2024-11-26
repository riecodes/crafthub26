<?php
ob_start();
session_start();
?>
<style>
#chatHistory {
    max-height: 500px;
    overflow-y: auto;
    padding: 10px;
}

#chatHistory li {
    list-style: none;
    margin: 10px 0;
    clear: both;
}

#chatHistory .sent {
    text-align: right;
}

#chatHistory .received {
    text-align: left;
}

#chatHistory .message-content {
    display: inline-block;
    padding: 10px;
    border-radius: 10px;
    max-width: 70%;
}

#chatHistory .sent .message-content {
    background-color: #f1d3a6;
    color: #000;
}

#chatHistory .received .message-content {
    background-color: lightgrey;
    color: #000;
}

#chatHistory .message-time {
    font-size: 0.8em;
    color: #666;
}

</style>
<div class="messages-section">
    <div class="container">
        <div class="chat-wrapper">
            <div class="sidebar">
                <!--=============== SEARCH ===============-->
                <div class="search-container">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search conversations">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <!--=============== CHAT LIST ===============-->
                <ul class="chat-list" id="tabList">
                    <!-- dynamically populate using ajax -->
                </ul>
            </div>

            <!--=============== CHAT MAIN ===============-->
            <div class="chat-main" id="chat-main" data-chat-with-id="">
                <div class="chat-header">
                    <div class="chat-user-info">
                        <h2 class="chat-user-name">Select a conversation</h2>
                        <p class="chat-user-status"></p>
                    </div>
                    <div class="chat-actions">
                        <button class="btn-action" id="captureBtn"><i class="fas fa-camera"></i></button>
                        <button class="btn-action" id="uploadBtn"><i class="fas fa-image"></i></button>
                    </div>
                </div>
                <div class="chat-history" id="chatHistory">
                    <ul class="m-b-0">
                        <!-- Messages will be populated dynamically -->
                    </ul>
                </div>
                <div class="chat-input-area">
                    <textarea id="messageInput" class="chat-input" placeholder="Type a message..." ></textarea>
                    <button class="btn-send" id="sendBtn" disabled><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div id="cameraContainer" class="camera-container" style="display: none;">
        <video id="videoElement" style="width: 100%; height: auto;"></video>
    </div>
    <input type="file" id="imageUploadInput" style="display: none;" accept="image/*">
</div>
<script>
function searchChats() {
    const searchQuery = document.getElementById('searchInput').value.toLowerCase();  
    const chatListItems = document.querySelectorAll('.chat-list .chat-item');  

    chatListItems.forEach(item => {
        const chatName = item.querySelector('.chat-name').textContent.toLowerCase(); 
        if (chatName.includes(searchQuery)) {
            item.style.display = 'block';  
        } else {
            item.style.display = 'none';  
        }
    });
}

document.getElementById('searchInput').addEventListener('input', searchChats);

function selectChat(element, username, chatWithId, status) {
    // const chatId = element.getAttribute('data-chat-id');
    const chatHistory = document.getElementById('chatHistory');
    const chatUserName = document.querySelector('.chat-user-name');
    const chatUserStatus = document.querySelector('.chat-user-status');

    chatUserName.textContent = username;
    chatUserStatus.textContent = status;

    document.getElementById('chat-main').dataset.chatWithId = chatWithId;

    fetch(`fetch_message.php?chat_with_id=${chatWithId}`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(messages => {
            if (messages.error) {
                chatHistory.innerHTML = `<p class="error-message">${messages.error}</p>`;
                return;
            }

            chatHistory.innerHTML = ''; // Clear previous messages

            messages.forEach(message => {
                let messageItem;

                // Check if message type is text or media
                if (message.type === 'text') {
                    messageItem = `
                        <li class="${message.is_sender ? 'sent' : 'received'}">
                            <div class="message-content">${message.content}</div>
                            <div class="message-time">${message.time}</div>
                        </li>`;
                } else {
                    messageItem = `
                        <li class="${message.is_sender ? 'sent' : 'received'}">
                            <div class="message-content">
                                <img src="${message.media_path}" alt="Sent Media" class="sent-media" style="max-width:200px;">
                            </div>
                            <div class="message-time">${message.time}</div>
                        </li>`;
                }

                chatHistory.innerHTML += messageItem;
            });


            chatHistory.scrollTop = chatHistory.scrollHeight; // Scroll to the bottom
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
            chatHistory.innerHTML = `<p class="error-message">Failed to load messages</p>`;
        });
}


function fetchChatList() {
    fetch('fetch_chat_list_mermessages.php')
        .then(response => response.text())
        .then(html => {
            document.getElementById('tabList').innerHTML = html;
        })
        .catch(error => console.error('Error fetching chat list:', error));
}

// Fetch the chat list every 10 seconds (adjust the interval as needed)
setInterval(fetchChatList, 3000);

// Initial fetch
fetchChatList();

document.getElementById('messageInput').addEventListener('input', function () {
    const sendBtn = document.getElementById('sendBtn');
    sendBtn.disabled = !this.value.trim(); 
});

document.getElementById('sendBtn').addEventListener('click', sendMessage);

function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    const chatHistory = document.getElementById('chatHistory');
    const sendBtn = document.getElementById('sendBtn');

    if (!message) return; // Do nothing if the message is empty

    // Retrieve sender and receiver dynamically
    const senderId = <?= $_SESSION['userID'] ?>; 
    const senderType = 'merchant';
    let receiverId;
    <?php if (isset($_GET['chat_with_id'])) { ?>
        sendBtn.disabled = false;
        receiverId = <?= $_GET['chat_with_id'] ?>;
    <?php } else { ?>
        receiverId = document.getElementById('chat-main').dataset.chatWithId;
    <?php } ?>
    const receiverType = 'user'; 

    if (!senderId || !receiverId) {
        console.error('Sender or Receiver ID not found');
        return;
    }

    const payload = {
        sender_id: senderId,
        sender_type: senderType,
        receiver_id: receiverId,
        receiver_type: receiverType,
        message: message,
    };

    fetch('send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
    })
        .then(response => response.json())
        .then(data => {
            console.log(data.status)
            if (data.status === 'success') {
                const messageItem = `
                    <li class="sent">
                        <div class="message-content">${message}</div>
                        <div class="message-time">${new Date().toLocaleTimeString()}</div>
                    </li>`;
                chatHistory.innerHTML += messageItem;
                chatHistory.scrollTop = chatHistory.scrollHeight; 

                messageInput.value = ''; 
                sendBtn.disabled = true; 

                <?php if(isset($_GET['chat_with_id'])){ ?>
                    window.location.href = './chatroom.php';
                <?php } ?>
            } else {
                console.error('Error sending message:', data.message);
                alert('Failed to send message. Please try again.');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An error occurred while sending the message.');
        });
}

const captureBtn = document.getElementById('captureBtn');
const cameraContainer = document.getElementById('cameraContainer');
const videoElement = document.getElementById('videoElement');

captureBtn.addEventListener('click', () => {
    cameraContainer.style.display = 'block';
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            videoElement.srcObject = stream;
            videoElement.play();
        })
        .catch(error => {
            console.error('Error accessing camera:', error);
            alert('Unable to access camera. Please check your device settings.');
        });
});

videoElement.addEventListener('click', function () {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    canvas.width = videoElement.videoWidth;
    canvas.height = videoElement.videoHeight;
    context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(blob => {
        const file = new File([blob], 'captured-image.png', { type: 'image/png' });

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file); 
        imageUploadInput.files = dataTransfer.files; 

        imageUploadInput.dispatchEvent(new Event('change'));
    }, 'image/png');

    videoElement.srcObject.getTracks().forEach(track => track.stop());
    cameraContainer.style.display = 'none';
});


document.getElementById('uploadBtn').addEventListener('click', function() {
    document.getElementById('imageUploadInput').click();
});

document.getElementById('imageUploadInput').addEventListener('change', function() {
    var fileInput = this;
    var file = fileInput.files[0];

    if (file) {
        var formData = new FormData();
        formData.append('media', file);

        let receiverId;
        <?php if (isset($_GET['chat_with_id'])) { ?>
            receiverId = <?= $_GET['chat_with_id'] ?>;
        <?php } else { ?>
            receiverId = document.getElementById('chat-main').dataset.chatWithId;
        <?php } ?>

        console.log(receiverId)

        formData.append('sender_id', <?= $_SESSION['userID'] ?>);
        formData.append('receiver_id', receiverId);

        fetch('send_message.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            console.log(data)
            if (data.status === 'success') {
                const chatHistory = document.getElementById('chatHistory');
                const mediaItem = `
                    <li class="sent">
                        <div class="message-content">
                            <img src="${data.media_path}" alt="Sent Media" class="sent-media" style="max-width:200px;">
                        </div>
                        <div class="message-time">${new Date().toLocaleTimeString()}</div>
                    </li>`;
                chatHistory.innerHTML += mediaItem;
                chatHistory.scrollTop = chatHistory.scrollHeight;
            } else {
                console.error('Failed to send media:', data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }
});
</script>
<?php
$content = ob_get_clean();
include 'mersidebar.php';
?> 