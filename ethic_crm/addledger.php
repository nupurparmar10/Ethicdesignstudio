<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	if(isset($_REQUEST['s1']))
	{
		$name=$_REQUEST['lname'];
		$chk1=mysqli_query($con,"select * from ledger_accounts where name='$name'");
		if($chk=mysqli_fetch_row($chk1))
		{
			$msg1="Ledger Name Already Exists!!!";
		}
		else
		{
			$person=$_REQUEST['cperson'];
			$accgrp=$_REQUEST['accgroup'];
			$tinno=strtoupper($_REQUEST['tinno']);
			$mobile=$_REQUEST['mobile'];
			$email=$_REQUEST['email'];
			$opbal=$_REQUEST['opbal'];
			$address=$_REQUEST['address'];
			$g_id=mysqli_query($con,"select max(ledger_id) from ledger_accounts");
			$g=mysqli_fetch_row($g_id);
			$id=$g[0]+1;
			
			mysqli_query($con,"insert into ledger_accounts set ledger_id='$id', name='$name', group_id='$accgrp', opening_bal='$opbal'");
			
			if($person!=""  || $address!=""  || $tinno!="" || $mobile!="" ||  $email!="" )
			{
				mysqli_query($con,"insert into ledger_details set ledger_id=$id, contact_person='$person',address='$address', tinno='$tinno',  mobile='$mobile',  email='$email'");
			}
			
			$msg="Ledger added successfully!!!";
		}
	}
	if(isset($_REQUEST['s3']))
	{
		$name=$_REQUEST['lname'];
		$id=$_REQUEST['ledger_id'];
		$cur1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$id'");
		$cur=mysqli_fetch_row($cur1);
		$chk1=mysqli_query($con,"select * from ledger_accounts where name='$name' and name!='$cur[0]'");
		if($chk=mysqli_fetch_row($chk1))
		{
			$msg1="Ledger Name Already Exists!!!";
		}
		else
		{
			$name=$_REQUEST['lname'];
			$person=$_REQUEST['cperson'];
			$accgrp=$_REQUEST['accgroup'];
			$tinno=strtoupper($_REQUEST['tinno']);
			$mobile=$_REQUEST['mobile'];
			$email=$_REQUEST['email'];
			$address=$_REQUEST['address'];
			$opbal=$_REQUEST['opbal'];
			mysqli_query($con,"update ledger_accounts set name='$name', group_id='$accgrp' where ledger_id='$id'");
			$chk=mysqli_query($con,"select ledger_id from ledger_details where ledger_id=$id");
			if($c=mysqli_fetch_row($chk))
			{
				if($person!="" || $address!=""  || $tinno!=""  || $mobile!="" || $email!="" )
				{
					mysqli_query($con,"update ledger_details set contact_person='$person',  address='$address',  tinno='$tinno', , mobile='$mobile',  email='$email' , opening_bal='$opbal' where ledger_id=$id");
				}
				else
				{
					mysqli_query($con,"delete from ledger_details where ledger_id=$id");
				}
			}
			else
			{
				if($person!="" || $desgn!="" || $address!="" || $city!="" || $state!="" || $tinno!="" || $panno!="" || $mobile!="" || $fax!="" || $email!="" || $website!="")
				{
					mysqli_query($con,"insert into ledger_details set ledger_id=$id, contact_person='$person', address='$address', tinno='$tinno', mobile='$mobile', email='$email' , opening_bal='$opbal'");
				}
			}
			header("Location: viewledger.php?msg=set"); die;
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
		<script src="js\jquery.min.js"></script>  
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu3=true; $smenu3="2"; $ssmenu3="21";include_once("sidebar.php"); ?>
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
                    <li><a href="#">Accounts Master</a></li>
                    <li><a href="#">Ledger Accounts</a></li>
                    <li class="active"><?php if(isset($_REQUEST['ledger_id'])) echo "Modify Ledger Details";  else echo "Add New Ledger"; ?></li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                    <div class="row">
                        <div class="col-md-12">
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
                            <form class="form-horizontal" method="post" action="addledger.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									<?php
										if(isset($_REQUEST['ledger_id']))
										{
											$led1=mysqli_query($con,"select * from ledger_accounts where ledger_id=$_REQUEST[ledger_id]");
											$led=mysqli_fetch_row($led1);
											$ledger1=mysqli_query($con,"select * from ledger_details where ledger_id=$_REQUEST[ledger_id]");
											if($l=mysqli_fetch_row($ledger1)){}
											else
											{
												$l[1]=$l[2]=$l[3]=$l[4]=$l[5]=$l[6]=$l[7]=$l[8]=$l[9]=$l[10]=$l[11]=$l[12]="";	
											}
											echo "<input type='hidden' value='$_REQUEST[ledger_id]' name='ledger_id'/>";
									?>
                                    <h3 class="panel-title"><strong>Modify</strong> Ledger Details</h3>
									<?php
										}
										else
										{
										
											$l[1]=$l[2]=$l[3]=$l[4]=$l[5]=$l[6]=$l[7]=$l[8]=$l[9]=$l[10]=$l[11]=$l[12]="";
											$led[1]=$led[2]=""; $led[3]="0";
									?>
									 <h3 class="panel-title"><strong>Add New</strong> Ledger</h3>
									<?php
										}
									?>
                                </div>
                                <div class="panel-body">                                                                        
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Ledger Name</label>
                                                <div class="col-md-9">                                            
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                                        <input type="text" class="form-control" name="lname" value="<?php echo $led[1];?>" required onkeypress="return onlyCharacters(event);"/>
                                                    </div>                                            
                                                </div>
                                            </div>
                                            
                                           
											 <div class="form-group">
                                                <label class="col-md-3 control-label">GST No.</label>
                                                <div class="col-md-9">                                            
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                        <input type="text" class="form-control" name="tinno"  value="<?php echo $l[3];?>" style='text-transform:uppercase;' onkeyup="return allowOnly15AlphaNumeric(this);" oninput="allowOnly15AlphaNumeric(this);" />
														<span id="gstError" style="color: red; font-size: 14px;"></span>
                                                    </div>                                            
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Mobile No.</label>
                                                <div class="col-md-9">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-mobile"></span></span>                                                        <input type="text" class="form-control" value="<?php echo $l[4];?>" name="mobile" onkeyup="return allowOnly10Numeric(this);" oninput="allowOnly10Numeric(this);">
														<span id="mobileError" style="color: red; font-size: 14px;"></span>                                  
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Address</label>
                                                <div class="col-md-9 col-xs-12">                                            
                                                    <textarea class="form-control" rows="5" name="address"><?php echo $l[2]; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                <label class="col-md-3 control-label">Contact Person</label>
                                                <div class="col-md-9">                                            
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                                        <input type="text" class="form-control" name="cperson" onkeypress="return onlyCharacters(event);" value="<?php echo $l[1];?>"/>
                                                    </div>                                            
                                                </div>
                                            </div>
											 <div class="form-group">                                        
                                                <label class="col-md-3 control-label">Account Group</label>
                                                <div class="col-md-9">                                                                                            
													<?php
													$list1=mysqli_query($con,"select group_id,group_name from group_master order by group_name");
													if($list=mysqli_fetch_row($list1))
													{
														echo "<select class='form-control select' name='accgroup' required>";
														do{
															if($led[2]==$list[0])
															echo "<option value='$list[0]' selected='selected'>$list[1]</option>";
															else
															echo "<option value='$list[0]'>$list[1]</option>";
														}while($list=mysqli_fetch_row($list1));
														echo "</select>";
													}
													?>
                                                </div>
                                            </div>
											
											 <div class="form-group">
                                                <label class="col-md-3 control-label">Email ID</label>
                                                <div class="col-md-9">                                            
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-envelope"></span></span>
                                                        <input type="email" class="form-control" name="email" value="<?php echo $l[5];?>"/>
                                                    </div>                                            
                                                </div>
                                            </div>
											<div class="form-group">
                                                <label class="col-md-3 control-label">Op. Balance</label>
                                                <div class="col-md-9">                                            
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-inr"></span></span>
                                                        <input type="text" class="form-control" name="opbal" value="<?php echo $led[3];?>"/>
                                                    </div>                                            
                                                </div>
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
									<button class="btn btn-primary" type="submit" name="s1">Create</button>
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