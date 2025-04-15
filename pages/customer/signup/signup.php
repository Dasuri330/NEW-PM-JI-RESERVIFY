<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/vendor/autoload.php';

$host = 'localhost';
$db = 'db_pmji';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  die('Database connection failed: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $firstName = trim($_POST['firstName']);
  $middleName = trim($_POST['middleName']);
  $lastName = trim($_POST['lastName']);
  $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
  $contact = trim($_POST['contact']);
  $password = $_POST['Password'];
  $confirmPassword = $_POST['confirmPassword'];

  // Basic validation
  if (!$firstName || !$lastName || !$email || !$contact || !$password || !$confirmPassword) {
    die('Please fill in all required fields.');
  }

  if ($password !== $confirmPassword) {
    die('Passwords do not match.');
  }

  // Enforce minimum password length of 8 characters
  if (strlen($password) < 8) {
    die('Password must be at least 8 characters long.');
  }

  // Additional validation: check if contact number only contains digits and proper length
  if (!preg_match('/^\d{10,15}$/', $contact)) {
    die('Please enter a valid contact number with 10 to 15 digits.');
  }

  // Hash password and generate verification token
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $verificationToken = bin2hex(random_bytes(16));
  $tokenCreatedAt = date("Y-m-d H:i:s");

  // Make sure the tbl_users table has a column for contact, e.g., "contact"
  $sql = "INSERT INTO tbl_users 
            (first_name, middle_name, last_name, email, contact_no, password, verification_token, token_created_at)
            VALUES (:firstName, :middleName, :lastName, :email, :contact_no, :password, :verificationToken, :tokenCreatedAt)";
  $stmt = $pdo->prepare($sql);

  try {
    $stmt->execute([
      ':firstName' => $firstName,
      ':middleName' => $middleName,
      ':lastName' => $lastName,
      ':email' => $email,
      ':contact_no' => $contact,
      ':password' => $hashedPassword,
      ':verificationToken' => $verificationToken,
      ':tokenCreatedAt' => $tokenCreatedAt,
    ]);

    // Set up PHPMailer for SMTP
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'skypemain01@gmail.com';
      $mail->Password = 'nxkt whiw tlft udhl';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      $mail->setFrom('skypemain01@gmail.com', 'PMJI Support');
      $mail->addAddress($email, $firstName . ' ' . $lastName);
      $mail->isHTML(true);
      $mail->Subject = 'Verify your email address';

      // construct verification link (adjust the URL as needed for local or production)
      $verificationLink = "http://localhost/NEW-PM-JI-RESERVIFY/pages/customer/signup/verify.php?email="
        . urlencode($email) . "&token=" . $verificationToken;

      $mail->Body = "
                <h3>Hello {$firstName},</h3>
                <p>Thank you for signing up. Click the link below to Activate your account:</p>
                <a href='{$verificationLink}'>Activate Account</a>
                <p>If you did not register with PM&JI Reversify you can safely ignore this message.</p>
            ";

      $mail->send();

      // On success, display a professional confirmation page.
      echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up Successful</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #fac08d;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      position: relative;
      min-height: 100vh;
      padding-bottom: 80px;
      text-align: left;
    }
    .container {
      max-width: 800px;
      margin: 100px auto 0 auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      text-align: center;
    }
    h1 {
      text-align: left;
      font-size: 2rem;
      margin-bottom: 20px;
    }
    .header-icon {
      font-size: 1.5rem;
      color: green;
      margin-right: 10px;
    }
    p {
      text-align: left;
      font-size: 1.1rem;
      margin-bottom: 20px;
    }
    .instructions {
      text-align: left;
      margin-top: 30px;
      font-size: 0.95rem;
      color: #555;
    }
    .instructions ul {
      padding-left: 20px;
    }
    .btn-custom {
      background-color: #f4a36c;
      border: none;
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s;
    }
    .btn-custom:hover {
      background-color: #e3caab;
      color: #000;
    }
    .sticky-footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background: #fff;
      border-top: 1px solid #ddd;
      padding: 15px;
      text-align: center;
      font-size: 1rem;
      box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
    }
    .sticky-footer a {
      text-decoration: none;
      color: #f4a36c;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1><span class="header-icon">&#10003;</span>Check your Email</h1>
    <p>We've sent you an email. Click the link in the email to activate your account.</p>
    <div class="instructions">
      <p><strong>Not receiving it?</strong></p>
      <ul>
        <li>Check other places it might be, like your junk, spam, social, or other folders.</li>
        <li>Make sure your email address is correct: {$email}</li>
        <li>Depending on your email provider, it can take a while to arrive.</li>
      </ul>
    </div>
  </div>
  <div class="sticky-footer">
    Almost there, {$firstName}! Check your Email ({$email}) to activate account. <a href="#">Help?</a>
  </div>
</body>
</html>
HTML;

    } catch (Exception $e) {
      echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up - Email Error</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #fac08d;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 100px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      text-align: center;
    }
    h1 {
      font-size: 2rem;
      margin-bottom: 20px;
    }
    p {
      font-size: 1.1rem;
      margin-bottom: 30px;
    }
    .btn-custom {
      background-color: #f4a36c;
      border: none;
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s;
    }
    .btn-custom:hover {
      background-color: #e3caab;
      color: #000;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Sign Up Successful!</h1>
    <p>Your account was created, but we were unable to send the verification email.</p>
    <p>Error: {$mail->ErrorInfo}</p>
    <a href="login.php" class="btn-custom">Go to Login</a>
  </div>
</body>
</html>
HTML;
    }
  } catch (PDOException $e) {
    if ($e->getCode() == 23000) {
      die('This email address is already registered.');
    }
    die('Error: ' . $e->getMessage());
  }
}
?>