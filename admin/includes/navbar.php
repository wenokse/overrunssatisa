
<header class="main-header">
  <!-- Logo -->
  <a href="home.php" class="logo">
    <span class="logo-lg" style="font-size: 12px;">
      <b><?php echo htmlspecialchars($admin['store']); ?></b>
    </span>
  </a>
  
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- Notifications Menu -->
        <li class="dropdown notifications-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-bell-o"></i>
            <?php
            $admin_id = $_SESSION['admin'] ?? null;
            if ($admin_id) {
              $conn = $pdo->open();
              try {
                if ($admin['type'] == 1) {
                  $stmt = $conn->prepare("SELECT COUNT(*) AS pending_count FROM sales WHERE status = 0");
                } else {
                  $stmt = $conn->prepare("SELECT COUNT(*) AS pending_count FROM sales WHERE status = 0 AND admin_id = :admin_id");
                  $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                }
                $stmt->execute();
                $row = $stmt->fetch();
                $pending_count = $row['pending_count'] ?? 0;
              } catch (PDOException $e) {
                $pending_count = 0;
                error_log($e->getMessage());
              }
              $pdo->close();
            }
            
            if (!empty($pending_count)):
            ?>
            <span class="label label-danger"><?php echo $pending_count; ?></span>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have <?php echo $pending_count; ?> pending orders</li>
            <li>
              <ul class="menu">
                <li>
                  <a href="sales.php">
                    <i class="fa fa-shopping-cart text-yellow"></i> <?php echo $pending_count; ?> new orders
                  </a>
                </li>
              </ul>
            </li>
            <li class="footer"><a href="sales.php">View all</a></li>
          </ul>
        </li>

        <!-- Messages Menu -->
        <li class="dropdown messages-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="messageToggle">
            <i class="fa fa-envelope-o"></i>
            <span class="label label-danger" id="unreadCount"></span>
          </a>
        </li>

        <!-- User Account -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="<?php echo (!empty($admin['photo'])) ? '../images/' . htmlspecialchars($admin['photo']) : '../images/profile.jpg'; ?>" class="user-image" alt="User Image">
            <span class="hidden-xs"><?php echo htmlspecialchars($admin['firstname']) . ' ' . htmlspecialchars($admin['lastname']); ?></span>
          </a>
          <ul class="dropdown-menu">
            <li class="user-header">
              <img src="<?php echo (!empty($admin['photo'])) ? '../images/' . htmlspecialchars($admin['photo']) : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image">
              <p>
                <?php echo htmlspecialchars($admin['firstname']) . ' ' . htmlspecialchars($admin['lastname']); ?>
                <small>Member since <?php echo date('M. Y', strtotime($admin['created_on'])); ?></small>
              </p>
            </li>
            <li class="user-footer">
              <div class="pull-left">
                <a href="#profile" data-toggle="modal" class="btn btn-default btn-flat" style="border-radius: 8px;" id="admin_profile">Update</a>
              </div>
              <div class="pull-right">
                <a href="../logout.php" class="btn btn-default btn-flat" style="border-radius: 8px;">Log out</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>

<?php include 'includes/profile_modal.php'; ?>
<!-- Message Panel -->
<div id="messagePanel" class="message-panel">
  <div class="message-header">
    <h3>Messages</h3>
    <button id="closeMessages">&times;</button>
    
  </div>
  <div class="search-bar">
      <input type="text" id="searchBar" placeholder="Search users..." />
    </div>

  <div class="sender-list" id="senderList"></div>

  <!-- Chat Window -->
  <div class="chat-window" id="chatWindow" style="display:none;">
    <div class="chat-header">
      <h3 id="chatUserName"></h3>
      <button id="backToSenders">&times;</button>
    </div>
    <div class="message-list" id="chatMessageList"></div>
    <div class="message-form">
      <form id="sendMessageForm">
        <input type="text" id="messageInput" placeholder="Type a message..." />
        <button type="submit">Send</button>
      </form>
    </div>
  </div>
</div>

<!-- Styles -->
<style>
  .search-bar {
  background-color: #f1f1f1;
  padding: 10px;
  border-top: 1px solid #ddd;
}

.search-bar input {
  border: none;
  padding: 5px;
  border-radius: 20px;
  width: calc(100% - 10px); /* Adjust width to fit padding */
  box-sizing: border-box; /* Ensure padding is included in the width */
}
.message-panel {
  position: fixed;
  top: 50px;
  right: -300px;
  width: 300px;
  height: calc(100% - 50px);
  background-color: #fff;
  border-left: 1px solid #ddd;
  transition: right 0.3s ease-in-out;
  z-index: 1000;
}

.message-panel.active {
  right: 0;
}

.message-header {
  background-color: #3b5998; 
  color: #fff;
  padding: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ddd;
}

.message-header h3 {
  margin: 0;
  font-size: 16px;
}

.message-header button {
  background: none;
  border: none;
  color: #fff;
  font-size: 20px;
 
}



.sender-list {
  height: calc(100% - 160px);
  overflow-y: auto;
}

.sender {
  display: flex;
  align-items: center;
  padding: 10px;
  cursor: pointer;
  border-bottom: 1px solid #ddd;
}

.sender img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
}


#searchBar {
  margin: 10px 0;
  padding: 5px;
}

.conversation-list {
  height: calc(100% - 160px);
  overflow-y: auto;
}

.conversation {
  display: flex;
  align-items: center;
  padding: 10px;
  cursor: pointer;
  border-bottom: 1px solid #ddd;
}

.conversation img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
}

.chat-window {
  width: 300px;
  max-height: 400px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  position: fixed;
  bottom: 0;
  right: 20px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid #ddd;
}

.message-list {
    flex-grow: 1;
    padding: 10px;
    overflow-y: auto;
    max-height: 255px;
}

.message {
    margin: 5px 0;
    padding: 8px;
    border-radius: 10px;
    background-color: #f1f1f1;
}

.sent {
    background-color: #dcf8c6;
    text-align: left;
}
.left-message {
    flex-direction: row;  
}

.right-message {
    flex-direction: row-reverse;  
}
.received {
    background-color: #fff;
    text-align: right;
}

.message-form {
    padding: 10px;
    background-color: #f9f9f9;
    border-top: 1px solid #ddd;
    display: flex;
}

.message-form input {
    flex-grow: 1;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.message-form button {
    margin-left: 10px;
    padding: 8px 12px;
    background-color: #4CAF50;
    color: #fff;
    border: none;
    border-radius: 5px;
}


.chat-header {
  background-color: #3b5998; /* Facebook blue */
  color: #fff;
  padding: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ddd;
}

.chat-header h3 {
  margin: 0;
  font-size: 16px;
}

.chat-header button {
  background: none;
  border: none;
  color: #fff;
  font-size: 20px;
  cursor: pointer;
}


#backToConversations {
  margin-right: 10px;
}

.message-list {
  flex: 1;
  padding: 10px;
  overflow-y: auto;
  border-bottom: 1px solid #ddd;
  overflow-y: auto;
  padding: 10px;
}

.message-list p {
  margin: 0;
  padding: 5px;
}

.message-form {
  background-color: #f1f1f1;
  border-top: 1px solid #ddd;
  padding: 10px;
}

.message-form form {
  display: flex;
  align-items: center;
}

.message-form input {
  flex: 1;
  padding: 5px;
  border-radius: 20px;
  border: 1px solid #ccc;
  margin-right: 5px;
}

.message-form button {
  background-color: #3b5998;
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 5px 10px;
  cursor: pointer;
}
.message-content {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.message-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin: 0 10px;
}

.message-content div {
    display: inline-block;
}

.timestamp {
    font-size: 12px;
    color: #999;
}
.message-status {
  font-size: 12px;
  color: #888;
  margin-left: 0;
}

.message-context-menu {
    position: fixed;
    background-color: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    padding: 5px 0;
    z-index: 1000;
}

.message-context-menu .context-menu-item {
    padding: 8px 12px;
    cursor: pointer;
    transition: background-color 0.2s;
}


.delete-conversation-btn {
    background-color: transparent;
    color: #ff4d4d;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px;
    transition: color 0.3s ease;
}

.delete-conversation-btn:hover {
    color: #ff3333;
}

.delete-conversation-btn i {
    font-size: 18px; /* Adjust size as needed */
}
.message {
    position: relative;
}

</style>
<?php
  if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
    $message = isset($_SESSION['error']) ? $_SESSION['error'] : $_SESSION['success'];
    $icon = isset($_SESSION['error']) ? 'error' : 'success';
    echo "
      <script>
        swal({
          title: '". $message ."',
          icon: '". $icon ."',
          button: 'OK'
        });
      </script>
    ";
    unset($_SESSION['error']);
    unset($_SESSION['success']);
  }
?>
<script src="../js/sweetalert.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const messageToggle = document.getElementById('messageToggle');
  const messagePanel = document.getElementById('messagePanel');
  const closeMessages = document.getElementById('closeMessages');
  const senderList = document.getElementById('senderList');
  const chatWindow = document.getElementById('chatWindow');
  const backToSenders = document.getElementById('backToSenders');
  const chatUserName = document.getElementById('chatUserName');
  const unreadCount = document.getElementById('unreadCount');
  const searchBar = document.getElementById('searchBar');

  let allSenders = [];

  messageToggle.addEventListener('click', function(e) {
    e.preventDefault();
    messagePanel.classList.toggle('active');
    loadSenders();  
  });

  closeMessages.addEventListener('click', function() {
    messagePanel.classList.remove('active');
  });

  backToSenders.addEventListener('click', function() {
    chatWindow.style.display = 'none';
    senderList.style.display = 'block';
  });

  function loadSenders() {
  fetch('get_messages.php')
    .then(response => response.json())
    .then(data => {
      allSenders = data;
      renderSenders(allSenders);
    })
    .catch(error => console.error('Error fetching senders:', error));
}

function renderSenders(senders) {
  senderList.innerHTML = '';  // Clear the sender list
  senders.forEach(sender => {
    const senderElement = document.createElement('div');
    senderElement.classList.add('sender');
    senderElement.innerHTML = `
      <img src="${sender.photo ? '../images/' + sender.photo : '../images/profile.jpg'}" alt="User Photo" />
      <div>
        <strong>${sender.firstname} ${sender.lastname}</strong>
        <p>${sender.message_type === 'sent' ? 
             `${sender.last_message} <span class="message-status">Sent</span>` : 
             `<strong>${sender.last_message}</strong>`}</p>
      </div>
    `;
    senderElement.addEventListener('click', () => openChat(sender));
    senderList.appendChild(senderElement);
  });
}
setInterval(loadSenders, 10000);

loadSenders();


  searchBar.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const filteredSenders = allSenders.filter(sender => 
      sender.firstname.toLowerCase().includes(searchTerm) || 
      sender.lastname.toLowerCase().includes(searchTerm)
    );
    renderSenders(filteredSenders);
  });

  function openChat(sender) {
    senderList.style.display = '';
    chatWindow.style.display = 'block';
    chatUserName.textContent = `${sender.firstname} ${sender.lastname}`;
    currentReceiverId = sender.sender_id;
    loadChatMessages(sender.sender_id);
  }


  function loadChatMessages(senderId) {
    fetch(`get_chat.php?sender_id=${senderId}`)
        .then(response => response.json())
        .then(data => {
            const chatMessageList = document.getElementById('chatMessageList');
            chatMessageList.innerHTML = '';  // Clear the chat message list

            const chatHeader = document.querySelector('.chat-header');

            // Remove the previous delete button if it exists
            const existingDeleteButton = chatHeader.querySelector('.delete-conversation-btn');
            if (existingDeleteButton) {
                existingDeleteButton.remove();
            }

            // Add the delete conversation button
            const deleteConversationButton = document.createElement('button');
            deleteConversationButton.innerHTML = '<i class="fa fa-trash"></i>'; // Use Font Awesome trash icon
            deleteConversationButton.classList.add('delete-conversation-btn');
            deleteConversationButton.setAttribute('title', 'Delete Conversation'); // Add tooltip
            deleteConversationButton.addEventListener('click', () => deleteConversation(senderId));
            chatHeader.appendChild(deleteConversationButton);

            data.forEach(message => {
                const isSent = message.sender_id != senderId; // Reversed logic as per your setup
                
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', isSent ? 'sent' : 'received');
                const messageAlignmentClass = isSent ? 'right-message' : 'left-message';
                
                messageElement.innerHTML = `
                    <div class="message-content ${messageAlignmentClass}">
                        <img src="${message.photo ? '../images/' + message.photo : '../images/profile.jpg'}" alt="User Photo" class="message-photo" />
                        <div>
                            <p>${message.message}</p>
                        </div>
                    </div>
                    <span class="timestamp">${new Date(message.timestamp).toLocaleString()}</span>
                `;
                
                // Add context menu event listener
                messageElement.addEventListener('contextmenu', (e) => showMessageContextMenu(e, message, isSent));
                
                chatMessageList.appendChild(messageElement);
            });
            
            // Scroll to the bottom of the chat
            chatMessageList.scrollTop = chatMessageList.scrollHeight;
        });
}


function deleteConversation(senderId) {
    swal({
        title: 'Are you sure?',
        text: 'You are about to delete this entire conversation.',
        icon: 'warning',
        buttons: ['Cancel', 'Delete'],
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            fetch('delete_conversation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ sender_id: senderId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal('Deleted!', 'Conversation deleted successfully', 'success');
                    // Close the chat window and refresh the sender list
                    document.getElementById('chatWindow').style.display = 'none';
                    document.getElementById('senderList').style.display = 'block';
                    loadSenders();
                } else {
                    swal('Failed!', 'Failed to delete conversation', 'error');
                }
            });
        }
    });
}


function showMessageContextMenu(e, message, isSent) {
    e.preventDefault();
    const contextMenu = document.createElement('div');
    contextMenu.className = 'message-context-menu';
    
    let menuItems = `<div class="context-menu-item" data-action="delete">Delete</div>`;
    
    if (isSent) {
        menuItems += `
            <div class="context-menu-item" data-action="edit">Edit</div>
            <div class="context-menu-item" data-action="unsend">Unsend</div>
        `;
    }
    
    contextMenu.innerHTML = menuItems;
    
    contextMenu.style.position = 'fixed';
    contextMenu.style.left = `${e.clientX}px`;
    contextMenu.style.top = `${e.clientY}px`;
    
    document.body.appendChild(contextMenu);
    
    contextMenu.addEventListener('click', (event) => {
        const action = event.target.getAttribute('data-action');
        if (action) {
            handleMessageAction(action, message);
        }
        contextMenu.remove();
    });
    
    // Close context menu when clicking outside
    document.addEventListener('click', function closeMessageContextMenu(e) {
        if (!contextMenu.contains(e.target)) {
            contextMenu.remove();
            document.removeEventListener('click', closeMessageContextMenu);
        }
    });
}

function handleMessageAction(action, message) {
    switch (action) {
        case 'delete':
            deleteMessage(message.id);
            break;
        case 'edit':
            editMessage(message);
            break;
        case 'unsend':
            unsendMessage(message.id);
            break;
    }
}

function deleteMessage(messageId) {
    swal({
        title: 'Are you sure?',
        text: 'You are about to delete this message.',
        icon: 'warning',
        buttons: ['Cancel', 'Delete'],
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            fetch('delete_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message_id: messageId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadChatMessages(currentReceiverId); 
                    loadSenders();
                } else {
                    swal('Failed!', 'Failed to delete message', 'error');
                }
            });
        }
    });
}

function editMessage(message) {
    swal({
        title: 'Edit your message',
        content: {
            element: 'input',
            attributes: {
                value: message.message,
            },
        },
        button: {
            text: 'Update',
            closeModal: false,
        },
    }).then(newMessage => {
        if (newMessage !== null && newMessage !== message.message) {
            fetch('edit_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message_id: message.id, new_message: newMessage }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadChatMessages(currentReceiverId); 
                    loadSenders();
                } else {
                    swal('Failed!', 'Failed to edit message', 'error');
                }
            });
        }
    });
}

function unsendMessage(messageId) {
    swal({
        title: 'Are you sure?',
        text: 'You are about to unsend this message.',
        icon: 'warning',
        buttons: ['Cancel', 'Unsend'],
        dangerMode: true,
    }).then((willUnsend) => {
        if (willUnsend) {
            fetch('unsend_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message_id: messageId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadChatMessages(currentReceiverId); 
                    loadSenders();
                } else {
                    swal('Failed!', 'Failed to unsend message', 'error');
                }
            });
        }
    });
}

  function updateUnreadCount() {
    fetch('get_unread_count.php')
        .then(response => response.json())
        .then(data => {
            const unreadCountElement = document.getElementById('unreadCount');
            unreadCountElement.textContent = data.unread_count > 0 ? data.unread_count : '';
        });
}
updateUnreadCount();

  setInterval(updateUnreadCount, 60000);

 
  const sendMessageForm = document.getElementById('sendMessageForm');
  sendMessageForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const message = document.getElementById('messageInput').value.trim();
    const receiverId = currentReceiverId; 
    
    if (message) {
        fetch('send_message.php', {
            method: 'POST',
            body: JSON.stringify({ message, receiver_id: receiverId }),
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              loadChatMessages(receiverId); 
          });
        document.getElementById('messageInput').value = '';  
    }
});

  updateUnreadCount();
});
</script>