<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/admin/index.css">
</head>
<body>
  <div class="admin-login-wrapper">
    <form id="adminLoginForm" action="admin_process_login.php" method="POST">
      <h1>Admin Login</h1>
      <div class="admin-input-box">
        <input type="text" name="username" placeholder="ID" required>
        <i class='bx bxs-user'></i>
      </div>
      <div class="admin-input-box password-box">
        <input type="password" name="password" placeholder="Password" required>
        <i class='bx bxs-lock-alt'></i>
      </div>
      <button type="submit" class="admin-btn">Login</button>
      <div id="adminLoginError" class="error-message"></div>
    </form>
  </div>
</body>
</html>
