<?php
include 'dbconnect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';


require 'vendor/autoload.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $connection->prepare("SELECT * FROM `crafthub_users` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        date_default_timezone_set('Asia/Manila');
        $expiresAt = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $stmt = $connection->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expiresAt);
        $stmt->execute();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'crafthubmartkeplace@gmail.com'; 
            $mail->Password = 'xaai wayq rzbh zhyc';    
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
    
            // Recipients
            $mail->setFrom('crafthubmartkeplace@gmail.com', 'CraftHub Admin'); 
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Reset your password';
            $mail->Body = "Here is your password reset link: <a href='https://crafthubbataanmarketplace.online/reset_password.php?token=$token'>Reset Password</a>";

            $mail->send();
            echo "<script>alert('Password reset link sent to your email!');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email address.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <title>CraftHub: Reset Password Request</title>
</head>
<body>
        <!--=============== RESET PASSWORD ===============-->
    <div class="login">
        <img src="images/craftsbg.png" alt="login image" class="login__img">

        <form action="" method="POST" class="login__form login__form--solid">
            <h1 class="login__title">Reset Password</h1>
            <div class="login__content">
                <div class="login__box">
                    <i class="ri-mail-line login__icon"></i>
                    <div class="login__box-input">
                        <input  required class="login__input" type="email" id="email" name="email"  placeholder=" ">
                        <label for="login-email" class="login__label">Email</label>
                    </div>
                </div>
            </div>
          <!--=============== REQUEST RESET PASSWORD ===============-->
            <button type="submit" name="submit" class="login__button">Request Reset</button>
            <p class="login__register">
                Remember your password? <a href="login.php">Login</a>
            </p>
        </form>
    </div>

<script src="js/login.js"></script>
</body>
</html>
