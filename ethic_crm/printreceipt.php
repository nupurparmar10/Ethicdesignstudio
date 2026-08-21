<?php
ob_start();
session_start();
include_once("connect.php");
$msg="";
if(!isset($_REQUEST['rec_id']))
{
	header("Location: viewreceipt.php"); die;
}
function no_to_words($no)
{
    $words = array('0'=> '' ,'1'=> 'One' ,'2'=> 'Two' ,'3' => 'Three','4' => 'Four','5' => 'Five','6' => 'Six','7' => 'Seven','8' => 'Eight','9' => 'Nine','10' => 'Ten','11' => 'Eleven','12' => 'Twelve','13' => 'Thirteen','14' => 'Fouteen','15' => 'Fifteen','16' => 'Sixteen','17' => 'Seventeen','18' => 'Eighteen','19' => 'Nineteen','20' => 'Twenty','30' => 'Thirty','40' => 'Fourty','50' => 'Fifty','60' => 'Sixty','70' => 'Seventy','80' => 'Eighty','90' => 'Ninty','100' => 'Hundred &','1000' => 'Thousand','100000' => 'Lakh','10000000' => 'Crore');
    if($no == 0)
        return ' ';
    else {
		$no = round ($no, 0);
	$novalue='';
	$highno=$no;
	$remainno=0;
	$value=100;
	$value1=1000;       
            while($no>=100)    {
                if(($value <= $no) &&($no  < $value1))    {
                $novalue=$words["$value"];
                $highno = (int)($no/$value);
                $remainno = $no % $value;
                break;
                }
                $value= $value1;
                $value1 = $value * 100;
            }       
          if(array_key_exists("$highno",$words))
              return $words["$highno"]." ".$novalue." ".no_to_words($remainno);
          else {
             $unit=$highno%10;
             $ten =(int)($highno/10)*10;            
             return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".no_to_words($remainno);
           }
    }
}
$rec1=mysqli_query($con,"select * from receipt where rec_id='$_REQUEST[rec_id]'");
			$rec=mysqli_fetch_row($rec1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $rec[7]; ?></title>                
<link rel="icon" href="logo3.png" type="image/x-icon" />
<script type="text/javascript">
function printreceipt()
  {
	window.print()
  }
</script>
</head>
<body onload="printreceipt();">
		<?php
			
			$party=mysqli_query($con,"select name from ledger_accounts where ledger_id=$rec[2]");
			$p1=mysqli_fetch_row($party);
		?>
		<div style="font-size:14px;">
		<table align="center" width="100%" style="font-size:14px;">
			<tr>
				<td><h4 align="center"><U>RECEIPT</U></h4></td>
			</tr>
		</table>
		<table style="border-collapse:collapse; min-height:350px;" align="center" width="100%" height="250px">
				<tr height='100px'>
					<td colspan="2" align="center">
						<img src="assets/header1.png" width="100%" height="100"/>
					</td>
				</tr>
				<tr style='border-top:2px solid black;'><td colspan='2' height='5px'></td></tr>
				<tr height='5px'>
					<td><b>Receipt No. : </b> <?php echo $rec[7]; ?></td>
					<td align="right"><strong>Date : </strong><?php $date= DateTime::createFromFormat('Y-m-d', $rec[1]); echo $date->format('d-m-Y'); ?></td>
				</tr>
				<tr height='5px'>
					<td><b>Party Name : </b><?php echo $p1[0]; ?></td>		
					<td align="right"><b>Paid By : </b><?php
					$l1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$rec[4]'");
					$l=mysqli_fetch_row($l1);
					echo $l[0]; ?></td>			
				</tr>
				<tr height='5px'>
					<td><b>Amount Paid : </b><?php echo $rec[3]." &#x20B9;"; ?></td>
					<td>
					<?php
						if($rec[5]!=0)
						{
					?>
						<b>Cheque No. : </b><?php echo $rec[5]; ?>
					</tr>
					<?php
						}
					?>
					</td>
				</tr>
				<tr height='5px'>
					<td colspan='2'><b>(In Words) : </b><?php echo "INR ".no_to_words($rec[3])." Only"; ?></td>
				</tr>
				<tr><td colspan='2' ></td></tr>
				<tr style='border-bottom:2px solid black;'><td colspan='2' height='5px'></td></tr>
				<tr height='5px'>
					<td colspan="2" align="right"><div style="font-size:13px;"><strong>For Ethic Design Studio</strong></div></td>
				</tr>
				<tr > 
					<td colspan="2" height='80px' align="right">&nbsp;</td>
				</tr>
				<tr style="font-size:12px;" height='5px'>
					<td valign="bottom"><b>Customer's Signature</b></td>
					<td align="right" valign="bottom"><b>Authorised Signatory</b></td>					
				</tr>
      </table>   			
</body>
</html>