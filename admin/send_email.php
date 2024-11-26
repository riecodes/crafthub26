<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include 'dbconnect.php';

if (isset($_POST['decision'])) {
    $decision = $_POST['decision'];
    $application_id = $_POST['application_id'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $comment = $_POST['comment'];

    if ($decision == 'approve') {
        $update_status_query = "
        UPDATE merchant_applications
        SET status = 'approved' 
        WHERE application_id = '$application_id'";
        $update_result = mysqli_query($connection, $update_status_query);

        if (!$update_result) {
            die("Status Update Failed: " . mysqli_error($connection));
        }
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'crafthubmartkeplace@gmail.com';
        $mail->Password = 'xaai wayq rzbh zhyc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('crafthubmartkeplace@gmail.com', 'CraftHub Admin');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);

        if ($decision == 'approve') {
            $mail->Subject = 'Congratulations! Your Application has been Approved';
            $mail->Body    = "<p>Dear $name,</p>
                                <p>Congratulations! Your application has been approved, and we’re thrilled to welcome you to the CraftHub community. As a new member, you’ll have access to various tools and features designed to help you showcase and manage your unique creations.</p>
                                <p>To help you get started, we’ve attached a Merchant Manual with essential guidelines for navigating your account and utilizing the latest features on the platform. This guide will be a valuable resource as you explore everything CraftHub has to offer.</p>
                                <p>If you have any questions or need assistance, please feel free to reach out to us at support@crafthub.com.</p>
                                <p>Welcome aboard, and we look forward to seeing your success on CraftHub!</p>
                                <p>Best regards,</p>
                                <p>The CraftHub Team</p>";

            // Attach the PDF file
            $filePath = '../uploads/SAMPLE MERCHANT MANUAL.pdf';
            if (file_exists($filePath)) {
                $mail->addAttachment($filePath, 'SAMPLE MERCHANT MANUAL.pdf');
            } else {
                throw new Exception("Attachment file not found.");
            }
        } elseif ($decision == 'reject') {
            $mail->Subject = 'Application Rejection';
            $mail->Body    = "<p>Dear $name,</p>
                                <p>We regret to inform you that your application (ID: $application_id) has been rejected.</p>
                                <p>$comment</p>
                                <p>If you need assistance, please contact our support team at support@crafthub.com.</p>
                                <p>Thank you for your interest in CraftHub.</p>
                                <br>
                                <p>Best regards,</p>
                                <p>The CraftHub Team</p>";
        }

        $mail->send();
        echo "<script>alert('Email has been sent successfully.'); window.location.href = 'adminhomepage.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Email could not be sent. Error: {$mail->ErrorInfo}');</script>";
    }
}

?>
