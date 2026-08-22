<?php
// $con = mysqli_connect("localhost", "u622759878_ethicstore", "@Rinku2009", "u622759878_ethicstore");
$con = mysqli_connect("localhost", "root", "", "u622759878_ethicstore");
mysqli_set_charset($con, 'utf8');
date_default_timezone_set("Asia/Kolkata");

$menu1 = $menu2 = $menu3 = $menu4 = $menu5 = $menu6 = $menu7 = $menu8 = $menu9 = $menu10 = $menu11 = $menu12 = $menu13 = $menu14 = $menu15 = "";

$smenu1 = $smenu2 = $smenu3 = $smenu4 = $smenu5 = $smenu6 = $smenu7 = $smenu8 = $smenu9 = $smenu10 = $smenu11 = $smenu12 = $smenu13 = $smenu14 = $smenu15 = "";

$ssmenu1 = $ssmenu2 = $ssmenu3 = $ssmenu4 = $ssmenu5 = $ssmenu6 = $ssmenu7 = $ssmenu8 = $ssmenu9 = $ssmenu10 = $ssmenu11 = $ssmenu12 = $ssmenu13 = $sssmenu13 = $menu16 = $smenu16 = "";

if (!defined('BC_OFFSET')) {
    define('BC_OFFSET',     '100000000000'); 
    define('BC_RANGE',      '900000000000'); 
    define('BC_MULTIPLIER', '333667');        
    define('BC_INVERSE',    '702999997003');  
}

if (!function_exists('encryptId')) {
    function encryptId($v_id)
    {
        $enc = bcmod(bcmul((string)$v_id, BC_MULTIPLIER), BC_RANGE);
        $enc = bcadd($enc, BC_OFFSET);
        return str_pad($enc, 12, "0", STR_PAD_LEFT);
    }
}
?>
