<?php
	ob_start();
	session_start();
	include_once("connect.php");
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
             <?php $menu3=true; $smenu3="2"; $ssmenu3="22"; include_once("sidebar.php"); ?>
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
                    <li class="active">Ledger Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                    <div class="row">
                        <div class="col-md-12">
                            <form class="form-horizontal" method="post" action="viewleddet.php" enctype="multipart/form-data">
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
									?>
                                    <h3 class="panel-title"><strong>Ledger</strong> Details</h3>
									<?php
										}
										else
										{
											header("Location: viewledger.php"); die;
										}
									?>
                                </div>
                                <div class="panel-body">                                                                        
                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            
                                            <div class="form-group">
                                                <label class="col-md-4 control-label"><strong>Ledger Name :</strong></label>
                                                <div class="col-md-8 control-label" style="text-align:left;">                                            
                                                   <?php echo $led[1];?>                                          
                                                </div>
                                            </div>
											
											 <div class="form-group">
                                                <label class="col-md-4 control-label"><strong>Designation :</strong></label>
                                                 <div class="col-md-8 control-label" style="text-align:left;">                                            
                                                   <?php echo $l[2];?>                                          
                                                </div>
                                            </div>
											 
                                            
											<div class="form-group">                                        
                                                <label class="col-md-4 control-label"><strong>GST No. :</strong></label>
                                                 <div class="col-md-8 control-label" style="text-align:left;">                                            
                                                  <?php echo $l[6];?>                                      
                                                </div>
                                            </div>
											
											 <div class="form-group">
                                                <label class="col-md-4 control-label"><strong>Phone No. :</strong></label>
                                                <div class="col-md-8 control-label" style="text-align:left;">                                            
                                                  <?php echo $l[8];?>                                      
                                                </div>
                                            </div>
											 
											<div class="form-group">
                                                <label class="col-md-4 control-label"><strong>Fax :</strong></label>
                                                <div class="col-md-8 control-label" style="text-align:left;">                                            
                                                  <?php echo $l[10];?>                                      
                                                </div>
                                            </div>
											
											<div class="form-group">
                                                <label class="col-md-4 control-label"><strong>Website :</strong></label>
												 <div class="col-md-8 control-label" style="text-align:left;">                                            
													 <?php echo $l[12];?>                                          
													</div>                                                                                          
                                            </div>
											
											
                                            <div class="form-group">
                                                <label class="col-md-4 control-label"><strong>City :</strong></label>
                                                 <div class="col-md-8 control-label" style="text-align:left;">                                            
                                                   <?php echo $l[4];?>                                          
                                                </div>
                                            </div>
                                            
                                           <div class="form-group">
                                                <label class="col-md-4 control-label"><strong>State :</strong></label>
                                                 <div class="col-md-8 control-label" style="text-align:left;">                                            
                                                   <?php echo $l[5];?>                                          
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                <label class="col-md-5 control-label"><strong>Contact Person :</strong></label>
                                                 <div class="col-md-7 control-label" style="text-align:left;">                                            
                                                   <?php echo $l[1];?>                                          
                                                </div>
                                            </div>
											 
											<div class="form-group">
                                                <label class="col-md-5 control-label"><strong>Account Group :</strong></label>
                                               <div class="col-md-7 control-label" style="text-align:left;">                                            
                                                   <?php
												   $list1=mysqli_query($con,"select group_name from group_master where group_id=$led[2]");
													$list=mysqli_fetch_row($list1);
													echo "$list[0]";
													?>  
                                                </div>
                                            </div>
											<div class="form-group">                                        
                                                <label class="col-md-5 control-label"><strong>Pan No. :</strong></label>
                                                 <div class="col-md-7 control-label" style="text-align:left;">                                            
                                                   <?php echo $l[7];?>                                          
                                                </div>
                                            </div>
											 <div class="form-group">
                                                <label class="col-md-5 control-label"><strong>Mobile No. :</strong></label>
                                                <div class="col-md-7 control-label" style="text-align:left;">                                            
                                                   <?php echo $l[9];?>                                          
                                                </div>
                                            </div>
											<div class="form-group">
                                                <label class="col-md-5 control-label"><strong>Email ID :</strong></label>
                                               <div class="col-md-7 control-label" style="text-align:left;">  
												<?php echo $l[11];?>
                                                </div>
                                            </div>
											<div class="form-group">
                                                <label class="col-md-5 control-label"><strong>Opening Balance :</strong></label>
                                                <div class="col-md-7 control-label" style="text-align:left;">                                            
                                                   <?php echo $led[3];?>                                          
                                                </div>
                                            </div>
											<div class="form-group">
                                                <label class="col-md-5 control-label"><strong>Address :</strong></label>
                                                <div class="col-md-7 control-label" style="text-align:left;">                                            
                                                   <?php echo $l[3];?>                                          
                                                </div>
                                            </div>
                                            
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