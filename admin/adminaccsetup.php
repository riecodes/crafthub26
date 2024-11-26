<?php 
    session_start();
    include 'dbconnect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: An Online Marketplace</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!--=============== BOXICONS ===============-->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/adminsidebar.css">
    <link rel="stylesheet" href="css/adminaccsetup2.css">
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
                    <li class="nav-link">
                        <a href="adminaccsetup.php">
                            <i class='bx bxs-user-account icon'></i>
                            <span class="text nav-text">Account Set Up</span>
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
    <section id="content">
        <nav></nav>

        <!--=============== MAIN ===============-->
            <main>
                <div class="head-title">
                    <div class="left">
                        <h1>Merchant Account Set Up</h1>
                    </div>
                </div>
                <div class="table-data">
                    <div class="order">
                        <div class="head">
                            <h3>Approved Applicants</h3>
                            <div class="search_form">
                                <form action="" method="GET">
                                    <div class="form-input">
                                        <input type="search" name="search" placeholder="Search Applicant" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        <button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Shop Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Base query to fetch approved applicants with status 'processing'
                                $selectall = "
                                    SELECT mp.reg_id, mp.fname, mp.mname, mp.lname, mp.shop_name, mp.shop_email, mp.status
                                    FROM crafthub_merchant_applicant mp
                                    WHERE mp.status = 'processing'
                                ";

                                // If a search query is present, modify the query
                                if (isset($_GET['search']) && !empty($_GET['search'])) {
                                    $search = mysqli_real_escape_string($connection, $_GET['search']);
                                    $selectall .= "
                                        AND (mp.reg_id LIKE '%$search%'
                                        OR mp.fname LIKE '%$search%'
                                        OR mp.mname LIKE '%$search%'
                                        OR mp.lname LIKE '%$search%'
                                        OR mp.shop_name LIKE '%$search%'
                                        OR mp.shop_email LIKE '%$search%')
                                    ";
                                }

                                // Execute the query
                                $result = mysqli_query($connection, $selectall);

                                // Check if the query was successful
                                if (!$result) {
                                    die("Query Failed: " . mysqli_error($connection));
                                } else {
                                    // Display the results
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['reg_id']; ?></td>
                                            <td><?php echo $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']; ?></td>
                                            <td><?php echo $row['shop_name']; ?></td>
                                            <td><?php echo $row['shop_email']; ?></td>
                                            <td><?php echo $row['status']; ?></td>
                                            <td>
                                                <button type="button" class="create-btn btn btn-primary" 
                                                        id="createAccountBtn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#newMerchantModal" 
                                                        data-email="<?php echo $row['shop_email']; ?>" 
                                                        data-regid="<?php echo $row['reg_id']; ?>">
                                                    Create Account
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        <!--=============== END OF MAIN ===============-->

    </section>
    <!--=============== END OF CONTENT ===============-->


    
        <!--=============== MODAL FOR CREATE NEW MERCHANT ACCOUNT ===============-->
        <div class="modal fade" id="newMerchantModal" tabindex="-1" aria-labelledby="newMerchantModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title" id="newMerchantModalLabel">Create New Merchant Account</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="create_merchant.php" method="POST">
                            <div class="mb-3">
                                <label for="merchantRegId" class="form-label"></label>
                                <input type="hidden" class="form-control" id="merchantRegId" name="reg_id" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="merchantEmail" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="merchantEmail" name="email" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="merchantPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="merchantPassword" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="merchantConfirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="merchantConfirmPassword" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--=============== END OF MODAL FOR CREATE NEW MERCHANT ACCOUNT ===============-->

            <!--=============== END OF MODAL FOR CREATE NEW MERCHANT ACCOUNT ===============-->

<script>
    document.addEventListener('DOMContentLoaded', function() {
    var createAccountBtn = document.querySelectorAll('#createAccountBtn');
    
    createAccountBtn.forEach(function(button) {
        button.addEventListener('click', function() {
            var email = button.getAttribute('data-email');
            var reg_id = button.getAttribute('data-regid');
            
            document.getElementById('merchantEmail').value = email;
            document.getElementById('merchantRegId').value = reg_id;
        });
    });
});

</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
<script src="js/adminsidebar.js"></script>


</body>
</html>
