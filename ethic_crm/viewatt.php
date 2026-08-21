<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";	
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
            <?php $menu8=true; $smenu8="3"; $ssmenu8="32";  include_once("sidebar.php"); ?>
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
					<li><a href="#">Attendance Master</a></li>
					<li class="active">Attendance Sheet</li>
                </ul>
                <!-- END BREADCRUMB -->                                                
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-users"></span>  Attendance <small> Sheet</small></h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    
                    <div class="row">
                        <div class="col-md-12">
                           
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <form class="form-horizontal" action="viewatt.php" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
											<?php
												if(isset($_REQUEST['amonth']))
													$month=$_REQUEST['amonth'];
												else
													$month=date("m");
												if(isset($_REQUEST['ayear']))
													$ayear=$_REQUEST['ayear'];
												else
													$ayear=date("Y");
											?>
											<div class="col-md-4">
													<select class="form-control" name="amonth" required>
														<option value="">--Select Month--</option>
														<option value="01" <?php if($month=="01") { ?> selected="selected" <?php }?> >January</option>
														<option value="02" <?php if($month=="02") { ?> selected="selected" <?php }?> >Febuary</option>
														<option value="03" <?php if($month=="03") { ?> selected="selected" <?php }?> >March</option>
														<option value="04" <?php if($month=="04") { ?> selected="selected" <?php }?> >April</option>
														<option value="05" <?php if($month=="05") { ?> selected="selected" <?php }?> >May</option>
														<option value="06" <?php if($month=="06") { ?> selected="selected" <?php }?> >June</option>
														<option value="07" <?php if($month=="07") { ?> selected="selected" <?php }?> >July</option>
														<option value="08" <?php if($month=="08") { ?> selected="selected" <?php }?> >August</option>
														<option value="09" <?php if($month=="09") { ?> selected="selected" <?php }?> >September</option>
														<option value="10" <?php if($month=="10") { ?> selected="selected" <?php }?> >October</option>
														<option value="11" <?php if($month=="11") { ?> selected="selected" <?php }?> >November</option>
														<option value="12" <?php if($month=="12") { ?> selected="selected" <?php }?> >December</option>
													</select>
                                            </div>
                                            <div class="col-md-4">
												<div class="input-group">
                                                    <input type="text" class="form-control" name="ayear" value="<?php echo $ayear; ?>" required placeholder="Enter Year"/>
													 <div class="input-group-btn">
                                                        <button class="btn btn-primary" type="submit" name="s2">Open</button>
                                                    </div>
												</div>
											</div>
											
                                        </div>
                                    </form>                                    
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <?php
						if(isset($_REQUEST['s2']))
						{
					?>
                    <div class="row">
						<div class="table-responsive">
							<table class="table table-bordered table-striped table-actions">
							<?php
								$sno=1;
								$f_d_t = mktime(0,0,0,$_REQUEST['amonth'],1,$_REQUEST['ayear']);
								$no_days=date("t",$f_d_t);
								echo "<thead><tr>";
								echo "<th>S.No.</th>";
								echo "<th>Employee Name</th>";
								for($i=1;$i<=$no_days;$i++)
								{
									echo "<th>$i</th>";
								}
								
								echo "<th>Total</th>";
								echo "</tr></thead>";
								echo "<tbody>";
								$dfrom=$_REQUEST['ayear']."-".$_REQUEST['amonth']."-01";
								$dto=$_REQUEST['ayear']."-".$_REQUEST['amonth']."-".$no_days;
								$atte="";
								$str1=mysqli_query($con,"select distinct(ledger_id) from empattendance where adate>='$dfrom' and adate<='$dto'");
								while($str=mysqli_fetch_row($str1))
								{
									$atte.=$str[0].",";
								}
								$atte=substr($atte,0,strlen($atte)-1);
								if($atte!="")
								{
									$stu1=mysqli_query($con,"select ledger_id,empname from empdet where ledger_id in ($atte) order by empname");
									$today=date("Y-m-d");
									while($stu=mysqli_fetch_row($stu1))
									{
										$tot=0;
										echo "<tr>";
										echo "<td><b>$sno</b></td>";
										$sno++;
										echo "<td><b>$stu[1]</b></td>";
										
										for($i=1;$i<=$no_days;$i++)
										{
											if($i<10)
												$j="0".$i;
											else
												$j=$i;
											$date=$_REQUEST['ayear']."-".$_REQUEST['amonth']."-".$j;
											$list1=mysqli_query($con,"select att from empattendance where adate='$date' and ledger_id='$stu[0]'");	
											
											if($l=mysqli_fetch_row($list1))
											{
												if($l[0]=="P")
												{
													echo "<td style='background-color: #95b75d; color:#fff; '>$l[0]</td>";
													$tot++;
												}
												else if($l[0]=="A")
													echo "<td style='background-color: #b64645; color:#fff; '>$l[0]</td>";
												else if($l[0]=="H")
												{
													echo "<td style='background-color: #ff9800; color:#fff; '>$l[0]</td>";
													$tot=$tot+0.5;
												}
											}
											else
											{
												$list1=mysqli_query($con,"select att from empattendance where adate='$date' and ledger_id='0'");	
												if($l=mysqli_fetch_row($list1))
												{
													echo "<td style='background-color:#112e96; color:#fff; '>&nbsp;</td>";
												}
												else
												{
													if($date<$today)
													{
														echo "<td style='background-color: #ccc; color:#fff; '>&nbsp;</td>";
													}
													else
														echo "<td>&nbsp;</td>";
												}
											}
										}
										echo "<td>$tot</td>";
										echo "</tr>";	
									}
								}
								echo "</tbody>";
							?>
							</table>
						</div>              
                    </div>
					<?php
						}
					?>
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

        <!-- START THIS PAGE PLUGINS-->        
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        <!-- END THIS PAGE PLUGINS-->        

        <!-- START TEMPLATE -->
        
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
        <!-- END TEMPLATE -->

    <!-- END SCRIPTS -->         
    
   
    </body>
</html>
