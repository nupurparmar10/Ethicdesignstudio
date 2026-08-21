<?php
ob_start();
session_start();
include_once("connect.php");
if(isset($_REQUEST['weight']))
{
    $weight=$_REQUEST['weight'];
    $delivery_pincode=$_REQUEST['delivery_pincode'];
    $pickup_pincode='380007';
    $token=$_SESSION['shiprocket_token'];
    $curl = curl_init();
    $url = "https://apiv2.shiprocket.in/v1/external/courier/serviceability/?pickup_postcode=".$pickup_pincode."&delivery_postcode=".$delivery_pincode."&weight=".$weight."&cod=0";

    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $token"
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $result = json_decode($response, true);

    if(isset($result['data']['available_courier_companies'][0]))
    {
        $courier = $result['data']['available_courier_companies'][0];

        echo json_encode([
            "status" => 1,
            "shipping_cost" => $courier['rate'],
            "courier_name" => $courier['courier_name'],
            "delivery_days" => $courier['estimated_delivery_days']
        ]);
    }
    else
    {
        echo json_encode([
            "status" => 0
        ]);
    }
}
?>