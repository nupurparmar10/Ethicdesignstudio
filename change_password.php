<?php
ob_start();
session_start();
include_once("connect.php");

if (!isset($_SESSION['u_id'])) {
    header("Location: index");
    exit;
}

$u_id = $_SESSION['u_id'];
$msg = "";
$msg_type = "";

if (isset($_POST['change_password'])) {
    $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $msg = "All fields are required.";
        $msg_type = "danger";
    } elseif ($new_password !== $confirm_password) {
        $msg = "New Password and Confirm Password do not match.";
        $msg_type = "danger";
    } else {
        // Fetch current hashed password from database
        $u_id_escaped = mysqli_real_escape_string($con, $u_id);
        $query = mysqli_query($con, "SELECT password FROM user_login_det WHERE u_id = '$u_id_escaped' LIMIT 1");
        if ($row = mysqli_fetch_assoc($query)) {
            if (password_verify($old_password, $row['password'])) {
                // Update password
                $new_hash = mysqli_real_escape_string($con, password_hash($new_password, PASSWORD_DEFAULT));
                $update = mysqli_query($con, "UPDATE user_login_det SET password = '$new_hash' WHERE u_id = '$u_id_escaped'");
                if ($update) {
                    $msg = "Password updated successfully.";
                    $msg_type = "success";
                } else {
                    $msg = "Failed to update password. Please try again.";
                    $msg_type = "danger";
                }
            } else {
                $msg = "Incorrect old password.";
                $msg_type = "danger";
            }
        } else {
            $msg = "User account not found.";
            $msg_type = "danger";
        }
    }
}
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>
<head>
    <meta charset="utf-8" />
    <title>Change Password | Ethic Design Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="Change account password">
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
        .change-password-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid rgba(232, 112, 56, 0.2);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(232, 112, 56, 0.08);
        }
        .btn-change-password {
            background-color: #e87038 !important;
            border-color: #e87038 !important;
            color: #ffffff !important;
        }
        .btn-change-password:hover {
            background-color: #d15f2a !important;
            border-color: #d15f2a !important;
        }
    </style>
</head>
<body class="" x-data="{ showMenuScroll : false }">
    <?php include_once("header.php"); ?>
    
    <div class="backdrop-shadow d-none"></div>
    
    <div class="container my-5">
        <div class="change-password-container">
            <h3 class="text-center mb-4 text-uppercase fw-semibold" style="color: #e87038;">Change Password</h3>
            
            <?php if (!empty($msg)): ?>
                <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form action="change_password" method="POST" id="change-password-form">
                <div class="mb-3">
                    <label for="old_password" class="form-label">Old Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="old_password" name="old_password" required placeholder="Enter old password">
                </div>
                
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required placeholder="Enter new password">
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm new password">
                </div>
                
                <button type="submit" name="change_password" class="btn btn-change-password w-100 py-2 rounded-pill mt-3 fw-semibold">UPDATE PASSWORD</button>
            </form>
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
            $('#change-password-form').on('submit', function(e) {
                const newPassword = $('#new_password').val();
                const confirmPassword = $('#confirm_password').val();
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert("New Password and Confirm Password do not match.");
                    $('#confirm_password').val('');
                }
            });
        });
    </script>
</body>
</html>
