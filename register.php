<?php
ob_start();
session_start();
include_once("connect.php");

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

$msg = "";
$msg_type = "";

// Handle AJAX Email Check
if (isset($_GET['action']) && $_GET['action'] == 'check_email') {
    header('Content-Type: application/json');
    $email = isset($_GET['email']) ? trim($_GET['email']) : '';
    $email_escaped = mysqli_real_escape_string($con, $email);
    $check_email = mysqli_query($con, "SELECT id FROM user_login_det WHERE email = '$email_escaped'");
    if (mysqli_num_rows($check_email) > 0) {
        echo json_encode(['status' => 'exists', 'message' => 'This email ID already exists. Please enter a new email ID.']);
    } else {
        echo json_encode(['status' => 'ok']);
    }
    exit;
}

// Handle AJAX OTP generation and sending
if (isset($_GET['action']) && $_GET['action'] == 'send_otp') {
    header('Content-Type: application/json');
    $email = isset($_GET['email']) ? trim($_GET['email']) : '';
    
    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email address is required.']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }
    
    // Check for duplicate email in user_login_det
    $email_escaped = mysqli_real_escape_string($con, $email);
    $check_email = mysqli_query($con, "SELECT id FROM user_login_det WHERE email = '$email_escaped'");
    if (mysqli_num_rows($check_email) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This email ID already exists. Please enter a new email ID.']);
        exit;
    }
    
    // Generate a 6-digit OTP
    $otp = sprintf("%06d", rand(100000, 999999));
    $_SESSION['register_otp'] = $otp;
    $_SESSION['register_email'] = $email;
    
    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'Ethicdesignstudiotech@gmail.com';
        $mail->Password   = 'btrh kksb rbcw hdkl';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('Ethicdesignstudiotech@gmail.com', 'Ethic Studio');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Registration - Ethic Studio';
        $mail->Body    = "<h3>Welcome to Ethic Studio!</h3><p>Your OTP for registration is: <strong>$otp</strong></p><p>Please use this OTP to verify your email address and complete your registration.</p>";

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'OTP has been sent to your email.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP. Mailer Error: ' . $mail->ErrorInfo]);
    }
    exit;
}

// Handle Form Submission
if (isset($_POST['register'])) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    
    if (empty($name) || empty($email) || empty($password) || empty($otp)) {
        $msg = "All fields are required.";
        $msg_type = "danger";
    } elseif (!isset($_SESSION['register_otp']) || !isset($_SESSION['register_email']) || $_SESSION['register_email'] !== $email) {
        $msg = "Please verify your email and request a new OTP.";
        $msg_type = "danger";
    } elseif ($_SESSION['register_otp'] != $otp) {
        $msg = "Invalid OTP. Please try again.";
        $msg_type = "danger";
    } else {
        $name_escaped = mysqli_real_escape_string($con, $name);
        $email_escaped = mysqli_real_escape_string($con, $email);
        $pass_hashed = mysqli_real_escape_string($con, password_hash($password, PASSWORD_DEFAULT));
        
        // Double check duplicate email
        $check_email = mysqli_query($con, "SELECT id FROM user_login_det WHERE email = '$email_escaped'");
        if (mysqli_num_rows($check_email) > 0) {
            $msg = "Email is already registered.";
            $msg_type = "danger";
        } else {
            // Insert into users
            $insert_user = mysqli_query($con, "INSERT INTO users (name, email, status) VALUES ('$name_escaped', '$email_escaped', 1)");
            if ($insert_user) {
                $u_id = mysqli_insert_id($con);
                
                // Insert into user_login_det
                $insert_login = mysqli_query($con, "INSERT INTO user_login_det (u_id, uname, email, password, status) VALUES ('$u_id', '$name_escaped', '$email_escaped', '$pass_hashed', 1)");
                
                if ($insert_login) {
                    // Clear OTP session variables
                    unset($_SESSION['register_otp']);
                    unset($_SESSION['register_email']);
                    
                    // Set user session
                    $_SESSION['u_id'] = $u_id;
                    
                    header("Location: index");
                    exit;
                } else {
                    $msg = "Error creating account credentials.";
                    $msg_type = "danger";
                }
            } else {
                $msg = "Error creating user profile.";
                $msg_type = "danger";
            }
        }
    }
}
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>
<head>
    <meta charset="utf-8" />
    <title>Register | Ethic Design Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="Register an account with Ethic Design Studio">
    <meta content="Ethic Design Studio" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/k_favicon_32x.png">
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/icons/font-icon.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid rgba(232, 112, 56, 0.2);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(232, 112, 56, 0.08);
        }
        .btn-register {
            background-color: #e87038 !important;
            border-color: #e87038 !important;
            color: #ffffff !important;
        }
        .btn-register:hover {
            background-color: #d15f2a !important;
            border-color: #d15f2a !important;
        }
    </style>
</head>
<body class="" x-data="{ showMenuScroll : false }">
    <?php include_once("header.php"); ?>
    
    <div class="backdrop-shadow d-none"></div>
    
    <div class="container my-5">
        <div class="register-container">
            <h3 class="text-center mb-4 text-uppercase fw-semibold" style="color: #e87038;">Create Account</h3>
            
            <?php if (!empty($msg)): ?>
                <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div id="ajax-message" class="alert d-none" role="alert"></div>
            
            <form action="register" method="POST" id="register-form">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Enter your full name">
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email address">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Create a strong password">
                </div>
                
                <div class="mb-3">
                    <label for="otp" class="form-label">OTP Verification <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="otp" name="otp" required placeholder="Enter 6-digit OTP" maxlength="6">
                        <button class="btn btn-outline-secondary" type="button" id="resend-otp-btn">Send OTP</button>
                    </div>
                    <div class="form-text">OTP will be sent automatically when you select the password field after typing a valid email.</div>
                </div>
                
                <button type="submit" name="register" class="btn btn-register w-100 py-2 rounded-pill mt-3 fw-semibold">REGISTER</button>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted">Already have an account? <a href="#accountOffcanvas" data-bs-toggle="offcanvas" class="fw-semibold" style="color: #e87038; text-decoration: none;">Sign In</a></p>
            </div>
        </div>
    </div>

    <?php include_once("footer.php"); ?>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/alpinejs/cdn.min.js"></script>
    <script src="assets/js/store.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        $(document).ready(function() {
            let otpSentForEmail = '';

            function sendOTP() {
                const email = $('#email').val().trim();
                if (email === '') {
                    return;
                }
                
                // Simple email pattern check
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    $('#ajax-message').removeClass('d-none alert-success').addClass('alert-danger').text('Please enter a valid email address.');
                    return;
                }

                if (otpSentForEmail === email) {
                    return; // Prevent repeated OTP sending for the same email
                }

                $('#resend-otp-btn').prop('disabled', true).text('Sending...');
                $('#ajax-message').removeClass('d-none alert-success alert-danger').addClass('alert-info').text('Sending OTP...');

                $.ajax({
                    url: 'register',
                    type: 'GET',
                    data: {
                        action: 'send_otp',
                        email: email
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#resend-otp-btn').prop('disabled', false).text('Resend OTP');
                        if (response.status === 'success') {
                            otpSentForEmail = email;
                            $('#ajax-message').removeClass('alert-info alert-danger d-none').addClass('alert-success').text(response.message);
                        } else {
                            $('#ajax-message').removeClass('alert-info alert-success d-none').addClass('alert-danger').text(response.message);
                        }
                    },
                    error: function() {
                        $('#resend-otp-btn').prop('disabled', false).text('Resend OTP');
                        $('#ajax-message').removeClass('alert-info alert-success d-none').addClass('alert-danger').text('An error occurred. Please try again.');
                    }
                });
            }

            // Generate and send OTP when password field receives focus
            $('#password').on('focus', function() {
                sendOTP();
            });

            // Perform instant email uniqueness check on change/blur
            $('#email').on('blur change', function() {
                const email = $(this).val().trim();
                if (email === '') return;
                
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    $('#ajax-message').removeClass('d-none alert-success alert-info').addClass('alert-danger').text('Please enter a valid email address.');
                    return;
                }

                $.ajax({
                    url: 'register',
                    type: 'GET',
                    data: {
                        action: 'check_email',
                        email: email
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'exists') {
                            $('#ajax-message').removeClass('alert-info alert-success d-none').addClass('alert-danger').text(response.message);
                        } else {
                            if ($('#ajax-message').text() === 'This email ID already exists. Please enter a new email ID.') {
                                $('#ajax-message').addClass('d-none').text('');
                            }
                        }
                    }
                });
            });

            // Resend OTP button handler
            $('#resend-otp-btn').on('click', function() {
                // Clear the sentinel so they can force-resend
                otpSentForEmail = '';
                sendOTP();
            });
        });
    </script>
</body>
</html>
