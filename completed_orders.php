<?php
session_start();
require_once('./dbcon.php');

if (isset($_SESSION['userID'])) {
    $user_id = $_SESSION['userID'];
    $query = "SELECT * FROM crafthub_user WHERE user_id = '$user_id'";
    $result22 = mysqli_query($connection, $query);

    if ($result22) {
        $row11 = mysqli_fetch_assoc($result22);
        $_SESSION['username'] = $row11['username'];
        $_SESSION['user_profile'] = $row11['user_profile'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: An Online Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="css/shop.css" rel="stylesheet">
    <link href="css/footer.css" rel="stylesheet">
    <link href="css/nav.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav.php'; ?>

    <?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple counter animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter-value');
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 1500;
            const step = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        });
    });
</script>
</body>
</html>