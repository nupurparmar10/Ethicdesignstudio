<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	
	if(isset($_REQUEST['s1']))
	{
		$cmp1=mysqli_query($con,"select max(a_id) from empadvance");
		$cmp=mysqli_fetch_row($cmp1);
		$id=$cmp[0]+1;
		
		$rid="ADV".$id;
		mysqli_query($con,"insert into empadvance set a_id=$id, adate='$_REQUEST[adate]', ledger_id='$_REQUEST[ledger_id]', amount='$_REQUEST[amount]', reason='$_REQUEST[reason]', relatedwith='$rid', paidby='$_REQUEST[paidby]'");
		
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
				
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$_REQUEST['adate']."', ledger_id='".$_REQUEST['ledger_id']."', amount='".$_REQUEST['amount']."', particulars='Advance Salary Taken', type='Dr.', relatedto='$rid'");
		$tid++;
		
		$emp=mysqli_fetch_row(mysqli_query($con,"select name from ledger_accounts where ledger_id='$_REQUEST[ledger_id]'"));
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$_REQUEST['adate']."', ledger_id='".$_REQUEST['paidby']."', amount='".$_REQUEST['amount']."', particulars='Advance Salary Paid to $emp[0]', type='Cr.', relatedto='$rid'");
		mysqli_query($con,"update empdet set advance=advance+$_REQUEST[amount] where  ledger_id='$_REQUEST[ledger_id]'");
		$msg="Advance Salary Entry Saved Successfully!!!";
	}
	if(isset($_REQUEST['s3']))
	{
		$rid="ADV".$_REQUEST['a_id'];
		$old=mysqli_fetch_row(mysqli_query($con,"select * from empadvance where a_id='$_REQUEST[a_id]'"));
		mysqli_query($con,"update empdet set advance=advance-$old[3] where  ledger_id='$old[1]'");
		
		mysqli_query($con,"update empadvance set adate='$_REQUEST[adate]', ledger_id='$_REQUEST[ledger_id]', amount='$_REQUEST[amount]', reason='$_REQUEST[reason]', paidby='$_REQUEST[paidby]' where a_id='$_REQUEST[a_id]'");
		mysqli_query($con,"delete from transaction where relatedto='$rid'");
		
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
		
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$_REQUEST['adate']."', ledger_id='".$_REQUEST['ledger_id']."', amount='".$_REQUEST['amount']."', particulars='Advance Salary Taken', type='Dr.', relatedto='$rid'");
		$tid++;
		
		$emp=mysqli_fetch_row(mysqli_query($con,"select name from ledger_accounts where ledger_id='$_REQUEST[ledger_id]'"));
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$_REQUEST['adate']."', ledger_id='".$_REQUEST['paidby']."', amount='".$_REQUEST['amount']."', particulars='Advance Salary Paid to $emp[0]', type='Cr.', relatedto='$rid'");
		mysqli_query($con,"update empdet set advance=advance+$_REQUEST[amount] where  ledger_id='$_REQUEST[ledger_id]'");
		header("Location: viewadvance.php?msg=set"); die;
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
		<script src="js\jquery.min.js"></script>    
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu8=true; $smenu8="5"; $ssmenu8="51"; include_once("sidebar.php"); ?>
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
				    <li><a href="#">Employee Master</a></li>
                    <li><a href="#">Advance Salary</a></li>
                    <li class="active">Give Advance</li>
                </ul>
                <!-- END BREADCRUMB -->
                
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
							<div class="alert alert-warning" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg1; ?></strong>
							</div>
							<?php 
								}
							?>
                            <form class="form-horizontal" method="post" action="giveadvance.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									 <h3 class="panel-title"><strong>Give </strong> Advance</h3>
                                </div>
								<?php
									if(isset($_REQUEST['a_id']))
									{
										$a=mysqli_fetch_row(mysqli_query($con,"select * from empadvance where a_id='$_REQUEST[a_id]'"));
										echo "<input type='hidden' name='a_id' value='$a[0]'/>";
									}
									else
									{
										$a[1]=$a[2]=$a[3]=$a[4]=$a[5]="";
										$a[2]=date("Y-m-d");
									}
								?>
                                <div class="panel-body">   
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Date</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                <input type="date" class="form-control" name="adate" value='<?php echo $a[2]; ?>' required />
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Employee</label>
                                        <div class="col-md-6 col-xs-12">   
										<select class='form-control' name='ledger_id' required>
											<option value="">--Select--</option>
										<?php
												$list1=mysqli_query($con,"select ledger_id,empname from empdet where status=1 order by empname");
												while($list=mysqli_fetch_row($list1))
												{
													if($a[1]==$list[0])
														echo "<option value='$list[0]' selected>$list[1]</option>";
													else
														echo "<option value='$list[0]'>$list[1]</option>";
												}
												?>       
										</select>												
										</div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Advance Amount</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                <input type="text" class="form-control" name="amount" value='<?php echo $a[3]; ?>'required />
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Reason</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <textarea class="form-control" rows="3" name="reason"><?php echo $a[4]; ?></textarea>
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Paid By</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <select class="form-control" name="paidby" required>
												<option value=''>--Select--</option>
												<?php
													$list1=mysqli_query($con,"select ledger_id,name from ledger_accounts  where status=1 and (name like 'Cash Account' or group_id in (select group_id from group_master where group_name like 'Bank Accounts')) order by name");
													if($l=mysqli_fetch_row($list1))
													{
														do{
															if($l[0]==$a[5])
																echo "<option value='$l[0]' selected>$l[1]</option>";
															else
																echo "<option value='$l[0]'>$l[1]</option>";
														}while($l=mysqli_fetch_row($list1));
													}
												?>
											</select>
                                        </div>
                                    </div> 
                                </div>
                                <div class="panel-footer">
									<?php
										if(isset($_REQUEST['a_id']))
										{
									?>
									<button class="btn btn-primary" type="submit" name="s3">Modify</button>
									<?php
										}
										else
										{
									?>
									<button class="btn btn-primary" type="submit" name="s1">Give Advance</button>
									<?php
										}
									?>
                                    <button class="btn btn-default">Clear Form</button>                                    
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>                    
                    
                </div>
                <!-- END PAGE CONTENT WRAPPER -->                                                
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->
        
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