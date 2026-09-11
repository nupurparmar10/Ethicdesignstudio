<?php
	ob_start();
	 session_start();
     include_once("connect.php");
	 $msg="";
	 if(!isset($_REQUEST['sale_id']))
	 {
	 	header("Location: viewsaleretun.php"); die;
	 }
	$accno="bank account no.";
	$bank="BANK NAME";
	$branch="BANK BRANCH";
	$ifsc="IFSCCODE";
	$accname="Ethic Design Studio";
	
	 function no_to_words($no)
{
 $words = array('0'=> '' ,'1'=> 'One' ,'2'=> 'Two' ,'3' => 'Three','4' => 'Four','5' => 'Five','6' => 'Six','7' => 'Seven','8' => 'Eight','9' => 'Nine','10' => 'Ten','11' => 'Eleven','12' => 'Twelve','13' => 'Thirteen','14' => 'Fourteen','15' => 'Fifteen','16' => 'Sixteen','17' => 'Seventeen','18' => 'Eighteen','19' => 'Nineteen','20' => 'Twenty','30' => 'Thirty','40' => 'Fourty','50' => 'Fifty','60' => 'Sixty','70' => 'Seventy','80' => 'Eighty','90' => 'Ninty','100' => 'Hundred','1000' => 'Thousand','100000' => 'Lakh','10000000' => 'Crore');
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
$d1=mysqli_query($con,"select * from billreturn where sale_id='".$_REQUEST['sale_id']."'");
$d=mysqli_fetch_row($d1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style='background:#fff;'>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $d[3]; ?></title>                
<link rel="icon" href="logo3.png" type="image/x-icon" />
<script type="text/javascript">
function printinvoice()
  {
	window.print()
  }
  
</script>
<style type="text/css" media="print">
@page{
	size: portrait;
}
@media print {
    #new {page-break-before: always;}
}
</style>
</head>
<body onload="printinvoice();" style='font-family:calibri;'>
<?php
	$c1=mysqli_query($con,"select count(*) from sr_items where sale_id='$_REQUEST[sale_id]'");
	$c=mysqli_fetch_row($c1);
	$start=0;
	$limit=15;
	$count=ceil($c[0]/15);
	$amt1=0;
	$tot=0;
	$dis=0;
	$sgst=0;
	$cgst=0;
	$j=1;
	$taxableamt28=0;
	$taxableamt18=0;
	$taxableamt12=0;
	$taxableamt5=0;
	$exempted=0;
	
?>
		<?php
			
			$k1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$d[2]");
			$k=mysqli_fetch_row($k1);
			$party1=mysqli_query($con,"select * from ledger_details where ledger_id=$d[2]");	
			if($p1=mysqli_fetch_row($party1)){}
			else
			{
				for($i=0;$i<=5;$i++)
					$p1[$i]="";
			}
			
			$emp1=mysqli_query($con,"select empname from empdet where ledger_id='$d[16]'");
			if($emp=mysqli_fetch_row($emp1)) {} else $emp[0]="";
		?>
		<?php
			for($i=1;$i<$count;$i++)
			{
		?>
		<div style="font-size:14px; background: url(assets/admin.png)  no-repeat center center; padding-top:10px;">		
		<table style="border-collapse:collapse;" align="center" width="100%">
				<tr>
					<td colspan='3' align='center'  style='color:#c50505d6;'>||  श्री पार्श्वनाथाय नमः  ||</td>
				</tr>
				<tr>
					<td colspan='3' align='center' style='color:#c50505d6; font-size:18px; font-weight:bold;'><u>SALE RETURN INVOICE</u></td>
				</tR>
				<tr>
					<td colspan='2' width='65%'><img src="img/VHS.png" width="100%" height="80"/></td>
					<td align='right'><span style='color:#19b5d8; font-weight:bold; font-size:16px;'>Return Invoice No. : <?php echo $d[3]; ?></span><br>
					<b>Date :</b> <?php if($d[1]!="0000-00-00"){
					$date= DateTime::createFromFormat('Y-m-d', $d[1]);
					echo $date->format('M d, Y'); } ?>
					<br>
					<b>Salesman:</b> <?php echo $emp[0]; ?>
					</td>
				</tr>
				<tr>
					<td colspan='3' style='border-top:2px solid #19b5d8;'></td>
				</tr>
				<tr>
					<td colspan='3'>
						<table width='100%' style='line-height:12px;' cellspacing='0'>
							<tr>
								<td width='2%'></td>
								<td valign='top' width='40%'><span style='font-weight:bold; font-size:16px; color:#19b5d8;  font-style:italic;'>ETHIC DESIGNS LLP</span></td>
								<td width='2%'></td>
								<td valign='top' width='30%'><span style='font-weight:bold; font-size:16px; color:#19b5d8; padding-left:15px; font-style:italic;'>Billed To:</span></td>
								<td></td>
							</tr>
							<tr>
								<td><img src='img/home.png' width='15px' height='15px' style='color:#19b5d8;' align='top'></tD>
								<td><b>MAIN BRANCH</b> : 2370/71, Rani No Haziro, Manek Chowk, Ahemdabad - 380001.<br><b>BRANCH(2) :</b> 100, Lavanya Society, Nr. Jivraj Mehta Hospital, vasna, Ahemdabad - 380007.</td>
								<td></td>
								<td><?php echo $k[0]; ?></span></td>
								<td></td>
							</tr>
							<tr>
								<td><img src='img/phone.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td>9824077818, 9825162255, 8980060002</td>
								<td><img src='img/home.png' width='15px' height='15px' style='color:#19b5d8;' align='middle'> </td>
								<td><span style='word-wrap:break-word; white-space:pre-line;'><?php echo $p1[3]; ?></span><br>
									<?php echo "$p1[4], $p1[5]"; ?>, India</td>
								<td></td>
							</tr>
							<tr>
								<td><img src='img/email.png' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td>ethicdesignstudio@gmail.com</td>
								<td><img src='img/phone.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><?php echo $p1[9]; ?></td>
								<td><b>Paid By </b>: <?php if($d[5]=="3") $paidby="Cash"; else if($d[5]=="Credit") $paidby="Credit"; else $paidby='Cheque';
									echo $paidby; ?></td>
							</tr>
							<tr>
								<td><img src='img/web.png' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td>www.ethicdesignstudio.com</td>
								<td><img src='img/person.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><?php echo $p1[1]; ?></td>
								<td><?php if($d[8]!="") echo "<b>Mobile No.</b> : $d[8]"; ?></td>
							</tr>
							<tr>
								<td><img src='img/info.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><b style='color:#19b5d8;'>GSTIN : 24AAJFE0234H1ZV</b></td>
								<td><img src='img/email.png' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><?php echo $p1[11]; ?></td>
								<td></td>
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td><img src='img/info.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><b style='color:#19b5d8;'>GSTIN: <?php echo $p1[6]; ?></b></td>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td colspan='3' style='border-top:2px solid #19b5d8;'>&nbsp;</td></tR>
				<tr>
				<td colspan="3">
				<table style="border-collapse:collapse; border:1px solid grey; font-size:12px;" border='1' align="center" width="100%" height="500px" cellpadding='5px'>
                  <tr height="5px" valign='top' align='center'>
                   <td width="6%"><b>S. No.</b></td>
                    <td width="51%"><b>Description of Goods<br />
                      (Code - Name - Variant)</b></td>
					<td width="6%"><b>HSN</b></td>  
                    <td width="6%"><b>Qty</b></td>
					<td width="6%"><b>Unit</b></td>
                    <td width="6%"><b>MRP</b></td>  
					 <td width="9%"><b>Disc<br>(&#x20B9;)</b></td>
					<td width="9%"><b>Rate<br>(&#x20B9;)</b></td>
					<td width="9%"><b>Tax (%)</b></td>
                    <td width="7%"><b>CGST</b></td>
					<td width="7%" ><b>SGST</b></td>
                    <td width="12%"><b>Amount<br>(&#x20B9;)</b></td>
                  </tr>
                   <?php
					$pro1=mysqli_query($con,"select * from bill_items where sale_id='".$_REQUEST['sale_id']."' LIMIT $start, $limit");
					while($pro=mysqli_fetch_row($pro1))
					{
						echo "<tr style='vertical-align:top;' height='5px'>";
						echo "<td align='center'>$j</td>";
						$v=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$pro[1]'"));
						$list1=mysqli_query($con,"select * from item_details where item_id='$v[1]'");
						$l=mysqli_fetch_row($list1);
						echo "<td>$l[1]-$l[5] $v[2] $v[3]</td>";
						echo "<td align='center'>$l[4]</td>";
						echo "<td align='center'>$pro[2]</td>";
						echo "<td align='center'>$l[6]</td>";
						echo "<td align='center'>".$pro[6]."</td>";		
						if($pro[7]=="P")		
						$d1=$pro[6]*$pro[4]/100;
						else
						$d1=$pro[4]*$pro[2];
						$dis+=$d1;
						$amt=$pro[2]*$pro[3];
						echo "<td align='center'>".$d1."</td>";
						echo "<td align='center'>".$pro[3]."</td>";			
						echo "<td align='center'>".$pro[5]."</td>";
						if($pro[5]==28)
							$taxableamt28+=$amt;
						else if($pro[5]==18)
							$taxableamt18+=$amt;
						else if($pro[5]==12)
							$taxableamt12+=$amt;
						else if($pro[5]==5)
							$taxableamt5+=$amt;
						
						$v1=$amt*$pro[5]/100;
						$amt=$amt+$v1;
						
						echo "<td align='center'>".round($v1/2,2)."</td>";
						echo "<td align='center'>".round($v1/2,2)."</td>";
						
						$sgst+=$v1/2;
						$cgst+=$v1/2;
												
						$tot+=$amt;
						$amt=number_format($amt,2);
						echo "<td align='right'>$amt</td>";
						echo "</tr>";
						$j++;
					}
					$dis1=number_format($dis,2);
					$vat1=number_format($sgst,2);
					$vat2=number_format($cgst,2);
					$vattot=($sgst+$cgst);
				?>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                  </tr>
                </table></td>
				</tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr>
					<td style="font-size:12px;">
						<b>Note:</b><br/>
						1. Warranty as per company rules & conditions.<br>
						2. Goods once sold can’t be returned or exchanged.<br>
						3. Rate should be valid for 7 days only.<br>
						4. Packing & Transportation charges extra.<br>
						5. Payment Should Be 100% Advance On Order<br>
						6. All Subject to Ahemdabad (Gujrat) Jurisdiction
					</td>
					<td rowspan='5' align='left'><img src='img/vhspay.jpg' width='80%' height='140px'/><br> For Payment Scan Here</td>
					<td  style="font-size:12px; font-weight:bold; padding:5px; border:1px solid grey;">
						Bank Details: <?php echo $bank; ?><br>
						A/c Name : <?php echo $accname; ?><br>
						A/c No.: <?php echo $accno; ?><br>
						Branch: <?php echo $branch; ?><br>
						IFSC : <?php echo $ifsc; ?>
					</td>
				</tr>
				<tr><td height="8px">&nbsp;</td></tr>
				<tr>
					<td></td>
					<td align="right"><strong>For ETHIC DESIGNS LLP<br /></strong></td>
				</tr>
				<tr><td height="20px">&nbsp;</td></tr>
				<tr>
					<td style='vertical-align:bottom;'><b>Customer's Signature</b></td>
					<td  align="right"><b>Authorised Signatory</b></td>					
				</tr>
      </table>   	
	  </div>
		<div id="new">
	  </div>	  
	  <?php
	  	$start=$start+$limit;
		}
		?>
		<div style="font-size:14px; background: url(assets/admin.png)  no-repeat center center; padding-top:10px;">		
		<table style="border-collapse:collapse;" align="center" width="100%">
				<tr>
					<td colspan='3' align='center'  style='color:#c50505d6;'>||  श्री पार्श्वनाथाय नमः  ||</td>
				</tr>
				<tr>
					<td colspan='3' align='center' style='color:#c50505d6; font-size:18px; font-weight:bold;'><u>SALE RETURN INVOICE</u></td>
				</tR>
				<tr>
					<td colspan='2' width='65%'><img src="img/VHS.png" width="100%" height="80"/></td>
					<td align='right'><span style='color:#19b5d8; font-weight:bold; font-size:16px;'> Return Invoice No. : <?php echo $d[3]; ?></span><br>
					<b>Date :</b> <?php if($d[1]!="0000-00-00"){
					$date= DateTime::createFromFormat('Y-m-d', $d[1]);
					echo $date->format('M d, Y'); } ?>
					<br>
					<b>Salesman:</b> <?php echo $emp[0]; ?>
					</td>
				</tr>
				<tr>
					<td colspan='3' style='border-top:2px solid #19b5d8;'></td>
				</tr>
				<tr>
					<td colspan='3'>
						<table width='100%' style='line-height:12px;' cellspacing='0'>
							<tr>
								<td width='2%'></td>
								<td valign='top' width='40%'><span style='font-weight:bold; font-size:16px; color:#19b5d8;  font-style:italic;'>ETHIC DESIGNS LLP</span></td>
								<td width='2%'></td>
								<td valign='top' width='30%'><span style='font-weight:bold; font-size:16px; color:#19b5d8; padding-left:15px; font-style:italic;'>Billed To:</span></td>
								<td></td>
							</tr>
							<tr>
								<td><img src='img/home.png' width='15px' height='15px' style='color:#19b5d8;' align='top'></tD>
								<td><b>MAIN BRANCH</b> : 2370/71, Rani No Haziro, Manek Chowk, Ahemdabad - 380001.<br><b>BRANCH(2) :</b> 100, Lavanya Society, Nr. Jivraj Mehta Hospital, vasna, Ahemdabad - 380007.</td>
								<td></td>
								<td><?php echo $k[0]; ?></span></td>
								<td></td>
							</tr>
							<tr>
								<td><img src='img/phone.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td>9824077818, 9825162255, 8980060002</td>
								<td><img src='img/home.png' width='15px' height='15px' style='color:#19b5d8;' align='middle'> </td>
								<td><span style='word-wrap:break-word; white-space:pre-line;'><?php echo $p1[2]; ?></span>
									</td>
								<td></td>
							</tr>
							<tr>
								<td><img src='img/email.png' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td>ethicdesignstudio@gmail.com</td>
								<td><img src='img/phone.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><?php echo $p1[4]; ?></td>
								<td><b>Paid By </b>: <?php if($d[5]=="3") $paidby="Cash"; else if($d[5]=="Credit") $paidby="Credit"; else $paidby='Cheque';
									echo $paidby; ?></td>
							</tr>
							<tr>
								<td><img src='img/web.png' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td>www.ethicdesignstudio.com</td>
								<td><img src='img/person.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><?php echo $p1[1]; ?></td>
								<td><?php if($d[6]!="") echo "<b>Mobile No.</b> : $d[6]"; ?></td>
							</tr>
							<tr>
								<td><img src='img/info.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><b style='color:#19b5d8;'>GSTIN : 24AAJFE0234H1ZV</b></td>
								<td><img src='img/email.png' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><?php echo $p1[5]; ?></td>
								<td></td>
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td><img src='img/info.jpg' width='15px' height='15px' style='color:#19b5d8;' align='middle'></td>
								<td><b style='color:#19b5d8;'>GSTIN: <?php echo $p1[4]; ?></b></td>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td colspan='3' style='border-top:2px solid #19b5d8;'>&nbsp;</td></tR>
				<tr>
				<td colspan="3">
					<table style="border-collapse:collapse; border:1px solid grey; font-size:14px;" border='1' align="center" width="100%" height="500px" cellpadding='5px'>
                  <tr height="5px" valign='top' align='center'>
                   <td width="6%"><b>S. No.</b></td>
                    <td width="51%"><b>Description of Goods<br />
                      (Code - Name - Variant)</b></td>
					<td width="6%"><b>HSN</b></td>  
                    <td width="6%"><b>Qty</b></td>
					<td width="6%"><b>Unit</b></td>
                    <td width="6%"><b>MRP</b></td>  
					<td width="9%"><b>Disc<br>(&#x20B9;)</b></td>
					<td width="9%"><b>Rate<br>(&#x20B9;)</b></td>
					<td width="9%"><b>Tax (%)</b></td>
                    <td width="7%"><b>CGST</b></td>
					<td width="7%" ><b>SGST</b></td>
                    <td width="12%"><b>Amount<br>(&#x20B9;)</b></td>
                  </tr>
                  <?php
					$pro1=mysqli_query($con,"select * from sr_items where sale_id='".$_REQUEST['sale_id']."' LIMIT $start, $limit");
					while($pro=mysqli_fetch_row($pro1))
					{
						echo "<tr style='vertical-align:top;' height='5px'>";
						echo "<td align='center'>$j</td>";
						$v=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$pro[1]'"));
						$list1=mysqli_query($con,"select * from item_details where item_id='$v[1]'");
						$l=mysqli_fetch_row($list1);
						echo "<td>$l[1]-$l[5] $v[2] $v[3]</td>";
						echo "<td align='center'>$l[4]</td>";
						echo "<td align='center'>$pro[2]</td>";
						echo "<td align='center'>$l[6]</td>";
						echo "<td align='center'>".$pro[6]."</td>";	
						if($pro[7]=="P")		
						$d1=$pro[6]*$pro[4]/100;
						else
						$d1=$pro[4]*$pro[2];
						$dis+=$d1;
						$amt=$pro[2]*$pro[3];
						echo "<td align='center'>".$d1."</td>";
						echo "<td align='center'>".$pro[3]."</td>";			
						echo "<td align='center'>".$pro[5]."</td>";
						if($pro[5]==28)
							$taxableamt28+=$amt;
						else if($pro[5]==18)
							$taxableamt18+=$amt;
						else if($pro[5]==12)
							$taxableamt12+=$amt;
						else if($pro[5]==5)
							$taxableamt5+=$amt;
						
						$v1=$amt*$pro[5]/100;
						$amt=$amt+$v1;
						
						echo "<td align='center'>".round($v1/2,2)."</td>";
						echo "<td align='center'>".round($v1/2,2)."</td>";
						
						$sgst+=$v1/2;
						$cgst+=$v1/2;
												
						$tot+=$amt;
						$amt=number_format($amt,2);
						echo "<td align='right'>$amt</td>";
						echo "</tr>";
						$j++;
					}
					$dis1=$dis;
					$vat1=number_format($sgst,2);
					$vat2=number_format($cgst,2);
					$vattot=($sgst+$cgst);
				?>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                  </tr>
                  <tr height="5px">
                    <td colspan='7' rowspan='6' valign='top'>
						<table border='1' style='border-collapse:collapse; font-size:14px;' width='100%'>
							<tr align='center'>
								<td><b>GST %</b></td>
								<td><b>Taxable Amount</b></td>
								<td><b>CGST</b></td>
								<td><b>SGST</b></td>
							</tr>
							<tr align='right'>
								<td align='center'><b>5%</b></td>
								<td><b><?php echo number_format($taxableamt5,2); ?></b></td>
								<td><b><?php echo number_format(($taxableamt5*5/100)/2,2); ?></b></td>
								<td><b><?php echo number_format(($taxableamt5*5/100)/2,2); ?></b></td>
							</tr>
							<tr align='right'>
								<td align='center'><b>12%</b></td>
								<td><b><?php echo number_format($taxableamt12,2); ?></b></td>
								<td><b><?php echo number_format(($taxableamt12*12/100)/2,2); ?></b></td>
								<td><b><?php echo number_format(($taxableamt12*12/100)/2,2); ?></b></td>
							</tr>
							<tr align='right'>
								<td align='center'><b>18%</b></td>
								<td><b><?php echo number_format($taxableamt18,2); ?></b></td>
								<td><b><?php echo number_format(($taxableamt18*18/100)/2,2); ?></b></td>
								<td><b><?php echo number_format(($taxableamt18*18/100)/2,2); ?></b></td>
							</tr>
							<tr align='right'>
								<td align='center'><b>28%</b></td>
								<td><b><?php echo number_format($taxableamt28,2); ?></b></td>
								<td><b><?php echo number_format(($taxableamt28*28/100)/2,2); ?></b></td>
								<td><b><?php echo number_format(($taxableamt28*28/100)/2,2); ?></b></td>
							</tr>
						</table>
					</td>
					<td colspan='4' style='line-height:10px;'><b>TOTAL :</b></td>
					<td align='right'  style='line-height:10px;'><strong>
                      <?php echo number_format($tot,2); ?>
                    </strong></td>
                  </tr>
				  <tr height="1px"  style='line-height:10px;'>
				  	<td colspan='4'><b>SPL DIS :</b></td>
					<td align='right'><strong>
                      <?php $tot=$tot-$d[7]; echo number_format($d[7],2); ?>
                    </strong></td>
				  </tr>
				  <tr height="1px"  style='line-height:10px;'>
					<td colspan='4' ><b>FREIGHT :</b></td>
					<td align='right'><strong>
                      <?php $tot=$tot+$d[9]; echo number_format($d[9],2); ?>
                    </strong></td>
				  </tr>
				  <tr height="1px"  style='line-height:10px;'>
					<td colspan='4' ><b>TRANSPORT :</b></td>
					<td align='right'><strong>
                      <?php $tot=$tot+$d[8]; echo number_format($d[8],2); ?>
                    </strong></td>
				  </tr>
				  <tr height="1px" style='line-height:10px;'>
					<td colspan='4' style="text-transform:uppercase;"><b><?php echo $d[18]; ?> :</b></td>
					<td align='right'><strong>
                      <?php $tot=$tot+$d[10]; echo number_format($d[10],2); ?>
                    </strong></td>
				  </tr>
				  <tr height="1px" style='line-height:10px;'>
					<td colspan='4' ><b>ROUND OFF :</b></td>
					<td align='right'><strong>
                      <?php $tot=$tot+$d[11]; echo number_format($d[11],2); ?>
                    </strong></td>
				  </tr>
				<?php
					$round1=round($tot,0);
					$r=$round1-$tot;
					$grand=$tot+$r;
				?>
                  <tr height="5px">
                    <td colspan='6' ><b>Grand Total</b></td>
                    <td align='right'><strong><?php echo $dis1; ?></strong></td>
					<td></td>
					<td></td>
                    <td align='right'><strong><?php echo number_format($vattot/2,2); ?></strong></td>
					<td align='right'><strong><?php echo number_format($vattot/2,2); ?></strong></td>
                    <td align='right'><strong>
                      <?php $tot1=number_format($grand,2); echo $tot1; ?>
                    </strong></td>
                  </tr>
                </table></td>
				</tr>
				<tr>
					<td colspan="3"><strong>Amount Payable (in words)- <b><?php echo "INR ".no_to_words($tot)." Only"; ?></b></strong>
					<span style="float:right;"><strong>E. & O.E</strong></span></td>
				</tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr>
					<td style="font-size:12px;">
						<b>Note:</b><br/>
						1. Warranty as per company rules & conditions.<br>
						2. Goods once sold can’t be returned or exchanged.<br>
						3. Rate should be valid for 7 days only.<br>
						4. Packing & Transportation charges extra.<br>
						5. Payment Should Be 100% Advance On Order<br>
						6. All Subject to Ahemdabad (Gujrat) Jurisdiction
					</td>
					<td rowspan='5' align='left'><img src='img/vhspay.jpg' width='80%' height='140px'/><br> For Payment Scan Here</td>
					<td  style="font-size:12px; font-weight:bold; padding:5px; border:1px solid grey;">
						Bank Details: <?php echo $bank; ?><br>
						A/c Name : <?php echo $accname; ?><br>
						A/c No.: <?php echo $accno; ?><br>
						Branch: <?php echo $branch; ?><br>
						IFSC : <?php echo $ifsc; ?>
					</td>
				</tr>
				<tr><td height="8px">&nbsp;</td></tr>
				<tr>
					<td></td>
					<td align="right"><strong>For ETHIC DESIGNS LLP<br /></strong></td>
				</tr>
				<tr><td height="20px">&nbsp;</td></tr>
				<tr>
					<td style='vertical-align:bottom;'><b>Customer's Signature</b></td>
					<td  align="right"><b>Authorised Signatory</b></td>					
				</tr>
      </table>   	
	  </div>
</body>
</html>
