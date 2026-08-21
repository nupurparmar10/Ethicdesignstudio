<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	if(isset($_REQUEST['s1']))
	{
		$bname=$_REQUEST['bname'];
		$contact=$_REQUEST['contact'];
		$branch=$_REQUEST['branch'];
		$accno=$_REQUEST['accno'];
		$address=$_REQUEST['address'];
		$code=$_REQUEST['ifsccode'];
		$lid1=mysqli_query($con,"select max(ledger_id) from ledger_accounts");
		$l=mysqli_fetch_row($lid1);
		$id=$l[0]+1;
		$a1=mysqli_query($con,"select group_id from group_master where group_name='Bank Accounts'");
		$a=mysqli_fetch_row($a1);
		mysqli_query($con,"insert into ledger_accounts set ledger_id=$id, name='$bname', group_id='$a[0]', opening_bal='$_REQUEST[opbal]'");
		mysqli_query($con,"insert into bank_details set ledger_id=$id, bank_name='$bname', contact='$contact', address='$address', branch='$branch', ifsccode='$code', accno='$accno'");
		$msg="Bank added successfully!!!";
	}
	if(isset($_REQUEST['s3']))
	{
		$bname=$_REQUEST['bname'];
		$contact=$_REQUEST['contact'];
		$branch=$_REQUEST['branch'];
		$accno=$_REQUEST['accno'];
		$address=$_REQUEST['address'];
		$code=$_REQUEST['ifsccode'];
		$id=$_REQUEST['ledger_id'];
		mysqli_query($con,"update ledger_accounts set name='$bname', opening_bal='$_REQUEST[opbal]' where ledger_id='$id'");
		mysqli_query($con,"update bank_details set bank_name='$bname', contact='$contact', address='$address', branch='$branch', ifsccode='$code', accno='$accno' where ledger_id=$id");
		header("Location: viewbank.php?msg=set");die;
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
             <?php $menu4=true; $smenu4="1";$ssmenu4="11"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Bank Master</a></li>
                    <li class="active"><?php if(isset($_REQUEST['ledger_id'])) {?>Modify Bank Account<?php } else { ?>Add Bank Account<?php }?></li>
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
                            <form class="form-horizontal" method="post" action="addbank.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									<?php
										if(isset($_REQUEST['ledger_id']))
										{
											$ledger1=mysqli_query($con,"select * from bank_details where ledger_id=$_REQUEST[ledger_id]");
											$d=mysqli_fetch_row($ledger1);
											$opbal=mysqli_fetch_row(mysqli_query($con,"select opening_bal from ledger_accounts where ledger_id='$_REQUEST[ledger_id]'"));
											echo "<input type='hidden' value='$_REQUEST[ledger_id]' name='ledger_id'/>";
									?>
                                    <h3 class="panel-title"><strong>Modify</strong> Bank Account</h3>
									<?php
										}
										else
										{
											$d[1]=$d[2]=$d[3]=$d[4]=$d[5]=$d[6]=$opbal[0]="";
									?>
									 <h3 class="panel-title"><strong>Add New</strong> Bank Account</h3>
									<?php
										}
									?>
                                </div>
                                <div class="panel-body">   
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Bank Name</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="bname" value='<?php echo $d[1]; ?>' required />
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">IFSC Code</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="ifsccode" value='<?php echo $d[2]; ?>' required maxlength='11'/>
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Account No.</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="accno" value='<?php echo $d[5]; ?>' required />
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Contact No.</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="contact" value='<?php echo $d[6]; ?>' />
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Branch</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="branch" value='<?php echo $d[3]; ?>' />
                                            </div>                                            
                                        </div>
                                    </div> 
                                     <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Address</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group col-md-12">
                                                <textarea class="form-control" rows="5" name="address"><?php echo $d[4]; ?></textarea>
                                            </div>                                            
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Opening Balance</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="opbal" value='<?php echo $opbal[0]; ?>' required/>
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