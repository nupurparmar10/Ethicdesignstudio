<?php
	ob_start();
	session_start();
	include_once("connect.php");

    if(!isset($_REQUEST['check_id']) || empty($_REQUEST['check_id'])) {
        die("Invalid Order ID. <a href='vieworderdetails.php'>Go Back</a>");
    }

    $check_id = mysqli_real_escape_string($con, $_REQUEST['check_id']);
    $order_res = mysqli_query($con, "SELECT * FROM checkout WHERE check_id = '$check_id'");
    
    if(mysqli_num_rows($order_res) == 0) {
        die("Order not found. <a href='vieworderdetails.php'>Go Back</a>");
    }
    
    $order = mysqli_fetch_assoc($order_res);
    
    function getLocationName($con, $table, $id) {
        if(empty($id) || !is_numeric($id)) return htmlspecialchars($id);
        $res = mysqli_query($con, "SELECT name FROM $table WHERE id = '$id'");
        if($res && mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            return htmlspecialchars($row['name']);
        }
        return htmlspecialchars($id);
    }
    
    $city_name = getLocationName($con, 'cities', $order['city']);
    $state_name = getLocationName($con, 'states', $order['state']);
    $country_name = getLocationName($con, 'countries', $order['country']);
?>
<!DOCTYPE html>
<html lang="en">
<head>        
    <title>Ethic Design Studio - Order Details</title>                 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="logo3.png" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
    <script src="js/jquery.min.js"></script>
</head>
<body>
    <div class="page-container">
        
        <?php  $menu13=true; $smenu13="6"; $ssmenu13="1"; include_once("sidebar.php"); ?>
        
        <div class="page-content">
            
            <?php include_once("topheader.php"); ?>
            
            <ul class="breadcrumb">
                <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="vieworderdetails.php">Order Details</a></li>
                <li class="active">View Order Info</li>
            </ul>
            
            <div class="page-title">                    
               <h2><span class="fa fa-list"></span> Order Information</h2>
            </div>
            
            <div class="page-content-wrap">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">   
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-actions">
                                                <tbody>  
                                                    <tr>
                                                        <th width="15%">Order ID</th>
                                                        <td width="35%"><strong><?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                                                        <th width="15%">Date</th>
                                                        <td width="35%"><strong><?php echo date('d-m-Y h:i A', strtotime($order['created_at'])); ?></strong></td>														
                                                    </tr>
                                                    <tr>
                                                        <th>Customer Name</th>
                                                        <td><?php echo htmlspecialchars($order['name']) . ' ' . htmlspecialchars($order['lastname']); ?></td>
                                                        <th>Email / Mobile</th>
                                                        <td><?php echo htmlspecialchars($order['email']); ?> / <?php echo htmlspecialchars($order['mobile']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Address</th>
                                                        <td>
                                                            <?php 
                                                                echo htmlspecialchars($order['address']) . "<br>";
                                                                if(!empty($order['address2'])) {
                                                                    echo htmlspecialchars($order['address2']) . "<br>";
                                                                }
                                                                echo "<b>City:</b> " . $city_name . ", <b>State:</b> " . $state_name . "<br>";
                                                                echo "<b>Country:</b> " . $country_name . " - <b>PIN:</b> " . htmlspecialchars($order['pincode']);
                                                            ?>
                                                        </td>
                                                        <th>Payment Type</th>
                                                        <td><strong><?php echo htmlspecialchars($order['payment_type']); ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Razorpay Order ID</th>
                                                        <td><?php echo htmlspecialchars($order['razorpay_order_id']); ?></td>
                                                        <th>Razorpay Payment ID</th>
                                                        <td><?php echo htmlspecialchars($order['razorpay_payment_id']); ?></td>
                                                    </tr>

                                                    <tr>
                                                        <th>Order Status</th>
                                                        <td colspan="3">
                                                            <?php 
                                                                $ostatus = strtoupper($order['order_status']);
                                                                if ($ostatus == 'PAID') {
                                                                    echo '<span class="label label-success" style="font-size:14px; padding:6px 12px;">' . htmlspecialchars($order['order_status']) . '</span>';
                                                                } elseif ($ostatus == 'NEW') {
                                                                    echo '<span class="label label-danger" style="font-size:14px; padding:6px 12px;">' . htmlspecialchars($order['order_status']) . '</span>';
                                                                } else {
                                                                    echo '<span class="label label-default" style="font-size:14px; padding:6px 12px;">' . htmlspecialchars($order['order_status']) . '</span>';
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    
                                                    <!-- Item Details Section -->
                                                    <tr>
                                                        <td colspan="4">
                                                            <h4><strong>Order Items</strong></h4>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-striped table-actions">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>S.No.</th>
                                                                            <th>Product Title</th>
                                                                            <th>Color</th>
                                                                            <th>Size</th>
                                                                            <th>Quantity</th>
                                                                            <th>Base Price</th>
                                                                            <th>Total</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                            $items_res = mysqli_query($con, "SELECT * FROM order_item WHERE check_id = '$check_id'");
                                                                            $i = 1;
                                                                            $sub_total = 0;
                                                                            if(mysqli_num_rows($items_res) > 0) {
                                                                                while($item = mysqli_fetch_assoc($items_res)) {
                                                                                    $item_total = $item['quantity'] * $item['base_price'];
                                                                                    $sub_total += $item_total;
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $i++; ?></td>
                                                                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                                                                            <td><?php echo htmlspecialchars($item['color']); ?></td>
                                                                            <td><?php echo htmlspecialchars($item['size']); ?></td>
                                                                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                                            <td>₹<?php echo number_format($item['base_price'], 2); ?></td>
                                                                            <td>₹<?php echo number_format($item_total, 2); ?></td>
                                                                        </tr>
                                                                        <?php
                                                                                }
                                                                            } else {
                                                                                echo "<tr><td colspan='7' align='center'>No items found for this order.</td></tr>";
                                                                            }
                                                                        ?>
                                                                    </tbody>
                                                                    <tr>
                                                                        <td colspan="6" align="right"><strong>Sub Total</strong></td>
                                                                        <td><strong>₹<?php echo number_format($sub_total, 2); ?></strong></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="6" align="right"><strong>Shipping Charge</strong></td>
                                                                        <td><strong>₹<?php echo number_format($order['shipping_charge'], 2); ?></strong></td>
                                                                    </tr>
                                                                    <?php if (isset($order['conv_charge']) && $order['conv_charge'] > 0): ?>
                                                                    <tr>
                                                                        <td colspan="6" align="right"><strong>Convenience Charge</strong></td>
                                                                        <td><strong>₹<?php echo number_format($order['conv_charge'], 2); ?></strong></td>
                                                                    </tr>
                                                                    <?php endif; ?>
                                                                    <tr style="font-size: 16px;">
                                                                        <td colspan="6" align="right"><strong>Grand Total</strong></td>
                                                                        <td><strong>₹<?php echo number_format($order['amount'], 2); ?></strong></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
    
    <?php include_once("footer.php"); ?>
    
    <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>                
    
    <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
    <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
    
    <script type="text/javascript" src="js/plugins.js"></script>        
    <script type="text/javascript" src="js/actions.js"></script>        
</body>
</html>
