<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";
    if(!isset($_REQUEST['m_id']))
    {
        header("Location: viewmanu.php"); die;
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
				   <li><a href="viewmanu.php">Job Master</a></li>
                    <li class="active">Submit Job</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                   <h2><span class="fa fa-lsit"></span>Submit Job</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                  
                    <div class="content-frame-body">
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
                            <form class="form-horizontal" method="post" action="submit_manu3.php" enctype="multipart/form-data" name='frm2'>
                                <input type='hidden' name='m_id' value="<?php echo $_REQUEST['m_id']; ?>"/>
                            <div class="panel panel-default">
                                <div class="panel-body">   
									<div class="row">
                                        <div class="col-md-12">
										<div class="table-responsive">
                                            <?php
                                                $m=mysqli_fetch_row(mysqli_query($con,"select * from manufacturejob where m_id='$_REQUEST[m_id]'"));
                                            ?>
											<table class="table table-bordered table-striped table-actions">
												<tbody>  
													<tr>
														<th width="15%">Date</th>
														<td width="15%"><?php echo date("d-m-Y", strtotime($m[1])); ?></td>
														<th width="15%">Jobber</th>
														<td width='20%'>
																<?php
																	$f=mysqli_fetch_row(mysqli_query($con,"select * from ledger_accounts where ledger_id='$m[2]'"));
                                                                    echo $f[1]; 
																?>	
															</select>
															</div>
														</td>
                                                    </tr>
                                                    <tr>
                                                        <th width="15%">Job Type</th>
														<td width='20%'><?php echo $m[6]; ?></td>
                                                        <th>Remarks</th>
                                                        <td><?php echo $m[3]; ?></td>
                                                    </tr>
                                                    <tr>
														<td colspan='4'>
															<div class="table-responsive">
															<table class="table table-bordered table-striped table-actions">
																<thead>
                                                                    <th>Fabric</th>
                                                                    <th>Total MTR Given</th>
                                                                    <th>Remaining MTR</th>
                                                                    <th>Rate</th>
                                                                    <th>Cost</th>
                                                                </thead>
                                                                <tbody>
                                                                  <?php
                                                                        $d1=mysqli_query($con,"select * from manu_fabric where m_id='$_REQUEST[m_id]' order by id");
                                                                        $tot=0;
                                                                        while($d=mysqli_fetch_row($d1))
                                                                        {
                                                                            $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$d[2]'"));
                                                                            $item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
                                                                            $tot+=($d[3]*$d[4]);
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo "$item[1]-$f[3]"; ?></td>
                                                                        <td><?php echo $d[3]; ?></td>
                                                                        <td><?php echo $d[5]; ?></td>
                                                                        <td><?php echo $d[4]; ?></td>
                                                                        <td><?php echo ($d[3]*$d[4]); ?></td>
                                                                    </tr>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </tbody>
                                                                <tr>
                                                                    <td colspan='4' align='right'>Total</td>
                                                                    <th> <?php echo $tot; ?></th>
                                                                    <input type='hidden' name='fabtot' value='<?php echo $tot; ?>'/>
                                                                </tr>
                                                            </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>New Products Received</th>
                                                        <td><div class="form-group">
                                                            <input type="number" class="form-control" name="newitem" required/>
                                                        </div></td>
                                                        <td colspan='2'><button class="btn btn-primary" type="submit" name="p1">Proceed</button></tD>
                                                    </tR>									
												</tbody>
											</table>
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