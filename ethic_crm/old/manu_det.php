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
            function getcost()
            {
                var cost=0;
                var newitem=document.frm2.newitem.value;
                for(var i=1; i<=newitem; i++)
                {
                    var qty = document.getElementsByName("qty"+i+"[]");
                    var manu = document.getElementsByName("manu_cost"+i+"[]");
                    for(var j=0; j<qty.length; j++)
                    {  
                        cost+=qty[j].value*manu[j].value;
                    } 
                }
                document.frm2.totalcost.value=cost;
            }
	function calc(id)
	{
        var fabric_cost = document.getElementsByName("fabric_cost"+id+"[]");
		var manu_cost = document.getElementsByName("manu_cost"+id+"[]");
        var newcost = document.getElementsByName("newcost"+id+"[]");
        var sellcost = document.getElementsByName("sellcost"+id+"[]");
        var tax = document.getElementsByName("tax"+id+"[]");
        var taxval = document.getElementsByName("taxval"+id+"[]");
		var total = 0;
		for(var i=0; i<fabric_cost.length; i++)
		{
            newcost[i].value= (manu_cost[i].value*1 + (manu_cost[i].value*60/100)).toFixed(2);
            var val1= newcost[i].value*tax[i].value/100;
            taxval[i].value=val1.toFixed(2);            
            val= fabric_cost[i].value*1+ (val1 *1 + newcost[i].value*1);
            sellcost[i].value=val.toFixed(2);
		}
	}
  
</script>
<script>
  
        function calc1(id)
        {
            var rate=document.getElementsByName("rate[]");
            var name="fqty"+id+"[]";
            var fqty=document.getElementsByName(name);
            
            name="fabric_cost"+id+"[]";
            var fabric=document.getElementsByName(name);

            name="qty"+id+"[]";
            var qty=document.getElementsByName(name);

            var pcs=0;
            for(i=0;i<qty.length;i++)
            {
                pcs += qty[i].value*1;
            }
            var cost=0;
            if(pcs>0)
            {
                for(i=0;i<fqty.length;i++)
                {
                    cost += rate[i].value * (fqty[i].value/pcs);
                }
                cost = cost.toFixed(2);
            }
            for(i=0;i<fabric.length;i++)
            {
                fabric[i].value=cost;
            }
            calc(id);
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
                            <form class="form-horizontal" method="post" action="manu_det.php" enctype="multipart/form-data" name='frm2'>
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
                                                                        <td><?php echo $d[4]; ?></td>
                                                                        <td><?php echo ($d[3]*$d[4]); ?></td>
                                                                    </tr>
                                                                    <?php
                                                                     echo "<input type='hidden' value='$d[3]' name='fqty[]'/>";
                                                                     echo "<input type='hidden' value='$d[4]' name='rate[]'/>";
                                                                        }
                                                                    ?>
                                                                </tbody>
                                                                <tr>
                                                                    <td colspan='3' align='right'>Total</td>
                                                                    <th> <?php echo $tot; ?></th>
                                                                </tr>
                                                            </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        $item1=mysqli_query($con,"select distinct(item_id) from variant where v_id in (select v_id from manu_item where m_id='$_REQUEST[m_id]')");
                                                        $i=1;
                                                        while($item=mysqli_fetch_row($item1))
                                                        {
                                                            $pro=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$item[0]'"));
                                                            $s_id1=mysqli_query($con,"select sname from pro_subcategory where s_id='$pro[10]'");
                                                            if(!$s_id=mysqli_fetch_row($s_id1)) { $s_id[0]="";} 
                                                    ?>
                                                    <tr>
														<td colspan='6'>
															<div class="table-responsive">
															<table class="table table-bordered table-striped table-actions" id="input_fields">
																<tr>
                                                                    <td>
                                                                        <table class='table table-bordered'>
                                                                            <tr style='background-color:#ff000096; '><th colspan='3' style='text-align:center; color:white;'>Product <?php echo $i; ?></th></tr>
                                                                            <tr>
                                                                                <th>Product Type</th>
                                                                                <th>Product Sub Category</th>
                                                                                <th>Sales Description</th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><?php echo $pro[2]; ?></td>
                                                                                <td><?php echo $s_id[0]; ?></td>
                                                                                <td><?php echo $pro[5]; ?></td>
                                                                            </tr>
                                                                            <tr><td colspan='3'><table class="table table-bordered">
                                                                            <thead>
                                                                                <th>Fabric</th>
                                                                                <th>Total MTR Consumed</th>
                                                                            </thead>
                                                                            <tbody>
                                                                            <?php
                                                                                    $d1=mysqli_query($con,"select * from manu_itemdesp where m_id='$_REQUEST[m_id]' and item_id='$item[0]' order by d_id");
                                                                                    while($d=mysqli_fetch_row($d1))
                                                                                    {
                                                                                        $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$d[3]'"));
                                                                                        $items=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
                                                                                ?>
                                                                                <tr>
                                                                                    <td><?php echo "$items[1]-$f[3]"; ?></td>
                                                                                    <td><div class="form-group">
                                                                                    <input type="text" class="form-control" readonly style='color:black;' value='<?php echo $d[4]; ?>' name="fqty<?php echo $i; ?>[]" onkeyup="calc1(<?php echo $i; ?>);"/>
                                                                                </div></td>
                                                                                </tr>
                                                                                <?php
                                                                                    }
                                                                                    echo "<script>calc1($i);</script>";
                                                                                ?>
                                                                            </tbody>
                                                                        </table></td></tr>
                                                                        <tr><td colspan='3'><table class="table table-bordered table-striped table-actions" id="input_fields<?php echo $i; ?>">
                                                                            <thead>
                                                                                <th>Size</th>
                                                                                <th>Color</th>
                                                                                <th>Qty</th>
                                                                                <Th>Fabric Cost/Pcs</th>
                                                                                <th>Manufacturing Cost/Pcs</th>
                                                                                <th>New Manu. Cost/Pcs</th>
                                                                                <th>Tax %</th>
                                                                                <th>Tax Value/Pcs</th>
                                                                                <th>Sell Price/Pcs</th>
                                                                                <th>Ethnic Price/Pcs</th>
                                                                            </thead>
                                                                            <tbody>
                                                                            <?php
                                                                                $v1=mysqli_query($con,"select * from manu_item where m_id='$_REQUEST[m_id]' and v_id in (select v_id from variant where item_id='$item[0]') order by id") ;
                                                                                while($v=mysqli_fetch_row($v1))
                                                                                {
                                                                                    $d=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$v[2]'"));
                                                                            ?>
                                                                                <tr>
                                                                                <td align="left" valign="middle">
                                                                                       <?php echo $d[2]; ?>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <?php echo $d[3]; ?>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                        <input type="text" class="form-control" name="qty<?php echo $i; ?>[]" onkeyup="getcost();" value='<?php echo $v[3]; ?>' readonly style='color:black;' />
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" name="fabric_cost<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);" value='<?php echo $v[4]; ?>' readonly style='color:black;' />
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" name="manu_cost<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>); getcost();" value='<?php echo $v[5]; ?>' readonly style='color:black;' />
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" name="newcost<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);" readonly style='color:black;' />
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" name="tax<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);" value='<?php echo $v[6]; ?>' readonly style='color:black;' />
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> <div class="form-group">
                                                                                        <input type="text" class="form-control" name="taxval<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);" readonly style='color:black;' /></div></td>
                                                                                    <td> <div class="form-group">
                                                                                        <input type="text" class="form-control" name="sellcost<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);" readonly style='color:black;' />
                                                                                    </div></td>
                                                                                    <td> <div class="form-group">
                                                                                        <input type="number" class="form-control" name="edsellrate<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);" value='<?php echo $v[7]; ?>' readonly style='color:black;' />
                                                                                    </div></td>
                                                                                </tr>
                                                                                <?php
                                                                                }
                                                                                echo "<script>calc($i);</script>";
                                                                                ?>
                                                                            </tbody>
                                                                        </table>
                                                                        </td></tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            </div>
                                                        </td>
                                                    </tr>	
                                                    <?php
                                                        $i++;
                                                        }
                                                        $i--;
                                                        echo "<input type='hidden' name='newitem' value='$i'/>";
                                                    ?>						
												</tbody>
											</table>
										</div>
                                        <div class="form-group">
                                            <label><b style='color:red;'>Total Manufacturing Cost</b></label>
                                            <input type="text" class="form-control" name="totalcost" onkeyup="getcost();"  readonly style='color:black; max-width:150px;'/>
                                        </div>
										</div>
									</div>
									<?php echo "<script>getcost();</script>"; ?>
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