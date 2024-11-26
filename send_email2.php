<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_GET['email']) && isset($_GET['name'])) {
    $email = $_GET['email'];
    $name = $_GET['name'];

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'crafthubmartkeplace@gmail.com'; 
        $mail->Password = 'xaai wayq rzbh zhyc';    
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('crafthubmartkeplace@gmail.com', 'CraftHub Admin'); 
        $mail->addAddress($email, $name);  // Add recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Congratulations! Your CraftHub Account is Successfully Registered';
        $mail->Body    = "<p>Hi $name,</p>
                        <p>Welcome to CraftHub! We’re thrilled to have you as part of our community handcrafted goods! We’re excited to let you know that your registration was successful, and you’re all set to start exploring the unique creations from talented artisans of the province of Bataan. </p>
                        <p>Get started by browsing our latest collections and finding something that speaks to your style and passion for handcrafted items. If you need any help, we’re here for you at support@crafthub.com. </p>
                        <p>Thank you for choosing CraftHub! We’re excited to have you with us. </p>
                        <br></br>
                        <p>Best regards, </p>
                        <p>The CraftHub Team  </p>";

        
        $mail->send();
        echo "<script>alert('Registration success!'); window.location.href='login.php';</script>";
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Email or Name not set!";
}
?>
