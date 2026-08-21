 <?php
  ob_start();
 session_start();
 include_once("connect.php");
  if($_REQUEST['ledger_id']!="") $ledger_id=" and ledger_id='$_REQUEST[ledger_id]'"; else $ledger_id="";
  if($_REQUEST['dfrom']!="") $dfrom=" and adate>='$_REQUEST[dfrom]'"; else $dfrom="";
  if($_REQUEST['dto']!="") $dto=" and adate<='$_REQUEST[dto]'"; else $dto="";
  
  $sql = "SELECT * FROM empadvance where true ".$ledger_id." ".$dfrom." ".$dto." order by adate desc";

	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Advance Salary Detail Available!!!";
	}
	else
	{
		$table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
			<caption><h1>Advance Salary Details</h1></caption>
			<tr>
				<th width='65'><span>S. No.</span></th>
				<th width='200'><span>Date</span></th>	
				<th width='200'><span>Employee Name</span></th>	
				<th width='200'><span>Paid By</span></th>		
				<th width='200'><span>Amount</span></th>	
				<th width='200'><span>Reason</span></th>	
			</tr>";
?>
		<table class="table datatable table-bordered table-striped table-actions">
			<thead>
				<tr>
					<th width='65'><span>S. No.</span></th>
					<th width='200'><span>Date</span></th>	
					<th width='200'><span>Employee Name</span></th>	
					<th width='200'><span>Paid By</span></th>		
					<th width='200'><span>Amount</span></th>	
					<th width='200'><span>Reason</span></th>	
					<th width="120">Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($row = mysqli_fetch_row($result))
		{		
			$j=1;		
			$tot1=0;
			do
			{
		?>
				<tr id="<?php echo $row[0]; ?>">
		<?php			
				echo "<td>$j</td>";
				$table .= "<tr><td>$j</td>";
				if($row[2]!="0000-00-00")
				{
					$date= DateTime::createFromFormat('Y-m-d', $row[2]);
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
				$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$row[1]");
				$a=mysqli_fetch_row($a1);
				
				echo "<td>".htmlspecialchars($a[0])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($a[0])."</td>";
				$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$row[5]");
				$a=mysqli_fetch_row($a1);
				
				echo "<td>".htmlspecialchars($a[0])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($a[0])."</td>";
				echo "<td>".htmlspecialchars($row[3])."</td>";
				echo "<td>".htmlspecialchars($row[4])."</td>";
				
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[3])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[4])."</td>";	
				
				$table .=  "</tr>";
				$tot1+=$row[3];
				?>
				<td>
					<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('giveadvance.php?a_id=<?php echo $row[0]; ?>','_self')"><span class="fa fa-pencil" title="Edit"></span></button>
					<button class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $row[0]; ?>');"><span class="fa fa-times" title="Delete"></span></button>
				</td>
			</tr>
			<?php
				$j++;
			}while($row = mysqli_fetch_array($result));
		}
		$table .= "<tr>
			<td colspan='4'>Total</td>
			<td>$tot1</td>
			<td></td>
		</tr></table>";
	?>	
		<tr>
			<td colspan='4'>Total</td>
			<td><?php echo $tot1; ?></td>
			<td></td>
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
				 <input type="hidden" name="fn" value="Advance Salary Details"/>
				 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
			 </form>
		</div>
            <?php
				}
		 ?>	  