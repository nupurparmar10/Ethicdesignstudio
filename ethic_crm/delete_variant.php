<?php
header('Content-Type: application/json');
ob_start();
session_start();
include_once("connect.php");

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$v_id = isset($_POST['v_id']) ? (int)$_POST['v_id'] : 0;

if ($v_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid variant ID.']);
    exit;
}

// Check variant exists and get item_id
$variant_sql = "SELECT item_id FROM variant WHERE v_id = $v_id";
$variant_res = $con->query($variant_sql);

if (!$variant_res || $variant_res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Variant not found.']);
    exit;
}

$row = $variant_res->fetch_assoc();
$item_id = (int)$row['item_id'];

// Delete associated images from disk
$pic_sql = "SELECT pic FROM variant_pic WHERE v_id = $v_id";
$pic_res = $con->query($pic_sql);
if ($pic_res && $pic_res->num_rows > 0) {
    while ($pic_row = $pic_res->fetch_assoc()) {
        $pic_path = $pic_row['pic'];
        if (file_exists($pic_path)) {
            unlink($pic_path);
        }
    }
}

// Delete variant pics and the variant itself
$con->query("DELETE FROM variant_pic WHERE v_id = $v_id");
$con->query("DELETE FROM variant WHERE v_id = $v_id");

// If no more variants remain for this item, delete the item details too
$check_sql = "SELECT COUNT(*) AS cnt FROM variant WHERE item_id = $item_id";
$check_res = $con->query($check_sql);
$check_row = $check_res->fetch_assoc();
if ((int)$check_row['cnt'] === 0) {
    $con->query("DELETE FROM item_details WHERE item_id = $item_id");
}

ob_end_clean();
echo json_encode(['success' => true, 'message' => 'Variant deleted successfully!']);
exit;
