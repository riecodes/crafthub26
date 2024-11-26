<?php
include 'dbconnect.php'; 


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Capture common fields
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $bday = $_POST['bday'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $address = $_POST['address'] ?? '';
    $isMerchant = ($_POST['merchantOption'] ?? 'no') === 'yes';

    // Set role based on merchant option
    $role = $isMerchant ? 'merchant' : 'customer';

    // Generate token for email verification
    $token = bin2hex(random_bytes(50));
    $tokenCreatedAt = date('Y-m-d H:i:s');

    // Insert user into `crafthub_users`
    $queryInsertUser = "INSERT INTO `crafthub_users`(`username`, `email`, `password`, `first_name`, `middle_name`, `last_name`, `birthday`, `contact_no`, `address`, `role`, `is_verified`, `token`, `token_created_at`)
                        VALUES ('$username', '$email', '$password', '$firstName', '$middleName', '$lastName', '$bday', '$phoneNumber', '$address', '$role', 0, '$token', '$tokenCreatedAt')";
    $resultUser = mysqli_query($connection, $queryInsertUser);

    // Check if user was successfully inserted
    if (!$resultUser) {
        die("User Query Failed: " . mysqli_error($connection));
    }

    // Send verification email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'crafthubmartkeplace@gmail.com';
        $mail->Password = 'xaai wayq rzbh zhyc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('crafthubmartkeplace@gmail.com', 'Crafthub Bataan Online Marketplace');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $verificationLink = "https://crafthubbataanmarketplace.online/verify.php?token=$token";
        $mail->Body = "Hi $firstName, <br><br>Thank you for signing up on CraftHub Bataan Marketplace!<br><br>
            To complete your registration, please click the link below to verify your email:<br><br>
            <a href='$verificationLink'>$verificationLink</a><br><br>
            You have 24 hours to verify your email address, otherwise, registration will be voided.<br><br>
            If you did not initiate this, please report it to crafthubmartkeplace@gmail.com.<br><br>
            This is a system-generated email. Please do not reply.<br><br>
            For any concerns, contact 09273363367.<br>";

        $mail->send();
        echo "<script>alert('User Registration Successful! Please check your email to verify your account.'); window.location.href = 'login.php';</script>"; 
    } catch (Exception $e) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }

    // If merchant option was selected, proceed with merchant data
    if ($isMerchant) {
        $user_id = mysqli_insert_id($connection);

        // Capture merchant-specific fields
        $businessName = $_POST['businessName'] ?? '';
        $businessContact = $_POST['businessContact'] ?? '';
        $businessAddress = $_POST['businessAddress'] ?? '';
        $businessMunicipality = $_POST['businessMunicipality'] ?? '';
        $businessBarangay = $_POST['businessBarangay'] ?? '';
        $businessStreet = $_POST['businessStreet'] ?? '';

        // Handle file uploads
        $dtiCertPath = uploadFile('dtiCertificate');
        $businessPermitPath = uploadFile('businessPermit');
        $birRegPath = uploadFile('birRegistration');
        $bir0605Path = uploadFile('bir0605');

        // Insert merchant application details
        $queryInsertMerchant = "INSERT INTO `merchant_applications`(`user_id`, `shop_name`, `shop_contact_no`, `shop_email`, `shop_address`, `shop_municipality`, `shop_barangay`, `shop_street`, `business_permit`, `BIR_registration`, `BIR_0605`, `DTI_certificate`, `status`)
                                VALUES ('$user_id', '$businessName', '$businessContact', '$email', '$businessAddress', '$businessMunicipality', '$businessBarangay', '$businessStreet', '$businessPermitPath', '$birRegPath', '$bir0605Path', '$dtiCertPath', 'pending')";
        $resultMerchant = mysqli_query($connection, $queryInsertMerchant);

        if (!$resultMerchant) {
            die("Merchant Query Failed: " . mysqli_error($connection));
        }
    }
}

// File upload function
function uploadFile($input_name) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES[$input_name]["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($fileType != "pdf" && $fileType != "docx") {
        echo "Only PDF and DOCX files are allowed.";
        return null;
    }

    if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        echo "Error uploading file.";
        return null;
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
    <link href="css/register.css" rel="stylesheet">
</head>
<body>
    <!--=============== REGISTER ===============-->
    <section class="h-100 h-custom gradient-custom-2">
        <img src="images/craftsbg.png" alt="register image" class="register__img">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12">
                    <div class="card card-registration card-registration-2" style="border-radius: 15px;">
                        <div class="card-body p-0">
                            <div class="registration-header text-center py-4">
                                <h2 class="fw-bold text-white mb-0">CraftHub Registration</h2>
                            </div>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="row g-0">
                                    <!--=============== PERSONAL INFORMATION ===============-->
                                    <div class="col-12 personal-section">
                                        <div class="p-5">
                                            <h3 class="fw-normal mb-5">Personal Information</h3>
                                            
                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <div class="form-outline">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" id="firstname" name="firstName" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-outline">
                                                        <label class="form-label">Middle Name</label>
                                                        <input type="text" id="middlename" name="middleName" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-outline">
                                                        <label class="form-label">Last Name</label>
                                                        <input type="text" id="lastname" name="lastName" class="form-control" />
                                                    </div>
                                                </div>
                                            </div>
                                            <!--=============== PHONE NUMBER AND BIRTHDAY ===============-->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-outline phone-input-group">
                                                        <label class="form-label">Phone Number</label>
                                                        <input type="tel" id="phonenumber" name="phoneNumber" class="form-control" maxlength="11" placeholder="09XXXXXXXXX"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-outline">
                                                        <label class="form-label">Birthday</label>
                                                        <input type="date" id="bday" name="bday" class="form-control" />
                                                    </div>
                                                </div>
                                            </div>
                                            <!--=============== ADDRESS AND EMAIL ===============-->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-outline">
                                                        <label class="form-label">Address</label>
                                                        <input type="text" id="address" name="address" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-outline email-input-group">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" 
                                                               id="email" 
                                                               name="email" 
                                                               class="form-control" 
                                                               pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                                               required />
                                                        <div class="validation-tooltip">Please enter a valid email address (e.g., example@domain.com)</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--=============== USERNAME, PASSWORD, AND CONFIRM PASSWORD ===============-->
                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <div class="form-outline">
                                                        <label class="form-label">Username</label>
                                                        <input type="text" id="username" name="username" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-outline password-input-group">
                                                        <label class="form-label">Password</label>
                                                        <div class="password-container">
                                                            <input type="password" id="password" name="password" class="form-control" required />
                                                            <i class="ri-eye-line password-toggle"></i>
                                                            <div class="validation-tooltip">Password must be at least 8 characters</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-outline password-input-group">
                                                        <label class="form-label">Confirm Password</label>
                                                        <div class="password-container">
                                                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required />
                                                            <i class="ri-eye-line password-toggle"></i>
                                                            <div class="validation-tooltip">Passwords do not match</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--=============== TERMS AND CONDITIONS AND BECOME A MERCHANT ===============-->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="terms-check">
                                                        <input class="form-check-input" type="checkbox" id="register-check" />
                                                        <label class="form-check-label">
                                                            I accept the <a href="#!" data-bs-toggle="modal" data-bs-target="#iagreeModal"><u>Terms and Conditions</u></a>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="merchant-radio">
                                                        <label class="form-label">Become a Merchant?</label>
                                                        <div class="radio-group">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="merchantOption" id="merchantYes" value="yes">
                                                                <label class="form-check-label" for="merchantYes">Yes</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="merchantOption" id="merchantNo" value="no" >
                                                                <label class="form-check-label" for="merchantNo">No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--=============== NEXT BUTTON ===============-->
                                            <div class="form-buttons">
                                                <div class="w-100">
                                                    <button type="button" id="nextBtn" class="btn btn-light w-100" disabled>Next</button>
                                                </div>
                                            </div>

                                            <div class="text-center mt-3">
                                                <p class="login-text">
                                                    Already have an account? <a href="login.php"><u>Login</u></a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!--=============== BUSINESS INFORMATION ===============-->
                                    <div class="col-12 business-section" style="display: none;">
                                        <div class="p-5">
                                            <h3 class="fw-normal mb-5">Business Information</h3>

                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-outline">
                                                        <label class="form-label">Business Name</label>
                                                        <input type="text" id="businessName" name="businessName" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-outline phone-input-group">
                                                        <label class="form-label">Business Contact</label>
                                                        <input type="tel" id="businessContact" name="businessContact" class="form-control" maxlength="11" placeholder="09XXXXXXXXX"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--=============== BUSINESS ADDRESS AND EMAIL ===============-->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-outline">
                                                        <label class="form-label">Province</label>
                                                        <input type="text" id="businessProvince" name="businessProvince" class="form-control" value="Bataan" readonly />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-outline">
                                                        <label class="form-label">Business Email</label>
                                                        <input type="email" id="businessEmail" name="businessEmail" class="form-control" />
                                                    </div>
                                                </div>
                                            </div>
                                            <!--=============== BUSINESS MUNICIPALITY, BARANGAY, AND STREET ===============-->
                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <div class="form-outline">
                                                        <label class="form-label">Municipality</label>
                                                        <select class="form-select" id="businessMunicipality" name="businessMunicipality">
                                                            <option value="">Select Municipality</option>
                                                            <option value="Abucay">Abucay</option>
                                                            <option value="Bagac">Bagac</option>
                                                            <option value="Balanga City">Balanga City</option>
                                                            <option value="Dinalupihan">Dinalupihan</option>
                                                            <option value="Hermosa">Hermosa</option>
                                                            <option value="Limay">Limay</option>
                                                            <option value="Mariveles">Mariveles</option>
                                                            <option value="Morong">Morong</option>
                                                            <option value="Orani">Orani</option>
                                                            <option value="Orion">Orion</option>
                                                            <option value="Pilar">Pilar</option>
                                                            <option value="Samal">Samal</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-outline">
                                                        <label class="form-label">Barangay</label>
                                                        <select class="form-select" id="businessBarangay" name="businessBarangay">
                                                            <option value="">Select Barangay</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-outline">
                                                        <label class="form-label">Street</label>
                                                        <input type="text" id="businessStreet" name="businessStreet" class="form-control" />
                                                    </div>
                                                </div>
                                            </div>
                                            <!--=============== FILE REQUIREMENTS ===============-->
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h3 class="fw-normal mb-5">File Requirements</h3>
                                                    <div class="file-requirements">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <div class="form-outline">
                                                                    <label class="form-label">DTI Certificate</label>
                                                                    <input type="file" id="dtiCertificate" name="dtiCertificate" class="form-control" accept=".pdf,.docx" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-outline">
                                                                    <label class="form-label">Business Permit</label>
                                                                    <input type="file" id="businessPermit" name="businessPermit" class="form-control" accept=".pdf,.docx" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-outline">
                                                                    <label class="form-label">BIR Registration</label>
                                                                    <input type="file" id="birRegistration" name="birRegistration" class="form-control" accept=".pdf,.docx" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-outline">
                                                                    <label class="form-label">BIR 0605</label>
                                                                    <input type="file" id="bir0605" name="bir0605" class="form-control" accept=".pdf,.docx" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--=============== BACK AND REGISTER BUTTONS ===============-->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <button type="button" id="backBtn" class="btn btn-light w-100">Back</button>
                                                </div>
                                                <div class="col-md-6">
                                                    <button type="submit" name="submit" class="btn btn-light w-100">Register</button>
                                                </div>
                                            </div>

                                            <div class="text-center mt-3">
                                                <p class="login-text">
                                                    Already have an account? <a href="login.php"><u>Login</u></a>
                                                </p>
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
    </section>

    <!--=============== TERMS AND CONDITIONS MODAL ===============-->
    <div class="modal fade" id="iagreeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><b>Introduction</b></p>
                    <p>Welcome to CraftHub, an online platform targeting buyers of handcrafted products and handmade crafts. 
                    When you access our systems and use the facilities made available to you on this website, 
                    you are bound by these terms and conditions. It is important that you take a few moments of your time to read these 
                    carefully to make sure that you understand your duties as well as rights when it comes to use of our services.</p></br>
                    <p><b>Terms and Conditions</b></p>
                    <p>Terms and Conditions of Use Upon creating an account or using CraftHub Inc. in any manner, 
                    you shall solely be bound to these terms and conditions. This agreement is a legally enforceable contract between you, 
                    the user, and CraftHub. Our platform is available to you on the condition that you will agree to the following terms 
                    and conditions As a result, if you do not accept these terms, it is recommended that you should not use our platform. 
                    CraftHub application requires the continued use by the user to signify their consent to any changes that we may make 
                    to these terms as and when necessary.</p></br>
                    <p><b>Definition of Terminology</b></p>
                    <p>For the purposes of these terms and conditions, the following definitions apply: Use refers to any individual 
                    who uses the services of CraftHub, whether as a buyer or a seller. The term 'Seller' in this context describes those 
                    who use the platform to offer handmade items for sale. For the purpose of this paper, a "Buyer" is defined as a user 
                    who is seeking to acquire a product from a seller. "Content" refers to all files that may be uploaded to the CraftHub 
                    platform, and this covers textual content, images, and product descriptions. Using the context of this particular project, 
                    the term "Platform" is defined as the CraftHub website and all sorts of services connected to it.</p></br>
                    <p><b>Intellectual Property Rights</b></p>
                    <p>Everything on CraftHub, such as texts, graphics, logos, and images is the property of CraftHub or the data's owner 
                    and protected by laws from copyright. As for the content, it remains the property of its owner but users provide 
                    CraftHub with a non-exclusive license for using, public display, and distribution of content as a part of the services 
                    provided. Access to the website or parts of it is strictly prohibited if not duly authorized by CraftHub and it may 
                    infringe trademark as well as copyright law and all other applicable laws.</p></br>
                </div>
                <div class="modal-footer">
                    <input type="checkbox" class="agreeCheckbox" id="agreeCheckbox">
                    <label for="agreeCheckbox">I Agree to the Terms & Conditions</label>
                </div>
            </div>
        </div>
    </div>

   <!--=============== FILE PREVIEW MODAL ===============-->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfPreview" style="display: none;" title="PDF Preview"></iframe>
                    <img id="imagePreview" style="display: none;" alt="File preview">
                    <pre id="textPreview" style="display: none;"></pre>
                    <div id="unsupportedFormat" style="display: none;">
                        <p>Preview not available for this file type</p>
                        <p>Filename: <span id="filename"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/termsandconditions.js"></script>
    <script src="js/register.js"></script>
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

        // Function to populate barangay dropdown based on selected municipality
        function populateBarangays() {
            var municipalitySelect = document.getElementById('businessMunicipality');
            var barangaySelect = document.getElementById('businessBarangay');
            var selectedMunicipality = municipalitySelect.value;

            // Clear existing options
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

            // If a municipality is selected, populate barangays
            if (selectedMunicipality && barangays[selectedMunicipality]) {
                barangays[selectedMunicipality].forEach(function(barangay) {
                    var option = document.createElement('option');
                    option.value = barangay;
                    option.textContent = barangay;
                    barangaySelect.appendChild(option);
                });
            }
        }

        // Add event listener to municipality select
        document.getElementById('businessMunicipality').addEventListener('change', populateBarangays);

        // File preview functionality
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const fileType = file.type;
                const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

                if (!validTypes.includes(fileType)) {
                    alert('Please upload only PDF or DOCX files');
                    this.value = ''; // Clear the input
                    return;
                }

                // Show preview modal
                const modal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
                const pdfPreview = document.getElementById('pdfPreview');
                const textPreview = document.getElementById('textPreview');
                const unsupportedFormat = document.getElementById('unsupportedFormat');
                const filename = document.getElementById('filename');

                // Hide all preview elements initially
                pdfPreview.style.display = 'none';
                textPreview.style.display = 'none';
                unsupportedFormat.style.display = 'none';

                // Update filename
                filename.textContent = file.name;

                if (fileType === 'application/pdf') {
                    // Handle PDF files
                    const fileURL = URL.createObjectURL(file);
                    pdfPreview.src = fileURL;
                    pdfPreview.style.display = 'block';
                } else if (fileType.includes('word')) {
                    // For Word documents, show filename and type
                    unsupportedFormat.style.display = 'block';
                }

                modal.show();
            });
        });

        // Clean up object URLs when modal is hidden
        document.getElementById('filePreviewModal').addEventListener('hidden.bs.modal', function () {
            const pdfPreview = document.getElementById('pdfPreview');
            if (pdfPreview.src) {
                URL.revokeObjectURL(pdfPreview.src);
                pdfPreview.src = '';
            }
        });
    </script>
</body>
</html> 