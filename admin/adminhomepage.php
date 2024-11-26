<?php
session_start();
include 'dbconnect.php'; 

// Query to get the number of new applicants
$new_applicants_query = "SELECT COUNT(*) as count FROM merchant_applications WHERE status = 'pending'";
$new_applicants_result = mysqli_query($connection, $new_applicants_query);
$new_applicants_count = mysqli_fetch_assoc($new_applicants_result)['count'];

// Query to get the number of approved merchants
$approved_merchants_query = "SELECT COUNT(*) as count FROM merchant_applications WHERE status = 'approved'";
$approved_merchants_result = mysqli_query($connection, $approved_merchants_query);
$approved_merchants_count = mysqli_fetch_assoc($approved_merchants_result)['count'];

// Query to retrieve details of new applicants
$new_applicants_query = "
    SELECT u.first_name, u.last_name, ma.shop_email, ma.status 
    FROM merchant_applications ma
    JOIN crafthub_users u ON ma.user_id = u.user_id
    WHERE ma.status = 'pending'";
$new_applicants_result = mysqli_query($connection, $new_applicants_query);

// Query to retrieve details of approved merchants
$approved_merchants_query = "
    SELECT m.user_id, u.first_name, u.last_name, m.shop_street, m.shop_barangay, m.shop_municipality, m.shop_email, m.shop_name, u.user_profile
    FROM merchant_applications m
    JOIN crafthub_users u ON m.user_id = u.user_id
    WHERE m.status = 'approved'";
$approved_merchants_result = mysqli_query($connection, $approved_merchants_query);

mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--=============== BOXICONS ===============-->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <title>CraftHub: An Online Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/adminsidebar.css">
    <link rel="stylesheet" href="css/adminhomepage.css">
</head>
<body>
    <!--=============== SIDEBAR ===============-->
    <nav class="sidebar">
        <header>
            <div class="image-text">
                <div class="text header-text">
                    <span class="name">CraftHub</span>
                    <span class="profession">Admin Panel</span>
                </div>
            </div>
            <i class='bx bx-menu toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="adminhomepage.php">
                            <i class='bx bxs-dashboard icon'></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="adminprocessing.php">
                            <i class='bx bx-list-check icon'></i>
                            <span class="text nav-text">Application Processing</span>
                        </a>
                    </li>
                   
                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="../index.php">
                        <i class='bx bx-log-out icon'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
                <li class="mode">
                    <div class="moon-sun">
                        <i class='bx bx-moon icon moon'></i>
                        <i class='bx bx-sun icon sun'></i>
                    </div>
                    <span class="mode-text text">Dark Mode</span>
                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
                </li>
            </div>
        </div>
    </nav>
    <!--=============== END OF SIDEBAR ===============-->

    <section id="content">
        <!--=============== CONTENT ===============-->
        <nav>
            <!-- Removed duplicate menu icon -->
        </nav>
        <!--=============== NAVBAR ===============-->

        <!--=============== MAIN ===============-->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Dashboard</h1>
                </div>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card new-applicants" onclick="window.location.href='adminprocessing.php'">
                    <div class="icon-container">
                        <i class='bx bxs-folder-plus'></i>
                    </div>
                    <div class="stat-content">
                        <h3>New Applicants</h3>
                        <span class="stat-number"><?php echo $new_applicants_count; ?></span>
                    </div>
                </div>
                <div class="stat-card merchants">
                    <div class="icon-container">
                        <i class='bx bx-store'></i>
                    </div>
                    <div class="stat-content">
                        <h3>Merchants</h3>
                        <span class="stat-number"><?php echo $approved_merchants_count; ?></span>
                    </div>
                </div>
            </div>

            <div class="applicants-merchants-container">
                <div class="data-section applicants-section">
                    <h2>New Applicants</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($new_applicants_result) > 0): ?>
                                    <?php while ($applicant = mysqli_fetch_assoc($new_applicants_result)): ?>
                                        <tr>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar"><?php echo strtoupper(substr($applicant['first_name'], 0, 1) . substr($applicant['last_name'], 0, 1)); ?></div>
                                                    <span><?php echo htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($applicant['shop_email']); ?></td>
                                            <td><span class="status-badge <?php echo strtolower($applicant['status']); ?>"><?php echo htmlspecialchars($applicant['status']); ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="no-data">No pending applicants</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="data-section merchants-section">
                    <h2>Existing Merchants</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Shop Name</th>
                                    <th>Shop Owner</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($approved_merchants_result) > 0): ?>
                                    <?php while ($merchant = mysqli_fetch_assoc($approved_merchants_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($merchant['shop_name']); ?></td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar"><?php echo strtoupper(substr($merchant['first_name'], 0, 1) . substr($merchant['last_name'], 0, 1)); ?></div>
                                                    <span><?php echo htmlspecialchars($merchant['first_name'] . ' ' . $merchant['last_name']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($merchant['shop_email']); ?></td>   
                                            <td><?php echo htmlspecialchars($merchant['shop_street']. ' ' .$merchant['shop_barangay']. ' ' .$merchant['shop_municipality']); ?></td>    
                                            
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="no-data">No merchants found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </section>
    <!--=============== END OF CONTENT ===============-->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybiiZ9zWnyPp6pBfQ5e1K6p0JjxOu/tV5aFUbJg5p6nAyKGGk" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Pw60HXBs2E/B15z6tRXBbmEpnf4lmyj6VdU5zuF6" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <script src="js/adminsidebar.js"></script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const toggleBtn = document.querySelector('#btn');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('hide');
            content.classList.toggle('expand');
        });

        // Add active class to the current button (highlight it)
        const listItems = document.querySelectorAll('.side-menu li');
        listItems.forEach(item => {
            item.addEventListener('click', function() {
                listItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
    <script>
    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, '', window.location.href);
        alert("You are logged out! Please log in again.");
        window.location.href = '../index.php'; // Redirect to login
    };
</script>
</body>
</html>
