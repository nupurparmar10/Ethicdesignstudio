 <?php
  ob_start();
 session_start();
 include_once("connect.php");
  if($_REQUEST['ledger_id']!="") $ledger_id=" and ledger_id='$_REQUEST[ledger_id]'"; else $ledger_id="";
  if($_REQUEST['month']!="") $month=" and month='$_REQUEST[month]'"; else $month="";
  if($_REQUEST['dfrom']!="") $dfrom=" and sdate>='$_REQUEST[dfrom]'"; else $dfrom="";
  if($_REQUEST['dto']!="") $dto=" and sdate<='$_REQUEST[dto]'"; else $dto="";
  
	$sql = "SELECT * FROM salary where remark like '%$_REQUEST[remark]%' ".$ledger_id." ".$month." ".$dfrom." ".$dto." and year like '%$_REQUEST[year]%' order by sdate desc";

	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Salary Detail Available!!!";
	}
	else
	{
		$table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
			<caption><h1>Salary Details</h1></caption>
			<tr>
				<th width='65'><span>S. No.</span></th>
				<th width='200'><span>Date</span></th>	
				<th width='200'><span>Employee Name</span></th>	
				<th width='200'><span>Paid By</span></th>		
				<th width='200'><span>Month</span></th>	
				<th width='200'><span>Salary</span></th>	
				<th width='200'><span>Present</span></th>	
				<th width='200'><span>Absent</span></th>	
				<th width='200'><span>Salary By Attendance</span></th>	
				<th width='200'><span>Remark</span></th>	
				<th width='200'><span>Commission</span></th>	
				<th width='200'><span>Remark</span></th>
				<th width='200'><span>Advance</span></th>	
				<th width='200'><span>Deduction</span></th>	
				<th width='200'><span>Remark</span></th>
				<th width='200'><span>Total Salary</span></th>
			</tr>";
?>
		<table class="table datatable table-bordered table-striped table-actions">
			<thead>
				<tr>
					<th width='65'><span>S. No.</span></th>
					<th width='200'><span>Date</span></th>	
					<th width='200'><span>Employee Name</span></th>	
					<th width='200'><span>Paid By</span></th>		
					<th width='200'><span>Month</span></th>	
					<th width='200'><span>Salary</span></th>	
					<th width='200'><span>Present</span></th>	
					<th width='200'><span>Absent</span></th>	
					<th width='200'><span>Salary By Attendance</span></th>	
					<th width='200'><span>Remark</span></th>	
					<th width='200'><span>Commission</span></th>	
					<th width='200'><span>Remark</span></th>
					<th width='200'><span>Advance</span></th>	
					<th width='200'><span>Deduction</span></th>	
					<th width='200'><span>Remark</span></th>
					<th width='200'><span>Total Salary</span></th>	
					<th width="120">Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($row = mysqli_fetch_row($result))
		{		
			$j=1;		
			$tot1=$tot2=$tot3=$tot4=$tot5=0;
			do
			{
		?>
				<tr id="<?php echo $row[0]; ?>">
		<?php			
				echo "<td>$j</td>";
				$table .= "<tr><td>$j</td>";
				if($row[1]!="0000-00-00")
				{
					$date= DateTime::createFromFormat('Y-m-d', $row[1]);
					echo "<td>";
					echo $date->format('d-m-Y');
					echo"</td>";
					$table .="<td style='text-align:left;'>".$date->format('d-m-Y')."</td>";
				}
				else 
				{
					echo "<td>&nbsp;</td>";
					$table .="<td></td>";
				}
				$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$row[2]");
				$a=mysqli_fetch_row($a1);
				
				echo "<td>".htmlspecialchars($a[0])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($a[0])."</td>";
				$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$row[3]");
				$a=mysqli_fetch_row($a1);
				
				echo "<td>".htmlspecialchars($a[0])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($a[0])."</td>";
				
				$date= DateTime::createFromFormat('m', $row[4]);
				echo "<td>";
				echo $date->format('M')." ".$row[17];
				echo"</td>";
				$table .="<td style='text-align:left;'>".$date->format('M')." $row[17]</td>";
				echo "<td>".htmlspecialchars($row[5])."</td>";
				echo "<td>".htmlspecialchars($row[6])."</td>";
				echo "<td>".htmlspecialchars($row[7])."</td>";
				echo "<td>".htmlspecialchars($row[8])."</td>";
				echo "<td>".htmlspecialchars($row[9])."</td>";
				echo "<td>".htmlspecialchars($row[10])."</td>";
				echo "<td>".htmlspecialchars($row[11])."</td>";
				echo "<td>".htmlspecialchars($row[16])."</td>";
				echo "<td>".htmlspecialchars($row[12])."</td>";
				echo "<td>".htmlspecialchars($row[13])."</td>";
				echo "<td>".htmlspecialchars($row[14])."</td>";
				
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[5])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[6])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[7])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[8])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[9])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[10])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[11])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[16])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[12])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[13])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[14])."</td>";
				
				$table .=  "</tr>";
				$tot1+=$row[8];
				$tot2+=$row[10];
				$tot3+=$row[12];
				$tot4+=$row[14];
				$tot5+=$row[16];
				?>
				<td>
					<button class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $row[0]; ?>');"><span class="fa fa-times" title="Delete"></span></button>
				</td>
			</tr>
			<?php
				$j++;
			}while($row = mysqli_fetch_array($result));
		}
		$table .= "<tr>
			<td colspan='8'>Total</td>
			<td>$tot1</td>
			<td></td>
			<td>$tot2</td>
			<td></td>
			<td>$tot5</td>
			<td>$tot3</td>
			<td></td>
			<td>$tot4</td>
		</tr></table>";
	?>	
		<tr>
			<td colspan='8'>Total</td>
			<td><?php echo $tot1; ?></td>
			<td></td>
			<td><?php echo $tot2; ?></td>
			<td></td>
			<td><?php echo $tot5; ?></td>
			<td><?php echo $tot3; ?></td>
			<td></td>
			<td><?php echo $tot4; ?></td>
			<td></td>
		</tr>
		</tbody>
      </table> 
	  		<div class="col-md-1 col-xs-1">
			  <form action="printlist.php" method="post" target="_blank">
				<input type="hidden" value="<?php echo $table; ?>" name="query"/>
				<button class="btn btn-primary" type="submit" name="s11">Print</button>		
				</form>
		</div>
		<div class="col-md-3 col-xs-3">
			<form action="excel.php" method="post">
				 <input type="hidden" name="query" value="<?php echo $table; ?>"/>
				 <input type="hidden" name="fn" value="Salary Details"/>
				 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
			 </form>
		</div>
            <?php
				}
		 ?>	  