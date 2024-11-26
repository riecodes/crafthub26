<?php
session_start();
include 'dbconnect.php';

$user_id = $_SESSION['userID']; 

// Fetch user details
$query = "SELECT * FROM crafthub_users WHERE user_id = '$user_id'";
$result = mysqli_query($connection, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    exit("No user found.");
}
// Define variables to control display logic
$row = mysqli_fetch_assoc($result);
$current_profile_image = $row['user_profile'] ?? 'images/user.png'; // Use default image if no profile image is found

$showStartSelling = false;
$showApplicationStatus = false;

// Determine user role
if (trim($row['role']) === 'merchant') {
    // User is a merchant, show the application status only
    $showApplicationStatus = true;
} elseif (trim($row['role']) === 'customer') {
    // Check if the customer has submitted an application
    $merchantQuery = "SELECT status FROM merchant_applications WHERE user_id = '$user_id'";
    $merchantResult = mysqli_query($connection, $merchantQuery);

    if ($merchantResult && mysqli_num_rows($merchantResult) > 0) {
        $merchantData = mysqli_fetch_assoc($merchantResult);
        
        // If the application is 'pending' or 'approved', show the application status
        if (trim($merchantData['status']) === 'approved' || trim($merchantData['status']) === 'pending') {
            $showApplicationStatus = true;
        } else {
            // Application status is not relevant; show "Start Selling" option
            $showStartSelling = true;
        }
    } else {
        // No application found, allow user to see "Start Selling" option
        $showStartSelling = true;
    }
}

// Add this before the status tab HTML
$merchantQuery = "SELECT * FROM merchant_applications WHERE user_id = '$user_id'";
$merchantResult = mysqli_query($connection, $merchantQuery);
$merchantData = mysqli_fetch_assoc($merchantResult);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    // Handle profile info update
    if (isset($_POST['update'])) {
        $username = mysqli_real_escape_string($connection, $_POST['username']);
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
        $middle_name = mysqli_real_escape_string($connection, $_POST['middle_name']);
        $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
        $birthday = mysqli_real_escape_string($connection, $_POST['birthday']);
        $contact_no = mysqli_real_escape_string($connection, $_POST['contact']);
        $address = mysqli_real_escape_string($connection, $_POST['address']);

        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $target_directory = "uploads/"; // Ensure this directory exists
            $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
            $new_filename = time() . '_' . $user_id . '.' . $file_extension; // Create unique filename
            $target_file = $target_directory . $new_filename;
            
            // Check if it's an actual image
            $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
            if ($check !== false) {
                // Check file size (limit to 5MB)
                if ($_FILES["profile_image"]["size"] <= 5000000) {
                    // Allow certain file formats
                    if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        // Delete old image if exists and is not the default image
                        if ($row['user_profile'] && $row['user_profile'] !== 'images/user.png' && file_exists($target_directory . $row['user_profile'])) {
                            unlink($target_directory . $row['user_profile']);
                        }
                        
                        // Upload new image
                        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                            // Update database with new image filename
                            $update_image_query = "UPDATE crafthub_users SET user_profile = '$new_filename' WHERE user_id = '$user_id'";
                            if (mysqli_query($connection, $update_image_query)) {
                                $_SESSION['user_profile'] = $new_filename;
                                $current_profile_image = $new_filename;
                                echo "<script>alert('Profile image updated successfully!');</script>";
                            } else {
                                echo "<script>alert('Error updating profile image in database.');</script>";
                            }
                        } else {
                            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
                        }
                    } else {
                        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
                    }
                } else {
                    echo "<script>alert('Sorry, your file is too large. Maximum size is 5MB.');</script>";
                }
            } else {
                echo "<script>alert('File is not an image.');</script>";
            }
        }

        // Update user information in the database
        $update_query = "UPDATE crafthub_users 
                        SET username = '$username', email = '$email', first_name = '$first_name', 
                            middle_name = '$middle_name', last_name = '$last_name', birthday = '$birthday', 
                            contact_no = '$contact_no', address = '$address' 
                        WHERE user_id = '$user_id'";
        if (mysqli_query($connection, $update_query)) {
            header("Refresh:0"); // Refresh page to show updated data
        } else {
            echo "Error updating profile: " . mysqli_error($connection);
        }
    }
    // Handle profile image reset
    if (isset($_POST['reset_image'])) {
        // Only proceed if current image is not already the default
        if ($current_profile_image !== 'images/user.png') {
            // Delete the current profile image file
            if (file_exists('uploads/' . $current_profile_image)) {
                unlink('uploads/' . $current_profile_image);
            }
            
            // Reset profile image in database to default
            $reset_image_query = "UPDATE crafthub_users SET user_profile = NULL WHERE user_id = '$user_id'";
            if (mysqli_query($connection, $reset_image_query)) {
                $_SESSION['user_profile'] = 'images/user.png';
                $current_profile_image = 'images/user.png';
                echo "<script>alert('Profile image reset successfully!');</script>";
            } else {
                echo "<script>alert('Error resetting profile image: " . mysqli_error($connection) . "');</script>";
            }
        }
    }

    // Change password
    if (isset($_POST['save_pass'])) {
        $current_password_input = mysqli_real_escape_string($connection, $_POST['current_password']);
        $new_password = mysqli_real_escape_string($connection, $_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($connection, $_POST['confirm_password']);

        // Fetch current password from the database
        $password_query = "SELECT password FROM crafthub_users WHERE user_id = '$user_id'";
        $password_result = mysqli_query($connection, $password_query);

        if ($password_result && mysqli_num_rows($password_result) > 0) {
            $stored_password = mysqli_fetch_assoc($password_result)['password'];

            // Check if the current password matches
            if ($current_password_input === $stored_password && $new_password === $confirm_password) {
                $update_password_query = "UPDATE crafthub_users SET password = '$new_password' WHERE user_id = '$user_id'";
                mysqli_query($connection, $update_password_query);
                echo "<script>alert('Password updated successfully!');</script>";
            } else {
                echo "<script>alert('Password mismatch or incorrect current password.');</script>";
            }
        }
    }

    // Update the merchant application processing code
    if (isset($_POST['become_merchant'])) {
        $business_name = mysqli_real_escape_string($connection, $_POST['business_name']);
        $business_contact = mysqli_real_escape_string($connection, $_POST['business_contact']);
        $business_email = mysqli_real_escape_string($connection, $_POST['business_email']);
        $province = "Bataan"; // Set fixed value for province
        $municipality = mysqli_real_escape_string($connection, $_POST['municipality']);
        $barangay = mysqli_real_escape_string($connection, $_POST['barangay']);
        $street = mysqli_real_escape_string($connection, $_POST['street']);

        // Handle file uploads
        $upload_dir = "uploads/merchant_documents/";
        $uploaded_files = [];
        $file_fields = ['business_permit', 'valid_id', 'barangay_clearance', 'dti_certificate'];

        foreach ($file_fields as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === 0) {
                $file_tmp = $_FILES[$field]['tmp_name'];
                $file_name = time() . '_' . $field . '_' . basename($_FILES[$field]['name']);
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($file_tmp, $file_path)) {
                    $uploaded_files[$field] = $file_name;
                } else {
                    echo "<script>alert('Error uploading " . ucwords(str_replace('_', ' ', $field)) . "');</script>";
                    exit;
                }
            }
        }

        // Insert into database (you'll need to modify your table structure)
        $merchant_query = "INSERT INTO merchant_applications (
            user_id, business_name, business_contact, business_email, 
            province, municipality, barangay, street,
            business_permit, valid_id, barangay_clearance, dti_certificate,
            status, application_date
        ) VALUES (
            '$user_id', '$business_name', '$business_contact', '$business_email',
            '$province', '$municipality', '$barangay', '$street',
            '{$uploaded_files['business_permit']}', '{$uploaded_files['valid_id']}',
            '{$uploaded_files['barangay_clearance']}', '{$uploaded_files['dti_certificate']}',
            'pending', NOW()
        )";

        if (mysqli_query($connection, $merchant_query)) {
            echo "<script>alert('Your seller application has been submitted successfully!');</script>";
        } else {
            echo "<script>alert('Error submitting application: " . mysqli_error($connection) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: Account Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/dd5559ee21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/account2.css">
    <link rel="stylesheet" href="css/footer.css">

</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container" id="accsettings">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-3 bg-creamy-brown py-4">
                                <div class="text-center mb-4">
                                    <div class="profile-image mx-auto">
                                        <?php if ($current_profile_image && $current_profile_image !== 'images/user.png'): ?>
                                            <img id="profileImage" src="uploads/<?php echo htmlspecialchars($current_profile_image); ?>" alt="Profile Image">
                                        <?php else: ?>
                                            <i class="fas fa-user profile-image-default"></i>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="mt-3"><?php echo htmlspecialchars($row['username']); ?></h5>
                                </div>
                                <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link active" data-bs-toggle="pill" href="#account-general">
                                        <i class="fas fa-user me-2"></i> User Profile
                                    </a>
                                    <a class="nav-link" data-bs-toggle="pill" href="#account-change-password">
                                        <i class="fas fa-lock me-2"></i> Change Password
                                    </a>
                                    <a class="nav-link" data-bs-toggle="pill" href="#account-privacy-settings">
                                        <i class="fas fa-shield-alt me-2"></i> Privacy Settings
                                    </a>
                                    
                                        <a class="nav-link" data-bs-toggle="pill" href="#account-merchant">
                                            <i class="fas fa-store me-2"></i> Become Merchant
                                        </a>
                                    
                                    <?php if ($showApplicationStatus): ?>
                                        <a class="nav-link" data-bs-toggle="pill" href="#account-status">
                                            <i class="fas fa-tags me-2"></i> Application Status
                                        </a>
                                    <?php endif; ?>
                                    
                                </div>
                            </div>
                            <div class="col-md-9 py-4 px-4">
                                <div class="tab-content">
                                    
                                    <div class="tab-pane fade show active" id="account-general">
                                        <h4 class="mb-4">User Profile</h4>
                                        <form action="" method="post" id="userProfileForm" enctype="multipart/form-data">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <label class="form-label d-none" id="profiletag">Profile Picture</label>
                                                    <div class="profile-image-actions">
                                                        <label class="btn d-none" id="uploadButton">
                                                            <i class="fas fa-cloud-upload-alt"></i> Upload new photo
                                                            <input type="file" class="account-settings-fileinput d-none" id="uploadImageInput" name="profile_image" accept="image/*" hidden>
                                                        </label>
                                                        <button type="button" class="btn d-none" id="resetImage">
                                                            <i class="fas fa-undo"></i> Reset
                                                        </button>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-primary" id="editButton">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Username</label>
                                                <input name="username" type="text" class="form-control mb-1" value="<?php echo htmlspecialchars($row['username']); ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">First Name</label>
                                                <input name="first_name" type="text" class="form-control" value="<?php echo htmlspecialchars($row['first_name']); ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Middle Name</label>
                                                <input name="middle_name" type="text" class="form-control" value="<?php echo htmlspecialchars($row['middle_name']); ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Last Name</label>
                                                <input name="last_name" type="text" class="form-control" value="<?php echo htmlspecialchars($row['last_name']); ?>" readonly>
                                            </div>
                                            <hr class="border-light m-3">
                                            <div class="form-group">
                                                <label class="form-label">Email</label>
                                                <input name="email" type="text" class="form-control mb-1" value="<?php echo htmlspecialchars($row['email']); ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Birthday</label>
                                                <input type="text" name="birthday" class="form-control" value="<?php echo htmlspecialchars($row['birthday']); ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Phone</label>
                                                <input type="number" name="contact" class="form-control" value="<?php echo htmlspecialchars($row['contact_no']); ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Address</label>
                                                <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" readonly>
                                            </div>
                                            <div class="text-end mt-4">
                                                <button type="submit" name="update" class="btn btn-success d-none" id="saveButton">Save changes</button>
                                                <button type="button" class="btn btn-danger d-none" id="cancelButton">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="account-change-password">
                                        <h4 class="mb-4">Change Password</h4>
                                        <form action="" method="post">
                                            <div class="form-group">
                                                <label class="form-label">Current password</label>
                                                <input type="password" name="current_password" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">New password</label>
                                                <input type="password" name="new_password" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Confirm new password</label>
                                                <input type="password" name="confirm_password" class="form-control" required>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" name="save_pass" class="btn btn-primary">Save changes</button>
                                                <button type="button" class="btn btn-danger">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="account-privacy-settings">
                                        <h4 class="mb-4">Privacy Settings</h4>
                                        <div id="accdeletion">
                                            <label class="form-label">Account Deletion</label>
                                            <p>Deleting your account is permanent and cannot be undone. All your data will be erased.</p>
                                            <button type="button" class="btn btn-danger" id="deleteAccountBtn">Delete Account</button>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account-merchant">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h4 class="mb-0">Merchant Application</h4>
                                            
                                        </div>
                                        <div class="merchant-info">
                                            <form action="" method="post" id="merchantForm" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label class="form-label">Business Name</label>
                                                    <input type="text" name="business_name" class="form-control" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Business Contact</label>
                                                            <input type="text" name="business_contact" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Business Email</label>
                                                            <input type="email" name="business_email" class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Province</label>
                                                    <input type="text" name="province" class="form-control bg-light" value="Bataan" readonly>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Municipality</label>
                                                            <select id="municipality_select" name="municipality" class="form-control" required>
                                                                <option value="">Select Municipality</option>
                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Barangay</label>
                                                            <select id="barangay_select" name="barangay" class="form-control" required>
                                                                <option value="">Select Barangay</option>
                                                             
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Street</label>
                                                            <input type="text" name="street" class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="document-upload-section">
                                                    <h5 class="mb-3">Required Documents</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">Business Permit</label>
                                                                <div class="custom-file-upload">
                                                                    <input type="file" name="business_permit" class="form-control preview-trigger" accept=".pdf,.jpg,.jpeg,.png" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">Valid Government ID</label>
                                                                <div class="custom-file-upload">
                                                                    <input type="file" name="valid_id" class="form-control preview-trigger" accept=".pdf,.jpg,.jpeg,.png" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">Barangay Clearance</label>
                                                                <div class="custom-file-upload">
                                                                    <input type="file" name="barangay_clearance" class="form-control preview-trigger" accept=".pdf,.jpg,.jpeg,.png" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">DTI Certificate</label>
                                                                <div class="custom-file-upload">
                                                                    <input type="file" name="dti_certificate" class="form-control preview-trigger" accept=".pdf,.jpg,.jpeg,.png" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-end mt-4">
                                                    <button type="submit" name="become_merchant" class="btn btn-primary">Submit Application</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account-status">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h4 class="mb-0">Merchant Application</h4>
                                            <?php
                                            // Fetch application status
                                            $statusQuery = "SELECT status FROM merchant_applications WHERE user_id = '$user_id'";
                                            $statusResult = mysqli_query($connection, $statusQuery);
                                            if ($statusResult && mysqli_num_rows($statusResult) > 0) {
                                                $statusData = mysqli_fetch_assoc($statusResult);
                                                $status = trim($statusData['status']);
                                                ?>
                                                <div class="application-status <?php echo strtolower($status); ?>">
                                                    <span class="status-dot"></span>
                                                    <span class="status-value"><?php echo ucfirst($status); ?></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="merchant-info">
                                            <form action="" method="post" id="merchantForm" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label class="form-label">Business Name</label>
                                                    <input type="text" name="business_name" class="form-control" value="<?php echo htmlspecialchars($merchantData['business_name'] ?? ''); ?>" readonly>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Business Contact</label>
                                                            <input type="text" name="business_contact" class="form-control" value="<?php echo htmlspecialchars($merchantData['business_contact'] ?? ''); ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Business Email</label>
                                                            <input type="email" name="business_email" class="form-control" value="<?php echo htmlspecialchars($merchantData['business_email'] ?? ''); ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Province</label>
                                                    <input type="text" name="province" class="form-control" value="Bataan" readonly>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Municipality</label>
                                                            <input type="text" name="municipality" class="form-control" value="<?php echo htmlspecialchars($merchantData['municipality'] ?? ''); ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Barangay</label>
                                                            <input type="text" name="barangay" class="form-control" value="<?php echo htmlspecialchars($merchantData['barangay'] ?? ''); ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Street</label>
                                                            <input type="text" name="street" class="form-control" value="<?php echo htmlspecialchars($merchantData['street'] ?? ''); ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="document-upload-section">
                                                    <h5 class="mb-3">Submitted Documents</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">Business Permit</label>
                                                                <div class="submitted-file">
                                                                    <?php if (!empty($merchantData['business_permit'])): ?>
                                                                        <a href="uploads/merchant_documents/<?php echo htmlspecialchars($merchantData['business_permit']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Document</a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">Valid Government ID</label>
                                                                <div class="submitted-file">
                                                                    <?php if (!empty($merchantData['valid_id'])): ?>
                                                                        <a href="uploads/merchant_documents/<?php echo htmlspecialchars($merchantData['valid_id']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Document</a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">Barangay Clearance</label>
                                                                <div class="submitted-file">
                                                                    <?php if (!empty($merchantData['barangay_clearance'])): ?>
                                                                        <a href="uploads/merchant_documents/<?php echo htmlspecialchars($merchantData['barangay_clearance']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Document</a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label">DTI Certificate</label>
                                                                <div class="submitted-file">
                                                                    <?php if (!empty($merchantData['dti_certificate'])): ?>
                                                                        <a href="uploads/merchant_documents/<?php echo htmlspecialchars($merchantData['dti_certificate']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Document</a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadImageInput = document.getElementById('uploadImageInput');
        const profileImageContainer = document.querySelector('.profile-image');
        const resetButton = document.getElementById('resetImage');
        const cancelButton = document.getElementById('cancelButton');
        const editButton = document.getElementById('editButton');
        const saveButton = document.getElementById('saveButton');
        const uploadButton = document.getElementById('uploadButton');
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="number"]');
        const selects = document.querySelectorAll('select');

        // Original image source (fetched from PHP)
        let originalImageSrc = '<?php echo $current_profile_image ? "uploads/" . $current_profile_image : "images/user.png"; ?>';

        // Preview the uploaded image
        uploadImageInput.addEventListener('change', function(event) {
            if (event.target.files.length > 0) {
                updateImagePreview(event.target.files[0]);
            }
        });

        // Update the image preview function
        function updateImagePreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImageContainer.innerHTML = `<img src="${e.target.result}" alt="Profile Image">`;
            };
            reader.readAsDataURL(file);
        }

        // Reset the image to the original source or default
        function resetImageToOriginal() {
            if (originalImageSrc && originalImageSrc !== 'images/user.png') {
                profileImageContainer.innerHTML = `<img id="profileImage" src="${originalImageSrc}" alt="Profile Image">`;
            } else {
                profileImageContainer.innerHTML = '<i class="fas fa-user profile-image-default"></i>';
            }
        }

        // Cancel button functionality
        cancelButton.addEventListener('click', function() {
            inputs.forEach(input => input.setAttribute('readonly', 'readonly'));
            selects.forEach(select => select.setAttribute('disabled', 'disabled'));
            
            uploadButton.classList.add('d-none');
            resetButton.classList.add('d-none');
            
            resetImageToOriginal();
            uploadImageInput.value = null;

            editButton.classList.remove('d-none');
            saveButton.classList.add('d-none');
            cancelButton.classList.add('d-none');
            uploadButton.classList.add('d-none');
            resetImage.classList.add('d-none');
            profiletag.classList.add('d-none');
            

        });

        // Reset button functionality
        resetButton.addEventListener('click', function(event) {
            event.preventDefault();
            const confirmReset = confirm('Are you sure you want to reset your profile picture? This action cannot be undone.');
            if (confirmReset) {
                resetImageToOriginal();
                uploadImageInput.value = '';

                const resetInput = document.createElement('input');
                resetInput.type = 'hidden';
                resetInput.name = 'reset_image';
                document.getElementById('userProfileForm').appendChild(resetInput);

                document.getElementById('userProfileForm').submit();
            }
        });

        // Edit button functionality
        editButton.addEventListener('click', function() {
            inputs.forEach(input => input.removeAttribute('readonly'));
            selects.forEach(select => select.removeAttribute('disabled'));

            uploadButton.classList.remove('d-none');
            resetButton.classList.remove('d-none');

            editButton.classList.add('d-none');
            profiletag.classList.remove('d-none');
            saveButton.classList.remove('d-none');
            cancelButton.classList.remove('d-none');

        });

        // Save button functionality
        saveButton.addEventListener('click', function() {
            document.getElementById('userProfileForm').submit();

            
        });
    });
    </script>

    <script>
        // Define municipality and barangay data
        var municipalities = ["Abucay", "Bagac", "Balanga City", "Dinalupihan", "Hermosa", "Limay", "Mariveles", "Morong", "Orani", "Orion", "Pilar", "Samal"];

        var barangays = {
            "Abucay": [
                "Bangkal", "Calaylayan", "Capitangan", "Gabon", "Laon",
                "Mabatang", "Omboy", "Salian", "Wawa"
            ],
            "Bagac": [
                "Atilano L. Ricardo", "Bagumbayan", "Banawang", "Binuangan", "Binukawan",
                "Ibaba", "Ibis", "Pag-asa", "Parang", "Paysawan",
                "Quinawan", "San Antonio", "Saysain", "Tabing-Ilog"
            ],
            "Balanga City": [
                "Bagong Silang", "Bagumbayan", "Cabog-Cabog", "Camacho", "Cataning",
                "Central", "Cupang North", "Cupang Proper", "Cupang West", "Dangcol",
                "Doña Francisca", "Ibayo", "Lote", "Malabia", "Munting Batangas",
                "Poblacion", "Pto. Rivas Ibaba", "Pto. Rivas Itaas", "San Jose",
                "Sibacan", "Talisay", "Tanato", "Tenejero", "Tortugas", "Tuyo"
            ],
            "Dinalupihan": [
                "A. Rivera", "Aquino", "Bangal", "Bayan-bayanan", "Bonifacio",
                "Burgos", "Colo", "Daang Bago", "Dalao", "Del Pilar",
                "Gen. Luna", "Gomez", "Happy Valley", "Jose C. Payumo, Jr.",
                "Kataasan", "Layac", "Luacan", "Mabini Ext.", "Mabini Proper",
                "Magsaysay", "Maligaya", "Naparing", "New San Jose", "Old San Jose",
                "Padre Dandan", "Pag-asa", "Pagalanggang", "Payangan", "Pentor",
                "Pinulot", "Pita", "Rizal", "Roosevelt", "Roxas", "Saguing",
                "San Benito", "San Isidro", "San Pablo", "San Ramon", "San Simon",
                "Santa Isabel", "Santo Niño", "Sapang Balas", "Torres Bugauen",
                "Tubo-tubo", "Tucop", "Zamora"
            ],
            "Hermosa": [
                "A. Rivera", "Almacen", "Bacong", "Balsic", "Bamban",
                "Burgos-Soliman", "Cataning", "Culis", "Daungan", "Judge Roman Cruz Sr.",
                "Mabiga", "Mabuco", "Maite", "Mambog-Mandama", "Palihan",
                "Pandatung", "Pulo", "Saba", "Sacrifice Valley", "San Pedro",
                "Santo Cristo", "Sumalo", "Tipo"
            ],
            "Limay": [
                "Alangan", "Duale", "Kitang 2 & Luz", "Kitang I", "Lamao",
                "Landing", "Poblacion", "Reformista", "Saint Francis II", "San Francisco de Asis",
                "Townsite", "Wawa"
            ],
            "Mariveles": [
                "Alas-asin", "Alion", "Balon-Anito", "Baseco Country", "Batangas II",
                "Biaan", "Cabcaben", "Camaya", "Ipag", "Lucanin",
                "Malaya", "Maligaya", "Mt. View", "Poblacion", "San Carlos",
                "San Isidro", "Sisiman", "Townsite"
            ],
            "Morong": [
                "Binaritan", "Mabayo", "Nagbalayong", "Poblacion", "Sabang"
            ],
            "Orani": [
                "Apollo", "Bagong Paraiso", "Balut", "Bayan", "Calero",
                "Centro I", "Centro II", "Dona", "Kabalutan", "Kaparangan",
                "Maria Fe", "Masantol", "Mulawin", "Pag-asa", "Paking-Carbonero",
                "Palihan", "Pantalan Bago", "Pantalan Luma", "Parang Parang",
                "Puksuan", "Sibul", "Silahis", "Tagumpay", "Tala",
                "Talimundoc", "Tapulao", "Tenejero", "Tugatog", "Wawa"
            ],
            "Orion": [
                "Arellano", "Bagumbayan", "Balagtas", "Balut", "Bantan",
                "Bilolo", "Calungusan", "Camachile", "Daang Bago", "Daang Bilolo",
                "Daang Pare", "General Lim", "Kapunitan", "Lati", "Lusungan",
                "Puting Buhangin", "Sabatan", "San Vicente", "Santa Elena", "Santo Domingo",
                "Villa Angeles", "Wakas", "Wawa"
            ],
            "Pilar": [
                "Ala-uli", "Bagumbayan", "Balut I", "Balut II", "Bantan Munti",
                "Burgos", "Del Rosario", "Diwa", "Landing", "Liyang",
                "Nagwaling", "Panilao", "Pantingan", "Poblacion", "Rizal",
                "Santa Rosa", "Wakas North", "Wakas South", "Wawa"
            ],
            "Samal": [
                "East Calaguiman", "East Daang Bago", "Gugo", "Ibaba", "Imelda",
                "Lalawigan", "Palili", "San Juan", "San Roque", "Santa Lucia",
                "Sapa", "Tabing Ilog", "West Calaguiman", "West Daang Bago"
            ]
        };

        // Populate municipality dropdown
        var municipalitySelect = document.getElementById('municipality_select');
        var barangaySelect = document.getElementById('barangay_select');

        municipalities.forEach(function(municipality) {
            var option = document.createElement('option');
            option.value = municipality;
            option.textContent = municipality;
            municipalitySelect.appendChild(option);
        });

        // Update barangay dropdown when municipality is selected
        municipalitySelect.addEventListener('change', function() {
            var selectedMunicipality = this.value;
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            
            if (selectedMunicipality && barangays[selectedMunicipality]) {
                barangays[selectedMunicipality].forEach(function(barangay) {
                    var option = document.createElement('option');
                    option.value = barangay;
                    option.textContent = barangay;
                    barangaySelect.appendChild(option);
                });
                barangaySelect.disabled = false;
            } else {
                barangaySelect.disabled = true;
            }
        });
    </script>

    <!-- Add this modal HTML at the bottom of the file, before the scripts -->
    <div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentPreviewModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="documentPreviewContent" class="text-center">
                        <!-- Preview content will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this script after your existing scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewModal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
        const previewContent = document.getElementById('documentPreviewContent');
        const modalTitle = document.getElementById('documentPreviewModalLabel');
        
        // Only target file inputs in the Become a Merchant tab
        const fileInputs = document.querySelectorAll('#account-merchant .preview-trigger');

        fileInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Update modal title with a cleaner document name
                const documentName = input.name
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
                modalTitle.textContent = `Preview: ${documentName}`;

                // Clear previous preview
                previewContent.innerHTML = '';

                if (file.type.startsWith('image/')) {
                    // Handle image files
                    const img = document.createElement('img');
                    img.className = 'img-fluid';
                    img.style.maxHeight = '70vh';
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    
                    previewContent.appendChild(img);
                } else if (file.type === 'application/pdf') {
                    // Handle PDF files
                    const embed = document.createElement('embed');
                    embed.src = URL.createObjectURL(file);
                    embed.type = 'application/pdf';
                    embed.style.width = '100%';
                    embed.style.height = '70vh';
                    
                    previewContent.appendChild(embed);
                } else {
                    // Handle unsupported file types
                    previewContent.innerHTML = `
                        <div class="unsupported-file">
                            <i class="fas fa-file fa-3x mb-3"></i>
                            <p>Preview not available for this file type</p>
                            <p class="file-info">File: ${file.name}</p>
                        </div>
                    `;
                }

                // Show the modal
                previewModal.show();
            });
        });
    });
    </script>

</body>
</html>
