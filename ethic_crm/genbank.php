 <?php
  ob_start();
 session_start();
 include_once("connect.php");

$sql = "SELECT * FROM bank_details where bank_name like '%$_REQUEST[bname]%' and ifsccode like '%$_REQUEST[ifsccode]%' and accno like '%$_REQUEST[accno]%' order by bank_name";

	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Bank Account Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' style='border-collapse:collapse;'>
			<tr>
				<th style='width:100px;'>S.No.</th>
				<th width='80'><span>Bank Name</span></th>
				<th width='200'><span>IFSC Code</span></th>
				<th width='120'><span>Contact</span></th>		
				<th width='120'><span>Account No.</span></th>		
				<th width='200'><span>Branch</span></th>
				<th width='120'><span>Address</span></th>	
				<th width='120'><span>Balance</span></th>	
			</tr>";
?>
		<table class="table datatable table-bordered table-striped table-actions">
			<thead>
				<tr>
					<th style="width:20px;">S. No.</th>
					<th width='80'><span>Bank Name</span></th>
					<th width='200'><span>IFSC Code</span></th>
					<th width='120'><span>Contact</span></th>		
					<th width='120'><span>Account No.</span></th>		
					<th width='200'><span>Branch</span></th>
					<th width='120'><span>Address</span></th>	
					<th width='120'><span>Balance</span></th>	
					<th width="120">Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($d = mysqli_fetch_row($result))
		{		
			$j=1;
			$tot=0;
			do
			{
				$table .= "<tr>
					<td align='center'>$j</td>
					<td>$d[1]</td>
					<td>$d[2]</td>
					<td>$d[6]</td>
					<td>$d[5]</td>
					<td>$d[3]</td>
					<td>$d[4]</td>
					";
			?>
			<tr id="<?php echo $d[0]; ?>" onDblClick="view('<?php echo $d[0]; ?>');">
				<td><?php echo $j; ?></td>
				<td><?php echo $d[1]; ?></td>
				<td><?php echo $d[2]; ?></td>
				<td><?php echo $d[6]; ?></td>
				<td><?php echo $d[5]; ?></td>
				<td><?php echo $d[3]; ?></td>
				<td><?php echo $d[4]; ?></td>
				<?php 
					$a1=mysqli_query($con,"Select opening_bal from ledger_accounts where ledger_id=$d[0]");
					$a=mysqli_fetch_row($a1);
					$cr=$dr=0;
					$bal=$a[0];
					
					$a=mysqli_fetch_row(mysqli_query($con,"SELECT sum(amount) FROM transaction where ledger_id='$d[0]' and type='Cr.'"));
					$cr+=$a[0];
					$a=mysqli_fetch_row(mysqli_query($con,"SELECT sum(amount) FROM transaction where ledger_id='$d[0]' and type='Dr.'"));
					$dr+=$a[0];
					
					$bal+=$dr-$cr;
					$tot+=$bal;
					echo "<td>$bal</td>";
					$table .= "<td align='center'>$bal</td></tr>";
				?>

				<td>
					<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('addbank.php?ledger_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-pencil"></span></button>					
				</td>
			</tr>
			<?php
				$j++;
			}while($d = mysqli_fetch_array($result));
			$table .= "<tr>
				<td colspan='7' align='right'>Total</td>
				<td align='right'>$tot</td>
				</tr>";
		}
		$table .="</table>";
	?>							
		<tr>
			<td colspan='7' align='right'>Total</td>
			<td><?php echo $tot; ?></td>
			<td>&nbsp;</td>
		</tr>
		</tbody>
      </table> 
		
		<div class="col-md-1 col-xs-1">
			  <form action="printlist.php" method="post" target="_blank">
				<input type="hidden" value="<?php echo $table; ?>" name="query"/>
				<button class="btn btn-primary" type="submit" name="s10">Print</button>		
				</form>
		</div>
		<div class="col-md-3 col-xs-3">
			<form action="excel.php" method="post">
				 <input type="hidden" name="query" value="<?php echo $table; ?>"/>
				 <input type="hidden" name="fn" value="Bank Account Details"/>
				 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
			 </form>
		</div>
            <?php
				}
		 ?>	  