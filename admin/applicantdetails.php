<?php 
    include 'dbconnect.php';
    session_start();
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
        header("Location: ../index.php"); // Redirect to login page if not logged in
        exit;
    }

    if (isset($_GET['application_id'])){
        $application_id = mysqli_real_escape_string($connection, $_GET['application_id']);

        // Query to select details from both merchant_applications and crafthub_users
        $select_details = "
        SELECT 
            ma.application_id, 
            cu.first_name, cu.middle_name, cu.last_name, 
            ma.shop_name, ma.shop_email, ma.shop_contact_no, ma.shop_municipality, ma.shop_barangay, ma.shop_street,
            ma.business_permit, ma.BIR_registration, ma.BIR_0605, ma.DTI_certificate, ma.status,
            cu.address
        FROM 
            merchant_applications ma
        LEFT JOIN 
            crafthub_users cu ON ma.user_id = cu.user_id
        WHERE 
            ma.application_id = '$application_id'";

        $result = mysqli_query($connection, $select_details);

        if(!$result){
            die("Query Failed: " . mysqli_error($connection));
        } else {
            $row = mysqli_fetch_assoc($result);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--=============== BOXICONS ===============-->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/dd5559ee21.js" crossorigin="anonymous"></script>
    <title>CraftHub: An Online Marketplace</title>
    <link rel="stylesheet" href="css/adminsidebar.css">
    <link rel="stylesheet" href="css/applicantdetails.css">
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
        <!--=============== NAVBAR ===============-->
        <nav>
            <!-- You can add navbar content here if needed -->
        </nav>

        <!--=============== MAIN ===============-->
        <main>
            <div class="container">
                <div class="mb-5 details"> <!--=============== USER/MERCHANT BASIC INFORMATION ===============-->
                    <div class="user-details">
                        <h5><i class="fas fa-user-circle"></i> Applicant's Information</h5>
                        <div class="applicant-details">
                            <h6 class="mt-5">
                                <span class="mb-4 applicant-input-id">Applicant ID Number: <?php echo $row['application_id'] ?></span>
                            </h6>
                        </div>
                    </div>
                    <div class="user-details">
                        <h6><label class="applicant-details">Business Owner Name</label></h6>
                        <div class="applicant-input">
                            <span name="regid" class="mb-1" readonly><?php echo $row['first_name'] .' '. $row['middle_name'] .' '. $row['last_name']; ?></span>
                        </div>
                    </div>
                    <div class="user-details">
                        <h6><label class="applicant-details">Address</label></h6>
                        <div class="applicant-input">
                            <span name="owner_add" class="mb-1" readonly><?php echo $row['address'] ?></span>
                        </div>
                    </div>
                    <div class="user-details">
                        <h6><i class="fas fa-building"></i> <label class="applicant-details">Business Name</label></h6>
                        <div class="applicant-input">
                            <span name="regid" class="mb-1" readonly><?php echo $row['shop_name'] ?></span>
                        </div>
                    </div>
                    <div class="user-details">
                        <h6><i class="fas fa-envelope"></i> <label class="applicant-details">Business Email Address</label></h6>
                        <div class="applicant-input">
                            <span name="regid" class="mb-1" readonly><?php echo $row['shop_email'] ?></span>
                        </div>
                    </div>
                    <div class="user-details">
                        <h6><i class="fas fa-phone"></i> <label class="applicant-details">Business Phone Number</label></h6>
                        <div class="applicant-input">
                            <span name="regid" class="mb-1" readonly><?php echo $row['shop_contact_no'] ?></span>
                        </div>
                    </div>
                    <hr class="border-light m-2">
                    <div class="user-details">
                        <h6><i class="fas fa-map-marker-alt"></i> <label class="applicant-details">Municipality</label></h6>
                        <div class="applicant-input">
                            <span name="regid" class="mb-1" readonly><?php echo $row['shop_municipality'] ?></span>
                        </div>
                    </div>
                    <div class="user-details">
                        <h6><label class="applicant-details">Barangay</label></h6>
                        <div class="applicant-input">
                            <span name="regid" class="mb-1" readonly><?php echo $row['shop_barangay'] ?></span>
                        </div>
                    </div>
                    <div class="user-details">
                        <h6><label class="applicant-details">Street</label></h6>
                        <div class="applicant-input">
                            <span name="regid" class="mb-1" readonly><?php echo $row['shop_street'] ?></span>
                        </div>
                    </div>
                    <!--=============== REQUIREMENTS ===============-->
                    <div class="mt-5 requirements">
                        <hr class="border-light m-2">
                        <h5><i class="fas fa-file-alt"></i> Uploaded Requirements</h5>
                        <div class="evaluation">
                            <div class="row">
                                <div class="col-md-3" id="permit">
                                    <ul>
                                        <li>
                                            <a href="<?php echo '../' . $row['business_permit'] ?>" class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye"></i> View Business Permit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo '../' . $row['BIR_registration'] ?>" class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye"></i> View BIR Registration
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo '../' . $row['BIR_0605'] ?>" class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye"></i> View BIR 0605
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo '../' . $row['DTI_certificate'] ?>" class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye"></i> View DTI Certificate
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-3" id="download">
                                    <ul>
                                        <li>
                                            <a href="<?php echo '../' . $row['business_permit'] ?>" class="btn btn-primary" download>
                                                <i class="fas fa-download"></i> Download Business Permit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo '../' . $row['BIR_registration'] ?>" class="btn btn-primary" download>
                                                <i class="fas fa-download"></i> Download BIR Registration
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo '../' . $row['BIR_0605'] ?>" class="btn btn-primary" download>
                                                <i class="fas fa-download"></i> Download BIR 0605
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo '../' . $row['DTI_certificate'] ?>" class="btn btn-primary" download>
                                                <i class="fas fa-download"></i> Download DTI Certificate
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="decisionForm" action="send_email.php" method="post">
                        <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                        <input type="hidden" name="email" value="<?php echo $row['shop_email']; ?>">
                        <input type="hidden" name="name" value="<?php echo $row['first_name'] . ' ' . $row['last_name']; ?>">

                        <!-- Add comments textarea -->
                        <div class="applicant-note">
                            <h5><i class="fas fa-comment"></i> Add Comment</h5>
                            <textarea class="form-control" id="reqscomment" rows="3" name="comment"></textarea>
                        </div>

                        <!-- Decision buttons -->
                        <div class="decision">
                            <button type="submit" name="decision" value="approve" class="btn btn-primary">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="submit" name="decision" value="reject" class="btn btn-danger">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="js/createacc.js"></script>
    <script src="js/adminsidebar.js"></script>
</body>
</html>
