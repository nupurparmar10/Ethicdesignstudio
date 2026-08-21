<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	if(isset($_REQUEST['msg']))
	{
		$msg="Sale Return Bill Modified Successfully!!!";
	}
	if(isset($_REQUEST['sale_id']))
	{
		$old=mysqli_fetch_row(mysqli_query($con,"select relatedwith from billreturn where sale_id='$_REQUEST[sale_id]'"));
		$p1=mysqli_query($con,"select * from sr_items where sale_id='$_REQUEST[sale_id]'");
		while($p=mysqli_fetch_row($p1))
		{
			mysqli_query($con,"update variant set stock=stock+$p[2], webstock=webstock+$p[2] where v_id='$p[1]'");
		}
		mysqli_query($con,"delete from sr_items where sale_id='$_REQUEST[sale_id]'");
		mysqli_query($con,"delete from transaction where relatedto='$old[0]'");
		mysqli_query($con,"delete from billreturn where sale_id='$_REQUEST[sale_id]'");
		
		$msg1="Sale Return Bill Deleted Successfully!!!";
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
		var path="viewsalereturn.php?sale_id="+row;
		window.open(path,"_self");
	}
		</script>  
		<script src="js\jquery.min.js"></script>           
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu10=true; $smenu10="8"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Sales Master</a></li>
                    <li class="active">Sale Return Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2> View Sale Return Details</h2>
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
							<?php
								if($msg2)
								{
							?>
							<div class="alert alert-warning" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg2; ?></strong>
							</div>
							<?php 
								}
							?>
                            
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                
                                <div class="panel-body">
                                   <form class="form-horizontal" method="post" action="viewsalereturn.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Party Name</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
												<label class="col-md-2 col-xs-2">Return Invoice No.</label>
												<label class="col-md-2 col-xs-2">Payment Mode</label>
												<label class="col-md-2 col-xs-2">GST Type</label>
											</div>
											<div class="row">
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="party">
														<option value=''>--Select--</option>
														<?php
															$l1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (26,27) order by name");
															while($l=mysqli_fetch_row($l1))
															{
																echo "<option value='$l[0]'>$l[1]</option>";
															}
														?>
													</select></div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dfrom"/></div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dto"/></div>
												<div class="col-md-2 col-xs-2"><input type="text" class="form-control" name="invno"/></div>
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="paidby">
														<option value="">--Select--</option>
														<option value="Credit">Credit</option>
														<?php
															$list1=mysqli_query($con,"select ledger_id,name from ledger_accounts where (group_id in (select group_id from group_master where group_name='Bank Accounts') or name='Cash Account') and status=1 order by name");
															if($l=mysqli_fetch_row($list1))
															{
																do{
																	echo "<option value='$l[0]'>$l[1]</option>";
																}while($l=mysqli_fetch_row($list1));
															}
														?>
													</select></div>
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="taxtype">
														<option value=''>--Select--</option>
														<option>GST</option>
														<option>IGST</option>
													</select></div>
											</div>
											<div class="row">
												<label class="col-md-2 col-xs-2">Sales Person</label>
											</div>
											<div class="row">
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="emp_id">
														<option value=''>--Select--</option>
														<?php
															$l1=mysqli_query($con,"select * from empdet where status=1 order by empname");
															while($l=mysqli_fetch_row($l1))
															{
																echo "<option value='$l[0]'>$l[1]</option>";
															}
														?>
													</select></div>
												
												<div class="col-md-2 col-xs-2"> 
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
	if($_REQUEST['dfrom']!="") $dfrom=" and invdate>='$_REQUEST[dfrom]'"; else $dfrom="";
	if($_REQUEST['dto']!="") $dto=" and invdate<='$_REQUEST[dto]'"; else $dto="";
	if($_REQUEST['party']!="") $party=" and party='$_REQUEST[party]'"; else $party="";
	if($_REQUEST['paidby']!="") $paidby=" and paidby='$_REQUEST[paidby]'"; else $paidby="";
	if($_REQUEST['taxtype']!="") $taxtype=" and taxtype='$_REQUEST[taxtype]'"; else $taxtype="";
	if($_REQUEST['emp_id']!="") $emp_id=" and emp_id='$_REQUEST[emp_id]'"; else $emp_id="";
	
	$sql="select * from billreturn where invno like '%$_REQUEST[invno]%' ".$paidby." ".$dfrom." ".$dto." ".$party." ".$taxtype." ".$emp_id." order by invdate desc,sale_id desc";
	
	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Sales Bill Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' cellpadding='3' width='100%' style='border-collapse:collapse; font-size:12px;'>
			<tr>
				<th>S.<br>No.</th>
				<th>Date</th>
				<th>Party Name</th>
				<th>Return Invoice No.</th>
				<th>Payment Mode</th>
                <th>Against Invoice No.</th>
				<th>Remark</th>
				<th>Amount</th>
				<th>GST Type</th>
				<th>Sales Person</th>
				<th>Total Commission</th>
			</tr>";
?>
		<table class="table table-bordered table-actions">
			<thead>
				<tr>
					<th style="width:20px;">S.<br>No.</th>
                    <th width='80'>Date</th>
                    <th>Party<br> Name</th>
                    <th>Return<br> Invoice No.</th>
                    <th>Payment<br> Mode</th>
                    <th>Against Invoice No.</th>
                    <th>Remark</th>
                    <th>Amount</th>
                    <th>GST Type</th>
                    <th>Sales<br> Person</th>
                    <th>Total<br>Comm.</th>
					<th width='20'>Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($d = mysqli_fetch_row($result))
		{		
			$j=1;
			$tot=0;
			$tot1=0;
			do
			{
				$table .= "<tr>";
			?>
				 <tr id="<?php echo $d[0]; ?>">
					<td><?php echo $j;?></td>
					<?php
						$table .= "<td>$j</td>";
						if($d[1]!="0000-00-00")
						{
							$date= DateTime::createFromFormat('Y-m-d', $d[1]);
							echo "<td>".$date->format('d-m-Y')."</td>";
							$table .="<td>".$date->format('d-m-Y')."</td>";
						}
						else
						{
							echo "<td>&nbsp;</td>";
							$table .= "<td>&nbsp;</td>";
						}
						$p=mysqli_fetch_row(mysqli_query($con,"select name from ledger_accounts where ledger_id='$d[2]'"));
						
						if($d[5]!="Credit")
						{
							$str=mysqli_fetch_row(mysqli_query($con,"select name from ledger_accounts where ledger_id='$d[5]'"));
							$d[5]=$str[0];
						}
						$emp1=mysqli_query($con,"select empname from empdet where ledger_id='$d[16]'");
						if($emp=mysqli_fetch_row($emp1)) {} else $emp[0]="";
						$comm=$d[17]*($d[12]+$d[7]-$d[8]-$d[9]-$d[10]+$d[11])/100;

					?>
					<td><?php echo $p[0]; ?></td>
					<td><?php echo $d[3]; ?></td>
                    <td><?php echo $d[5]; ?></td>
                    <td><?php echo $d[15]; ?></td>
					<td><?php echo $d[4]; ?></td>
					
                   
					<td align='right'><?php echo number_format($d[12],2); ?></td>
					<td><?php echo $d[14]; ?></td>
					<td><?php echo $emp[0]; ?></td>
					<td align='right'><?php echo number_format($comm,2); ?></td>
					<?php
						$table .= "<td>$p[0]</td>
								<td>$d[3]</td>
								<td>$d[5]</td>
								<td>$d[15]</td>
                                <td>$d[4]</td>
								<td align='right'>".number_format($d[12],2)."</td>
								<td>$d[14]</td>
								<td>$emp[0]</td>
								<td align='right'>".number_format($comm,2)."</td>";
						$tot+=$d[12];
						$tot1+=$comm;
					?>
					<td>
						<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('salereturn.php?sale_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-pencil"></span></button>
						
						<button class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $d[0]; ?>');"><span class="fa fa-times"></span></button>
						<button class="btn btn-warning btn-rounded btn-condensed btn-sm" onClick="window.open('salereturndet.php?sale_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-list"></span></button>
						<?php
							if($d[16]=="GST")
							{
						?>
						<button class="btn btn-success btn-rounded btn-condensed btn-sm" onClick="window.open('printcrinvoice.php?sale_id=<?php echo $d[0]; ?>','_blank');"><span class="fa fa-print"></span></button>
						
						<?php
							}
							else
							{
						?>
						<button class="btn btn-success btn-rounded btn-condensed btn-sm" onClick="window.open('printcrinvoice1.php?sale_id=<?php echo $d[0]; ?>','_blank');"><span class="fa fa-print"></span></button>
						
						<?php
							}
						?>
						
					</td>
				</tr>
			<?php
					$table .= "</tr>";
				$j++;
			}while($d = mysqli_fetch_array($result));
		}
		$table .="<tr>
			<td colspan='7'>Total</td>
			<td align='right'>".number_format($tot,2)."</td>
			<td></td>
			<td></td>
			<td align='right'>".number_format($tot1,2)."</td>
		</tr></table>";
	?>			
		<tr>
			<td colspan='7'>Total</td>
			<td align='right'><?php echo number_format($tot,2); ?></td>
			<td></td>
			<td></td>
			<td align='right'><?php echo number_format($tot1,2); ?></td>
			<td></td>
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
				 <input type="hidden" name="fn" value="Sale Bill Details"/>
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
        
        <!-- THIS PAGE PLUGINS -->
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>                
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
        <script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
        <!-- END THIS PAGE PLUGINS -->       
        
        <!-- START TEMPLATE -->
        
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->   
    </body>
</html>