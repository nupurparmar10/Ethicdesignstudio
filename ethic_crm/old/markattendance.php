<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	
	if(isset($_REQUEST['s1']))
	{
		mysqli_query($con,"delete from empattendance where adate='$_REQUEST[adate]'");
		$adate=$_REQUEST['adate'];
		if(isset($_REQUEST['holiday']))
		{
			mysqli_query($con,"insert into empattendance set adate='$adate', ledger_id='0', remark='Holiday', att=''");
		}
		else
		{
			$count=count($_REQUEST['ledger_id']);
			for($i=0;$i<$count;$i++)
			{
				$ledger_id=$_REQUEST['ledger_id'][$i];
				$remark=$_REQUEST['remark'][$i];
				$h="att".$ledger_id;
				$att=$_REQUEST[$h];
				if($remark!="" || $att!="")
				{
					mysqli_query($con,"insert into empattendance set adate='$adate', ledger_id='$ledger_id', remark='$remark', att='$att'");
				}		
			}
		}
		$msg="Employee Attendance Saved Successfully!!!";
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
		<script>
			function showdata()
			{
				if(document.frm3.holiday.checked==1)
				{
					document.getElementById("display").style.display = "none";
				}
				else
				{
					document.getElementById("display").style.display = "block";
				}
			}
		</script>
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
            <?php $menu8=true; $smenu8="3";  $ssmenu8="31"; include_once("sidebar.php"); ?>
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
                    <li class="active">Mark Attendance</li>
                </ul>
                <!-- END BREADCRUMB -->                                                
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-users"></span>  Mark <small>Attendance</small></h2>
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
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <form class="form-horizontal" action="markattendance.php" method="post" enctype="multipart/form-data" name="frm2">
										 <div class="col-md-6">
											<?php
												if(isset($_REQUEST['adate']))
													$adate=$_REQUEST['adate'];
												else $adate=date("Y-m-d");
											?>
											<div class="form-group">
                                                <label class="col-md-3 control-label">Date</label>
                                               	<div class="col-md-9">
													<input type="date" class="form-control" name="adate" value='<?php echo $adate; ?>' required/>
												</div>
											</div>
											
											<div class="form-group">
											   
												<div class="input-group-btn">
													<button class="btn btn-primary" type="submit" name="s2">Open</button>
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
					<form action="markattendance.php" method="post" enctype="multipart/form-data" name="frm3">
						<div class="row">
							<div class="col-md-5"></div>
							<div class="col-md-2" style='font-size:20px;'>
								<?php
									$chk1=mysqli_query($con,"select * from empattendance where adate='$adate' and ledger_id='0'");
									if($chk=mysqli_fetch_row($chk1)) $h=1; else $h=0;
								?>
								<input type='checkbox' class='mcheckbox' name='holiday' value='1' <?php if($h==1) { ?> checked <?php } ?> onclick='showdata();'/> &nbsp; Holiday
								<?php $adate=$_REQUEST['adate']; echo "<input type='hidden' name='adate' value='$adate'/>"; ?>
							</div>
							<div class="col-md-12" id="display" <?php if($h==1) { ?> style='display:none;' <?php } ?> >
								<div class="table-responsive">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>S.No.</th>
												<th>Employee Name</th>
												<th>Post</th>
												<th>Attendance</th>
												<th>Remarks</th>
											</tr>
										</thead>
										<tbody> 
										<?php
											$j=1;											
											$l1=mysqli_query($con,"select ledger_id,empname,post from empdet where status=1 order by empname");
											while($l=mysqli_fetch_row($l1))
											{
												echo "<input type='hidden' name='ledger_id[]' value='$l[0]'/>";
												echo "<tr>";
												echo "<td>$j</td>";
												echo "<td>$l[1]</td>";
												echo "<td>$l[2]</td>";
												$chk1=mysqli_query($con,"select * from empattendance where adate='$adate' and ledger_id='$l[0]'");
												if($chk=mysqli_fetch_row($chk1)) {}
												else { $chk[2]=$chk[3]=""; }
												$h="att".$l[0];
										?>
										<td><input type='radio' required class='mcheckbox' name='<?php echo $h; ?>' <?php if($chk[2]=="P") { ?> checked='checked' <?php } ?> value='P'/>&nbsp;P &nbsp;
											<input type='radio' required class='mcheckbox' name='<?php echo $h; ?>' <?php if($chk[2]=="A") { ?> checked='checked' <?php } ?> value='A'/>&nbsp;A &nbsp;
											<input type='radio' required class='mcheckbox' name='<?php echo $h; ?>' <?php if($chk[2]=="H") { ?> checked='checked' <?php } ?> value='H'/>&nbsp;H
										</td>
										<td><input type='text' class="form-control" name="remark[]" value='<?php echo $chk[3]; ?>'/></td>
										<?php
												$j++;
												echo "</tr>";
											}
										?>
									</tbody>
									</table>
								</div>
							</div>
							
						</div>
					<br>
					<br>
					<div class="row">
						<div class="col-md-2 pull-right">
							<button class="btn btn-info btn-block" type="submit" name="s1"><span class="fa fa-save"></span> Save Report</button>
						</div>	
					</div>
					<br>
					<br>
					</form>
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