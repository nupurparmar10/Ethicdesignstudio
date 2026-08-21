<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";
	if(!isset($_REQUEST['sale_id']))
	{
		header("Location: viewsalereturn.php"); die;
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
             <?php  $menu10=true; $smenu10="8"; include_once("sidebar.php"); ?>
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
				   <li><a href="viewsales.php">Sales Master</a></li>
                    <li class="active">View Sales Return Bill</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                   <h2><span class="fa fa-lsit"></span>Sales Bill Return Details</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                  
                    <div class="content-frame-body">
                        <div class="row">
                        <div class="col-md-12">
                           
                            <form class="form-horizontal" method="post" action="salereturndet.php" enctype="multipart/form-data" onsubmit="return confirm('Sure?');">
                            <div class="panel panel-default">
                                <div class="panel-body">   
									<div class="row">
                                        <div class="col-md-12">
										<?php
											$p1=mysqli_query($con,"select * from billreturn where sale_id='$_REQUEST[sale_id]'");
											$p=mysqli_fetch_row($p1);
										?>
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-actions">
												<tbody>  
													<tr>
														<th>Return Invoice No.</th>
														<td><?php echo $p[3]; ?></td>
														<th>Invoice Date</th>
														<td><?php 
															if($p[1]!="0000-00-00")
															{
																$date= DateTime::createFromFormat('Y-m-d', $p[1]);
																echo $date->format('d-m-Y');
															}
															else
															{
																echo " ";
															}?></td>
													</tr>
													<tr>
														<th>Party</th>
														<td><?php 
															$r=mysqli_fetch_row(mysqli_query($con,"select name from ledger_accounts where ledger_id='$p[2]'"));
															echo $r[0]; 
														?></td>
														<th>GST Type</th>
														<td><?php echo $p[16]; ?></td>
													</tr>
													<tr>
														<th>Payment Mode</th>
														<td><?php 
															if($p[5]=="Credit") echo "By Credit";
															else
															{
																$r=mysqli_fetch_row(mysqli_query($con,"select name from ledger_accounts where ledger_id='$p[5]'"));
																echo $r[0]; 
															}
														?></td>
														<th>Mobile No.</th>
														<td><?php echo $p[6]; ?></td>
													</tr>		
													<tr>
                                                        <th>Against Invoice No.</th>
														<td><div class="form-group">
                                                        <?php echo $p[15]; ?>
														</div></td>
														<th>Sales Person</th>
														<td><?php 
														$emp1=mysqli_query($con,"select empname from empdet where ledger_id='$p[16]'");
														if($emp=mysqli_fetch_row($emp1)) {} else $emp[0]="";
														echo $emp[0]; ?></td>
														
													</tR>
                                                    <Tr>
                                                        <th>Commission %</th>
                                                        <td><?php echo $p[17]; ?></td>
                                                    </tr>
													<tr>
														<td colspan='4'>
															<div class="table-responsive">
															<table class="table table-bordered table-striped table-actions" id="input_fields">
																<thead>
																	<tr>
																		<th>S.No.</th>
																		<th>Code- Sale Desp- Variant</th>
																		<th>MRP</th>
																		<th>Qty</th>
																		<th style='text-align:center;' colspan='3'>Discount</th>
																		<th>Rate</th>
																		<th colspan='2'>GST</th>
																		<th>Amount</th>
																	</tr>
																</thead>
																<tbody>
																<?php
																	$tot=$distot=$taxtot=0;$j=1;
																	$k1=mysqli_query($con,"select * from sr_items where sale_id='$_REQUEST[sale_id]'");
																	while($k=mysqli_fetch_row($k1))
																	{
																		$amt=0;
																?>
																	<tr>
																		<td><?php echo $j++; ?></td>
																		<td><?php
																				$f1=mysqli_query($con,"select * from variant where v_id='$k[1]'");
																				if($f=mysqli_fetch_row($f1))
																				{
																					$c=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																					echo "$c[1]-$c[5] $f[2] $f[3]";
																				}
																			?>	
																			</td>
																		<td><?php echo $k[6]; ?></td>
																		<td><?php echo $k[2]; ?></td>
																		<td><?php echo $k[4]; ?></td>
																		<td><?php if($k[7]=="P") echo "%"; else echo $k[7]; ?></td>
																		<td><?php $amt=$k[2]*$k[6]; 
																			if($k[7]=="P")
																			$val=$k[6]*$k[4]/100;
																			else $val=$k[4];
																			echo round($val,2);
																			$distot+=$val*1;
																			$amt=$k[2]*$k[3]; 
																		?></td>
																		<td><?php echo $k[3]; ?></td>
																		<td><?php echo $k[5]; ?></td>
																		<td><?php 
																			$val=$amt*$k[5]/100;
																			$taxtot+=$val*1;
																			echo round($val,2);
																			$amt=$amt*1+$val*1;
																			$tot+=$amt*1;
																		?></td>
																		<td><?php echo round($amt,2); ?></td>
																	</tr>
																	<?php
																	}
																	$tot1 = $tot*$p[17]/100;
																	?>
																	<tr>
																		<td align='right' colspan='6'>Total</td>
																		<td><?php echo number_format($distot,2); ?></td>
																		<td> </td>
																		<td> </td>
																		<td><?php echo number_format($taxtot,2); ?></td>
																		<td><?php echo number_format($tot,2); ?></td>
																	</tr>
																</tbody>
															</table>
															</div>
														</td>
													</tr>
													<tr>
														<th rowspan='5'>Remarks</th>
														<td rowspan='5'><?php echo $p[4]; ?></td>
														<th>Special Discount</th>
														<td><?php echo $p[7]; ?></td>
													</tr>
													<tr>
														<th>Freight Charges</th>
														<td><?php echo $p[9]; ?></td>
													</tr>
													<tr>
														<th>Transport Charges</th>
														<td><?php echo $p[8]; ?></td>
													</tr>
													<tr>
														<th><?php echo $p[18]; ?></th>
														<td><?php echo $p[10]; ?></td>
													</tr>
													<tr>
														<th>Round Off</th>
														<td><?php echo $p[11]; ?></td>
													</tr>
													<tr>
														<th>Total Commission</th>
														<td><?php echo number_format($tot1,2); ?></td>
														<th>Grand Total</th>
														<td><?php echo $p[12]; ?></td>
													</tr>
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