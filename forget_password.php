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
$reset_complete = false;

// Reset forget state if requested
if (isset($_GET['reset'])) {
    unset($_SESSION['forget_email']);
    unset($_SESSION['forget_otp']);
    unset($_SESSION['forget_verified']);
    header("Location: forget_password");
    exit;
}

// Step 1: Send OTP
if (isset($_POST['send_otp'])) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    if (empty($email)) {
        $msg = "Email is required.";
        $msg_type = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid email format.";
        $msg_type = "danger";
    } else {
        $email_escaped = mysqli_real_escape_string($con, $email);
        $check = mysqli_query($con, "SELECT id FROM user_login_det WHERE email = '$email_escaped' AND status = 1 LIMIT 1");
        if (mysqli_num_rows($check) == 0) {
            $msg = "Email address is not registered.";
            $msg_type = "danger";
        } else {
            $otp = sprintf("%06d", rand(100000, 999999));
            $_SESSION['forget_otp'] = $otp;
            $_SESSION['forget_email'] = $email;
            
            // Send OTP
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
                $mail->Subject = 'Reset Your Password - Ethic Studio';
                $mail->Body    = "<h3>Password Recovery</h3><p>Your OTP for resetting your password is: <strong>$otp</strong></p><p>Use this OTP to complete your password reset request.</p>";

                $mail->send();
                $msg = "OTP has been sent to your email.";
                $msg_type = "success";
            } catch (Exception $e) {
                $msg = "Failed to send OTP. Mailer Error: " . $mail->ErrorInfo;
                $msg_type = "danger";
            }
        }
    }
}

// Step 2: Verify OTP
if (isset($_POST['verify_otp'])) {
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    if (empty($otp)) {
        $msg = "OTP is required.";
        $msg_type = "danger";
    } elseif (!isset($_SESSION['forget_otp']) || $_SESSION['forget_otp'] != $otp) {
        $msg = "Invalid OTP. Please try again.";
        $msg_type = "danger";
    } else {
        $_SESSION['forget_verified'] = true;
        unset($_SESSION['forget_otp']);
    }
}

// Step 3: Reset Password
if (isset($_POST['reset_password'])) {
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (empty($new_password) || empty($confirm_password)) {
        $msg = "All fields are required.";
        $msg_type = "danger";
    } elseif ($new_password !== $confirm_password) {
        $msg = "Passwords do not match.";
        $msg_type = "danger";
    } elseif (!isset($_SESSION['forget_verified']) || !isset($_SESSION['forget_email'])) {
        $msg = "Session expired or invalid. Please start again.";
        $msg_type = "danger";
    } else {
        $email_escaped = mysqli_real_escape_string($con, $_SESSION['forget_email']);
        $new_hash = mysqli_real_escape_string($con, password_hash($new_password, PASSWORD_DEFAULT));
        
        $update = mysqli_query($con, "UPDATE user_login_det SET password = '$new_hash' WHERE email = '$email_escaped'");
        if ($update) {
            $msg = "Password reset successfully. Please login with your new password.";
            $msg_type = "success";
            $reset_complete = true;
            
            // Clean up session
            unset($_SESSION['forget_email']);
            unset($_SESSION['forget_verified']);
        } else {
            $msg = "Failed to update password. Please try again.";
            $msg_type = "danger";
        }
    }
}

// Determine current step
$step = 1;
if ($reset_complete) {
    $step = 4; // Complete state
} elseif (isset($_SESSION['forget_email']) && isset($_SESSION['forget_verified']) && $_SESSION['forget_verified'] === true) {
    $step = 3;
} elseif (isset($_SESSION['forget_email']) && isset($_SESSION['forget_otp'])) {
    $step = 2;
}
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>
<head>
    <meta charset="utf-8" />
    <title>Forget Password | Ethic Design Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="Recover account password">
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
        .forget-password-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid rgba(232, 112, 56, 0.2);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(232, 112, 56, 0.08);
        }
        .btn-forget {
            background-color: #e87038 !important;
            border-color: #e87038 !important;
            color: #ffffff !important;
        }
        .btn-forget:hover {
            background-color: #d15f2a !important;
            border-color: #d15f2a !important;
        }
    </style>
</head>
<body class="" x-data="{ showMenuScroll : false }">
    <?php include_once("header.php"); ?>
    
    <div class="backdrop-shadow d-none"></div>
    
    <div class="container my-5">
        <div class="forget-password-container">
            <h3 class="text-center mb-4 text-uppercase fw-semibold" style="color: #e87038;">Recover Password</h3>
            
            <?php if (!empty($msg)): ?>
                <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($step == 1): ?>
                <!-- Step 1: Send OTP -->
                <form action="forget_password" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your registered email address">
                    </div>
                    <button type="submit" name="send_otp" class="btn btn-forget w-100 py-2 rounded-pill mt-3 fw-semibold">SEND OTP</button>
                </form>
            <?php elseif ($step == 2): ?>
                <!-- Step 2: Verify OTP -->
                <form action="forget_password" method="POST">
                    <div class="mb-3">
                        <label for="otp" class="form-label">OTP Verification <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="otp" name="otp" required placeholder="Enter 6-digit OTP" maxlength="6">
                        <div class="form-text">OTP sent to: <strong><?php echo htmlspecialchars($_SESSION['forget_email']); ?></strong></div>
                    </div>
                    <button type="submit" name="verify_otp" class="btn btn-forget w-100 py-2 rounded-pill mt-3 fw-semibold">VERIFY OTP</button>
                    <p class="text-center mt-3"><a href="forget_password?reset=1" class="text-muted fs-14">Start Over / Back</a></p>
                </form>
            <?php elseif ($step == 3): ?>
                <!-- Step 3: Reset Password -->
                <form action="forget_password" method="POST" id="reset-password-form">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required placeholder="Enter new password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm new password">
                    </div>
                    <button type="submit" name="reset_password" class="btn btn-forget w-100 py-2 rounded-pill mt-3 fw-semibold">RESET PASSWORD</button>
                </form>
            <?php elseif ($step == 4): ?>
                <!-- Step 4: Reset Complete -->
                <div class="text-center mt-3">
                    <p>Your password has been successfully updated. You may now log in to your account with your new password.</p>
                    <a href="#accountOffcanvas" data-bs-toggle="offcanvas" class="btn btn-forget w-100 py-2 rounded-pill fw-semibold mt-3">LOGIN NOW</a>
                </div>
            <?php endif; ?>

            <?php if ($step != 4): ?>
                <div class="text-center mt-4">
                    <p class="text-muted">Remembered your password? <a href="#accountOffcanvas" data-bs-toggle="offcanvas" class="fw-semibold" style="color: #e87038; text-decoration: none;">Sign In</a></p>
                </div>
            <?php endif; ?>
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
            $('#reset-password-form').on('submit', function(e) {
                const newPassword = $('#new_password').val();
                const confirmPassword = $('#confirm_password').val();
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert("Passwords do not match.");
                    $('#confirm_password').val('');
                }
            });
        });
    </script>
</body>
</html>
