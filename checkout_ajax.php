<?php
ob_start();
session_start();
include_once("connect.php");
$apiKey = 'rzp_test_SuMLK9R3IZJYBP';
$apiSecret = 'QKXiEauOgHvuqcG56oIPRd0k';
function checkout_json_response($data)
{
    if (ob_get_length()) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function checkout_next_id($con, $table, $column)
{
    $result = mysqli_query($con, "SELECT COALESCE(MAX($column), 0) + 1 AS next_id FROM $table");
    if ($row = mysqli_fetch_assoc($result)) {
        return (int)$row['next_id'];
    }
    return 1;
}

function checkout_generate_invoice($con)
{
    $sale_id = 1;
    $serial = 1;
    $result = mysqli_query($con, "SELECT invno,sale_id FROM billbook ORDER BY sale_id DESC LIMIT 1");
    if ($row = mysqli_fetch_assoc($result)) {
        $sale_id = ((int)$row['sale_id']) + 1;
        $parts = explode("/", $row['invno']);
        $serial = ((int)end($parts)) + 1;
    }

    if ($serial < 10) {
        $invno = "EDS/" . date("Y-m") . "/00" . $serial;
    } elseif ($serial < 100) {
        $invno = "EDS/" . date("Y-m") . "/0" . $serial;
    } else {
        $invno = "EDS/" . date("Y-m") . "/" . $serial;
    }

    return [$sale_id, $invno];
}

function checkout_cart_summary($con, $user_id)
{
    $items = [];
    $subtotal = 0;
    $stmt = mysqli_prepare($con, "SELECT c.v_id,c.color,c.size,c.quantity,v.edsellrate,i.saledesp FROM cart c JOIN variant v ON c.v_id=v.v_id JOIN item_details i ON v.item_id=i.item_id WHERE c.u_id=?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $grouped_items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $v_id = (int)$row['v_id'];
        $qty = (int)$row['quantity'];
        $rate = (float)$row['edsellrate'];
        
        if (isset($grouped_items[$v_id])) {
            $grouped_items[$v_id]['quantity'] += $qty;
        } else {
            $row['quantity'] = $qty;
            $row['edsellrate'] = $rate;
            $grouped_items[$v_id] = $row;
        }
        $subtotal += $qty * $rate;
    }
    mysqli_stmt_close($stmt);

    return [
        'items' => array_values($grouped_items),
        'subtotal' => $subtotal
    ];
}

function checkout_user_ledger_id($con, $user)
{
    $mobile = $user['mobile'] ?? '';
    $email = $user['email'] ?? '';
    $name = $user['name'] ?? '';

    $stmt = mysqli_prepare($con, "SELECT la.ledger_id FROM ledger_accounts la JOIN ledger_details ld ON la.ledger_id=ld.ledger_id WHERE ld.mobile=? OR ld.email=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $mobile, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return (int)$row['ledger_id'];
    }
    mysqli_stmt_close($stmt);

    $ledger_id = checkout_next_id($con, 'ledger_accounts', 'ledger_id');
    $group_id = 26;
    $opening_bal = 0;
    $status = 1;
    $ledger_stmt = mysqli_prepare($con, "INSERT INTO ledger_accounts (ledger_id,name,group_id,opening_bal,status) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($ledger_stmt, "isidi", $ledger_id, $name, $group_id, $opening_bal, $status);
    mysqli_stmt_execute($ledger_stmt);
    mysqli_stmt_close($ledger_stmt);

    $address = $user['address'] ?? '';
    $tinno = '';
    $detail_stmt = mysqli_prepare($con, "INSERT INTO ledger_details (ledger_id,contact_person,address,tinno,mobile,email) VALUES (?,?,?,?,?,?)");
    mysqli_stmt_bind_param($detail_stmt, "isssss", $ledger_id, $name, $address, $tinno, $mobile, $email);
    mysqli_stmt_execute($detail_stmt);
    mysqli_stmt_close($detail_stmt);

    return $ledger_id;
}

function checkout_paidby_ledger($con)
{
    $result = mysqli_query($con, "SELECT ledger_id FROM ledger_accounts WHERE name='Cash Account' AND status=1 LIMIT 1");
    if ($row = mysqli_fetch_assoc($result)) {
        return (int)$row['ledger_id'];
    }

    $result = mysqli_query($con, "SELECT ledger_id FROM ledger_accounts WHERE status=1 AND group_id IN (SELECT group_id FROM group_master WHERE group_name='Bank Accounts') ORDER BY ledger_id LIMIT 1");
    if ($row = mysqli_fetch_assoc($result)) {
        return (int)$row['ledger_id'];
    }

    return 1;
}

function checkout_city_state_names($con, $city_id, $state_id)
{
    $city_name = (string)$city_id;
    $state_name = (string)$state_id;

    $city_stmt = mysqli_prepare($con, "SELECT name FROM cities WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($city_stmt, "i", $city_id);
    mysqli_stmt_execute($city_stmt);
    $city_result = mysqli_stmt_get_result($city_stmt);
    if ($city = mysqli_fetch_assoc($city_result)) {
        $city_name = $city['name'];
    }
    mysqli_stmt_close($city_stmt);

    $state_stmt = mysqli_prepare($con, "SELECT name FROM states WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($state_stmt, "i", $state_id);
    mysqli_stmt_execute($state_stmt);
    $state_result = mysqli_stmt_get_result($state_stmt);
    if ($state = mysqli_fetch_assoc($state_result)) {
        $state_name = $state['name'];
    }
    mysqli_stmt_close($state_stmt);

    return [$city_name, $state_name];
}

// Token logic has been moved to get_valid_shiprocket_token() in connect.php

// Creates the order in Shiprocket after payment is confirmed.
// Returns ['shiprocket_order_id' => ..., 'shipment_id' => ...] or null on failure.
function shiprocket_create_order($con, $check_id, $checkout, $city_name, $state_name)
{
    $token = get_valid_shiprocket_token();
    if (!$token) {
        return null;
    }

    $order_items = [];
    $total_qty = 0;
    $items_result = mysqli_query($con, "SELECT * FROM order_item WHERE check_id='$check_id'");
    while ($item = mysqli_fetch_assoc($items_result)) {
        $qty = (int)$item['quantity'];
        $total_qty += $qty;
        $order_items[] = [
            "name"          => $item['title'],
            "sku"           => "V" . $item['v_id'],
            "units"         => $qty,
            "selling_price" => (float)$item['base_price'],
        ];
    }

    // Same package-dimension lookup used on the checkout page, keyed by total quantity
    $length = $breadth = $height = 10;
    $weight = 0.5;
    $dim_result = mysqli_query($con, "SELECT * FROM dimension WHERE min_quantity<=$total_qty AND max_quantity>=$total_qty");
    if ($dim = mysqli_fetch_assoc($dim_result)) {
        $length  = (float)$dim['length'];
        $breadth = (float)$dim['width'];
        $height  = (float)$dim['height'];
        $weight  = ($length * $breadth * $height) / 5000;
    }

    $order_subtotal = 0;
    $subtotal_result = mysqli_query($con, "SELECT SUM(quantity * base_price) AS subtotal FROM order_item WHERE check_id='$check_id'");
    if ($row = mysqli_fetch_assoc($subtotal_result)) {
        $order_subtotal = (float)$row['subtotal'];
    }

    $name_parts = explode(' ', trim($checkout['name']), 2);
    $first_name = $name_parts[0];
    $last_name  = $name_parts[1] ?? '';

    $payload_array = [
        "order_id"              => (string)$check_id,
        "order_date"            => date("Y-m-d H:i"),
        "pickup_location"       => "Home",
        "billing_customer_name" => $first_name,
        "billing_last_name"     => $last_name,
        "billing_address"       => $checkout['address'],
        "billing_city"          => $city_name,
        "billing_pincode"       => $checkout['pincode'],
        "billing_state"         => $state_name,
        "billing_country"       => "India",
        "billing_email"         => $checkout['email'],
        "billing_phone"         => $checkout['mobile'],
        "shipping_is_billing"   => true,
        "order_items"           => $order_items,
        "payment_method"        => "Prepaid", 
        "sub_total"             => $order_subtotal,
        "length"                => $length,
        "breadth"               => $breadth,
        "height"                => $height,
        "weight"                => $weight,
    ];
    $payload = json_encode($payload_array);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ),
    ));
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $result = json_decode($response, true);

    $_SESSION['debug_shiprocket'] = [
        'called' => true,
        'payload' => $payload,
        'http_code' => $httpcode,
        'response' => $response,
        'order_id' => $result['order_id'] ?? null,
        'shipment_id' => $result['shipment_id'] ?? null,
        'error' => empty($result['order_id']) ? 'Order creation failed' : null
    ];

    if (empty($result['order_id'])) {
        error_log('Shiprocket order creation failed for check_id ' . $check_id . ': ' . $response);
        return null;
    }

    return [
        'shiprocket_order_id' => $result['order_id'],
        'shipment_id'         => $result['shipment_id'] ?? null,
    ];
}

if (isset($_POST['ajax_create_checkout'])) 
{
    if (empty($_SESSION['u_id'])) {
        checkout_json_response(['status' => 0, 'message' => 'Please login before placing order.']);
    }

    $user_id = (int)$_SESSION['u_id'];
    $cart_summary = checkout_cart_summary($con, $user_id);
    if (empty($cart_summary['items'])) {
        checkout_json_response(['status' => 0, 'message' => 'Cart is empty.']);
    }

    $name = trim($_POST['name'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $state_name = trim($_POST['state'] ?? '');
    $city_name = trim($_POST['city'] ?? '');
    $pincode = preg_replace('/\D+/', '', $_POST['pincode'] ?? '');
    $mobile = preg_replace('/\D+/', '', $_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $address === '' || $state_name === '' || $city_name === '' || strlen($pincode) !== 6 || $mobile === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        checkout_json_response(['status' => 0, 'message' => 'Please complete checkout information.']);
    }

    $shipping_charge = max(0, (float)($_POST['shipping_charge'] ?? 0));
    $convenience_percent = max(0, (float)($_POST['convenience_charge'] ?? 0));
    $convenience_charge = (($cart_summary['subtotal'] + $shipping_charge) * $convenience_percent) / 100;
    $grandtotal = round($cart_summary['subtotal'] + $shipping_charge + $convenience_charge);

    $full_address = trim($address);
    $status = 'pending';
    $country = 'India';
    $payment_type = 'Razorpay';
    $razorpay_payment_id = '';
    $razorpay_order_id = '';

    mysqli_begin_transaction($con);

    $next_num = 1001;
    $lock_q = mysqli_query($con, "SELECT order_id FROM checkout WHERE order_id LIKE 'ED/ORDER/%' ORDER BY CAST(SUBSTRING_INDEX(order_id, '/', -1) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");
    if ($lock_q && $lock_row = mysqli_fetch_assoc($lock_q)) {
        $parts = explode('/', $lock_row['order_id']);
        $last_num = (int)end($parts);
        if ($last_num >= 1001) {
            $next_num = $last_num + 1;
        }
    }
    $order_id = "ED/ORDER/" . $next_num;

    // Find country ID
    $country_id = 101; // Default to India (ID 101)
    $country_q = mysqli_prepare($con, "SELECT id FROM countries WHERE name=? LIMIT 1");
    mysqli_stmt_bind_param($country_q, "s", $country);
    mysqli_stmt_execute($country_q);
    $country_res = mysqli_stmt_get_result($country_q);
    if ($country_row = mysqli_fetch_assoc($country_res)) {
        $country_id = (int)$country_row['id'];
    }
    mysqli_stmt_close($country_q);

    // Find state ID
    $state_id = 0;
    $state_q = mysqli_prepare($con, "SELECT id FROM states WHERE name=? LIMIT 1");
    mysqli_stmt_bind_param($state_q, "s", $state_name);
    mysqli_stmt_execute($state_q);
    $state_res = mysqli_stmt_get_result($state_q);
    if ($state_row = mysqli_fetch_assoc($state_res)) {
        $state_id = (int)$state_row['id'];
    }
    mysqli_stmt_close($state_q);

    // Find city ID
    $city_id = 0;
    $city_q = mysqli_prepare($con, "SELECT id FROM cities WHERE name=? LIMIT 1");
    mysqli_stmt_bind_param($city_q, "s", $city_name);
    mysqli_stmt_execute($city_q);
    $city_res = mysqli_stmt_get_result($city_q);
    if ($city_row = mysqli_fetch_assoc($city_res)) {
        $city_id = (int)$city_row['id'];
    }
    mysqli_stmt_close($city_q);

    // Update user details if email matches exactly
    $found_u_id = $user_id;
    $user_lookup_stmt = mysqli_prepare($con, "SELECT u_id FROM users WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($user_lookup_stmt, "s", $email);
    mysqli_stmt_execute($user_lookup_stmt);
    $user_lookup_result = mysqli_stmt_get_result($user_lookup_stmt);
    if ($user_row = mysqli_fetch_assoc($user_lookup_result)) {
        $found_u_id = (int)$user_row['u_id'];
        
        // Update users table
        $update_user_stmt = mysqli_prepare($con, "UPDATE users SET name=?, mobile=?, address=?, city=?, state=?, country=?, pincode=?,lastname=? WHERE u_id=?");
        mysqli_stmt_bind_param($update_user_stmt, "ssssssssi", $name, $mobile, $full_address, $city_id, $state_id, $country_id, $pincode, $lastname, $found_u_id);
        mysqli_stmt_execute($update_user_stmt);
        mysqli_stmt_close($update_user_stmt);

        // Update user_login_det table
        $update_login_stmt = mysqli_prepare($con, "UPDATE user_login_det SET uname=?, mobile=? WHERE u_id=?");
        mysqli_stmt_bind_param($update_login_stmt, "ssi", $name, $mobile, $found_u_id);
        mysqli_stmt_execute($update_login_stmt);
        mysqli_stmt_close($update_login_stmt);
        
        $_SESSION['u_id'] = $found_u_id;
        $user_id = $found_u_id;
    }
    mysqli_stmt_close($user_lookup_stmt);

    $checkout_stmt=mysqli_prepare($con,"INSERT INTO checkout (u_id,order_id,mobile,name,lastname,email,address,city,state,country,pincode,status,payment_type,amount,shipping_charge,razorpay_payment_id,razorpay_order_id,conv_charge) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($checkout_stmt,"issssssiiisssddssd",$user_id,$order_id,$mobile,$name,$lastname,$email,$full_address,$city_id,$state_id,$country_id,$pincode,$status,$payment_type,$grandtotal,$shipping_charge,$razorpay_payment_id,$razorpay_order_id,$convenience_charge);
    if(!mysqli_stmt_execute($checkout_stmt)){
        mysqli_rollback($con);
        mysqli_stmt_close($checkout_stmt);
        checkout_json_response(['status'=>0,'message'=>'Unable to create checkout order.']);
    }
    $check_id=mysqli_insert_id($con);
    mysqli_stmt_close($checkout_stmt);

    $item_stmt = mysqli_prepare($con, "INSERT INTO order_item (check_id,v_id,color,size,quantity,base_price,title) VALUES (?,?,?,?,?,?,?)");
    foreach ($cart_summary['items'] as $item) {
        $v_id = (int)$item['v_id'];
        $color = $item['color'];
        $size = $item['size'];
        $quantity = (int)$item['quantity'];
        $base_price = (float)$item['edsellrate'];
        $title = $item['saledesp'];
        mysqli_stmt_bind_param($item_stmt, "iissids", $check_id, $v_id, $color, $size, $quantity, $base_price, $title);
        if (!mysqli_stmt_execute($item_stmt)) {
            mysqli_rollback($con);
            mysqli_stmt_close($item_stmt);
            checkout_json_response(['status' => 0, 'message' => 'Unable to save order items.']);
        }
    }
    mysqli_stmt_close($item_stmt);
    mysqli_commit($con);

    // Call Razorpay API to generate the actual order_id
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'amount' => round($grandtotal * 100),
        'currency' => 'INR',
        'receipt' => 'rcptid_' . $check_id
    ]));
    curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':' . $apiSecret);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $rp_response = curl_exec($ch);
    curl_close($ch);

    $order_data = json_decode($rp_response, true);
    if (isset($order_data['id']) && $order_data['id'] !== '') {
        $razorpay_order_id = $order_data['id'];
        mysqli_query($con, "UPDATE checkout SET razorpay_order_id='$razorpay_order_id' WHERE check_id=$check_id");
    }

    checkout_json_response([
        'status' => 1,
        'check_id' => $check_id,
        'amount' => round($grandtotal * 100),
        'display_amount' => number_format($grandtotal, 2),
        'razorpay_key' => $apiKey,
        'name' => $name,
        'email' => $email,
        'mobile' => $mobile,
        'razorpay_order_id' => $razorpay_order_id
    ]);
}

if (isset($_POST['ajax_finalize_checkout'])) 
{
    if (empty($_SESSION['u_id'])) {
        checkout_json_response(['status' => 0, 'message' => 'Please login before completing payment.']);
    }

    $user_id = (int)$_SESSION['u_id'];
    $check_id = (int)($_POST['check_id'] ?? 0);
    $razorpay_payment_id = trim($_POST['razorpay_payment_id'] ?? '');
    $razorpay_order_id = trim($_POST['razorpay_order_id'] ?? '');

    if ($check_id <= 0 || $razorpay_payment_id === '') {
        checkout_json_response(['status' => 0, 'message' => 'Invalid payment response.']);
    }

    $checkout_stmt = mysqli_prepare($con, "SELECT * FROM checkout WHERE check_id=? AND u_id=? LIMIT 1");
    mysqli_stmt_bind_param($checkout_stmt, "ii", $check_id, $user_id);
    mysqli_stmt_execute($checkout_stmt);
    $checkout_result = mysqli_stmt_get_result($checkout_stmt);
    if (!$checkout = mysqli_fetch_assoc($checkout_result)) {
        checkout_json_response(['status' => 0, 'message' => 'Checkout order not found.']);
    }
    mysqli_stmt_close($checkout_stmt);

    if (!empty($checkout['related_billbook'])) {
        checkout_json_response(['status' => 1, 'message' => 'Order already completed.']);
    }

    $user_stmt = mysqli_prepare($con, "SELECT * FROM users WHERE u_id=? LIMIT 1");
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    $user_result = mysqli_stmt_get_result($user_stmt);
    $user = mysqli_fetch_assoc($user_result);
    mysqli_stmt_close($user_stmt);

    $party = checkout_user_ledger_id($con, $user ?: ['name' => $checkout['name'], 'mobile' => $checkout['mobile'], 'email' => $checkout['email'], 'address' => $checkout['address']]);
    $paidby = checkout_paidby_ledger($con);
    [$sale_id, $invno] = checkout_generate_invoice($con);
    $invdate = date('Y-m-d');
    $rid = "S" . $sale_id;
    $amount = (float)$checkout['amount'];
    $shipping_charge = (float)$checkout['shipping_charge'];
    $order_subtotal = 0;
    $order_subtotal_result = mysqli_query($con, "SELECT SUM(quantity * base_price) AS subtotal FROM order_item WHERE check_id='$check_id'");
    if ($order_subtotal_row = mysqli_fetch_assoc($order_subtotal_result)) {
        $order_subtotal = (float)$order_subtotal_row['subtotal'];
    }
    $other = max(0, $amount - $order_subtotal - $shipping_charge);
    $roundoff = 0;
    $taxtot = 0;
    $taxtype = '';

    $order_items = mysqli_query($con, "SELECT * FROM order_item WHERE check_id='$check_id'");
    while ($item = mysqli_fetch_assoc($order_items)) {
        $v_id = (int)$item['v_id'];
        $qty = (float)$item['quantity'];
        $rate = (float)$item['base_price'];
        mysqli_query($con, "INSERT INTO bill_items SET sale_id='$sale_id', v_id='$v_id', qty='$qty', rate='$rate', dis='0', gst='0', mrp='$rate', distype='P'");
        mysqli_query($con, "UPDATE variant SET stock=stock-$qty, webstock=webstock-$qty WHERE v_id='$v_id'");
    }

    mysqli_query($con, "INSERT INTO billbook SET sale_id='$sale_id', party='$party', invno='$invno', invdate='$invdate', paidby='$paidby', roundoff='$roundoff', amount='$amount', chequeno='" . mysqli_real_escape_string($con, $checkout['mobile']) . "', relatedwith='$rid', spdis='0', freight='$shipping_charge', remark='Website Order #$check_id', other='$other', taxtype='$taxtype', emp_id='0', comm='0', oname='Convenience Charges', transport='0'");

    $tid = checkout_next_id($con, 'transaction', 'trans_id');
    mysqli_query($con, "INSERT INTO transaction SET trans_id='$tid', tdate='$invdate', ledger_id='$party', amount='$amount', particulars='Sales. Inv. No. :$invno', type='Dr.', relatedto='$rid'");
    $tid++;
    mysqli_query($con, "INSERT INTO transaction SET trans_id='$tid', tdate='$invdate', ledger_id='$paidby', amount='$amount', particulars='Sales. Inv. No. :$invno', type='Dr.', relatedto='$rid'");
    $tid++;
    mysqli_query($con, "INSERT INTO transaction SET trans_id='$tid', tdate='$invdate', ledger_id='$party', amount='$amount', particulars='Amount Paid. Inv. No. :$invno', type='Cr.', relatedto='$rid'");
    if ($shipping_charge > 0) {
        $tid++;
        mysqli_query($con, "INSERT INTO transaction SET trans_id='$tid', tdate='$invdate', ledger_id='9', amount='$shipping_charge', particulars='Inv. No. :$invno', type='Cr.', relatedto='$rid'");
    }
    if ($other > 0) {
        $tid++;
        mysqli_query($con, "INSERT INTO transaction SET trans_id='$tid', tdate='$invdate', ledger_id='14', amount='$other', particulars='Inv. No. :$invno', type='Cr.', relatedto='$rid'");
    }
    $tid++;
    mysqli_query($con, "INSERT INTO transaction SET trans_id='$tid', tdate='$invdate', ledger_id='2', amount='" . ($amount - $taxtot) . "', particulars='Inv. No. :$invno', type='Dr.', relatedto='$rid'");

    $update_stmt = mysqli_prepare($con, "UPDATE checkout SET status='paid', order_status='PAID', razorpay_payment_id=?, razorpay_order_id=?, related_billbook=? WHERE check_id=? AND u_id=?");
    mysqli_stmt_bind_param($update_stmt, "sssii", $razorpay_payment_id, $razorpay_order_id, $rid, $check_id, $user_id);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);

    mysqli_query($con, "DELETE FROM cart WHERE u_id='$user_id'");

    // Push the paid order into Shiprocket. Failure here does not affect the customer's success response.
    list($sr_city_name, $sr_state_name) = checkout_city_state_names($con, $checkout['city'], $checkout['state']);
    $shiprocket = shiprocket_create_order($con, $check_id, $checkout, $sr_city_name, $sr_state_name);
    if ($shiprocket) {
        $sr_update = mysqli_prepare($con, "UPDATE checkout SET shiprocket_order_id=?, shiprocket_shipment_id=? WHERE check_id=?");
        mysqli_stmt_bind_param($sr_update, "ssi", $shiprocket['shiprocket_order_id'], $shiprocket['shipment_id'], $check_id);
        mysqli_stmt_execute($sr_update);
        mysqli_stmt_close($sr_update);
    }
      $_SESSION['razorpay_order_id'] = $razorpay_order_id;
    checkout_json_response(['status' => 1, 'message' => 'Payment completed.', 'check_id' => $check_id, 'razorpay_order_id' => $razorpay_order_id, 'sale_id' => $sale_id, 'invno' => $invno]);
  
}

checkout_json_response(['status' => 0, 'message' => 'Invalid checkout request.']);