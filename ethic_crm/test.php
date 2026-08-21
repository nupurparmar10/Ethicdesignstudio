<?php
include 'connect.php';
$res = mysqli_query($con, 'DESCRIBE testimonial');
while($r=mysqli_fetch_assoc($res)){
    print_r($r);
}
?>
