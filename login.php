<?php 
    session_start();
    include 'dbconnect.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // admin login credentials
    $admin_email = 'crafthubadmin@admin.com';  
    $admin_password = 'admin123';  

    // Check if admin login
    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin'] = true;
        $_SESSION['user_type'] = 'admin';  // Set user type for admin
        echo "<script> 
                alert('Admin Successfully Logged In!'); 
                window.location.href = 'admin/adminhomepage.php'; 
              </script>";
        exit();
    }

    // For user
    $select = "SELECT * FROM crafthub_users WHERE (email = ? OR username = ?) AND password = ?";
    $stmt = $connection->prepare($select);
    $stmt->bind_param("sss", $email, $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        die("Query Failed: " . $connection->error);
    }
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['userID'] = $row['user_id'];
        echo "<script> 
                alert('You Successfully Logged In!'); 
                window.location.href = 'homepage.php';
              </script>";
    } else {
        echo "<script> alert('Account does not exist'); </script>";
    }
    
    $stmt->close();
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
    <title>CraftHub: An Online Marketplace</title>
</head>
<body>
    <!--=============== LOGIN CONTENT FORM ===============-->
    <div class="login">
        <img src="images/craftsbg.png" alt="login image" class="login__img">

        <form action="login.php" method="POST" class="login__form">
            <h1 class="login__title">CraftHub Login</h1>
            <div class="login__content">
                <div class="login__box">
                    <i class="ri-user-3-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="text" required class="login__input" name="email" id="login-email" placeholder=" ">
                        <label for="login-email" class="login__label">Email</label>
                    </div>
                </div>
                <div class="login__box">
                    <i class="ri-lock-2-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="password" required class="login__input" name="password" id="login-pass" placeholder=" ">
                        <label for="login-pass" class="login__label">Password</label>
                        <i class="ri-eye-off-line login__eye" id="login-eye"></i>
                    </div>
                </div>
                <!--=============== FORGOT PASSWORD / REQUEST RESET ===============-->
                <div class="login__forgot-password">
                    <a href="request_reset.php">Forgot Password?</a>
                </div>
            </div>
          
            <button type="submit" name="submit" class="login__button">Login</button>
            
            <p class="login__register">
                Don't have an account yet? <a href="register.php">Register</a>  
            </p>
        </form>
    </div>
    <!--=============== END OF LOGIN CONTENT ===============-->

<script src="js/login.js"></script>
</body>
</html>
