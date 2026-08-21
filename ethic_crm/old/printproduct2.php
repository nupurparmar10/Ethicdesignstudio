<?php
	ob_start();
	session_start();
	include_once("connect.php");

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
			width:1,
			height:30,
			displayValue:false
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
    size: A4;
    margin-top: 13mm;
    margin-bottom: 13mm;
    margin-left: 3mm;
    margin-right: 3mm;
}

*
{
	box-sizing:border-box;
}

body
{
	margin:0;
	padding:0;
	font-family:Calibri,sans-serif;
	background:#fff;
}

.barcode-sheet
{
	width:100%;
	border-collapse:collapse;
	table-layout:fixed;
	page-break-inside:avoid;
}

.barcode-sheet tr
{
	height:21mm;
}

.barcode-sheet td
{
	width:20%;
	height:21mm;
	padding:0mm 2mm;
	text-align:center;
	vertical-align:middle;
	overflow:hidden;
}

.barcode-label
{
	width:34mm;
	max-width:100%;
	font-size:10px;
	line-height:11px;
	margin:0 auto 1mm;
	word-break:break-word;
	white-space:normal;
	max-height:31px;
	overflow:hidden;
}

.barcode-img
{
	display:block;
	margin:0 auto;
	width:34mm;
	max-width:100%;
	height:8mm;
}

.page-break
{
	page-break-after:always;
}

@media print
{
	html,body
	{
		width:210mm;
		height:297mm;
	}

	.barcode-sheet
	{
		margin:0;
	}
}

</style>
</head>

<body onload="printreceipt();" class="table-responsive">

<?php
$count = count($v_ids);
?>

<input type="hidden" name="id" value="<?php echo $count; ?>">

<table border="0" class="barcode-sheet">
<tbody>

<?php

$i   = 0;
$col = 0;
$row = 0;

foreach($v_ids as $index => $v_id)
{
	// Start Row
	if($col == 0)
	{
		echo "<tr>";
	}

	$v = mysqli_fetch_row(mysqli_query($con,
	"SELECT * FROM variant WHERE v_id='$v_id'"));

	$s = mysqli_fetch_row(mysqli_query($con,
	"SELECT * FROM item_details WHERE item_id='$v[1]'"));

	$name  = "i".$i;
	$name1 = "barcode".$i;

	if($v[2] != "")
{
    // if long text then remove <br>
    if(strlen($s[5]) > 25)
    {
        $value =
        "$s[1]-$s[5] $v[3]-$v[2]<br>
        M.R.P $v[5]/-";
    }
    else
    {
        $value =
        "$s[1]-$s[5]<br>
        $v[3]-$v[2]<br>
        M.R.P $v[5]/-";
    }

    $barcode = "$s[1]-$v[3]-$v[2]";
}
else
{
    if(strlen($s[5]) > 25)
    {
        $value =
        "$s[1]-$s[5] $v[3]<br>
        M.R.P $v[5]/-";
    }
    else
    {
        $value =
        "$s[1]-$s[5]<br>
        $v[3]<br>
        M.R.P $v[5]/-";
    }

    $barcode = "$s[1]-$v[3]";
}
?>

<td>

	<input
	type="hidden"
	name="<?php echo $name; ?>"
	value="<?php echo $barcode; ?>">

	<p class="barcode-label">
		<?php echo $value; ?>
	</p>

	<img
	id="<?php echo $name1; ?>"
	class="barcode-img" />

</td>

<?php

	$i++;
	$col++;

	// Complete row after 5 columns
	if($col == 5)
	{
		echo "</tr>";

		$col = 0;
		$row++;

		// After 13 rows create next page
		if($row == 13 && $index < count($v_ids)-1)
		{
			echo "</tbody></table>";

			echo '<div class="page-break"></div>';

			echo '<table border="0" class="barcode-sheet">';
			echo '<tbody>';

			$row = 0;
		}
	}
}

// Fill remaining empty columns
if($col != 0)
{
	while($col < 5)
	{
		echo "<td width='20%'></td>";
		$col++;
	}

	echo "</tr>";
}
?>

</tbody>
</table>

</body>
</html>
