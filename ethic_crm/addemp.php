<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	if(isset($_REQUEST['s1']))
	{
		$g_id=mysqli_query($con,"select max(ledger_id) from ledger_accounts");
		$g=mysqli_fetch_row($g_id);
		$id=$g[0]+1;
		$a1=mysqli_query($con,"select group_id from group_master where group_name='Employees'");
		$a=mysqli_fetch_row($a1);
		mysqli_query($con,"insert into ledger_accounts set ledger_id=$id, name='$_REQUEST[empname]', group_id='$a[0]', opening_bal='0', status=1");
		mysqli_query($con,"insert into empdet set ledger_id='$id', empname='$_REQUEST[empname]', post='$_REQUEST[post]', mobile='$_REQUEST[mobile]', address='$_REQUEST[address]', aadharno='$_REQUEST[aadharno]', salary='$_REQUEST[salary]', daysallowed='$_REQUEST[daysallowed]', status=1");
		$msg="Employee added successfully!!!";
	}
	if(isset($_REQUEST['s3']))
	{
		$id=$_REQUEST['ledger_id'];
		mysqli_query($con,"update ledger_accounts set name='$_REQUEST[empname]' where ledger_id='$id'");
		mysqli_query($con,"update empdet set empname='$_REQUEST[empname]', post='$_REQUEST[post]', mobile='$_REQUEST[mobile]', address='$_REQUEST[address]', aadharno='$_REQUEST[aadharno]', salary='$_REQUEST[salary]', daysallowed='$_REQUEST[daysallowed]' where ledger_id='$id'");
		header("Location: viewemp.php?msg=set"); die;
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
             <?php $menu8=true; $smenu8="2"; $ssmenu8="21"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Manage Employees</a></li>
                    <li class="active"><?php if(isset($_REQUEST['ledger_id'])) {?>Modify Employee<?php } else { ?>Add Employee<?php }?></li>
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
							<div class="alert alert-danger" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg1; ?></strong>
							</div>
							<?php 
								}
							?>
                            <form class="form-horizontal" method="post" action="addemp.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									<?php
										if(isset($_REQUEST['ledger_id']))
										{
											$c1=mysqli_query($con,"select * from empdet where ledger_id='$_REQUEST[ledger_id]'");
											$c=mysqli_fetch_row($c1);											
											echo "<input type='hidden' name='ledger_id' value='$_REQUEST[ledger_id]'/>";
									?>
                                    <h3 class="panel-title"><strong>Modify</strong> Employee</h3>
									<?php
										}
										else
										{
											$c[1]=$c[2]=$c[3]=$c[4]=$c[5]=$c[6]=$c[7]="";
									?>
									 <h3 class="panel-title"><strong>Add New</strong> Employee</h3>
									<?php
										}
									?>
                                </div>
                                <div class="panel-body">   
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Employee Name</label>
                                        <div class="col-md-6 col-xs-12">       
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="empname" value='<?php echo $c[1]; ?>' required onkeypress="return onlyCharacters(event);" />
                                            </div>                                            
                                        </div>
                                    </div> 
									
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Post/ Designation</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="post" value='<?php echo $c[2]; ?>' required/>
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Aadhar No.</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="aadharno" value='<?php echo $c[3]; ?>' onkeyup="return allowOnly12Numeric(this);" oninput="allowOnly12Numeric(this);"/>
                                                <span id="aadharError" style="color: red; font-size: 14px;"></span>
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Mobile No.</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="mobile" value='<?php echo $c[4]; ?>' required onkeyup="return allowOnly10Numeric(this);" oninput="allowOnly10Numeric(this);"/>
                                                <span id="mobileError" style="color: red; font-size: 14px;"></span>
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Address</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="address" value='<?php echo $c[5]; ?>' />
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Salary</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="salary" value='<?php echo $c[6]; ?>' required/>
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">No. of Days Allowed to Take Leave</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="daysallowed" value='<?php echo $c[7]; ?>' required/>
                                            </div>                                            
                                        </div>
                                    </div> 
                                </div>
                                <div class="panel-footer">
									<?php
										if(isset($_REQUEST['ledger_id']))
										{
									?>
									<button class="btn btn-primary" type="submit" name="s3">Modify</button>
									<?php
										}
										else
										{
									?>
									<button class="btn btn-primary" type="submit" name="s1">Add</button>
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