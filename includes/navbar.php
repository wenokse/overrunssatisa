<!-- navbar -->
<header class="main-header">
  <nav class="navbar navbar-static-top">
    <div class="container">
      <div class="navbar-header">
        <a href="index" class="navbar-brand navbar-width"><b>Overruns Sa Tisa</b></a>
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
          <i class="fa fa-bars"></i>
        </button>
      </div>

      <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
        <ul class="nav navbar-nav">
          <li><a href="index">HOME</a></li>
          <li><a href="about_us">ABOUT US</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">CATEGORY <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <?php
                $conn = $pdo->open();
                try{
                  $stmt = $conn->prepare("SELECT * FROM category");
                  $stmt->execute();
                  foreach($stmt as $row){
                    echo "<li><a href='category?category=".$row['cat_slug']."'>".$row['name']."</a></li>";                  
                  }
                }
                catch(PDOException $e){
                  echo "There is some problem in connection: " . $e->getMessage();
                }
                $pdo->close();
              ?>
            </ul>
          </li>
        </ul>
        <form method="POST" class="navbar-form navbar-left" action="search">
          <div class="input-group">
              <input type="text" class=" form-control" id="navbar-search-input" style="border-radius: 20px;" name="keyword" placeholder="Search for Product" required>
          </div>
        </form>
      </div>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown messages-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-shopping-cart"></i>
                <span class="label label-danger cart_count">0</span>
            </a>
            <ul class="dropdown-menu">
                <li class="header">You have <span class="cart_count">0</span> item(s) in cart</li>
                <li>
                    <ul class="menu" id="cart_menu">
                    </ul>
                </li>
                <li class="footer"><a href="cart_view">Go to Cart</a></li>
            </ul>
          </li>
          <li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell"></i>
        <?php
            $conn = $pdo->open();
            $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM sales WHERE user_id=:user_id AND status=2");
            $stmt->execute(['user_id'=>$_SESSION['user']]);
            $row = $stmt->fetch();
            if($row['numrows'] > 0){
                echo "
                    <span class='label label-warning'>".$row['numrows']."</span>
                ";
            }
            $pdo->close();
        ?>
    </a>
    <ul class="dropdown-menu">
        <li class="header">You have <?php echo $row['numrows']; ?> delivery notification(s)</li>
        <li>
            <ul class="menu">
                <?php
                    $conn = $pdo->open();
                    $stmt = $conn->prepare("
                        SELECT sales.id, sales.pay_id, products.name 
                        FROM sales 
                        LEFT JOIN details ON sales.id = details.sales_id 
                        LEFT JOIN products ON details.product_id = products.id 
                        WHERE sales.user_id = :user_id AND sales.status = 2
                    ");
                    $stmt->execute(['user_id'=>$_SESSION['user']]);
                    foreach($stmt as $row){
                        echo "
                            <li>
                                <a href='profile?order=".$row['id']."'>
                                    <i class='fa fa-truck text-aqua'></i> Product: ".$row['name']." is on delivery
                                </a>
                            </li>
                        ";
                    }
                    $pdo->close();
                ?>
            </ul>
        </li>
    </ul>
</li>

 <!-- Messages Menu -->
 <li class="dropdown messages-menu">
 <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="messageToggle">
      <i class="fa fa-envelope-o"></i>
      <span class="label label-danger" id="unreadCount"></span>
    </a>
        </li>

          <?php
            if(isset($_SESSION['user'])){
              $image = (!empty($user['photo'])) ? 'images/'.$user['photo'] : 'images/profile.jpg';
              echo '
                <li class="dropdown user user-menu">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="'.$image.'" class="user-image" alt="User Image">
                    <span class="hidden-xs">'.$user['firstname'].' '.$user['lastname'].'</span>
                  </a>
                  <ul class="dropdown-menu">
                    <li class="user-header">
                      <img src="'.$image.'" class="img-circle" alt="User Image">
                      <p>
                        '.$user['firstname'].' '.$user['lastname'].'
                        <small>Member since '.date('M. Y', strtotime($user['created_on'])).'</small>
                      </p>
                    </li>
                    <li class="user-footer">
                      <div class="pull-left">
                        <a href="profile" class="btn btn-default btn-flat" style="border-radius: 15px;">Profile</a>
                      </div>
                      <div class="pull-right">
                        <a href="logout" class="btn btn-default btn-flat" style="border-radius: 15px;">Log out</a>
                      </div>
                    </li>
                  </ul>
                </li>
              ';
            }
            else{
              echo "
                <li><a href='login'>LOGIN</a></li>
                <li><a href='signup'>SIGNUP</a></li>
              ";
            }
          ?>
        </ul>
      </div>
    </div>
  </nav>
</header>
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
  <div id="chatWindow" class="chat-window" style="display: none;">
    <div class="chat-header">
    <span id="chatUserName"></span>
      <button id="backToSenders">&times;</button>
    </div>
    <div id="chatMessages" class="chat-messages"></div>
    <div class="chat-input">
      <input type="text" id="messageInput" placeholder="Type a message...">
      <button id="sendMessage">Send</button>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const messageToggle = document.getElementById('messageToggle');
  const messagePanel = document.getElementById('messagePanel');
  const closeMessages = document.getElementById('closeMessages');
  const senderList = document.getElementById('senderList');
  const chatWindow = document.getElementById('chatWindow');
  const chatMessages = document.getElementById('chatMessages');
  const messageInput = document.getElementById('messageInput');
  const sendMessage = document.getElementById('sendMessage');
  const backToSenders = document.getElementById('backToSenders');
  const chatUserName = document.getElementById('chatUserName');
  const unreadCount = document.getElementById('unreadCount');
  const searchBar = document.getElementById('searchBar');

  let allSenders = [];
  let currentChatUserId = null;

  // Toggle message panel
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

  // Load senders list
  function loadSenders() {
    fetch('get_messages')
      .then(response => response.json())
      .then(data => {
        allSenders = data;
        renderSenders(allSenders);
      })
      .catch(error => console.error('Error fetching senders:', error));
  }

  // Render senders list
  function renderSenders(senders) {
    senderList.innerHTML = '';
    if (!senders.length) {
      senderList.innerHTML = '<p>No messages found</p>';
      return;
    }
    
    senders.forEach(sender => {
      const senderElement = document.createElement('div');
      senderElement.classList.add('sender');
      senderElement.innerHTML = `
        <img src="${sender.photo ? 'images/' + sender.photo : 'images/profile.jpg'}" alt="User Photo" />
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

  // Open chat window
  function openChat(sender) {
    currentChatUserId = sender.sender_id;
    chatUserName.textContent = `${sender.firstname} ${sender.lastname}`;
    chatWindow.style.display = 'flex';
    senderList.style.display = '';
    loadChatMessages(sender.sender_id);
  }

  function loadChatMessages(senderId) {
    fetch(`get_chat_messages?sender_id=${senderId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success && data.messages) {
          chatMessages.innerHTML = '';
          data.messages.forEach(message => {
            const messageElement = document.createElement('div');
            messageElement.classList.add('message', message.is_sender ? 'sent' : 'received');
            
            // Create message container
            const messageContainer = document.createElement('div');
            messageContainer.classList.add('message-container');

            // Add photo based on whether message is sent or received
            const photoElement = document.createElement('img');
            if (message.is_sender) {
              // Use sender's photo for sent messages
              photoElement.src = message.user_photo ? 
                `images/${message.user_photo}` : 
                'images/profile.jpg';
              photoElement.alt = 'Your photo';
            } else {
              // Use receiver's photo for received messages
              photoElement.src = message.sender_photo ? 
                `images/${message.sender_photo}` : 
                'images/profile.jpg';
              photoElement.alt = 'Sender photo';
            }
            photoElement.classList.add('user-photo');
            messageContainer.appendChild(photoElement);

            // Create message content wrapper
            const contentWrapper = document.createElement('div');
            contentWrapper.classList.add('div');

            // Add message content
            contentWrapper.innerHTML = `
              <div class="message-bubble">
                <div class="message-content">${message.message}</div>
                <div class="message-timestamp">${message.timestamp_formatted}</div>
              </div>
            `;

            messageContainer.appendChild(contentWrapper);
            messageElement.appendChild(messageContainer);
            chatMessages.appendChild(messageElement);
          });
          
          chatMessages.scrollTop = chatMessages.scrollHeight;
        } else {
          console.error('Failed to load messages:', data.error);
        }
      })
      .catch(error => console.error('Error loading messages:', error));
  }

  function sendMessageFunction() {
    if (!messageInput.value.trim() || !currentChatUserId) return;

    fetch('send_message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            receiver_id: currentChatUserId,
            message: messageInput.value.trim()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            loadChatMessages(currentChatUserId);
        }
    })
    .catch(error => console.error('Error sending message:', error));
}

// Add these event listeners for message input
messageInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault(); // Prevent default to avoid newline
        sendMessageFunction();
    }
});

// Modify existing click event listener
sendMessage.addEventListener('click', function() {
    sendMessageFunction();
});

  // Search functionality
  searchBar.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const filteredSenders = allSenders.filter(sender => 
      sender.firstname.toLowerCase().includes(searchTerm) || 
      sender.lastname.toLowerCase().includes(searchTerm)
    );
    renderSenders(filteredSenders);
  });

  // Update unread count
  function updateUnreadCount() {
    fetch('get_unread_count')
      .then(response => response.json())
      .then(data => {
        unreadCount.textContent = data.unread_count > 0 ? data.unread_count : '';
      });
  }

  // Initial load
  loadSenders();
  updateUnreadCount();

  // Set up auto-refresh
  setInterval(loadSenders, 10000);
  setInterval(updateUnreadCount, 10000);
});

</script>
<style>
 /* Message Panel */
.message-panel {
  position: fixed;
  top: 50px;
  right: -350px;
  width: 350px;
  height: calc(100vh - 50px);
  background-color: #fff;
  box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
  transition: right 0.3s ease;
  display: flex;
  flex-direction: column;
  z-index: 1000;
}

.message-panel.active {
  right: 0;
}

.message-header {
  padding: 10px;
  background-color: #0072b2;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.message-header h3 {
  margin: 0;
  font-size: 18px;
}

.message-header button {
  background: none;
  border: none;
  color: white;
  font-size: 24px;
  cursor: pointer;
}

.search-bar {
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

.search-bar input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 20px;
  outline: none;
}

.sender-list {
  flex: 1;
  overflow-y: auto;
}

.sender {
  display: flex;
  align-items: center;
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
}

.sender:hover {
  background-color: #f5f5f5;
}

.sender img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 15px;
}

.sender div {
  flex: 1;
}

.sender p {
  margin: 5px 0 0;
  color: #666;
  font-size: 14px;
}

.message-status {
  font-size: 12px;
  color: #999;
}

/* Chat Window */
.chat-window {
  position: fixed;
    bottom: 0;
    right: 20px;
    width: 330px;
    max-height: 420px;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    background-color: white;
    z-index: 1001;
    display: flex;
    flex-direction: column;
}
.chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .chat-header {
  padding: 13px;
  background-color: #0072b2; 
  color: white; 
  display: flex;
  align-items: center;
  justify-content: space-between; 
}

.chat-header button {
  background: none; 
  border: none; 
  color: white; 
  font-size: 24px; 
  margin-left: auto; 
  cursor: pointer; 
  transition: color 0.3s; 
}

.chat-header button:hover {
  color: #ffcc00; 
}

.chat-header img {
  margin-right: 10px; 
}


.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 15px;
  background-color: #f5f5f5;
}

.message {
  margin-bottom: 15px;
}

.message-container {
  display: flex;
  align-items: flex-start;
  max-width: 80%;
}

.message.sent .message-container {
  margin-left: auto;
  flex-direction: row-reverse;
}

.user-photo {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  margin: 0 8px;
}

.message-bubble {
  background-color: white;
  padding: 10px;
  border-radius: 15px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.message.sent .message-bubble {
  background-color: #0072b2;
  color: white;
}

.message-content {
  margin-bottom: 5px;
}

.message-timestamp {
  font-size: 12px;
  color: #999;
}

.message.sent .message-timestamp {
  color: rgba(255, 255, 255, 0.8);
}

.chat-input {
  padding: 15px;
  background-color: #fff;
  border-top: 1px solid #ddd;
  display: flex;
  gap: 10px;
}

.chat-input input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 20px;
  outline: none;
}

.chat-input button {
  padding: 10px 20px;
  background-color: #0078ff;
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
}

.chat-input button:hover {
  background-color: #005bb5;
}

</style>
