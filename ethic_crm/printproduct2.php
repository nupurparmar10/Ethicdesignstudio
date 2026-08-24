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

:root
{
    /* Page settings */
    --page-width: 210mm;
    --page-height: 297mm;
    
    /* Grid starting position (Distance from top/left of page to the FIRST box) */
    --grid-offset-x: 3.0mm;
    --grid-offset-y: 9.0mm; 
    
    /* Individual box size (Measure one box on bg.jpeg) */
    --box-width: 38.0mm;
    --box-height: 20.0mm;
    
    /* Gaps between boxes (Measure the white space between boxes) */
    --col-gap: 3.5mm;
    --row-gap: 5.0mm; /* THIS CONTROLS THE SPACE BETWEEN EACH ROW (TR) */
    
    /* Inside box contents */
    --barcode-width: 31mm;
    --barcode-height: 8mm;
}

*
{
	box-sizing:border-box;
}

html
{
	width:var(--page-width);
	min-height:var(--page-height);
	margin:0;
	padding:0;
}

body
{
	width:var(--page-width);
	min-height:var(--page-height);
	margin:0;
	padding:0;
	font-family:Calibri,sans-serif;
	background:#fff;
}

.barcode-page
{
	width:var(--page-width);
	height:var(--page-height);
	margin:0 auto; 
	padding:0;
	overflow:hidden;
	background-image:url('bg.jpeg');
	background-size:var(--page-width) var(--page-height);
	background-repeat:no-repeat;
	background-position:top left;
	position:relative;
	page-break-inside:avoid;
	break-inside:avoid;
}

.barcode-sheet
{
	border-collapse:separate;
	border-spacing: var(--col-gap) var(--row-gap);
	position:absolute;
	top: calc(var(--grid-offset-y) - var(--row-gap));
	left: calc(var(--grid-offset-x) - var(--col-gap));
	margin: 0;
	table-layout:fixed;
	page-break-inside:avoid;
	break-inside:avoid;
}

.barcode-sheet tr
{
	height:var(--box-height);
	page-break-inside:avoid;
	break-inside:avoid;
}

.barcode-sheet td
{
	width:var(--box-width);
	height:var(--box-height);
	max-width:var(--box-width);
	max-height:var(--box-height);
	padding:0;
	text-align:center;
	vertical-align:middle;
	overflow:hidden;
	page-break-inside:avoid;
	break-inside:avoid;
}

.barcode-cell
{
	width:var(--box-width);
	max-width:100%;
	height:var(--box-height);
	max-height:var(--box-height);
	overflow:hidden;
	display:flex;
	flex-direction:column;
	align-items:center;
	justify-content:center;
	margin:0 auto;
	padding:1mm;
	box-sizing:border-box;
	page-break-inside:avoid;
	break-inside:avoid;
}



.barcode-label
{
	width:100%;
	max-width:100%;
	font-size:10px;
	line-height:11px;
	margin:0 0 0.5mm;
	word-break:break-word;
	white-space:normal;
	max-height:8.8mm;
	overflow:hidden;
	flex-shrink:1;
}

.barcode-img
{
	width:var(--barcode-width);
	max-width:100%;
	height:var(--barcode-height);
	flex-shrink:0;
	display:block;
	margin:0 auto;
}

.page-break
{
	page-break-after:always;
	break-after:page;
	height:0;
	margin:0;
	padding:0;
	overflow:hidden;
}


</style>
</head>

<body onload="printreceipt();" class="table-responsive">

<?php
$count = count($v_ids);
?>

<input type="hidden" name="id" value="<?php echo $count; ?>">

<div class="barcode-page">
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

	$v = mysqli_fetch_array(mysqli_query($con,
	"SELECT * FROM variant WHERE v_id='$v_id'"));

	$s = mysqli_fetch_row(mysqli_query($con,
	"SELECT * FROM item_details WHERE item_id='$v[1]'"));
	
	if($s[10]!=0)
    {
    	$ps=mysqli_fetch_row(mysqli_query($con,
    	"SELECT * FROM pro_subcategory WHERE s_id='$s[10]'"));
    }
    else
    {
        $ps[2]='';
    }

	$name  = "i".$i;
	$name1 = "barcode".$i;

	if($v[2] != "")
    {
        // if long text then remove <br>
        if(strlen($ps[2]) > 25)
        {
            $value =
            "$s[1]-$ps[2] $v[3]-$v[2]<br>
            M.R.P $v[5]/-";
        }
        else
        {
            $value =
            "$s[1]-$ps[2]<br>
            $v[3]-$v[2]<br>
            M.R.P $v[5]/-";
        }
    
        $barcode = encryptId($v['v_id']);
    }
    else
    {
        if(strlen($ps[2]) > 25)
        {
            $value =
            "$s[1]-$ps[2] $v[3]<br>
            M.R.P $v[5]/-";
        }
        else
        {
            $value =
            "$s[1]-$ps[2]<br>
            $v[3]<br>
            M.R.P $v[5]/-";
        }
    
        $barcode = encryptId($v['v_id']);
    }
?>

<td>

	<input
	type="hidden"
	name="<?php echo $name; ?>"
	value="<?php echo $barcode; ?>">

	<div class="barcode-cell">

		<p class="barcode-label">
			<?php echo $value; ?>
		</p>

		<img
		id="<?php echo $name1; ?>"
		class="barcode-img" />

	</div>

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
	}

	// Break page after exactly 64 barcodes
	if($i % 65 == 0 && $index < count($v_ids)-1)
	{
		// Pad remaining columns if we break mid-row
		if($col != 0)
		{
			while($col < 5)
			{
				echo "<td width='20%'></td>";
				$col++;
			}
			echo "</tr>";
			$col = 0;
			$row = 0;
		}

		echo "</tbody></table>";
		echo "</div>";

		echo '<div class="page-break"></div>';

		echo '<div class="barcode-page">';
		echo '<table border="0" class="barcode-sheet">';
		echo '<tbody>';

		$row = 0;
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
</div>

</body>
</html>
