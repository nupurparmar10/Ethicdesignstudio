<?php
  ob_start();
 session_start();
 include_once("connect.php");
 
	if($_REQUEST['dfrom']!="") $dfrom=" and tdate>='$_REQUEST[dfrom]'"; else $dfrom="";
	if($_REQUEST['dto']!="") $dto=" and tdate<='$_REQUEST[dto]'"; else $dto="";
	$name1=mysqli_query($con,"select name,opening_bal from ledger_accounts where ledger_id='$_REQUEST[ledger_id]'");
	if($name=mysqli_fetch_row($name1))
	{

	$query=array();
	$j=0;
	$flag=0;
	$sql = "SELECT * FROM transaction where ledger_id='$_REQUEST[ledger_id]' ".$dfrom." ".$dto." order by tdate,trans_id";
	$result = mysqli_query($con,$sql);
	foreach($result as $row)
	{
		$query[$j++]=$row;
		$flag=1;
	}
	
	$table ="";
	
		$ctot=$dtot=0;
		$table.= "<table align='center' cellpadding='3' border='1' style='border-collapse:collapse;'>
			<thead>
			<tr>
				<th colspan='6' align='center'>$name[0]";
		if($_REQUEST['dfrom']!="")
		{
			$date= DateTime::createFromFormat('Y-m-d', $_REQUEST['dfrom']);
			$table .= "<br>From ".$date->format('d-m-Y');
		}
		if($_REQUEST['dto']!="")
		{
			$date= DateTime::createFromFormat('Y-m-d', $_REQUEST['dto']);
			$table .= "&nbsp; To ".$date->format('d-m-Y');
		}
		$table .= "</th>
			</tr>
			<tr>
				<th width='65'><span>S. No.</span></th>
				<th width='100'><span>Date</span></th>
				<th width='250'><span>Particulars</span></th>
				<th width='120'><span>Credit</span></th>		
				<th width='120'><span>Debit</span></th>		
				<th width='120'><span>Balance</span></th>	
			</tr>
			</thead>";
?>
		<table class="table datatable table-bordered table-striped table-actions">
			<h4 align="center"><?php echo $name[0]; ?></h4>
			<thead>
				<tr>
					<th width="65"><span>S. No.</span></th>
					<th width="100"><span>Date</span></th>
					<th width="250"><span>Particulars</span></th>
					<th width="120"><span>Credit</span></th>		
					<th width="120"><span>Debit</span></th>		
					<th width="120"><span>Balance</span></th>
				</tr>
			</thead>
			<tbody>
 <?php	
			if($_REQUEST['dfrom']!="")
			{
				$optemp=0;
				$j=0;
				$flag=0;
				$sql1 = "SELECT * FROM transaction where ledger_id='$_REQUEST[ledger_id]' and tdate<'$_REQUEST[dfrom]' order by tdate ,trans_id";
				$result1 = mysqli_query($con,$sql1);
				if($row1 = mysqli_fetch_row($result1))
				{
					if($flag==0) $bal=$name[1];
					do
					{
						if($row1[5]=="Cr.")
						{
							$bal=$bal-$row1[3];			
						}
						else
						{
							$bal=$bal+$row1[3];
						}										
					}while($row1 = mysqli_fetch_array($result1));
					$flag=1;
				}
				
				if($flag==1)
				{
					echo "<tr>";
					echo "<td style='padding-left:10px;' colspan='3'>Opening Balance</td>";
					$table .= "<tr>
							<td style='padding-left:10px;' colspan='3'>Opening Balance</td>";
					$optemp+=$bal;
					if($bal<0)
					{
						$ctot+=$bal;
						$val=$bal*-1;
						$val=number_format($val,2);
						echo "<td style='padding-right:10px; text-align:right;'>$val</td>";
						echo "<td></td>";
						$table .= "<td style='padding-right:10px; text-align:right;'>$val</td>
								<td></td>";
					}
					else
					{
						$dtot+=$bal;
						$val=number_format($bal,2);
						echo "<td></td>";
						echo "<td style='padding-right:10px; text-align:right;'>$val</td>";
						$table .= "<td></td>
								<td style='padding-right:10px; text-align:right;'>$val</td>";
					}					
					echo "<td style='padding-right:10px; text-align:right;'>".number_format($optemp,2)."</td>";
					echo "</tr>";
					$table .="<td style='padding-right:10px; text-align:right;'>".number_format($optemp,2)."</td>
							</tr>";
				}
				else
				{
					goto l2;
				}
			}
			else
			{
				l2:
				echo "<tr>";
				echo "<td style='padding-left:10px;' colspan='3'>Opening Balance</td>";
				$table .= "<tr>
						<td style='padding-left:10px;' colspan='3'>Opening Balance</td>";
				$optemp=$name[1];
				if($name[1]<0)
				{
					$ctot+=$name[1];
					$name[1]=$name[1]*-1;
					$name[1]=number_format($name[1],2);
					echo "<td style='padding-right:10px; text-align:right;'>$name[1]</td>";
					echo "<td></td>";
					$table .= "<td style='padding-right:10px; text-align:right;'>$name[1]</td>
							<td></td>";
				}
				else
				{
					$dtot+=$name[1];
					$name[1]=number_format($name[1],2);
					echo "<td></td>";		
					echo "<td style='padding-right:10px; text-align:right;'>$name[1]</td>";
					$table .= "<td></td>
							<td style='padding-right:10px; text-align:right;'>$name[1]</td>";
				}			
				echo "<td style='padding-right:10px; text-align:right;'>".number_format($optemp,2)."</td>";
				echo "</tr>";
				$table .= "<td style='padding-right:10px; text-align:right;'>".number_format($optemp,2)."</td>
						</tr>";
			}
		$bal=$optemp;
		$j=1;
		foreach($query as $row)
		{
			echo "<tr>";
			echo "<td style='padding-left:10px;'>$j</td>";
			$table .= "<tr>
					<td style='padding-left:10px;'>$j</td>";
			if($row['tdate']!="0000-00-00")
			{
				$date= DateTime::createFromFormat('Y-m-d', $row['tdate']);
				echo "<td style='text-align:left; padding-left:10px;'>";
				echo $date->format('d-m-Y');
				echo"</td>";
				$table .= "<td style='text-align:left; padding-left:10px;'>".$date->format('d-m-Y')."</td>";
			}
			else
			{
					echo "<td></td>";
				$table .= "<td></td>";
			}
			$href="";
			if(strpos($row['relatedto'],'SR')!==false) $href="salereturndet.php?sale_id=".substr($row['relatedto'],2);
			else if(strpos($row['relatedto'],'SAL')!==false) $href="viewsalary.php";
			else if(strpos($row['relatedto'],'S')!==false) $href="saledet.php?sale_id=".substr($row['relatedto'],1);
			else if(strpos($row['relatedto'],'ADV')!==false) $href="giveadvance.php?a_id=".substr($row['relatedto'],3);
			else if(strpos($row['relatedto'],'PD')!==false) $href="viewpaydues.php";
			else if(strpos($row['relatedto'],'D')!==false) $href="adddeposit.php?d_id=".substr($row['relatedto'],1);
			else if(strpos($row['relatedto'],'E')!==false) $href="addexpentry.php?exp_id=".substr($row['relatedto'],1);
			else if(strpos($row['relatedto'],'I')!==false) $href="addincentry.php?inc_id=".substr($row['relatedto'],1);
			else if(strpos($row['relatedto'],'M')!==false) 
			{
				$p=mysqli_fetch_row(mysqli_query($con,"select m_id,type from manufacturejob where relatedwith='$row[relatedto]'"));
				if($p[1]=="Manufacturing")
				$href="manu_det.php?m_id=$p[0]";
				else 
				$href="manu_det1.php?m_id=$p[0]";
			}
			else if(strpos($row['relatedto'],'P')!==false) $href="purdet.php?pur_id=".substr($row['relatedto'],1);
			else if(strpos($row['relatedto'],'PR')!==false) $href="purreturndet.php?pur_id=".substr($row['relatedto'],2);
			else if(strpos($row['relatedto'],'RC')!==false) $href="printreceipt.php?rec_id=".substr($row['relatedto'],2);
			else if(strpos($row['relatedto'],'W')!==false) $href="addwithdrawl.php?w_id=".substr($row['relatedto'],1);

			if($href!="")
			{
				echo "<td style='padding-left:10px;'><a href='$href' target='_blank'>$row[particulars]</a></td>";
			}
			else
			{
				echo "<td style='padding-left:10px;'>$row[particulars]</td>";
			}
			
			$table .="<td style='padding-left:10px;'>$row[particulars]</td>";
			if($row['type']=="Cr.")
			{
				$bal=$bal-$row['amount'];			
				$ctot+=$row['amount'];
				$row['amount']=number_format($row['amount'],2);				
				echo "<td style='padding-right:10px; text-align:right;'>$row[amount]</td>";
				echo "<td></td>";
				$table .= "<td style='padding-right:10px; text-align:right;'>$row[amount]</td>
							<td></td>";
			}
			else
			{
				$bal=$bal+$row['amount'];
				$dtot+=$row['amount'];
				$row['amount']=number_format($row['amount'],2);								
				echo "<td></td>";
				echo "<td style='padding-right:10px; text-align:right;'>$row[amount]</td>";
				$table .= "<td></td>
							<td style='padding-right:10px; text-align:right;'>$row[amount]</td>";
			}					
			echo "<td style='padding-right:10px; text-align:right;'>".number_format($bal,2)."</td>";
			echo "</tr>";
			$table .="<td style='padding-right:10px; text-align:right;'>".number_format($bal,2)."</td>
					</tr>";
			$j++;
		}
			$c=$ctot+$bal;
			$d=$dtot;
		
		
			$table .= "<tr>
			<td style='padding-left:10px;' colspan='3'>Current Total</td>
			<td style='padding-right:10px; text-align:right;'>".number_format($ctot,2)."</td>
			<td style='padding-right:10px; text-align:right;'>".number_format($dtot,2)."</td>
			<td></td>
		</tr>
		<tr>
			<td style='padding-left:10px;' colspan='3'>By Closing Balance</td>
			<td style='padding-right:10px; text-align:right;'>".number_format($bal,2)."</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan='3'>&nbsp;</td>
			<td style='font-weight:bold; padding-right:10px; text-align:right; '>".number_format($c,2)."</td>
			<td style='font-weight:bold; padding-right:10px; text-align:right;'>".number_format($d,2)."</td>
			<td></td>
		</tr></table> ";
		
	?>							
		<tr>
			<td style='padding-left:10px;' colspan='3'>Current Total</td>
			<td style='padding-right:10px; text-align:right;'><?php echo number_format($ctot,2); ?></td>
			<td style='padding-right:10px; text-align:right;'><?php echo number_format($dtot,2); ?></td>
			<td></td>
		</tr>
		<tr>
			<td style='padding-left:10px;' colspan='3'>By Closing Balance</td>
			<td style='padding-right:10px; text-align:right;'><?php echo number_format($bal,2); ?></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan='3'>&nbsp;</td>
			<td style='font-weight:bold; padding-right:10px; text-align:right;'><?php echo number_format($c,2); ?></td>
			<td style='font-weight:bold; padding-right:10px; text-align:right;'><?php echo number_format($d,2); ?></td>
			<td></td>
		</tr>
		</tbody>
      </table> 
		
		<div class="col-md-3 col-xs-3">
			  <form action="printlist.php" method="post" target="_blank">
				<input type="hidden" value="<?php echo $table; ?>" name="query"/>
				<button class="btn btn-primary" type="submit" name="s10">Print</button>		
				</form>
		</div>
		<div class="col-md-3 col-xs-3">
			<form action="excel.php" method="post">
				 <input type="hidden" name="query" value="<?php echo $table; ?>"/>
				 <input type="hidden" name="fn" value="Transaction Details"/>
				 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
			 </form>
		</div>
    <?php
	}
	else
	echo false;
	?>