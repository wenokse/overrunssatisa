<?php
include 'includes/session.php';
include 'includes/header.php';

$conn = $pdo->open();

$shop_id = isset($_GET['id']) ? $_GET['id'] : 1; // Default to shop ID 1 if not specified
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $shop_id]);
$shop = $stmt->fetch();

if (!$shop) {
    echo "Shop not found";
    exit();
}

// Fetch top sales
$stmt = $conn->prepare("
    SELECT * FROM products 
    WHERE user_id = :shop_id
    ORDER BY counter DESC 
    LIMIT 5
");
$stmt->execute(['shop_id' => $shop_id]);
$top_sales = $stmt->fetchAll();

// Fetch all products for the specific shop
$stmt = $conn->prepare("
    SELECT * FROM products 
    WHERE user_id = :shop_id
");
$stmt->execute(['shop_id' => $shop_id]);
$all_products = $stmt->fetchAll();

$pdo->close();
?>
<body class="hold-transition skin-blue layout-top-nav">
<?php 
    if (isset($_SESSION['user'])) {
        include 'includes/navbar.php';
    } else {
        include 'includes/home_navbar.php';
    }
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="container">
   
    <br><br>
    <div class="row">
        <div class="col-md-4">
            <img src="<?php echo (!empty($shop['photo'])) ? 'images/'.$shop['photo'] : 'images/profile.jpg'; ?>" style="width: 250px; height: 250px; object-fit: cover; border-radius: 50%;"
                 alt="<?php echo $shop['store']; ?>" 
                 class="img-responsive shop-image">
        </div>
        <div class="col-md-8 shop-details">
        <h1><?php echo $shop['store']; ?></h1>
            <h3>Contact Us</h3>
            <p>Email: <?php echo $shop['email']; ?></p>
            <p>Phone: <?php echo $shop['contact_info']; ?></p>
            <button id="open-chat-btn" class="open-chat-btn">
    <i class="fa fa-comments"></i> Chat
</button>

        </div>
    </div>
   

<div id="chat-container" class="chat-container" style="display: none;">
    <div class="chat-header">
    <img src="<?php echo (!empty($shop['photo'])) ? 'images/'.$shop['photo'] : 'images/profile.jpg'; ?>" alt="<?php echo $shop['store']; ?>">
        <h4><?php echo $shop['store']; ?></h4>
        <button id="close-chat-btn" class="close-chat-btn">&times;</button>
    </div>
    <div id="chat-messages" class="chat-messages"></div>
    <div class="chat-input">
        <input type="text" id="message-input" placeholder="Type a message...">
        <button id="send-button">
    <i class="fas fa-paper-plane"></i> Send
</button>

    </div>
</div>
<div id="context-menu" class="context-menu" style="display:none;">
    <ul>
        <li id="copy-message">Copy</li>
        <li id="edit-message">Edit</li>
        <li id="delete-message">Delete</li>
    </ul>
</div>
    <h2 class="section-title">Top Sales</h2>
    <div class="row">
        <?php foreach ($top_sales as $product): ?>
            <div class="col-md-3">
                <div class="card product-card">
                    <a href="product.php?product=<?php echo $product['slug']; ?>" class="product-link">
                        <img src="<?php echo (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg'; ?>" 
                             alt="<?php echo $product['name']; ?>" 
                             class="card-img-top">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $product['name']; ?></h4>
                            <p class="card-price">₱<?php echo number_format($product['price'], 2); ?></p>
                            <p class="card-sold">Sold: <?php echo $product['sold']; ?></p>
                        </div>
                    </a>
                </div><br>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 class="section-title">All Products</h2>
    <div class="row">
        <?php foreach ($all_products as $product): ?>
            <div class="col-md-3">
                <div class="card product-card">
                    <a href="product.php?product=<?php echo $product['slug']; ?>" class="product-link">
                        <img src="<?php echo (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg'; ?>" 
                             alt="<?php echo $product['name']; ?>" 
                             class="card-img-top">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $product['name']; ?></h4>
                            <p class="card-price">₱<?php echo number_format($product['price'], 2); ?></p>
                        </div>
                    </a>
                </div><br>
            </div>
        <?php endforeach; ?>
    </div>

<?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script>
const chatMessages = document.getElementById('chat-messages');
const messageInput = document.getElementById('message-input');
const sendButton = document.getElementById('send-button');
const openChatBtn = document.getElementById('open-chat-btn');
const closeChatBtn = document.getElementById('close-chat-btn');
const chatContainer = document.getElementById('chat-container');
const shopId = <?php echo json_encode($shop_id); ?>;
const userId = <?php echo json_encode($_SESSION['user']); ?>;

let lastMessageId = 0;

openChatBtn.addEventListener('click', () => {
    chatContainer.style.display = 'block';
    loadMessages(true);
});

closeChatBtn.addEventListener('click', () => {
    chatContainer.style.display = 'none';
});

function loadMessages(isInitialLoad = false) {
    fetch('chat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get&other_id=${shopId}&last_id=${lastMessageId}`
    })
    .then(response => response.json())
    .then(messages => {
        let shouldScroll = chatMessages.scrollTop + chatMessages.clientHeight === chatMessages.scrollHeight;
        
        messages.forEach(message => {
            if (message.id > lastMessageId) {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', message.sender_id == userId ? 'sent' : 'received');
                messageElement.innerHTML = `
                    <img src="${message.photo ? 'images/' + message.photo : 'images/profile.jpg'}" alt="${message.firstname} ${message.lastname}">
                    <p>${message.message}</p>
                    <span>${new Date(message.timestamp).toLocaleString()}</span>
                `;
                chatMessages.appendChild(messageElement);
                lastMessageId = message.id;
            }
        });

        if (shouldScroll || isInitialLoad) {
            chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to the bottom if the user was already at the bottom or on the initial load
        }
    });
}

sendButton.addEventListener('click', sendMessage);
messageInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

function sendMessage() {
    const message = messageInput.value.trim();
    if (message) {
        fetch('chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=send&receiver_id=${shopId}&message=${encodeURIComponent(message)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                messageInput.value = '';
                loadMessages();
            }
        });
    }
}

// Load messages every 5 seconds when chat is open
let messageInterval;
openChatBtn.addEventListener('click', () => {
    messageInterval = setInterval(() => loadMessages(), 5000);
});
closeChatBtn.addEventListener('click', () => {
    clearInterval(messageInterval);
});

const contextMenu = document.getElementById('context-menu');
let selectedMessageElement = null;

// Add context menu on right-click
chatMessages.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    
    // Get the clicked message
    if (e.target.closest('.message')) {
        selectedMessageElement = e.target.closest('.message');
        const { pageX: x, pageY: y } = e;
        contextMenu.style.left = `${x}px`;
        contextMenu.style.top = `${y}px`;
        contextMenu.style.display = 'block';
    }
});

// Hide context menu if clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.context-menu') && contextMenu.style.display === 'block') {
        contextMenu.style.display = 'none';
    }
});

// Copy message to clipboard
document.getElementById('copy-message').addEventListener('click', function() {
    const messageText = selectedMessageElement.querySelector('p').innerText;
    navigator.clipboard.writeText(messageText).then(() => {
        alert('Message copied to clipboard');
    });
    contextMenu.style.display = 'none';
});

// Edit message
document.getElementById('edit-message').addEventListener('click', function() {
    const messageId = selectedMessageElement.dataset.messageId;
    const currentText = selectedMessageElement.querySelector('p').innerText;

    const newText = prompt('Edit your message:', currentText);
    if (newText !== null && newText !== currentText) {
        fetch('chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=edit&message_id=${messageId}&new_message=${encodeURIComponent(newText)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                selectedMessageElement.querySelector('p').innerText = newText;
            } else {
                alert('Error editing message');
            }
        });
    }
    contextMenu.style.display = 'none';
});

// Delete message (mark as unsent)
document.getElementById('delete-message').addEventListener('click', function() {
    const messageId = selectedMessageElement.dataset.messageId;

    fetch('chat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=delete&message_id=${messageId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            selectedMessageElement.querySelector('p').innerText = 'Message unsent';
        } else {
            alert('Error deleting message');
        }
    });
    contextMenu.style.display = 'none';
});
</script>

<style>
    .chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
    .context-menu {
    position: absolute;
    z-index: 10000;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    padding: 10px;
    width: 120px;
}

.context-menu ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.context-menu ul li {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
}

.context-menu ul li:hover {
    background-color: #f0f0f0;
}

    .open-chat-btn {
    
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    z-index: 1000;
}

.chat-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 300px;
    height: 450px;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background-color: white;
    z-index: 1001;
    display: flex;
    flex-direction: column;
}

.chat-header {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header h3 {
    margin: 0;
}

.close-chat-btn {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}

.chat-messages {
    flex-grow: 1;
    overflow-y: auto;
    padding: 10px;
    max-height: 300px;
}

.message {
    margin-bottom: 10px;
    display: flex;
    align-items: flex-start;
}

.message img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 10px;
}

.message p {
    background-color: #f1f0f0;
    padding: 8px 12px;
    border-radius: 18px;
    max-width: 70%;
}

.message span {
    font-size: 0.8em;
    color: #777;
    margin-left: 10px;
}

.sent {
    flex-direction: row-reverse;
}

.sent img {
    margin-right: 0;
    margin-left: 10px;
}

.sent p {
    background-color: #dcf8c6;
}

.chat-input {
    display: flex;
    padding: 10px;
    background-color: #f8f8f8;
}

.chat-input input {
    flex-grow: 1;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.chat-input button {
    padding: 8px 16px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    margin-left: 10px;
    cursor: pointer;
}
    body {
    background-color: #f8f9fa;
    font-family: 'Arial', sans-serif;
}

.section-title {
    font-size: 24px;
    margin-top: 40px;
    margin-bottom: 20px;
    color: #333;
}

.shop-image {
    border-radius: 10px;
    margin-bottom: 20px;
}

.shop-details {
    padding-top: 20px;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
    overflow: hidden;
    background-color: #fff;
}

.product-card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 1px solid #f0f0f0;
}

.card-body {
    padding: 15px;
}

.card-title {
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
}

.card-price {
    font-size: 16px;
    color: #e74c3c;
    font-weight: bold;
    margin-bottom: 5px;
}

.card-sold {
    font-size: 14px;
    color: #7f8c8d;
}

.product-link {
    text-decoration: none;
    color: inherit;
}

.product-link:hover .card-title {
    color: #3498db;
}

</style>
</body>