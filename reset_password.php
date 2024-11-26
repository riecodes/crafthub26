<?php
include 'dbconnect.php'; 

date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = trim($_POST['token']);
    $new_password = $_POST['new_password'];

    $stmt = $connection->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        $stmt = $connection->prepare("UPDATE `crafthub_users` SET user_pass = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);

        if ($stmt->execute()) {
            $stmt = $connection->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            echo "<script>alert('Your password has been successfully updated!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error updating password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Invalid or expired token.');</script>";
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
    <title>CraftHub: Reset Password</title>
</head>
<body>
    <!--=============== RESET PASSWORD CONTENT FORM ===============-->
    <div class="login">
        <img src="images/craftsbg.png" alt="login image" class="login__img">

        <form action="#" method="POST" class="login__form login__form--solid">
        <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
            <h1 class="login__title">Reset Password</h1>
            <div class="login__content">
                <div class="login__box">
                    <i class="ri-lock-2-line login__icon"></i>
                    <!--=============== NEW PASSWORD ===============-->
                    <div class="login__box-input">
                        <input type="password" required class="login__input" name="new_password" id="new_password" placeholder=" ">
                        <label for="new-password" class="login__label">New Password</label>
                    </div>
                </div>
                <!--=============== CONFIRM NEW PASSWORD ===============-->
                <div class="login__box">
                    <i class="ri-lock-2-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="password" required class="login__input" name="confirm_password" id="confirm_password" placeholder=" ">
                        <label for="confirm-password" class="login__label">Confirm New Password</label>
                    </div>
                </div>
            </div>
          <!--=============== RESET PASSWORD BUTTON ===============-->
            <button type="submit" name="submit" class="login__button">Reset Password</button>
            <p class="login__register">
                Remember your password? <a href="login.php">Login</a>
            </p>
        </form>
    </div>
    <!--=============== END OF RESET PASSWORD CONTENT ===============-->
    <script>
    // Simple client-side validation to check if both passwords match
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        const password = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            e.preventDefault(); // Prevent form submission
        }
    });
</script>
<script src="js/login.js"></script>
</body>
</html>
