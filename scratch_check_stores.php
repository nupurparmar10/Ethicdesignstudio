<?php
include("../connect.php");
$res = mysqli_query($con, "DESCRIBE stores");
if (!$res) {
    echo "Error: " . mysqli_error($con);
} else {
    while($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
}
$res = mysqli_query($con, "SELECT * FROM stores LIMIT 2");
if ($res) {
    echo "\n\nSample Data:\n";
    while($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
}
?>
