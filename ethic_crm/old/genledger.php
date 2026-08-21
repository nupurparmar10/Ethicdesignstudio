<?php
  ob_start();
  session_start();
  include_once("connect.php");
 if($_REQUEST['group_id']!="") $group_id=" and group_id='$_REQUEST[group_id]'"; else $group_id="";
 
$sql = "SELECT * FROM ledger_accounts where name like '%$_REQUEST[name]%' ".$group_id." and status=1 order by name";
 
	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Ledger Details Available!!!";
	}
	else
	{
		$table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
			<caption><h1>Ledger Details</h1></caption>
			<tr>
				<th width='65'><span>S. No.</span></th>
				<th>Ledger<br>Name</th>
				<th>Mobile</th>
				<th>GST No.</th>
				<th>Account Group</th>
				<th>Starting Session</th>
				<th>Credit</th>
				<th>Debit</th>
				<th>Contact Person</th>
				<th>Address</th>
				<th>Email</th>
			</tr>";
?>
		<table class="table table-bordered table-striped table-actions">
			<thead>
				<tr>
					<th width='65'><span>S. No.</span></th>
					<th>Ledger<br>Name</th>
					<th>Contact<br>Person</th>
					<th>Mobile</th>
					<th>Account Group</th>
					<th>Credit</th>
					<th>Debit</th>
					<th width="120">Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($d = mysqli_fetch_row($result))
		{		
			$j=1;
			$totc=0;
			$totd=0;
			do
			{
				$p1=mysqli_query($con,"select * from ledger_details where ledger_id='$d[0]'");
				if(!$p=mysqli_fetch_row($p1)) { $p[1]=$p[2]=$p[3]=$p[4]=$p[5]="";}
				$g=mysqli_fetch_row(mysqli_query($con,"select group_name from group_master where group_id='$d[2]'"));
				
			
				$cr=$dr=0;
				
				$a=mysqli_fetch_row(mysqli_query($con,"SELECT sum(amount) FROM transaction where ledger_id='$d[0]' and type='Cr.'"));
				$cr+=$a[0];
				$a=mysqli_fetch_row(mysqli_query($con,"SELECT sum(amount) FROM transaction where ledger_id='$d[0]' and type='Dr.'"));
				$dr+=$a[0];
				
				$bal=$dr-$cr+$d[3];
				if($_REQUEST['due']!="")
				{
					if($_REQUEST['due']=="D")
					{
						if($bal<=0) continue;
					}
					else if($bal>=0) continue;
				}
				$table .= "<tr>
					<td>$j</td>
					<td>$d[1]</td>
					<td>$p[4]</td>
					<td>$p[3]</td>
					<td>$g[0]</td>
					<td>$d[4]</td>
					";
		?>
				<tr id="<?php echo $d[0]; ?>" onDblClick="view('<?php echo $d[0]; ?>');">
					<td><?php echo $j; $j++;?></td>
					<td><?php echo $d[1]; ?></td>
					<td><?php echo $p[1]; ?></td>
					<td><?php echo $p[4]; ?></td>
					<td><?php echo $g[0]; ?></td>
					
					<?php
						if($bal>=0)
						{
							echo "<td>&nbsp;</td>";
							echo "<td>$bal</td>";
							$table .= "<td></td>
									<td>$bal</td>";
							$totd+=$bal;
						}
						else
						{
							$bal=$bal*-1;
							echo "<td>$bal</td>";
							echo "<td>&nbsp;</td>";
							$table .= "<td>$bal</td>
									<td></td>";
							$totc+=$bal;
						}
					?>
					<td>
						<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('addledger.php?ledger_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-pencil"></span></button>
						<button class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $d[0]; ?>');"><span class="fa fa-times"></span></button>
						 <button class="btn btn-warning btn-rounded btn-condensed btn-sm" onClick="window.open('viewleddet.php?ledger_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-list"></span></button>
					</td>
				</tr>
			<?php
				$table .= "<td>".htmlspecialchars($p[1])."</td>
				<td>".htmlspecialchars($p[2])."</td>
				<td>".htmlspecialchars($p[5])."</td>";
				$table .= "</tr>";
			}while($d = mysqli_fetch_array($result));
		}
	?>
		<tr>
				<td colspan="5" align='right'><b>Total :</b></td>
				<td><b><?php echo "$totc"; ?></b></td>
				<td><b><?php echo "$totd"; ?></b></td>
				<td>&nbsp;</td>
		</tr>		              
            <?php
				$table .= "<tr>
				<td colspan='6' style='text-align:right; padding-right:10px;'><b>Total :</b></td>
				<td style='padding-right:10px; text-align:right;'><b>$totc</b></td>
				<td style='padding-right:10px; text-align:right;'><b>$totd</b></td>
				</tr>
      			</table>";
	?>	
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
				 <input type="hidden" name="fn" value="Sale Details"/>
				 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
			 </form>
		</div>
            <?php
				}
		 ?>	                                