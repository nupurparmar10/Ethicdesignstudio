<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
    if(isset($_REQUEST['msg']))
	{
		$msg="Purchase Return Bill Modified Successfully!!!";
	}
	if(isset($_REQUEST['pur_id']))
	{
		$old=mysqli_fetch_row(mysqli_query($con,"select relatedwith from purreturn where pur_id='$_REQUEST[pur_id]'"));
		$col="w".$old[1];
		$p1=mysqli_query($con,"select * from pr_items where pur_id='$_REQUEST[pur_id]'");
		while($p=mysqli_fetch_row($p1))
		{
            mysqli_query($con,"update variant set stock=stock+$p[1] where v_id='$p[0]'");
		}
		mysqli_query($con,"delete from pr_items where pur_id='$_REQUEST[pur_id]'");
		mysqli_query($con,"delete from transaction where relatedto='$old[0]'");
		mysqli_query($con,"delete from purreturn where pur_id='$_REQUEST[pur_id]'");
		$msg1="Purchase Return Bill Deleted Successfully!!!";
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
		var path="viewpurreturn.php?pur_id="+row;
		window.open(path,"_self");
	}
		</script>  
		<script src="js\jquery.min.js"></script>           
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu9=true; $smenu9="8"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Purchase Master</a></li>
                    <li class="active">Purchase Return Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2> View Purchase Return  Details</h2>
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
                                   <form class="form-horizontal" method="post" action="viewpurreturn.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Party Name</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
												<label class="col-md-2 col-xs-2">Invoice No.</label>
												<label class="col-md-2 col-xs-2">Payment Mode</label>
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
	
	$sql="select * from purreturn where invno like '%$_REQUEST[invno]%' ".$paidby." ".$dfrom." ".$dto." ".$party." order by invdate desc";
	
	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Purchase Bill Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' cellpadding='3' width='100%' style='border-collapse:collapse; font-size:12px;'>
			<tr>
				<th>S.<br>No.</th>
				<th>Date</th>
				<th>Vendor Name</th>
				<th>Vendor Bill No.</th>
				<th>Vendor GST NO.</th>
				<th>Taxable Amount</th>
				<th>CGST</th>
				<th>SGST</th>
				<th>Tax Total</th>
				<th>Sp. Dis</th>
				<th>Transport Charge</th>
				<th>Freight Charge</th>
				<th>Other Charge</th>
				<th>Round Off</th>
				<th>Amount</th>
			</tr>";
?>
		<table class="table table-bordered table-actions">
			<thead>
				<tr>
					<th style="width:20px;">S.<br>No.</th>
					<th width='80'>Date</th>
					<th>Vendor<br>Name</th>
					<th>Vendor<br>Bill No.</th>
					<th>Vendor<br>GST NO.</th>
					<th>Taxable<br>Amount</th>
					<th>CGST</th>
					<th>SGST</th>
					<th>Tax<br>Total</th>
					<th>Sp. Dis</th>
					<th>Transport<br>Charge</th>
					<th>Freight<br>Charge</th>
					<th>Other<br>Charge</th>
					<th>Round<br>Off</th>
					<th>Amount</th>
					<th width='20'>Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($d = mysqli_fetch_row($result))
		{		
			$j=1;
			$tot=array('0','0','0','0','0','0','0','0','0','0');
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
						
						$g1=mysqli_query($con,"select tinno from ledger_details where ledger_id='$d[2]'");
						if(!$g=mysqli_fetch_row($g1)) $g[0]="";
						$tax=mysqli_fetch_row(mysqli_query($con,"select sum((stock*purrate) - (stock*purrate)*dis/100), sum(((stock*purrate) - (stock*purrate)*dis/100)*tax/100) from pr_items where pur_id=$d[0]"));
						$gst=round($tax[1]/2,2);
					?>
					<td><?php echo $p[0]; ?></td>
					<td><?php echo $d[3]; ?></td>
					<td><?php echo $g[0]; ?></td>
					<td align='right'><?php echo $tax[0]; ?></td>
					<td align='right'><?php echo $gst; ?></td>
					<td align='right'><?php echo $gst; ?></td>
					<td align='right'><?php echo $tax[1]; ?></td>
					<td align='right'><?php echo $d[6]; ?></td>
					<td align='right'><?php echo $d[7]; ?></td>
					<td align='right'><?php echo $d[8]; ?></td>
					<td align='right'><?php echo $d[9]; ?></td>
					<td align='right'><?php echo $d[10]; ?></td>
					<td align='right'><?php echo number_format($d[11],2); ?></td>
					<?php
						$table .= "<td>$p[0]</td>
								<td>$d[3]</td>
								<td>$g[0]</td>
								<td align='right'>$tax[0]</td>
								<td align='right'>$gst</td>
								<td align='right'>$gst</td>
								<td align='right'>$tax[1]</td>
								<td align='right'>$d[6]</td>
								<td align='right'>$d[7]</td>
								<td align='right'>$d[8]</td>
								<td align='right'>$d[9]</td>
								<td align='right'>$d[10]</td>
								<td align='right'>".number_format($d[11],2)."</td>";
						$tot[0]+=$tax[0];
						$tot[1]+=$gst;
						$tot[2]+=$gst;
						$tot[3]+=$tax[1];
						$tot[4]+=$d[6];
						$tot[5]+=$d[7];
						$tot[6]+=$d[8];
						$tot[7]+=$d[9];
						$tot[9]+=$d[11];
					?>
					<td>
						<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('purreturn.php?pur_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-pencil"></span></button>
						<button class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $d[0]; ?>');"><span class="fa fa-times"></span></button>
						<button class="btn btn-warning btn-rounded btn-condensed btn-sm" onClick="window.open('purreturndet.php?pur_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-list"></span></button>
					</td>
				</tr>
			<?php
					$table .= "</tr>";
				$j++;
			}while($d = mysqli_fetch_array($result));
		}
		$table .="<tr>
			<td colspan='5'>Total</td>
			<td align='right'>".number_format($tot[0],2)."</td>
			<td align='right'>".number_format($tot[1],2)."</td>
			<td align='right'>".number_format($tot[2],2)."</td>
			<td align='right'>".number_format($tot[3],2)."</td>
			<td align='right'>".number_format($tot[4],2)."</td>
			<td align='right'>".number_format($tot[5],2)."</td>
			<td align='right'>".number_format($tot[6],2)."</td>
			<td align='right'>".number_format($tot[7],2)."</td>
			<td align='right'>".number_format($tot[8],2)."</td>
			<td align='right'>".number_format($tot[9],2)."</td>
		</tr></table>";
	?>			
		<tr style='font-weight:bold;'>
			<td colspan='5'>Total</td>
			<td align='right'><?php echo number_format($tot[0],2); ?></td>
			<td align='right'><?php echo number_format($tot[1],2); ?></td>
			<td align='right'><?php echo number_format($tot[2],2); ?></td>
			<td align='right'><?php echo number_format($tot[3],2); ?></td>
			<td align='right'><?php echo number_format($tot[4],2); ?></td>
			<td align='right'><?php echo number_format($tot[5],2); ?></td>
			<td align='right'><?php echo number_format($tot[6],2); ?></td>
			<td align='right'><?php echo number_format($tot[7],2); ?></td>
			<td align='right'><?php echo number_format($tot[8],2); ?></td>
			<td align='right'><?php echo number_format($tot[9],2); ?></td>
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
				 <input type="hidden" name="fn" value="Purchase Bill Details"/>
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