<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	if(isset($_REQUEST['msg']))
	{
		$msg="Income Entry Modified Successfully!!!";
	}
	if(isset($_REQUEST['inc_id']))
	{
		$pid1=mysqli_query($con,"select relatedwith from income_detail where inc_id=$_REQUEST[inc_id]");
		$pid=mysqli_fetch_row($pid1);
		$rid=$pid[0];
		mysqli_query($con,"delete from income_detail where inc_id=$_REQUEST[inc_id]");
		mysqli_query($con,"delete from transaction where relatedto='$rid'");
		$msg1="Income Entry Deleted Successfully!!!";
	}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>        
        <!-- META SECTION -->
        <title>Ethic Design Studio</title>             
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
        <link rel="icon" href="logo3.png" type="image/x-icon" />
        <!-- END META SECTION -->
        
        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->                
		<script type="text/javascript" language="javascript">
	function delete_row(row)
	{
        var box = $("#mb-remove-row");
        box.addClass("open");
        box.find(".mb-control-yes").on("click",function(){
            box.removeClass("open");
			delete_row1(row);
            $("#"+row).hide("slow",function(){
                $(this).remove();
            });
			

        });
    }
	function delete_row1(row)
	{
			var path="viewincentry.php?inc_id="+row;
			window.open(path,"_self");
	}
	</script>     
		<script src="js\jquery.min.js"></script>                 
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu6=true; $smenu6="2";  $ssmenu6="22"; include_once("sidebar.php"); ?>
            <!-- END PAGE SIDEBAR -->
            
            <!-- PAGE CONTENT -->
            <div class="page-content">
                
                <!-- START X-NAVIGATION VERTICAL -->
                <?php include_once("topheader.php"); ?>
                <!-- END X-NAVIGATION VERTICAL -->                     
                
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                   <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
					<li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="#">Income Master</a></li>
					<li><a href="#">Income Entry</a></li>
                    <li class="active">View Income Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2> View Income Details</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                    
                    
                    <div class="row">
                        <div class="col-md-12">
							<?php
								if($msg)
								{
							?>
							<div class="alert alert-info" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg; ?></strong>
							</div>
							<?php 
								}
							?>
							<?php
								if($msg1)
								{
							?>
							<div class="alert alert-danger" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg1; ?></strong>
							</div>
							<?php 
								}
							?>
							
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                
                                <div class="panel-body">
									 <form class="form-horizontal" method="post" action="viewincentry.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Income Type</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
												<label class="col-md-2 col-xs-2">Received By</label>
												<label class="col-md-2 col-xs-2">Amount</label>
												<label class="col-md-2 col-xs-2">Remark</label>
											</div>
											
											<div class="row">
												 <div class="col-md-2 col-xs-12"> <select class="form-control" name="ledger_id" id="ledger_id">
													<option value="">--Select--</option>
													<?php
														$c1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (select group_id from group_master where group_name in ('Direct Incomes','Indirect Incomes')) order by name");
														while($c=mysqli_fetch_row($c1))
														{
															echo "<option value='$c[0]'>$c[1]</option>";
														}
													?>
												</select></div>
												<div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dfrom" id="dfrom"/>
												</div>
												<div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dto" id="dto"/>
												</div>
												<div class="col-md-2 col-xs-12"> <select class="form-control" name="paidby" id="paidby">
													<option value="">--Select--</option>
													<option value="Creidt">Credit</option>
													 <?php
													$list1=mysqli_query($con,"select ledger_id,name from ledger_accounts  where status=1 and (name like 'Cash Account' or group_id in (select group_id from group_master where group_name like 'Bank Accounts')) order by name");
													if($l=mysqli_fetch_row($list1))
													{
														do{
															echo "<option value='$l[0]'>$l[1]</option>";
														}while($l=mysqli_fetch_row($list1));
													}
												?>
												</select></div>		
												<div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="amount" id="amount"/>
												</div>
												<div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="remark" id="remark"/>
												</div>												
											</div>
											<div class="row">
												<label class="col-md-2 col-xs-2">Party</label>
											</div>
											<div class="row">
												<div class="col-md-2 col-xs-12"> <select class="form-control" name="party" id="party">
													<option value="">--Select--</option>
													 <?php
													$list1=mysqli_query($con,"select ledger_id,name from ledger_accounts  where status=1 and group_id in (26,27) order by name");
													if($l=mysqli_fetch_row($list1))
													{
														do{
															echo "<option value='$l[0]'>$l[1]</option>";
														}while($l=mysqli_fetch_row($list1));
													}
												?>
												</select></div>	
												<div class="col-md-2 col-xs-12"> 
													<button class="btn btn-primary" type="submit" name="open">Open</button>
												</div>
											</div>
									</div>
									 </form>
									 <br>
                                    <div class="table-responsive" id="display">
                                        <?php
 if(isset($_REQUEST['open']))
 {
if($_REQUEST['ledger_id']!="")
{
	$ledger_id=" and ledger_id='$_REQUEST[ledger_id]'";
}
else
{
	$ledger_id="";
}
if($_REQUEST['dfrom']!="")
{
	$dfrom=" and incdate>='$_REQUEST[dfrom]'";
}
else
{
	$dfrom="";
}
if($_REQUEST['dto']!="")
{
	$dto=" and incdate<='$_REQUEST[dto]'";
}
else
{
	$dto="";
}
if($_REQUEST['paidby']!="") $paidby=" and paidby='$_REQUEST[paidby]'"; else $paidby="";
if($_REQUEST['party']!="") $party=" and party='$_REQUEST[party]'"; else $party="";
$sql = "SELECT * FROM income_detail where remark like '%$_REQUEST[remark]%' and amount like '$_REQUEST[amount]%' ".$ledger_id." ".$dfrom." ".$dto." ".$paidby." ".$party." order by inc_id desc";

	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Income Entry Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' style='border-collapse:collapse;'>
			<tr>
				<th style='width:100px;'>S.No.</th>
				<th style='width:100px;'>Party</th>
				<th style='width:100px;'>Income Type</th>
				<th style='width:150px;'>Amount</th>
				<th style='width:100px;'>Received By</th>
				<th style='width:100px;'>Date</th>
				<th style='width:100px;'>Remark</th>
			</tr>";
?>
		<table class="table table-bordered table-striped table-actions">
			<thead>
				<tr>
					<th style="width:20px;">S. No.</th>
					<th style='width:100px;'>Party</th>
					<th style='width:10px;'>Income Type</th>
					<th style='width:150px;'>Amount</th>
					<th style='width:100px;'>Received By</th>
					<th style='width:100px;'>Date</th>
					<th style='width:100px;'>Remark</th>
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
					$e1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$d[7]'");
					$e=mysqli_fetch_row($e1);
					echo "<td>$e[0]</td>";
					$table .= "<td>$e[0]</td>";
					$e1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$d[2]'");
					$e=mysqli_fetch_row($e1);
					echo "<td>$e[0]</td>";
					$table .= "<td>$e[0]</td>";
					echo "<td>$d[4]</td>";
					$table .= "<td>$d[4]</td>";
					if($d[3]=="Credit") $e[0]=$d[3];
					else
					{
						$e1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$d[3]'");
						$e=mysqli_fetch_row($e1);
					}
					echo "<td>$e[0]</td>";
					$table .= "<td>$e[0]</td>";
					$date= DateTime::createFromFormat('Y-m-d', $d[1]);
					echo "<td>".$date->format('d-m-Y')."</td>";
					$table .= "<td>".$date->format('d-m-Y')."</td>";
					echo "<td>$d[5]</td>";
					$table .= "<td>$d[5]</td></tr>";
				?>

				<td>
					<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('addincentry.php?inc_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-pencil"></span></button>
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
				 <input type="hidden" name="fn" value="Income Details"/>
				 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
			 </form>
		</div>
            <?php
				}
			}
		 ?>	                                 
                                    </div>
                                </div>
                            </div>
                            <!-- END DATATABLE EXPORT -->                            
                       
                        </div>
                    </div>

                </div>         
                <!-- END PAGE CONTENT WRAPPER -->
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->    

        <!-- MESSAGE BOX-->
        <div class="message-box animated fadeIn" data-sound="alert" id="mb-remove-row">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title"><span class="fa fa-times"></span> Remove <strong>Data</strong> ?</div>
                    <div class="mb-content">
                        <p>Are you sure you want to remove this row?</p>                    
                        <p>Press Yes if you sure.</p>
                    </div>
                    <div class="mb-footer">
                        <div class="pull-right">
                            <button class="btn btn-success btn-lg mb-control-yes">Yes</button>
                            <button class="btn btn-default btn-lg mb-control-close">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MESSAGE BOX-->        
        
        <!-- MESSAGE BOX-->
         <?php include_once("footer.php"); ?>
        <!-- END MESSAGE BOX-->

        <!-- START PRELOADS -->
        <audio id="audio-alert" src="audio/alert.mp3" preload="auto"></audio>
        <audio id="audio-fail" src="audio/fail.mp3" preload="auto"></audio>
        <!-- END PRELOADS -->                      

    <!-- START SCRIPTS -->
        <!-- START PLUGINS -->
        <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>        
        <!-- END PLUGINS -->
        
        <!-- START THIS PAGE PLUGINS-->        
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
	
        
        <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script>        
        <!-- END THIS PAGE PLUGINS-->  
        
        <!-- START TEMPLATE -->
        
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->     
    
    </body>

</html>