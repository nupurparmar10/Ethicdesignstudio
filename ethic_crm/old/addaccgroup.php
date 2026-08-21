<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";
	if(isset($_REQUEST['s1']))
	{
		$id=mysqli_fetch_row(mysqli_query($con,"select max(group_id) from group_master"));
		$id[0]+=1;
		mysqli_query($con,"insert into group_master set group_id='$id[0]', group_name='$_REQUEST[group]', under_group='$_REQUEST[undergrp]'");
		$msg="Account Group Created Successfully!!!";
	}
	if(isset($_REQUEST['s3']))
	{
		mysqli_query($con,"update group_master set group_name='$_REQUEST[group]', under_group='$_REQUEST[undergrp]' where group_id='$_REQUEST[g_id]'");
		header("Location: viewaccgroup.php?msg=set"); die;
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
             <?php $menu3=true; $smenu3="1"; $ssmenu3="11"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Account Groups</a></li>
                    <li class="active"><?php if(isset($_REQUEST['g_id'])) {?>Modify Group<?php } else { ?>Create Group<?php }?></li>
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
                            <form class="form-horizontal" method="post" action="addaccgroup.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									<?php
										if(isset($_REQUEST['g_id']))
										{
											$d1=mysqli_query($con,"select * from group_master where group_id='$_REQUEST[g_id]'");
											$d=mysqli_fetch_row($d1);
											echo "<input type='hidden' name='g_id' value='$_REQUEST[g_id]'/>";
									?>
                                    <h3 class="panel-title"><strong>Modify</strong> Group</h3>
									<?php
										}
										else
										{
											$d[1]=$d[2]="";
									?>
									 <h3 class="panel-title"><strong>Create</strong> Group</h3>
									<?php
										}
									?>
                                </div>
                                <div class="panel-body">                                                                        
                                     <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Main Group</label>
                                        <div class="col-md-6 col-xs-12">                                                                                            
                                            <select class="form-control select" name="undergrp" required>
                                                <option value="">--Select--</option>
												<?php
													$e1=mysqli_query($con,"select group_id,group_name from group_master order by group_name");
													while($e=mysqli_fetch_row($e1))
													{
														if($d[2]==$e[0])
														echo "<option value='$e[0]' selected='selected'>$e[1]</option>";
														else
														echo "<option value='$e[0]'>$e[1]</option>";
													}
												?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Sub Group</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="group" value='<?php echo $d[1]; ?>'/>
                                            </div>                                            
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="panel-footer">
									<?php
										if(isset($_REQUEST['g_id']))
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