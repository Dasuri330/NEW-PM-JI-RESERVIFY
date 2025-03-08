<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require 'vendor/autoload.php';

// Kunin ang email details mula sa POST request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])) {
    $customer_email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    
    // Trim and replace newline characters with a space for a continuous paragraph
    $message = trim($_POST['message']);
    $message = str_replace(array("\r\n", "\r", "\n"), " ", $message);

    $mail = new PHPMailer(true);

    try {
        // 1️⃣ Gmail SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'reservifypmji@gmail.com';  // Iyong Gmail
        $mail->Password   = 'zxjs nprh gvrn dkny'; // Gumamit ng Google App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // 2️⃣ Set Email Headers and Body
        $mail->setFrom('reservifypmji@gmail.com', 'PM&JI Reservify Support');
        $mail->addAddress($customer_email);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
        // Set email format to plain text
        $mail->isHTML(false);

        // 3️⃣ Send Email
        $mail->send();

        echo "<script>alert('Email sent successfully to $customer_email!'); window.location.href = 'admin_manageinq.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Failed to send email. Error: " . $mail->ErrorInfo . "');</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'admin_manageinq.php';</script>";
}
