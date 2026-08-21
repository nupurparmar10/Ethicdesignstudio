 <?php
  ob_start();
 session_start();
 include_once("connect.php");

if($_REQUEST['dfrom']!="") $dfrom=" and w_date>='$_REQUEST[dfrom]'"; else $dfrom="";
if($_REQUEST['dto']!="") $dto=" and w_date<='$_REQUEST[dto]'"; else $dto="";
if($_REQUEST['bank']!="") $bank=" and bank_acc='$_REQUEST[bank]'"; else $bank="";
$sql = "SELECT * FROM withdrawl where amt like '$_REQUEST[amt]%' and remark like '%$_REQUEST[remark]%' ".$bank." ".$dfrom." ".$dto." order by w_id desc";

	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Withdrawl Entry Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' style='border-collapse:collapse;'>
			<tr>
				<th style='width:100px;'>S.No.</th>
				<th width='30%'><span>Bank Account</span></th>	
				<th width='16%'><span>Amount</span></th>	
				<th width='13%'><span>Date</span></th>
				<th width='23%'><span>Remarks</span></th>
			</tr>";
?>
		<table class="table datatable table-bordered table-striped table-actions">
			<thead>
				<tr>
					<th style="width:20px;">S. No.</th>
					<th width="30%"><span>Bank Account</span></th>	
					<th width="16%"><span>Amount</span></th>	
					<th width="13%"><span>Date</span></th>
					<th width="23%"><span>Remarks</span></th>
					<th width="120">Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($d = mysqli_fetch_row($result))
		{		
			$j=1;
			do
			{
				$table .= "<tr>
					<td>$j</td>
					";
			?>
			<tr id="<?php echo $d[0]; ?>">
				<td><?php echo $j; ?></td>
				<?php 
					$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$d[3]");
					$a=mysqli_fetch_row($a1);
					echo "<td>$a[0]</td>";
					$table .= "<td>$a[0]</td>";
					echo "<td>$d[2]</td>";
					$table .= "<td>$d[2]</td>";
					
					$date= DateTime::createFromFormat('Y-m-d', $d[1]);
					echo "<td>".$date->format('d-m-Y')."</td>";
					$table .= "<td>".$date->format('d-m-Y')."</td>";
					echo "<td>$d[4]</td>";
					$table .= "<td>$d[4]</td></tr>";
				?>

				<td>
					<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('addwithdrawl.php?w_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-pencil"></span></button>
					<button class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $d[0]; ?>');"><span class="fa fa-times"></span></button>
				</td>
			</tr>
			<?php
				$j++;
			}while($d = mysqli_fetch_array($result));
		}
		$table .="</table>";
	?>							
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
				 <input type="hidden" name="fn" value="Withdrawl Details"/>
				 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
			 </form>
		</div>
            <?php
				}
		 ?>	  