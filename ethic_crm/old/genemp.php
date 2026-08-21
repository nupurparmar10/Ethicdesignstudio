 <?php
  ob_start();
 session_start();
 include_once("connect.php");
 if($_REQUEST['status']!="") $status=" and status='$_REQUEST[status]'"; else $status="";
 
 $sql = "SELECT * FROM empdet where empname like '%$_REQUEST[empname]%' and post like '%$_REQUEST[post]%' ".$status." order by empname";

	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Employee Available!!!";
	}
	else
	{
		$table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
			<caption><h1>Employee Details</h1></caption>
			<tr>
				<th width='65'><span>S. No.</span></th>
				<th width='80'><span>Name</span></th>
				<th width='200'><span>Post</span></th>
				<th width='200'><span>Aadhar No.</span></th>
				<th width='200'><span>Mobile</span></th>
				<th width='200'><span>Address</span></th>
				<th width='200'><span>Salary</span></th>
				<th width='200'><span>No. of Days Allowed for Leave</span></th>
			</tr>";
?>
		<table class="table datatable table-bordered table-actions">
			<thead>
				<tr>
					<th width='65'><span>S. No.</span></th>
					<th width='80'><span>Name</span></th>
					<th width='200'><span>Post</span></th>
					<th width='200'><span>Aadhar No.</span></th>
					<th width='200'><span>Mobile</span></th>
					<th width='200'><span>Address</span></th>
					<th width='200'><span>Salary</span></th>
					<th width='200'><span>No. of Days Allowed for Leave</span></th>
					<th width="120">Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($row = mysqli_fetch_row($result))
		{		
			$j=1;
			$tot=0;
			do
			{
		?>
		<tr id="<?php echo $row[0]; ?>" class="<?php if($row[8]==1) echo "success"; else if($row[8]==2) echo "warning"; else echo "danger"; ?>">
		<?php				
				echo "<td>$j</td>";
				echo "<td>".htmlspecialchars($row[1])."</td>";
				echo "<td>".htmlspecialchars($row[2])."</td>";
				echo "<td>".htmlspecialchars($row[3])."</td>";
				echo "<td>".htmlspecialchars($row[4])."</td>";
				echo "<td>".htmlspecialchars($row[5])."</td>";
				echo "<td>".htmlspecialchars($row[6])."</td>";
				echo "<td>".htmlspecialchars($row[7])."</td>";
				
				$table .=  "<tr>
					<td style='padding-left:10px;'>$j</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[1])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[2])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[3])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[4])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[5])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[6])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[7])."</td>
					";
			?>
				<td>
					<?php
						if($row[8]!=0)
						{
					?>
					<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('addemp.php?ledger_id=<?php echo $row[0]; ?>','_self');" title="Edit"><span class="fa fa-pencil"></span></button>
					<button class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $row[0]; ?>');"><span class="fa fa-times" title="Delete"></span></button>
					<?php
						}
						if($row[8]==1)
						{
					?>
						<button class="btn btn-default btn-rounded btn-condensed btn-sm" onClick="window.open('viewemp.php?deactive=<?php echo $row[0]; ?>','_self');" title="Deactive"><span class="fa fa-toggle-on"></span></button>
					<?php
						}
						else if($row[8]==2)
						{
					?>
						<button class="btn btn-success btn-rounded btn-condensed btn-sm" onClick="window.open('viewemp.php?active=<?php echo $row[0]; ?>','_self');" title="Active"><span class="fa fa-toggle-off"></span></button>
					<?php
						}
					?>
				</td>
			</tr>
			<?php
				$j++;
			}while($row = mysqli_fetch_array($result));
		}
				$table .= "</table>";
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
				 <input type="hidden" name="fn" value="Employee Details"/>
				 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
			 </form>
		</div>
            <?php
				}
		 ?>	  