<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PM&JI Reservify</title>
  <link rel="stylesheet" href="customer_mybookings.css?v=1.1">
</head>
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
    font-family: "Poppins", sans-serif;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    font-weight: normal;
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
  }
  .message-content {
    flex: 2;
    padding: 10px;
  }
  ul {
    list-style: none;
    padding: 0;
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
    font-family: "Poppins", sans-serif;
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
</style>
<body>

<button class="back-btn" onclick="goBack()">Back</button>

<div class="inbox-container">
  <h2>Notifications Inbox</h2>
  <div class="inbox">
    <div class="message-list">
      <h3>Notifications</h3>
      <ul id="messages"></ul>
      <button class="undo-btn" onclick="undoDelete()">Undo Last Delete</button>
    </div>
    <div class="message-content">
      <h3>Notification Details</h3>
      <p id="message-details">Click a notification to view details.</p>
    </div>
  </div>
</div>

<script>
  let messages = [];
  let deletedMessages = [];

  function goBack() {
    window.history.back();
  }

  function fetchNotifications() {
    fetch("fetch_notification.php")
      .then(response => response.json())
      .then(data => {
        // Kung walang time property, i-set ito sa current time
        messages = data.map(msg => {
          if (!msg.time) {
            msg.time = new Date().toISOString();
          }
          return msg;
        });
        loadMessages();
      })
      .catch(error => console.error("Error fetching notifications:", error));
  }

  function loadMessages() {
    const messageList = document.getElementById("messages");
    messageList.innerHTML = "";

    messages.forEach((msg, index) => {
      const li = document.createElement("li");
      li.innerHTML = `
        <span onclick="showMessageDetails(${index})">
          <strong>From PM&JI Pictures:</strong> ${msg.message.substring(0, 25)}...
        </span>
        <button class="delete-btn" onclick="deleteMessage(${index})">Delete</button>
      `;
      messageList.appendChild(li);
    });

    document.querySelector(".undo-btn").disabled = deletedMessages.length === 0;
  }

  function showMessageDetails(index) {
    const msg = messages[index];
    const senderName = msg.sender || "PM&JI Pictures";
    // I-parse ang msg.time at i-format gamit ang 12-hour format
    const dateObj = new Date(msg.time);
    const displayTime = !isNaN(dateObj) ? dateObj.toLocaleString('en-US', { 
      hour12: true,
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric'
    }) : "N/A";

    document.getElementById("message-details").innerHTML = `
      <strong>From:</strong> ${senderName}<br>
      <strong>Message:</strong> ${msg.message}<br>
      <strong>Time:</strong> ${displayTime}
    `;
  }

  function deleteMessage(index) {
    if (confirm("Are you sure you want to delete this notification?")) {
      deletedMessages.push(messages[index]);
      messages.splice(index, 1);
      loadMessages();
      document.getElementById("message-details").innerHTML = "Click a notification to view details.";
    }
  }

  function undoDelete() {
    if (deletedMessages.length > 0) {
      const lastDeleted = deletedMessages.pop();
      messages.push(lastDeleted);
      loadMessages();
    } else {
      alert("No notifications to restore.");
    }
  }

  document.addEventListener("DOMContentLoaded", fetchNotifications);
</script>
</body>
</html>
