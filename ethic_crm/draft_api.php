<?php
session_start();
include_once("connect.php");

header('Content-Type: application/json');

if (!isset($_POST['action']) || !isset($_POST['draft_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing required parameters"]);
    exit;
}

$action = $_POST['action'];
$draft_id = mysqli_real_escape_string($con, $_POST['draft_id']);
$now = date('Y-m-d H:i:s');

// Cleanup expired drafts
mysqli_query($con, "DELETE FROM sale_drafts WHERE expires_at < '$now'");

if ($action == 'heartbeat') {
    $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    
    // Check if draft exists
    $chk = mysqli_query($con, "SELECT draft_id FROM sale_drafts WHERE draft_id = '$draft_id'");
    if (mysqli_num_rows($chk) == 0) {
        mysqli_query($con, "INSERT INTO sale_drafts (draft_id, created_at, expires_at) VALUES ('$draft_id', '$now', '$expires')");
    } else {
        mysqli_query($con, "UPDATE sale_drafts SET expires_at = '$expires' WHERE draft_id = '$draft_id'");
    }
    
    echo json_encode(["status" => "success", "message" => "Heartbeat received"]);
    exit;
}

if ($action == 'add_item') {
    if (!isset($_POST['v_id']) || !isset($_POST['qty'])) {
        echo json_encode(["status" => "error", "message" => "Missing item parameters"]);
        exit;
    }
    
    $v_id = (int)$_POST['v_id'];
    $qty = (int)$_POST['qty'];
    
    // Get actual stock
    $stock_q = mysqli_query($con, "SELECT stock FROM variant WHERE v_id = '$v_id'");
    if (mysqli_num_rows($stock_q) == 0) {
        echo json_encode(["status" => "error", "message" => "Product not found"]);
        exit;
    }
    $stock_row = mysqli_fetch_row($stock_q);
    $actual_stock = (int)$stock_row[0];
    
    // Get reserved stock from OTHER drafts
    $res_q = mysqli_query($con, "SELECT SUM(qty) FROM sale_draft_items 
                                 INNER JOIN sale_drafts ON sale_drafts.draft_id = sale_draft_items.draft_id 
                                 WHERE v_id = '$v_id' AND sale_draft_items.draft_id != '$draft_id' AND expires_at > '$now'");
    $res_row = mysqli_fetch_row($res_q);
    $reserved_stock = (int)$res_row[0];
    
    $available = $actual_stock - $reserved_stock;
    
    if ($qty > $available) {
        echo json_encode(["status" => "error", "message" => "Maximum available quantity is $available. $reserved_stock units are already reserved by another open sale."]);
        exit;
    }
    
    // Create/update draft
    $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    $chk = mysqli_query($con, "SELECT draft_id FROM sale_drafts WHERE draft_id = '$draft_id'");
    if (mysqli_num_rows($chk) == 0) {
        mysqli_query($con, "INSERT INTO sale_drafts (draft_id, created_at, expires_at) VALUES ('$draft_id', '$now', '$expires')");
    } else {
        mysqli_query($con, "UPDATE sale_drafts SET expires_at = '$expires' WHERE draft_id = '$draft_id'");
    }
    
    // Insert/update item in draft
    // Prevent duplicate entries for the same product in a single draft by using INSERT ... ON DUPLICATE KEY UPDATE
    mysqli_query($con, "INSERT INTO sale_draft_items (draft_id, v_id, qty) VALUES ('$draft_id', '$v_id', '$qty') 
                        ON DUPLICATE KEY UPDATE qty = '$qty'");
                        
    echo json_encode(["status" => "success", "message" => "Item reserved successfully", "available" => $available]);
    exit;
}

if ($action == 'remove_item') {
    if (!isset($_POST['v_id'])) {
        echo json_encode(["status" => "error", "message" => "Missing item parameter"]);
        exit;
    }
    
    $v_id = (int)$_POST['v_id'];
    
    mysqli_query($con, "DELETE FROM sale_draft_items WHERE draft_id = '$draft_id' AND v_id = '$v_id'");
    
    echo json_encode(["status" => "success", "message" => "Item reservation released"]);
    exit;
}

if ($action == 'release_all') {
    mysqli_query($con, "DELETE FROM sale_drafts WHERE draft_id = '$draft_id'");
    
    echo json_encode(["status" => "success", "message" => "All reservations released"]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
exit;
?>
