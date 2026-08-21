<?php
include 'connect.php'; // your DB connection

if (isset($_POST['v_id']) && isset($_POST['label_id'])) {
    $v_id = intval($_POST['v_id']);
    $label_id = intval($_POST['label_id']);

    $sql = "UPDATE variant SET label = '$label_id' WHERE v_id = '$v_id'";
    if (mysqli_query($con, $sql)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
