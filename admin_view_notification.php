<?php
require_once "database.php";

// Fetch all notifications
$sql = "SELECT * FROM admin_notifications ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$notifications = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Inbox</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
        font-family: "Poppins", sans-serif;
        margin: 0;
        padding: 0;
        background-color: #fac08d;
    }
    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        background: white;
        color: black;
        border: none;
        padding: 10px 15px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    .back-btn:hover {
        background: #e89b6e;
    }
    .inbox-container {
        width: 80%;
        margin: auto;
        margin-top: 50px;
        padding: 20px;
        background: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        border-radius: 5px;
    }
    h2, h3 {
        text-align: center;
    }
    .inbox {
        display: flex;
        gap: 20px;
    }
    .message-list {
        flex: 1;
        border-right: 2px solid #ddd;
        padding: 10px;
        max-height: 400px;
        overflow-y: auto;
    }
    .message-content {
        flex: 2;
        padding: 10px;
    }
    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    ul li {
        padding: 10px;
        margin: 5px 0;
        background: #ddd;
        cursor: pointer;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    ul li:hover {
        background: #bbb;
    }
    .delete-btn {
        background: red;
        color: white;
        border: none;
        padding: 5px;
        cursor: pointer;
        border-radius: 5px;
    }
    .delete-btn:hover {
        background: darkred;
    }
    .undo-btn {
        display: block;
        width: 100%;
        margin-top: 10px;
        padding: 10px;
        background: #fac08d;
        text-align: center;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        color: black;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    .undo-btn:disabled {
        background: #fac08d;
        cursor: not-allowed;
    }
    .message-image {
        max-width: 200px;
        height: auto;
        margin-top: 10px;
        border-radius: 5px;
        display: block;
    }
    /* Modal Styles */
    .modal {
        display: none; 
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }
    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border-radius: 5px;
        width: 300px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    .modal-buttons {
        margin-top: 20px;
    }
    .modal-buttons button {
        padding: 10px 20px;
        margin: 0 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .confirm-btn {
        background-color: #28a745;
        color: white;
    }
    .cancel-btn {
        background-color: #dc3545;
        color: white;
    }
  </style>
</head>
<body>

<button class="back-btn" onclick="goBack()">Back</button>

<div class="inbox-container">
    <h2>Admin Inbox</h2>
    <div class="inbox">
        <div class="message-list">
            <h3>Messages</h3>
            <ul id="messages">
                <?php foreach ($notifications as $index => $notification): ?>
                    <li onclick="showMessageDetails(<?php echo $index; ?>)">
                        <span>
                            <strong><?php echo htmlspecialchars($notification['title']); ?>:</strong>
                            <?php echo htmlspecialchars(substr($notification['message'], 0, 25)); ?>...
                        </span>
                        <!-- Ipinapasa ang unique id ng notification -->
                        <button class="delete-btn" onclick="deleteMessage(<?php echo $index; ?>, event, <?php echo $notification['id']; ?>)">Delete</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button class="undo-btn" onclick="undoDelete()" disabled>Undo Last Delete</button>
        </div>
        <div class="message-content">
            <h3>Message Details</h3>
            <p id="message-details">Click a message to view details.</p>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <p>Are you sure you want to delete this message?</p>
    <div class="modal-buttons">
      <button class="confirm-btn" onclick="confirmDelete()">Yes</button>
      <button class="cancel-btn" onclick="cancelDelete()">Cancel</button>
    </div>
  </div>
</div>

<script>
let messages = <?php echo json_encode($notifications); ?>;
let deletedMessages = [];
let currentDeleteIndex = null;
let currentDeleteId = null;

function goBack() {
    window.history.back();
}

function convertDateTimeRange(dateRangeString) {
    let result = dateRangeString;
    if (dateRangeString.includes(" - ")) {
        let parts = dateRangeString.split(" - ");
        let startDate = new Date(parts[0]);
        let endDate = new Date(parts[1]);
        let options = { 
            month: 'long', 
            day: 'numeric', 
            year: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true 
        };
        let startFormatted = startDate.toLocaleString('en-US', options);
        let endFormatted = endDate.toLocaleString('en-US', options);
        result = `${startFormatted} - ${endFormatted}`;
    }
    return result;
}

function parseAndConvertDateInMessage(message) {
    let regex = /(Date|Time):\s*([\d-:\sT]+)\s*-\s*([\d-:\sT]+)/i;
    let match = message.match(regex);
    if (match) {
        let originalRange = match[0];
        let prefix = match[1];
        let start = match[2];
        let end = match[3];
        let converted = convertDateTimeRange(`${start} - ${end}`);
        let newString = `${prefix}: ${converted}`;
        message = message.replace(originalRange, newString);
    }
    message = message.replace(/(AM|PM)Image:/g, '$1 Image:');
    return message;
}

function showMessageDetails(index) {
    if (!messages[index]) {
        document.getElementById("message-details").innerHTML = "Message not found.";
        return;
    }
    const msg = messages[index];
    let formattedCreatedAt = "Unknown Date";
    if (msg.created_at) {
        const dateObj = new Date(msg.created_at);
        const options = {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        };
        formattedCreatedAt = dateObj.toLocaleString('en-US', options);
    }
    let convertedMessage = msg.message ? parseAndConvertDateInMessage(msg.message) : "No Content";
    let imagePath = msg.payment_image ? `images/${msg.payment_image}` : msg.image ? `images/${msg.image}` : '';
    document.getElementById("message-details").innerHTML = `
        <strong>Title:</strong> ${msg.title || "No Title"} <br>
        <strong>Message:</strong> ${convertedMessage} <br>
        <strong>Created At:</strong> ${formattedCreatedAt} <br>
        ${imagePath ? `<br><img src="${imagePath}" alt="Notification Image" class="message-image" onerror="this.style.display='none'">` : ""}
    `;
}

// Modified deleteMessage to show the modal instead of using alert
function deleteMessage(index, event, id) {
    event.stopPropagation();
    currentDeleteIndex = index;
    currentDeleteId = id;
    // Ipakita ang modal
    document.getElementById("deleteModal").style.display = "block";
}

// Function to confirm deletion from the modal
function confirmDelete() {
    // Isagawa ang AJAX call o client-side deletion
    // Para sa halimbawang ito, tatanggalin lang natin sa client-side at ipapakita ang modal para sa deletion.
    deletedMessages.push(messages[currentDeleteIndex]);
    messages.splice(currentDeleteIndex, 1);
    loadMessages();
    document.getElementById("message-details").innerHTML = "Click a message to view details.";
    // Itago ang modal
    document.getElementById("deleteModal").style.display = "none";
}

// Function para i-cancel ang deletion
function cancelDelete() {
    currentDeleteIndex = null;
    currentDeleteId = null;
    document.getElementById("deleteModal").style.display = "none";
}

function loadMessages() {
    const messageList = document.getElementById("messages");
    messageList.innerHTML = "";
    messages.forEach((msg, index) => {
        const li = document.createElement("li");
        li.innerHTML = `
            <span onclick="showMessageDetails(${index})">
                <strong>${msg.title}</strong>: ${msg.message.substring(0, 25)}...
            </span>
            <button class="delete-btn" onclick="deleteMessage(${index}, event, ${msg.id})">Delete</button>
        `;
        messageList.appendChild(li);
    });
    document.querySelector(".undo-btn").disabled = (deletedMessages.length === 0);
}

function undoDelete() {
    if (deletedMessages.length > 0) {
        const lastDeleted = deletedMessages.pop();
        messages.push(lastDeleted);
        loadMessages();
    } else {
        alert("No messages to restore.");
    }
}

loadMessages();
</script>

</body>
</html>
