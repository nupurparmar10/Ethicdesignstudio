<?php
include("../connect.php");

header('Content-Type: application/json');

if (!isset($_GET['shipment_id']) || empty($_GET['shipment_id'])) {
    echo json_encode(['status' => 'error', 'message' => "Shipment ID is required."]);
    exit;
}

$shipment_id = intval($_GET['shipment_id']);
$token = get_valid_shiprocket_token();

if (!$token) {
    echo json_encode(['status' => 'error', 'message' => "Failed to obtain Shiprocket authentication token."]);
    exit;
}

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
} else {
  echo $response;
}
?>
