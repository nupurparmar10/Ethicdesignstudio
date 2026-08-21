<?php
include("../connect.php");
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!isset($_POST['shipment_id']) || empty($_POST['shipment_id'])) {
    echo json_encode(['status' => 'error', 'message' => "Shipment ID is required."]);
    exit;
}

$shipment_id = mysqli_real_escape_string($con, trim($_POST['shipment_id']));

// Fetch order details from checkout table
$sql = "SELECT * FROM checkout WHERE shiprocket_shipment_id = '$shipment_id' LIMIT 1";
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) == 0) {
    echo json_encode(['status' => 'error', 'message' => "Order not found."]);
    exit;
}
$order = mysqli_fetch_assoc($result);
$customer_name = $order['name'] . ' ' . $order['lastname'];
$customer_email = $order['email'];
$order_id = $order['order_id'];

// Get Shiprocket token
$token = get_valid_shiprocket_token();
if (!$token) {
    echo json_encode(['status' => 'error', 'message' => "Failed to obtain Shiprocket authentication token."]);
    exit;
}

// Fetch tracking data from Shiprocket
$url = "https://apiv2.shiprocket.in/v1/external/courier/track/shipment/" . $shipment_id;
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer " . $token,
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(['status' => 'error', 'message' => "cURL Error #:" . $err]);
    exit;
}

$track_res = json_decode($response, true);
$trackData = isset($track_res['tracking_data']) ? $track_res['tracking_data'] : [];

if (empty($trackData) || empty($trackData['track_url'])) {
    echo json_encode(['status' => 'error', 'message' => "No valid tracking URL found."]);
    exit;
}

$trackInfo = (isset($trackData['shipment_track']) && count($trackData['shipment_track']) > 0) ? $trackData['shipment_track'][0] : [];
$track_url = $trackData['track_url'];

$awb_code = isset($trackInfo['awb_code']) ? $trackInfo['awb_code'] : 'N/A';
$courier_name = isset($trackInfo['courier_name']) ? $trackInfo['courier_name'] : 'N/A';
$current_status = isset($trackInfo['current_status']) ? $trackInfo['current_status'] : 'N/A';
$edd = (isset($trackInfo['expected_date']) && $trackInfo['expected_date']) ? $trackInfo['expected_date'] : (isset($trackInfo['edd']) ? $trackInfo['edd'] : 'N/A');

// Compose Email
$subject = "Your Order is on the way! Track your Shipment - " . $order_id;

$htmlBody = "
<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
    <div style='background-color: #007bff; color: white; padding: 20px; text-align: center;'>
        <h2>Shipment Tracking Update</h2>
    </div>
    <div style='padding: 20px;'>
        <p>Dear <strong>{$customer_name}</strong>,</p>
        <p>Good news! Your order <strong>#{$order_id}</strong> is on its way. Here are your tracking details:</p>
        
        <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
            <tr>
                <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Current Status:</strong></td>
                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$current_status}</td>
            </tr>
            <tr>
                <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>AWB / Tracking ID:</strong></td>
                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$awb_code}</td>
            </tr>
            <tr>
                <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Courier Partner:</strong></td>
                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$courier_name}</td>
            </tr>
            <tr>
                <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Estimated Delivery:</strong></td>
                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$edd}</td>
            </tr>
        </table>
        
        <div style='text-align: center; margin: 30px 0;'>
            <a href='{$track_url}' style='background-color: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Track Your Order</a>
        </div>
        
        <p>If you have any questions or concerns, please don't hesitate to contact us.</p>
        
        <p>Thank you for shopping with us!</p>
        
        <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
        <p style='font-size: 12px; color: #777;'>
            <strong>Ethic Studio</strong><br>
            Email: Ethicdesignstudiotech@gmail.com
        </p>
    </div>
</div>
";

// Send Email via PHPMailer
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
    $mail->addAddress($customer_email, $customer_name);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $htmlBody;

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Tracking email sent successfully to ' . $customer_email]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
}
?>
