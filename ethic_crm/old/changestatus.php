<?php
    ob_start();
    session_start();
    include_once("connect.php");
    if(isset($_REQUEST['item_id']))
    {
        mysqli_query($con,"update item_details set website='$_REQUEST[status]' where item_id='$_REQUEST[item_id]'");
    }
?>