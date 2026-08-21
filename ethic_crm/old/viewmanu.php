<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	if(isset($_REQUEST['msg']))
	{
		$msg="Job Completed Successfully!!!";
	}
	if(isset($_REQUEST['m_id']))
	{		
		$old1=mysqli_query($con,"select type from manufacturejob where m_id='$_REQUEST[m_id]' and status=0");	
		if($old=mysqli_fetch_row($old1))
		{
			if($old[0]=="Manufacturing")
			{
				$p1=mysqli_query($con,"select * from manu_fabric where m_id='$_REQUEST[m_id]'");
				while($p=mysqli_fetch_row($p1))
				{
					mysqli_query($con,"update variant set stock=stock+$p[3] where v_id='$p[2]'");
				}
				mysqli_query($con,"delete from manu_fabric where m_id='$_REQUEST[m_id]'");
			}
			else
			{
				$p1=mysqli_query($con,"select * from manu_product where m_id='$_REQUEST[m_id]'");
				while($p=mysqli_fetch_row($p1))
				{
					mysqli_query($con,"update variant set stock=stock+$p[3] where v_id='$p[2]'");
				}
				mysqli_query($con,"delete from manu_product where m_id='$_REQUEST[m_id]'");
			}
			mysqli_query($con,"delete from manufacturejob where m_id='$_REQUEST[m_id]'");
			$msg1="Job Deleted Successfully!!!";
		}
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
		var path="viewmanu.php?m_id="+row;
		window.open(path,"_self");
	}
		</script>  
		<script src="js\jquery.min.js"></script>           
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu15=true; $smenu15="2"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Job Master</a></li>
                    <li class="active">Pending Jobs</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2> View Pending Jobs</h2>
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
							<div class="alert alert-success" role="alert">
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
                                   <form class="form-horizontal" method="post" action="viewmanu.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
                                                <label class="col-md-2 col-xs-2">Jobber</label>
												<label class="col-md-2 col-xs-2">Job Type</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
											</div>
											<div class="row">
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="jobber">
														<option value=''>--Select--</option>
														<?php
															$l1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (33) order by name");
															while($l=mysqli_fetch_row($l1))
															{
																echo "<option value='$l[0]'>$l[1]</option>";
															}
														?>
													</select></div>
													<div class="col-md-2 col-xs-12">
													<select class="form-control" name="type">
														<option value=''>--Select--</option>
														<option>Manufacturing</option>
														<option>Emboidery</option>
														<option>Alteration</option>
														<option>Other</option>
													</select></div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dfrom"/></div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dto"/></div>
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
	if($_REQUEST['dfrom']!="") $dfrom=" and mdate>='$_REQUEST[dfrom]'"; else $dfrom="";
	if($_REQUEST['dto']!="") $dto=" and mdate<='$_REQUEST[dto]'"; else $dto="";
	if($_REQUEST['jobber']!="") $jobber=" and jobber='$_REQUEST[jobber]'"; else $jobber="";
	if($_REQUEST['type']!="") $type=" and type='$_REQUEST[type]'"; else $type="";
	
	$sql="select * from manufacturejob where status=0 ".$jobber." ".$dfrom." ".$dto." ".$type." order by mdate";
	
	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Details Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' cellpadding='3' width='100%' style='border-collapse:collapse; font-size:12px;'>
			<tr>
				<th>S.<br>No.</th>
				<th>Date</th>
				<th>Jobber</th>
				<th>Type</th>
				<th>Total Given Material Cost</th>
				<th>Remarks</th>
			</tr>";
?>
		<table class="table table-bordered table-actions">
			<thead>
				<tr>
					<th style="width:20px;">S.<br>No.</th>
					<th>Date</th>
					<th>Jobber</th>
					<th>Type</th>
					<th>Total Given<br>Material Cost</th>
					<th>Remarks</th>
					<th width='20'>Actions</th>
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
						$p1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$d[2]'");
                        if(!$j1=mysqli_fetch_row($p1)) $j1[0]="";

						if($d[6]=="Manufacturing")
						$sum=mysqli_fetch_row(mysqli_query($con,"select sum(qty*rate) from manu_fabric where m_id='$d[0]'"));
						else
						$sum=mysqli_fetch_row(mysqli_query($con,"select sum(qty*rate) from manu_product where m_id='$d[0]'"));
					?>
					<td><?php echo $j1[0]; ?></td>
					<td><?php echo $d[6]; ?></td>
					<td align='right'><?php echo number_format($sum[0],2); ?></td>
					<td><?php echo $d[3]; ?></td>
					
					<?php
						$table .= "<td>$j1[0]</td>
								<td>$d[6]</td>
								<td align='right'>".number_format($sum[0],2)."</td>
								<td>$d[3]</td>";
						$tot+=$sum[0];
					?>
					<td>
						<?php
							if($d[6]=="Manufacturing")
							{
						?>
						<button class="btn btn-success btn-rounded btn-condensed btn-sm" onClick="window.open('submit_manu2.php?m_id=<?php echo $d[0]; ?>','_self');" title="return"><span class="fa fa-arrow-left"></span></button>
						<?php
							}
							else
							{
						?>
						<button class="btn btn-success btn-rounded btn-condensed btn-sm" onClick="window.open('submit_manu1.php?m_id=<?php echo $d[0]; ?>','_self');" title="return"><span class="fa fa-arrow-left"></span></button>
						<?php
							}
						?>
						<button class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $d[0]; ?>');"><span class="fa fa-times"></span></button>
					</td>
				</tr>
			<?php
					$table .= "</tr>";
				$j++;
			}while($d = mysqli_fetch_array($result));
		}
		$table .="<tr>
			<td colspan='4'>Total</td>
			<td align='right'>".number_format($tot,2)."</td>
            <td></td>
		</tr></table>";
	?>			
		<tr style='font-weight:bold;'>
			<td colspan='4'>Total</td>
			<td align='right'><?php echo number_format($tot,2); ?></td>
			<td></td><td></td>
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
				 <input type="hidden" name="fn" value="Pending Jobs"/>
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