<?php
ob_start();
session_start();

if (!isset($_SESSION['u_id'])) {
    header("Location: index");
    exit;
}
include_once("connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

if (isset($_SESSION['razorpay_order_id']) && !empty($_SESSION['razorpay_order_id'])) 
{
    $rzp_order_id = $_SESSION['razorpay_order_id'];
    $u_id = (int)$_SESSION['u_id'];
    
    $session_key = "email_sent_" . $rzp_order_id;
    if (!isset($_SESSION[$session_key])) {
        // Fetch order details
        $chk_q = mysqli_query($con, "SELECT * FROM checkout WHERE razorpay_order_id='$rzp_order_id' AND u_id='$u_id' LIMIT 1");
        if (mysqli_num_rows($chk_q) > 0) {
            $checkout = mysqli_fetch_assoc($chk_q);
            $check_id = $checkout['check_id'];
            
            // Get state, city names
            $state_name = ''; $city_name = ''; $country_name = 'India';
            $st_q = mysqli_query($con, "SELECT name FROM states WHERE id='" . (int)$checkout['state'] . "' LIMIT 1");
            if ($st_row = mysqli_fetch_assoc($st_q)) $state_name = $st_row['name'];
            $ct_q = mysqli_query($con, "SELECT name FROM cities WHERE id='" . (int)$checkout['city'] . "' LIMIT 1");
            if ($ct_row = mysqli_fetch_assoc($ct_q)) $city_name = $ct_row['name'];
            
            // Fetch order items
            $items_q = mysqli_query($con, "SELECT * FROM order_item WHERE check_id='$check_id'");
            $items = [];
            $order_subtotal = 0;
            while ($row = mysqli_fetch_assoc($items_q)) {
                $items[] = $row;
                $order_subtotal += $row['quantity'] * $row['base_price'];
            }
            
            // Store Contact Info
            $store_name = "Ethic Design Studio";
            $store_email = "Ethicdesignstudiotech@gmail.com";
            $store_phone = "";
            $store_address = "";
            $ci_q = mysqli_query($con, "SELECT * FROM contact_info");
            while ($ci = mysqli_fetch_assoc($ci_q)) {
                $title = strtolower($ci['title']);
                if (strpos($title, 'email') !== false) $store_email = $ci['cvalue'];
                if (strpos($title, 'phone') !== false || strpos($title, 'contact') !== false) $store_phone = $ci['cvalue'];
                if (strpos($title, 'address') !== false || $ci['c_id'] == 2) $store_address = $ci['cvalue'];
            }
            
            // Calculate convenience charge
            $convenience_charge = $checkout['amount'] - $order_subtotal - $checkout['shipping_charge'];
            if ($convenience_charge < 0) $convenience_charge = 0;
            
            $order_date = isset($checkout['date']) ? $checkout['date'] : date('Y-m-d H:i:s');

            // Prepare Email Content
            $html = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px;'>";
            $html .= "<h2 style='color: #0d9488; text-align: center; border-bottom: 2px solid #0d9488; padding-bottom: 10px;'>Order Confirmation</h2>";
            $html .= "<p>Dear <strong>" . htmlspecialchars($checkout['name']) . "</strong>,</p>";
            $html .= "<p>Thank you for your order! Your payment was successful and your order has been received.</p>";
            
            $html .= "<table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>";
            $html .= "<tr><td style='padding: 5px 0;'><strong>Order ID:</strong></td><td>" . htmlspecialchars($checkout['order_id']) . "</td></tr>";
            $html .= "<tr><td style='padding: 5px 0;'><strong>Order Date:</strong></td><td>" . date("d M Y h:i A", strtotime($order_date)) . "</td></tr>";
            $html .= "<tr><td style='padding: 5px 0;'><strong>Payment Status:</strong></td><td><span style='color: green; font-weight: bold;'>" . strtoupper(htmlspecialchars($checkout['status'])) . "</span> via " . htmlspecialchars($checkout['payment_type']) . "</td></tr>";
            $html .= "</table>";
            
            $html .= "<h3 style='margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 5px;'>Shipping Address</h3>";
            $html .= "<p style='line-height: 1.6;'>";
            $html .= "<strong>" . htmlspecialchars($checkout['name']) . " " . htmlspecialchars($checkout['lastname'] ?? '') . "</strong><br>";
            $html .= htmlspecialchars($checkout['address']) . "<br>";
            $html .= htmlspecialchars($city_name) . ", " . htmlspecialchars($state_name) . " - " . htmlspecialchars($checkout['pincode']) . "<br>";
            $html .= htmlspecialchars($country_name) . "<br>";
            $html .= "<strong>Phone:</strong> " . htmlspecialchars($checkout['mobile']) . "<br>";
            $html .= "<strong>Email:</strong> " . htmlspecialchars($checkout['email']);
            $html .= "</p>";
            
            $html .= "<h3 style='margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 5px;'>Items Ordered</h3>";
            $html .= "<table style='width: 100%; border-collapse: collapse;'>";
            $html .= "<tr style='background: #f4f4f4;'>";
            $html .= "<th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Product</th>";
            $html .= "<th style='padding: 10px; text-align: center; border: 1px solid #ddd;'>Qty</th>";
            $html .= "<th style='padding: 10px; text-align: right; border: 1px solid #ddd;'>Price</th>";
            $html .= "<th style='padding: 10px; text-align: right; border: 1px solid #ddd;'>Total</th>";
            $html .= "</tr>";
            
            foreach ($items as $item) {
                $line_total = $item['quantity'] * $item['base_price'];
                $desc = strtoupper($item['title']);
                if (!empty($item['color'])) $desc .= " (COLOR: " . strtoupper($item['color']) . ")";
                if (!empty($item['size'])) $desc .= " (SIZE: " . strtoupper($item['size']) . ")";
                
                $html .= "<tr>";
                $html .= "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($desc) . "</td>";
                $html .= "<td style='padding: 10px; text-align: center; border: 1px solid #ddd;'>" . $item['quantity'] . "</td>";
                $html .= "<td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>₹" . number_format($item['base_price'], 2) . "</td>";
                $html .= "<td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>₹" . number_format($line_total, 2) . "</td>";
                $html .= "</tr>";
            }
            
            $html .= "<tr><td colspan='3' style='padding: 10px; text-align: right; border: 1px solid #ddd;'><strong>Subtotal:</strong></td><td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>₹" . number_format($order_subtotal, 2) . "</td></tr>";
            $html .= "<tr><td colspan='3' style='padding: 10px; text-align: right; border: 1px solid #ddd;'><strong>Shipping:</strong></td><td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>₹" . number_format($checkout['shipping_charge'], 2) . "</td></tr>";
            
            if ($convenience_charge > 0) {
                $html .= "<tr><td colspan='3' style='padding: 10px; text-align: right; border: 1px solid #ddd;'><strong>Convenience Charge:</strong></td><td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>₹" . number_format($convenience_charge, 2) . "</td></tr>";
            }
            
            $html .= "<tr><td colspan='3' style='padding: 10px; text-align: right; border: 1px solid #ddd; background: #f9f9f9;'><strong>Grand Total:</strong></td><td style='padding: 10px; text-align: right; border: 1px solid #ddd; background: #f9f9f9; color: #0d9488;'><strong>₹" . number_format($checkout['amount'], 2) . "</strong></td></tr>";
            $html .= "</table>";
            
            $html .= "<div style='margin-top: 40px; background: #f9f9f9; padding: 20px; border-radius: 5px;'>";
            $html .= "<h4 style='margin-top: 0; color: #333;'>Contact Us</h4>";
            $html .= "<p style='margin: 5px 0; font-size: 14px;'>If you have any questions, please contact us at:</p>";
            $html .= "<p style='margin: 5px 0; font-size: 14px;'><strong>Email:</strong> " . htmlspecialchars($store_email) . "</p>";
            if (!empty($store_phone)) {
                $html .= "<p style='margin: 5px 0; font-size: 14px;'><strong>Phone:</strong> " . htmlspecialchars($store_phone) . "</p>";
            }
            if (!empty($store_address)) {
                $html .= "<p style='margin: 5px 0; font-size: 14px;'><strong>Address:</strong> " . htmlspecialchars($store_address) . "</p>";
            }
            $html .= "<p style='margin-top: 20px; font-size: 14px;'>Regards,<br><strong>" . htmlspecialchars($store_name) . "</strong></p>";
            $html .= "</div>";
            $html .= "</div>";
            
            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->CharSet    = 'UTF-8';
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'Ethicdesignstudiotech@gmail.com';
                $mail->Password   = 'btrh kksb rbcw hdkl'; // From register.php
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
        
                $mail->setFrom('Ethicdesignstudiotech@gmail.com', 'Ethic Studio');
                $mail->addAddress($checkout['email'], $checkout['name'] . ' ' . ($checkout['lastname'] ?? ''));
        
                $mail->isHTML(true);
                $mail->Subject = 'Order Confirmation - ' . $checkout['order_id'];
                $mail->Body    = $html;
        
                $mail->send();
                $_SESSION[$session_key] = true;
                unset($_SESSION['razorpay_order_id']);
                echo "<script>alert('Order Confirmation Email Sent Successfully!');</script>";
            } catch (Exception $e) {
                echo "<script>alert('Failed to send Order Confirmation Email.');</script>";
                echo "<p>Mailer Error: " . htmlspecialchars($mail->ErrorInfo) . "</p>";
            }
        } else {
            echo "<script>alert('Order not found or access denied.');</script>";
        }
    } else {
         echo "<script>alert('Order Confirmation Email was already sent for this order.');</script>";
    }
}

$u=mysqli_fetch_assoc(mysqli_query($con,"select * from users where u_id='$_SESSION[u_id]'"));
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>
<head>
    <meta charset="utf-8" />
    <title>Order History - Ethic Design Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="assets/images/k_favicon_32x.png">
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/flickity/flickity.min.css">
    <link rel="stylesheet" href="assets/libs/jarallax/jarallax.min.css">
    <link href="https://fonts.googleapis.com/css?family=Libre+Baskerville:300,300i,400,400i,500,500i&amp;display=swap" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/icons/font-icon.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        .text-teal { color: #0d9488 !important; }
        .bg-teal { background-color: #0d9488 !important; }
        .btn-teal { background-color: #0d9488; color: #fff !important; }
        .btn-teal:hover { background-color: #0b7a70; color: #fff !important; }
    </style>
</head>

<body class="" x-data="{ showMenuScroll : false }">
<!--head banner-->
<?php include_once("header.php"); ?>

<div class="backdrop-shadow d-none"></div>
<div>
    <?php
        $query = mysqli_query($con, "SELECT * FROM banner WHERE b_id = 20");
        if ($row = mysqli_fetch_assoc($query)) {
            $banner = $row['pic'];
        }
    ?>
    <!-- main slide -->
    <div style="background-image: url('<?php echo $banner; ?>'); background-position: center; background-size: cover;" class="position-relative">
        <div class="position-absolute top-0 start-0 right-0 bottom-0 bg-dark w-100 opacity-50"></div>
        <div class=" container">
            <div class="text-white text-center py-51 position-relative">
                <h4 class="fs-20 fw-medium">ORDER HISTORY</h4>
            </div>
        </div>
    </div>
    
    <section class="py-5 bg-light">
        <div class="container">
            <?php
            $u_id = (int)$_SESSION['u_id'];
            $orders_q = mysqli_query($con, "SELECT * FROM checkout WHERE u_id='$u_id' ORDER BY check_id DESC");
            if (mysqli_num_rows($orders_q) > 0) 
            {
                while ($order = mysqli_fetch_assoc($orders_q)) 
                {   
                    if($order['related_billbook']!='') 
                    {
                        $sales_id=explode('S',$order['related_billbook']);
                        $sales_id=$sales_id[1];
                    }
                    else
                    {
                        $sales_id='0';
                    }
                    $check_id = $order['check_id'];
                    $order_id = $order['order_id'];
                    $status = strtoupper($order['status'] ?? 'Cancelled');
                    $order_date = date("d M Y h:i A", strtotime($order['date'] ?? date('Y-m-d H:i:s')));
                    
                    // Format status badge
                    $badge_class = 'bg-secondary';
                    if (strpos(strtolower($status), 'paid') !== false || strpos(strtolower($status), 'success') !== false) {
                        $badge_class = 'bg-success';
                    } else if (strpos(strtolower($status), 'pending') !== false) {
                        $badge_class = 'bg-warning text-dark';
                    } else if (strpos(strtolower($status), 'cancel') !== false || strpos(strtolower($status), 'fail') !== false) {
                        $badge_class = 'bg-danger';
                    }

                    // Fetch state and city names
                    $state_name = ''; $city_name = '';
                    $st_q = mysqli_query($con, "SELECT name FROM states WHERE id='" . (int)$order['state'] . "' LIMIT 1");
                    if ($st_row = mysqli_fetch_assoc($st_q)) $state_name = $st_row['name'];
                    
                    $ct_q = mysqli_query($con, "SELECT name FROM cities WHERE id='" . (int)$order['city'] . "' LIMIT 1");
                    if ($ct_row = mysqli_fetch_assoc($ct_q)) $city_name = $ct_row['name'];

                    // Fetch items
                    $items_q = mysqli_query($con, "SELECT * FROM order_item WHERE check_id='$check_id'");
                    ?>
                    <div class="card mb-4 shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fs-16 fw-bold">Order ID: <?php echo htmlspecialchars($order_id); ?></h5>
                                <small class="text-muted d-block mb-3">Placed on <?php echo $order_date; ?></small>
                            </div>
                            <span class="badge <?php echo $badge_class; ?> px-3 py-2 fs-13 rounded-pill"><?php echo $status; ?></span>
                        </div>
                        <div class="card-body pt-0">
                            <hr class="mt-0 mb-4">
                            <div class="row mb-4">
                                <!-- Customer Details -->
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <h6 class="fw-bold mb-3 text-uppercase fs-12 text-muted letter-spacing-1">Customer Details</h6>
                                    <p class="mb-1 fw-medium"><?php echo htmlspecialchars($order['name']) . ' ' . htmlspecialchars($order['lastname'] ?? ''); ?></p>
                                    <p class="mb-1 text-muted fs-14"><?php echo htmlspecialchars($order['email']); ?></p>
                                    <p class="mb-0 text-muted fs-14"><?php echo htmlspecialchars($order['mobile']); ?></p>
                                </div>
                                <!-- Shipping Details -->
                                <div class="col-md-4 mb-3 mb-md-0 border-start border-end px-md-4">
                                    <h6 class="fw-bold mb-3 text-uppercase fs-12 text-muted letter-spacing-1">Shipping Address</h6>
                                    <p class="mb-1 text-muted fs-14"><?php echo htmlspecialchars($order['address']); ?></p>
                                    <p class="mb-1 text-muted fs-14"><?php echo htmlspecialchars($city_name) . ', ' . htmlspecialchars($state_name); ?></p>
                                    <p class="mb-0 text-muted fs-14">India - <?php echo htmlspecialchars($order['pincode']); ?></p>
                                </div>
                                <!-- Payment Details -->
                                <div class="col-md-4 ps-md-4">
                                    <h6 class="fw-bold mb-3 text-uppercase fs-12 text-muted letter-spacing-1">Payment Summary</h6>
                                    <div class="d-flex justify-content-between mb-1 fs-14 text-muted">
                                        <span>Shipping:</span>
                                        <span>₹<?php echo number_format($order['shipping_charge'], 2); ?></span>
                                    </div>
                                    <?php if (isset($order['conv_charge']) && $order['conv_charge'] > 0): ?>
                                    <div class="d-flex justify-content-between mb-1 fs-14 text-muted">
                                        <span>Convenience Charge:</span>
                                        <span>₹<?php echo number_format($order['conv_charge'], 2); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between mb-2 fs-14 text-muted border-bottom pb-2">
                                        <span>Method:</span>
                                        <span class="text-capitalize"><?php echo htmlspecialchars($order['payment_type']); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold text-dark">Total Amount:</span>
                                        <span class="text-teal fw-bold fs-15">₹<?php echo number_format($order['amount'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Items List -->
                            <h6 class="fw-bold mb-3 text-uppercase fs-12 text-muted letter-spacing-1 mt-2">Items Ordered</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="py-2 px-3 fw-medium text-muted">Product</th>
                                            <th class="py-2 px-3 fw-medium text-muted text-center" style="width: 100px;">Qty</th>
                                            <th class="py-2 px-3 fw-medium text-muted text-end" style="width: 120px;">Price</th>
                                            <th class="py-2 px-3 fw-medium text-muted text-end" style="width: 120px;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($item = mysqli_fetch_assoc($items_q)) {
                                            $line_total = $item['quantity'] * $item['base_price'];
                                            $desc = strtoupper($item['title']);
                                            if (!empty($item['color'])) $desc .= " (COLOR: " . strtoupper($item['color']) . ")";
                                            if (!empty($item['size'])) $desc .= " (SIZE: " . strtoupper($item['size']) . ")";
                                            ?>
                                            <tr>
                                                <td class="py-2 px-3 text-dark"><?php echo htmlspecialchars($desc); ?></td>
                                                <td class="py-2 px-3 text-center text-muted"><?php echo $item['quantity']; ?></td>
                                                <td class="py-2 px-3 text-end text-muted">₹<?php echo number_format($item['base_price'], 2); ?></td>
                                                <td class="py-2 px-3 text-end fw-medium text-dark">₹<?php echo number_format($line_total, 2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                        if($sales_id!=0)
                        {
                        ?>
                        <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-end">
                            <a href="/ethic_crm/printinvoice1.php?sale_id=<?php echo (int)$sales_id; ?>" target="_blank" class="btn btn-teal btn-sm px-3 rounded-pill">Print Invoice</a>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <div class="mb-4">
                        <i class="pe-7s-shopbag text-muted opacity-50" style="font-size: 64px;"></i>
                    </div>
                    <h3 class="fs-22 text-dark fw-bold">You have no past orders.</h3>
                    <p class="text-muted">Looks like you haven't made your first purchase yet.</p>
                    <a href="shop" class="btn btn-teal mt-3 px-5 py-2 rounded-pill fw-medium">Start Shopping</a>
                </div>
                <?php
            }
            ?>
        </div>
    </section>

    <?php include_once("footer.php"); ?>
    
    <a href="#" x-on:click.prevent="
          window.scrollTo({
             top: 0,
             behavior: 'smooth'
          });
       " class="position-fixed bg-white border rounded d-flex align-items-center justify-content-center shadow" id="nt_backtop">
        <i class="pr pegk pe-7s-angle-up"></i>
    </a>
    
    <div class="backdrop-shadow d-none"></div>
</div>

<!-- custom header -->
<?php include_once("custom_header.php"); ?> 

<!-- JAVASCRIPT -->
<script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/js/store.js"></script>
<script src="assets/libs/jarallax/jarallax.min.js"></script>
<script src="assets/libs/swiper/swiper-bundle.min.js"></script>
<script src="assets/libs/alpinejs/cdn.min.js"></script>
<script src="assets/libs/jquery-countdown/jquery.countdown.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/product-slider.init.js"></script>
<script src="assets/js/popup.js"></script>
<script src="assets/libs/flickity/flickity.pkgd.min.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>