<?php
	ob_start();
	session_start();
	include_once("connect.php");
if(!defined('BC_OFFSET')) define('BC_OFFSET','100000000000');
if(!defined('BC_RANGE')) define('BC_RANGE','900000000000');
if(!defined('BC_MULTIPLIER')) define('BC_MULTIPLIER','333667');
if(!defined('BC_INVERSE')) define('BC_INVERSE','702999997003');

	function encryptId($v_id)
	{
		$enc = bcmod(bcmul((string)$v_id, BC_MULTIPLIER), BC_RANGE);
		$enc = bcadd($enc, BC_OFFSET);
		return str_pad($enc, 12, "0", STR_PAD_LEFT);
	}

	function decryptId($code)
	{
		$temp = bcmod(bcsub((string)$code, BC_OFFSET), BC_RANGE);
		$dec  = bcmod(bcmul($temp, BC_INVERSE), BC_RANGE);
		return (int)$dec;
	}

	$msg="";

	if(!isset($_REQUEST['v_id']))
	{
		header("Location: viewproduct1.php");
		die;
	}

	if(isset($_POST['v_id']))
	{
		$v_ids = array();
		foreach($_POST['v_id'] as $v_id)
		{
			$v_id = (int)$v_id;
			$stock = isset($_POST['stock'][$v_id]) ? (int)$_POST['stock'][$v_id] : 1;

			for($j = 0; $j < $stock; $j++)
			{
				$v_ids[] = $v_id;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Ethic Design Studio</title>

<link rel="icon" href="logo3.png" type="image/x-icon" />

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/JsBarcode.all.min.js"></script>

<script>
$(document).ready(function()
{
	var count = $('input:hidden[name=id]').val();

	for(i=0;i<count*1;i++)
	{
		var text1 = $('input:hidden[name=i'+i+']').val();

		$("#barcode"+i).JsBarcode(text1,{
			// width:1,
			// height:30,
			// displayValue:false
			width:2,
			height:30,
			displayValue:false,
			margin:0,
			marginTop:2,
			marginBottom:2,
			marginLeft:0,
			marginRight:0
		});
	}
});

function printreceipt()
{
	window.print();
}
</script>

<style>

/* @page
{
	size:A4 portrait;
	margin:5mm;
} */

@page {
	size: A4 portrait;
	margin: 0px;
}
@media print {
	#new { page-break-before:always;}
}
html,
body {
    width: 210mm;
    height: 297mm;
    margin: 0 !important;
    padding: 0 !important;
}

body {
    position: relative;
}

.bg-page {
    width: 210mm;
    height: 297mm;
    margin: 0 !important;
    padding: 0 !important;

    background-image: url('bg1.jpeg');
    background-repeat: no-repeat;
    background-position: 0 0;
    background-size: 210mm 297mm;
}
</style>
</head>

<body>
	<div class="bg-page" >

	</div>
</body>
</html>
