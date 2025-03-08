<?php
session_start();

// Kunin ang email details mula sa URL
if (isset($_GET['email']) && isset($_GET['concern']) && isset($_GET['details'])) {
    $customer_email = htmlspecialchars($_GET['email']);
    $concern = htmlspecialchars($_GET['concern']);
    $concern_details = htmlspecialchars($_GET['details']);
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'admin_manageinq.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Compose Email</title>
  <!-- Include Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    /* Global Styles */
    body {
      font-family: 'Poppins', sans-serif;
      background: #fac08d;
      margin: 0;
      padding: 0;
    }
    .email-container {
      max-width: 600px;
      margin: 50px auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    form {
      display: flex;
      flex-direction: column;
    }
    label {
      font-weight: 500;
      margin-top: 10px;
      color: #555;
    }
    input[type="email"],
    input[type="text"],
    textarea {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    textarea {
      resize: vertical;
    }
    button[type="submit"] {
      background-color: #fac08d;
      color: #fff;
      padding: 12px;
      margin-top: 20px;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-family: 'Poppins', sans-serif;
      color: black;
      font-weight: 500;
    }
    button[type="submit"]:hover {
      background-color: #fac08d;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <h2>Compose Email</h2>
    <form action="send_email.php" method="POST">
      <label for="email">To:</label>
      <input type="email" name="email" value="<?php echo $customer_email; ?>" readonly>
      
      <label for="subject">Subject:</label>
      <input type="text" name="subject" value="Response to Your Inquiry: <?php echo $concern; ?>. Details: <?php echo $concern_details; ?>" required>
      
      <label for="message">Message:</label>
      <textarea name="message" rows="8" required>
      </textarea>
      
      <button type="submit">Send Email</button>
    </form>
  </div>
</body>
</html>
