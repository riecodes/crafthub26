<?php
ob_start();
include 'dbconnect.php';
include 'cart_count.php';

if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['userID'];

// Fetch the application ID for the logged-in user
$appQuery = "SELECT application_id FROM merchant_applications WHERE user_id = ?";
$stmtApp = $connection->prepare($appQuery);
$stmtApp->bind_param('s', $user_id);
$stmtApp->execute();
$appResult = $stmtApp->get_result();

if ($appResult && $appResult->num_rows > 0) {
    $appRow = $appResult->fetch_assoc();
    $application_id = $appRow['application_id'];
} else {
    die('Application ID not found for this user.');
}

// Fetch merchant and user information
$query = "
    SELECT 
        ma.shop_name, ma.shop_contact_no, ma.shop_email, ma.shop_address, 
        ma.shop_municipality, ma.shop_barangay, ma.shop_street, 
        cu.user_profile 
    FROM merchant_applications ma
    JOIN crafthub_users cu ON ma.user_id = cu.user_id
    WHERE ma.user_id = ? 
    LIMIT 1";
    
$stmt = $connection->prepare($query);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Display data if available
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $shop_name = $row['shop_name'];
    $user_profile = $row['user_profile'] ? 'uploads/' . $row['user_profile'] : 'images/default-shop.jpg'; // Use default if no profile image
} else {
    die('No data found for this user.');
}
?>

<!--=============== HEADER IMAGE AND PROFILE INFO ===============-->
<div class="container-fluid">
    <div class="row position-relative">
        <img src="images/mprofile.png" id="header-image" alt="header-image" class="img">
        <div class="merchant-info">
            <img src="<?php echo htmlspecialchars($user_profile); ?>" id="merchant-image" alt="merchant-image">
            <div class="merchant-details">
                <p class="shop-name"><?php echo htmlspecialchars($shop_name); ?></p>
                <div class="shop-rating">
                    <span class="rating">Shop Rating</span>
                    <span class="rating-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span class="rating-text">(4.5)</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!--=============== PROFILE BUSINESS FORM ===============-->
<div class="container mt-4">
    <div class="add-product-container">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="heading-with-count">Business Information</h4>
            </div>
        </div>
        <form action="" method="post" id="businessForm">
            <div class="row">
                <div class="col-md-12">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-building-2-line me-2"></i>Business Name
                                </label>
                                <input type="text" name="business_name" class="form-control custom-input" value="<?php echo htmlspecialchars($row['shop_name']); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-phone-line me-2"></i>Business Contact
                                </label>
                                <input type="text" name="business_contact" class="form-control custom-input" value="<?php echo htmlspecialchars($row['shop_contact_no']); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-mail-line me-2"></i>Business Email
                                </label>
                                <input type="email" name="business_email" class="form-control custom-input" value="<?php echo htmlspecialchars($row['shop_email']); ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label">
                            <i class="ri-map-pin-2-line me-2"></i>Business Address
                        </label>
                        
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-building-line me-2"></i>Municipality
                                </label>
                                <input type="text" name="municipality" class="form-control custom-input" value="<?php echo htmlspecialchars($row['shop_municipality']); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-community-line me-2"></i>Barangay
                                </label>
                                <input type="text" name="barangay" class="form-control custom-input" value="<?php echo htmlspecialchars($row['shop_barangay']); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-road-map-line me-2"></i>Street
                                </label>
                                <input type="text" name="street" class="form-control custom-input" value="<?php echo htmlspecialchars($row['shop_street']); ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12" id="form-buttons">
                            <button type="button" class="btn btn-primary w-100 h-50px" id="editBusinessBtn" style="height: 50px; font-size: 1.1rem; font-weight: 600;">
                                <i class="ri-edit-line me-2"></i>Edit Information
                            </button>
                            
                            <div class="d-none" id="edit-buttons">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-danger flex-grow-1" id="cancelBusinessBtn">
                                        <i class="ri-close-line me-2"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-success flex-grow-1" id="saveBusinessBtn">
                                        <i class="ri-save-line me-2"></i>Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'mersidebar.php';
?> 

<script>
document.addEventListener('DOMContentLoaded', function() {
    const businessForm = document.getElementById('businessForm');
    const editBusinessBtn = document.getElementById('editBusinessBtn');
    const cancelBusinessBtn = document.getElementById('cancelBusinessBtn');
    const editButtons = document.getElementById('edit-buttons');
    const formButtons = document.getElementById('form-buttons');
    
    // Store original values
    let originalValues = {};
    
    // Function to toggle form fields readonly state
    function toggleFormFields(readonly) {
        const inputs = businessForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.readOnly = readonly;
            if (!readonly) {
                input.classList.remove('custom-input-readonly');
            } else {
                input.classList.add('custom-input-readonly');
            }
        });
    }
    
    // Function to store original values
    function storeOriginalValues() {
        const inputs = businessForm.querySelectorAll('input');
        inputs.forEach(input => {
            originalValues[input.name] = input.value;
        });
    }
    
    // Function to restore original values
    function restoreOriginalValues() {
        const inputs = businessForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = originalValues[input.name];
        });
    }
    
    // Edit button click handler
    editBusinessBtn.addEventListener('click', function() {
        storeOriginalValues();
        toggleFormFields(false);
        editBusinessBtn.classList.add('d-none');
        editButtons.classList.remove('d-none');
    });
    
    // Cancel button click handler
    cancelBusinessBtn.addEventListener('click', function() {
        restoreOriginalValues();
        toggleFormFields(true);
        editBusinessBtn.classList.remove('d-none');
        editButtons.classList.add('d-none');
    });
    
    // Form submission handler
    businessForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Here you would typically send the data to the server
        // For now, we'll just show an alert
        alert('Changes saved successfully!');
        
        toggleFormFields(true);
        editBusinessBtn.classList.remove('d-none');
        editButtons.classList.add('d-none');
    });
});
</script> 