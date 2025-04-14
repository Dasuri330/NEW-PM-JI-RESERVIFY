





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PM&JI Admin Login</title>
    <link rel="stylesheet" href="admin_login.css?v=1.1">
</head>
<body>
    <div class="login-container">
        <!-- Logo aligned to the left corner of the login container -->
        <img src="images/reservify_logo.png" alt="PM&JI Logo" class="logo">
        <h1>PM&JI Admin</h1>
        <p>Login</p>
        <form action="admin_login.php" method="POST">

            <input type="text" name="admin_id" placeholder="ID:" required>
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password:" required>
                <img src="images/password_hide.png.png" alt="Toggle Password" id="toggle-password" onclick="togglePassword()">
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>

    <!-- JavaScript for password toggle -->
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleImage = document.getElementById('toggle-password');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleImage.src = 'images/password_unhide.png.png'; // Show "unhide" icon
            } else {
                passwordField.type = 'password';
                toggleImage.src = 'images/password_hide.png.png'; // Show "hide" icon
            }
        }
    </script>
</body>
</html>