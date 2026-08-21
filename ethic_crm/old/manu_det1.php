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
		
		
		<script>
	function calc()
	{
        var qty = document.getElementsByName("qty[]");
        var rate = document.getElementsByName("rate[]");
		var cost = document.getElementsByName("cost[]");
        var newcost = document.getElementsByName("newcost[]");
        var sellcost = document.getElementsByName("sellcost[]");
        var tax = document.getElementsByName("taxper[]");
		var total = 0;
		for(var i=0; i<qty.length; i++)
		{
			total=total *1 + cost[i].value*1;				
            var val=cost[i].value/qty[i].value;
            newcost[i].value= (val*1 + (val*60/100)).toFixed(2);
            var val1= newcost[i].value*tax[i].value/100;
            val= (newcost[i].value*1) + (rate[i].value*1) + (val1*1);
            sellcost[i].value=val.toFixed(2);
		}
		document.getElementById("sertot").value = (total*1).toFixed(2); 
	}
</script>

    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu15=true; $smenu15="3"; include_once("sidebar.php"); ?>
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
                    <li class="active">Job Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                   <h2><span class="fa fa-lsit"></span>Job Details</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                  
                    <div class="content-frame-body">
                        <div class="row">
                        <div class="col-md-12">
                            <form class="form-horizontal" method="post" action="manu_det1.php" enctype="multipart/form-data" name='frm2'>
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
                                                        <th width="15%">Job Type</th>
														<td width='20%'><?php echo $m[6]; ?></td>
													</tr>
                                                    <tr>
                                                        <th>Remarks</th>
                                                        <td colspan='5'><?php echo $m[3]; ?></td>
                                                    </tr>
													<tr>
														<td colspan='6'>
															<div class="table-responsive">
															<table class="table table-bordered table-striped table-actions" id="input_fields">
																<thead>
                                                                    <th>Product</th>
                                                                    <th>Qty</th>
                                                                    <Th>Rate</th>
                                                                    <th>Remark</th>
                                                                    <th>Service Cost</th>
                                                                    <th>New Manu. Cost/Pcs</th>
                                                                    <th>Tax %</th>
                                                                    <th>Sell Price/Pcs</th>
                                                                    <th>Ethnic Price/Pcs</th>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        $d1=mysqli_query($con,"select * from manu_product where m_id='$_REQUEST[m_id]' order by id");
                                                                        $tot=0;
                                                                        while($d=mysqli_fetch_row($d1))
                                                                        {
                                                                            $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$d[2]'"));
                                                                            $item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
                                                                            $tot+=($d[3]*$d[4]);
                                                                    ?>
                                                                    <input type='hidden' name='id[]' value='<?php echo $d[0]; ?>'/>
                                                                    <input type='hidden' name='v_id[]' value='<?php echo $d[2]; ?>'/>
                                                                    <tr>
                                                                        <td><?php echo "$item[1]-$f[3]-$f[2]"; ?></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="qty[]" onkeyup="calc();" value="<?php echo $d[3]; ?>" readonly style='color:black;' step="0.01"/>
																		</div></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="rate[]" onkeyup="calc();" value="<?php echo $d[4]; ?>" readonly style='color:black;' step="0.01"/>
																		</div></td>
                                                                        <td><?php echo $d[5]; ?></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="cost[]" onkeyup="calc();"  value="<?php echo $d[6]; ?>" step="0.01" readonly style='color:black;'/>
																		</div></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="newcost[]" onkeyup="calc();" step="0.01" readonly style='color:black;'/>
																		</div></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="taxper[]" onkeyup="calc();" step="0.01" readonly style='color:black;' value="<?php echo $d[7]; ?>"/>
																		</div></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="sellcost[]" onkeyup="calc();" step="0.01" readonly style='color:black;'/>
																		</div></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="edsellrate[]" onkeyup="calc();"  value="<?php echo $f[5]; ?>" step="0.01" readonly style='color:black;'/>
																		</div></td>
                                                                    </tr>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </tbody>
                                                                <tr>
                                                                    <th colspan='4' style='text-align:right;'>Total</th>
                                                                    <td> <div class="form-group">
																		<input type="text" class="form-control" name="sertot" onkeyup="calc();" id="sertot"  readonly style='color:black;'/>
																	</div></td>
                                                                    <td colspan='5'></tD>
                                                                </tr>
                                                            </table>
                                                            </div>
                                                        </td>
                                                    </tr>											
                                                    <?php echo "<script>calc();</script>"; ?>
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