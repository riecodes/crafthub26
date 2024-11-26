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
        $mail->Subject = 'Merchant Application Submitted';
        $mail->Body    = "<p>Dear $name,</p>
                        <p>Thank you for registering with CraftHub: An Online Marketplace for Handcrafted Goods Enthusiasts!</p> 
                        <p>We have received your registration details and are currently processing your account. Please allow us some time to verify your information. You will receive another email with the result of your registration once your application has been reviewed.</p>
                        <p>In the meantime, if you have any questions or need assistance, feel free to contact our support team at support@crafthub.com.</p>                       
                        <p>We appreciate your patience and look forward to welcoming you to CraftHub!</p>
                        <br></br>
                        <p>Best regards </p>
                        <p>The CraftHub Team  </p>";

        // Send the email
        $mail->send();
        echo "<script>alert('Your Application is Successfully Submitted!'); window.location.href='login.php';</script>";
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Email or Name not set!";
}
?>
