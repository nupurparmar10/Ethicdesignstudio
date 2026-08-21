<?php
	ob_start();
	 session_start();
     include_once("connect.php");
	 $msg="";
	 if(!isset($_REQUEST['item_id']))
	 {
		 header("Location: viewproduct1.php"); die;
	 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 <title>Ethic Design Studio</title>         
<link rel="icon" href="logo3.png" type="image/x-icon" />
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/JsBarcode.all.min.js"></script>

<script>
$(document).ready(function() {                       

	var count = $('input:hidden[name=id]').val();
	
	for(i=0;i<count*1;i++)
	{
		var text1 = $('input:hidden[name=i'+i+']').val();
		$("#barcode"+i).JsBarcode(text1,{
			 width:1,
			  height:30,
			  displayValue: false
		});
	}
	
});
</script>	
<style>
    @page {
    size: A4;
    margin-top: 13mm;
    margin-bottom: 13mm;
    margin-left: 3mm;
    margin-right: 3mm;
}
</style>
</head>
<body>
		<?php
			$p=mysqli_fetch_row(mysqli_query($con,"select count(*) from variant where item_id='$_REQUEST[item_id]'"));
			$count=$p[0];
		?>	
		<input type="hidden" name="id" value="<?php echo "$count"; ?>">
		<table width='100%' cellspacing='3' cellpadding='0'>
			<?php
				$item=$_REQUEST['item_id'];
				$s=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$item'"));
				$v1=mysqli_query($con,"select * from variant where item_id='$item'");
				$i=0;
				while($v=mysqli_fetch_row($v1))
				{
			?>
			<tr>
				<?php
					$name="i".$i;
					$div="div".$i;
					$name1="barcode".$i;
					if($v[2]!="")
					$value="$s[1]-$s[5]-$v[3]-$v[2]";
					else
					$value="$s[1]-$s[5]-$v[3]";
				?>
				<td width='33%' id="<?php echo $div; ?>"><center>
					<input type="hidden" name="<?php echo $name; ?>" value="<?php echo "$s[1]-$v[3]-$v[2]"; ?>">
					<p><?php echo $value;?></p>
					<img id="<?php echo $name1; ?>" style="margin-top:-18px;"/>
					</center></td>
				<?php
					$i++;
					if($v=mysqli_fetch_row($v1))
					{
				?>
				<?php
					$name="i".$i;
					$div="div".$i;
					$name1="barcode".$i;
					if($v[2]!="")
					$value="$s[1]-$s[5]-$v[3]-$v[2]";
					else
					$value="$s[1]-$s[5]-$v[3]";
				?>
				<td width='33%' id="<?php echo $div; ?>"><center>
					<input type="hidden" name="<?php echo $name; ?>" value="<?php echo "$s[1]-$v[3]-$v[2]"; ?>">
					<p><?php echo $value;?></p>
					<img id="<?php echo $name1; ?>" style="margin-top:-18px;"/>
					</center></td>
				<?php
					}
					$i++;
					if($v=mysqli_fetch_row($v1))
					{
				?>
				<?php
					$name="i".$i;
					$div="div".$i;
					$name1="barcode".$i;
					if($v[2]!="")
					$value="$s[1]-$s[5]-$v[3]-$v[2]";
					else
					$value="$s[1]-$s[5]-$v[3]";
				?>
				<td width='33%' id="<?php echo $div; ?>"><center>
					<input type="hidden" name="<?php echo $name; ?>" value="<?php echo "$s[1]-$v[3]-$v[2]"; ?>">
					<p><?php echo $value;?></p>
					<img id="<?php echo $name1; ?>" style="margin-top:-18px;"/>
					</center></td>
				<?php
					}
				?>
			</tr>
			<?php
					$i++;
				}
			?>
			
		</table>
</body>
</html>