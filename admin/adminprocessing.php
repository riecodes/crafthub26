<?php 
    include 'dbconnect.php';
    session_start();
    
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
        header("Location: ../index.php"); // Redirect to login page if not logged in
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: An Online Marketplace</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="css/adminsidebar.css">
    <link rel="stylesheet" href="css/adminprocessing.css">
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

    <!--=============== CONTENT ===============-->
    <section class="home">
        <div class="header">
            <h1>Application Processing</h1>
        </div>
        
        <main>
            <div class="insights">
                <div class="card total-applicants">
                    <i class='bx bx-user-plus'></i>
                    <div class="middle">
                        <div class="left">
                            <h3>Total Applicants</h3>
                            <h1></h1>
                        </div>
                    </div>
                    <small class="text-muted">Last 24 Hours</small>
                </div>
                <div class="card pending-applications">
                    <i class='bx bx-time'></i>
                    <div class="middle">
                        <div class="left">
                            <h3>Pending Applications</h3>
                            <h1>15</h1>
                        </div>
                    </div>
                    <small class="text-muted">Last 24 Hours</small>
                </div>
                <div class="card approved-applications">
                    <i class='bx bx-check-circle'></i>
                    <div class="middle">
                        <div class="left">
                            <h3>Approved Applications</h3>
                            <h1>10</h1>
                        </div>
                    </div>
                    <small class="text-muted">Last 24 Hours</small>
                </div>
            </div>

            <div class="recent-applications">
                <div class="recent-applications-header">
                    <h2>Merchant Applicants</h2>
                    <div class="search-container">
                        <form method="GET" action="adminprocessing.php">
                            <input type="search" name="search" placeholder="Search applicants..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit"><i class='bx bx-search'></i></button>
                        </form>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Business Name</th>
                            <th>Email</th>
                            <th>Application Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Base query to fetch all merchant applicants
                        $selectall = "
                            SELECT ma.application_id, ma.user_id, mu.username, mu.email, mu.first_name, mu.last_name, 
                                ma.shop_name, ma.shop_email, ma.applied_at, ma.status
                            FROM merchant_applications ma
                            JOIN crafthub_users mu ON ma.user_id = mu.user_id
                        ";

                        // Initialize a variable to hold WHERE clause
                        $whereClauses = [];

                        // Check if a search query is provided
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $search = mysqli_real_escape_string($connection, $_GET['search']);
                            $whereClauses[] = "ma.status = 'pending'"; // Only fetch pending applicants
                            $whereClauses[] = "(ma.application_id LIKE '%$search%' 
                                                OR mu.username LIKE '%$search%' 
                                                OR mu.first_name LIKE '%$search%' 
                                                OR mu.last_name LIKE '%$search%' 
                                                OR ma.shop_email LIKE '%$search%' 
                                                OR ma.shop_name LIKE '%$search%')";
                        } else {
                            // If no search term, we want to fetch all pending applicants
                            $whereClauses[] = "ma.status = 'pending'";
                        }

                        // Build the complete query
                        if (count($whereClauses) > 0) {
                            $selectall .= " WHERE " . implode(' AND ', $whereClauses);
                        }

                        // Execute the query
                        $result = mysqli_query($connection, $selectall);

                        if (!$result) {
                            die("Query Failed: " . mysqli_error($connection));
                        } else {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Define status class based on status
                                $status_class = ($row['status'] == 'approved') ? 'status-approved' : 'status-pending'; 
                                ?>
                                <tr>
                                    <td><?php echo $row['application_id']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                    <td><?php echo $row['shop_name']; ?></td>
                                    <td><?php echo $row['shop_email']; ?></td>
                                    <td><?php echo $row['applied_at']; ?></td>
                                    <td>
                                        <a href="applicantdetails.php?application_id=<?php echo $row['application_id']; ?>" class="status-btn <?php echo $status_class; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <a href="#" class="show-all">Show All</a>
            </div>

        </main>
    </section>

    <script src="js/adminsidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

        <script>
        // Fetch applicant data
        fetch('get_applicant_stats.php')
            .then(response => response.json())
            .then(data => {
                document.querySelector('.total-applicants h1').textContent = data.total; 
                document.querySelector('.pending-applications h1').textContent = data.pending;
                document.querySelector('.approved-applications h1').textContent = data.approved;
            })
            .catch(error => console.error('Error:', error));
        </script>

</body>
</html>