<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	if(isset($_REQUEST['s1']))
	{
		$oldpwd=md5($_REQUEST['oldpwd']);
		$newpwd=md5($_REQUEST['newpwd']);
		$query=mysqli_query($con,"select * from login where password='".$oldpwd."' and acc_id='$_SESSION[account]'");
		if($q=mysqli_fetch_row($query))
		{
			mysqli_query($con,"update login set password='".$newpwd."' where acc_id='$_SESSION[account]'");
			$msg="Password changed successfully!";
		}
		else
			$msg1="Old Password is incorrect!";
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
        <link rel="stylesheet" type="text/css" href="css/cropper/cropper.min.css"/>
        <!--  EOF CSS INCLUDE -->        
        
        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->  
		<script src="js\jquery.min.js"></script>                                 
    </head>
	<script language="javascript">
	function check()
	{
		var1=document.getElementById('newpwd').value;
		var2=document.getElementById('confirmpwd').value;
		
		if(var1!=var2)
		{
			var str = "<div class='row'><div class='col-md-12'><div class='alert alert-info' role='alert'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>×</span><span class='sr-only'>Close</span></button>New Password & Confirm Password mismatch!!!</div></div></div>";
			
			$("#msg").html(str);
			document.getElementById('confirmpwd').focus();
			return false;
		}
		return true;
	}
	</script>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
              <?php include_once("sidebar.php"); ?>
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
                    <li class="active">Change Password</li> 
                </ul>
                <!-- END BREADCRUMB -->                                                
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-cogs"></span> Change Password</h2>
                </div>
                <!-- END PAGE TITLE -->                     
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    <?php
						if($msg)
							{
					?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                              	<?php echo $msg;?>
                            </div>                            
                        </div>
                    </div>   
					<?php
						}
					?> 
					<?php
						if($msg1)
							{
					?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                              	<?php echo $msg1;?>
                            </div>                            
                        </div>
                    </div>   
					<?php
						}
					?> 
					<div id="msg">
					</div> 
                    <div class="row">                        
                        <div class="col-md-3">
                                                        
                        </div>
                        <div class="col-md-6 col-sm-12">
                            
                            <form action="admin_edit.php" class="form-horizontal" method="post" enctype="multipart/form-data" onSubmit="return check();">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h3><span class="fa fa-pencil"></span> Change Password</h3>
                                   
                                </div>
                                <div class="panel-body form-group-separated">
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-5 control-label">Old Password</label>
                                        <div class="col-md-9 col-xs-7">
                                            <input type="password" name="oldpwd" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-5 control-label">New Password</label>
                                        <div class="col-md-9 col-xs-7">
                                            <input type="password" name="newpwd" id="newpwd" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-5 control-label">Confirm Password</label>
                                        <div class="col-md-9 col-xs-7">
                                            <input type="password" name="confirmpwd" id="confirmpwd" class="form-control" required/>
                                        </div>
                                    </div>
                                                                          
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-5">
                                            <button class="btn btn-primary btn-rounded pull-right" name="s1">Change</button>
                                        </div>
                                    </div>
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
        
        <script type="text/javascript" src="js/plugins/jquery/jquery-migrate.min.js"></script>
        <!-- END PLUGINS -->

        <!-- START THIS PAGE PLUGINS-->        
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>  
        
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
        <script type="text/javascript" src="js/plugins/form/jquery.form.js"></script>
        
        <script type="text/javascript" src="js/plugins/cropper/cropper.min.js"></script>
        <!-- END THIS PAGE PLUGINS-->        

        <!-- START TEMPLATE -->
                
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>
        
        <script type="text/javascript" src="js/demo_edit_profile.js"></script>
        <!-- END TEMPLATE -->

    <!-- END SCRIPTS -->         
    
   
    </body>
</html>