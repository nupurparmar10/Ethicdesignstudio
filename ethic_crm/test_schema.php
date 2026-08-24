<?php
include 'd:\xampp\htdocs\17-7-26 Ethic New Store Website\ethic_crm\connect.php';
$res = mysqli_query($con, 'SHOW COLUMNS FROM material_type');
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
