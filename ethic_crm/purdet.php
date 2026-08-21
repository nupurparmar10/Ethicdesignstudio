<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";
	if(!isset($_REQUEST['pur_id']))
	{
		header("Location: viewpurchase.php?msg=set"); die;
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
		function getvalues(val)
{
	var v_id = document.getElementsByName("v_id[]");	
	var purrate = document.getElementsByName("purrate[]");
	var taxper = document.getElementsByName("taxper[]");
	var edsellrate = document.getElementsByName("edsellrate[]");
	for(i=0;i<v_id.length;i++)
	{
		if(v_id[i].value==val)
		{
			str=(v_id[i].value).split("-");
			purrate[i].value=str[1];
			taxper[i].value=str[2];
			edsellrate[i].value=str[3];
		}
	}
	calc();
}
	function calc()
	{
		var qty = document.getElementsByName("qty[]");
		var purrate = document.getElementsByName("purrate[]");
		var taxable = document.getElementsByName("taxable[]");
		var taxper = document.getElementsByName("taxper[]");
		var taxamt = document.getElementsByName("taxamt[]");
		var purvalue = document.getElementsByName("purvalue[]");
		var tpurrate = document.getElementsByName("tpurrate[]");
		var sellamt = document.getElementsByName("sellamt[]");
		var dis = document.getElementsByName("dis[]");
		var total = 0;		
		var taxtot=0;
		var qtytot=0;
		var taxabletot=0;
		for(var i=0; i<qty.length; i++)
		{
			var q = qty[i].value;
			var r = purrate[i].value;
			var amt=0;
			var val=0;
			if(q > 0 && r > 0)
			{
				qtytot += q*1;
				amt=q*r;
				val= amt * dis[i].value/100;			
				amt=amt-val;
				taxable[i].value=amt;
				taxabletot += amt*1;
				val=amt*taxper[i].value/100;
				taxamt[i].value=val.toFixed(2);
				taxtot+=val*1;
				amt+=val*1;
				amt=amt.toFixed(2);
				purvalue[i].value=(amt*1);
				total += (amt*1);
				val=amt/q;
				tpurrate[i].value=val.toFixed(2);
				val1=val*60/100;
				val=val+val1;
				sellamt[i].value=val.toFixed(2);
			}
		}
		document.getElementById("qtytot").value = (qtytot*1).toFixed(2);
		document.getElementById("taxtot").value = (taxtot*1).toFixed(2);
		document.getElementById("taxabletot").value = (taxabletot*1).toFixed(2);
		document.getElementById("total").value = (total*1).toFixed(2);
		var spdis =document.getElementById("spdis").value;
		var freight =document.getElementById("freight").value;
		var transport =document.getElementById("transport").value;
		var other =document.getElementById("other").value;
		var nettotal = total*1 - spdis*1 + freight*1 + transport*1 + other*1;
		var r=(nettotal*1).toFixed(0);
		var roundoff = r - nettotal;
		document.getElementById("roundoff").value = roundoff.toFixed(2);
		var grandt = nettotal*1 + roundoff*1;
		document.getElementById("gtotal").value = (grandt*1).toFixed(2);
	}
</script>
<script>
	var counter=1;  
</script>
  <script>

 function more() {
			var $table = $('#input_fields');
            var $tr = $table.find('tr').eq(1).clone();
			$tr.attr("id",counter);
            $tr.appendTo($table).find('input').val('');
			$tr.appendTo($table).find('select').eq(0).val('');
            $("#input_fields").append($tr);
            $tr.appendTo($table).find('select').eq(0).focus();
			counter++;
  }
  </script>
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu9=true; $smenu9="3"; include_once("sidebar.php"); ?>
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
				   <li><a href="viewpurchase.php">Purchase Master</a></li>
                    <li class="active">Purchase Bill Detail</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                   <h2><span class="fa fa-lsit"></span>Purchase Bill Detail</h2>
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
                            <form class="form-horizontal" method="post" action="addpurchase.php" enctype="multipart/form-data" onsubmit="return confirm('Sure?');">
                            <div class="panel panel-default">
                                <div class="panel-body">   
									<div class="row">
                                        <div class="col-md-12">
										<?php
											if(isset($_REQUEST['pur_id']))
											{
												$p1=mysqli_query($con,"select * from purbook where pur_id='$_REQUEST[pur_id]'");
												$p=mysqli_fetch_row($p1);
												echo "<input type='hidden' name='pur_id' value='$_REQUEST[pur_id]'/>";
											}
											else
											{
												$p[1]=$p[2]=$p[3]=$p[4]=$p[5]=$p[6]=$p[7]=$p[8]=$p[9]=$p[10]=$p[11]=$p[12]=$p[13]=$p[14]=$p[15]=$p[16]="";
												$p[1]=date("Y-m-d");
												$p[16]="Other Charges";
											}
										?>
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-actions">
												<tbody>  
													<tr>
														<th width="25%">Vendor Invoice No.</th>
														<td  width="25%"><div class="form-group">
															<?php echo $p[3]; ?>
														</div></td>
														<th width="25%">Date</th>
														<td width="25%"><div class="form-group">
															<?php  echo $p[1]; ?>
														</div></td>
														
													</tr>
													<tr>
														<th width="10%">Vendor</th>
														<td width='25%'><div class="ui-widget form-group">
                                                        <?php
																	$f1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (26,27) and ledger_id='$p[2] 'order by name");
																	$f=mysqli_fetch_row($f1);
																	echo $f[1]; 
																?>	
															</div>
														</td>
														<th width="10%">Payment Mode</th>
														<td><div class="form-group">
                                                            <?php
                                                                if($p[7]=='Credit')
                                                                {
                                                                    echo 'Credit';
                                                                }
                                                                else
                                                                {
																	$list1=mysqli_query($con,"select ledger_id,name from ledger_accounts where (group_id in (select group_id from group_master where group_name='Bank Accounts') or name='Cash Account') and status=1 and ledger_id='$p[7]' order by name");
																	if($l=mysqli_fetch_row($list1))
																	{
                                                                        echo $l[1]; 
																	}
                                                                }
																?>
														</div></td>
													</tr>
													<tr>
														<td colspan='4' style="max-width:300px; overflow-x:auto;">
															<div class="table-responsive">
															<table class="table table-bordered table-striped table-actions" id="input_fields">
																<thead>
																	<tr>
																		<th>Product</th>
																		<th>Qty</th>
																		<th>Purchase Rate</th>
																		<th>Dis %</th>
																		<th>Taxable Value</th>
																		<th>Tax %</th>
																		<th>Tax Amt</th>
																		<th>Total Purchase Value</th>
																		<th>Total Purchase Rate</th>
																		<th>Selling Price</th>
																		<th>Ethic Selling Price</th>
																	</tr>
																</thead>
																<tbody>
																	<?php
																		if(isset($_REQUEST['pur_id']))
																		{
																			$k1=mysqli_query($con,"select * from pur_items where pur_id='$p[0]'");
																			if($k=mysqli_fetch_row($k1))
																			{
																			$a=-1;
																				do{
																					$a++;
																	?>
                                                                    <tr id="0">
																		<td align="left" valign="middle">
                                                                            <?php
																					$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1) and v_id='$k[0]'");
																					if($f=mysqli_fetch_row($f1))
																					{
																						$item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																						echo "$item[1] - $item[5] $f[2] $f[3]";
																					}
																				?>	
																		</td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="qty[]" onkeyup="calc();" value='<?php echo $k[1]; ?>' readonly style="color:black !important;" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purrate[]" onkeyup="calc();" value='<?php echo $k[2]; ?>' readonly style="color:black !important;" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="dis[]" onkeyup="calc();" value='<?php echo $k[6]; ?>' readonly style="color:black !important;" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxable[]" onkeyup="calc();" readonly style="color:black !important;" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxper[]" onkeyup="calc();" value='<?php echo $k[3]; ?>' readonly style="color:black !important;" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxamt[]" onkeyup="calc();" readonly style="color:black !important; min-width:100px;" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purvalue[]" onkeyup="calc();" readonly style="color:black !important;" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="tpurrate[]" onkeyup="calc();" readonly style="color:black !important;" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="sellamt[]" onkeyup="calc();" readonly style="color:black !important;" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="edsellrate[]" onkeyup="calc();" value='<?php echo $k[4]; ?>' readonly style="color:black !important;" />
																		</div></td>
																	</tr>
																	
																	<?php
																			}while($k=mysqli_fetch_row($k1));
																			echo "<script>counter=$a;</script>";
																			}
																		}
																	?>
																	
																</tbody>
																<tr>
																	<td align='right'>Total</td>
																	<td> <div class="form-group">
																			<input type="text" class="form-control" name="qtytot" onkeyup="calc();" id="qtytot" readonly style="color:black !important;" />
																	</div></td>
																	<td> </td>
																	<td> </td>
																	<td> <div class="form-group">
																		<input type="text" class="form-control" name="taxabletot" onkeyup="calc();" id="taxabletot" readonly style="color:black !important;" />
																	</div></td>
																	<td></td>
																	<td> <div class="form-group">
																		<input type="text" class="form-control" name="taxtot" onkeyup="calc();" id="taxtot" readonly style="color:black !important;" />
																	</div></td>
																	<td> <div class="form-group">
																			<input type="text" class="form-control" name="total" id="total" onkeyup="calc();" readonly style="color:black !important;" />
																	</div></td>
																	<td colspan='3'></td>
																</tr>
															</tbody>
															</table>
															</div>															
														</td>
													</tr>
													<tr>
														<th rowspan='6'>Remark</th>
														<td rowspan='6'> <div class="input-group">
															<?php echo $p[5]; ?>
														</div></td>
														<th  style="width:300px;">Special Discount</th>
														<td> <div class="input-group">
															<input type="text" readonly style="color:black !important;" class="form-control" name="spdis" id="spdis" value="<?php echo $p[6]; ?>" onkeyup="calc();" style="width:100px;"  />
														</div></td>
													</tr>
													<tr>
														<th>Freight Charges</th>
														<td> <div class="input-group">
															<input type="text" readonly style="color:black !important;"  class="form-control" name="freight" id="freight" value="<?php echo $p[8]; ?>" onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Transport Charges</th>
														<td> <div class="input-group">
															<input type="text"  readonly style="color:black !important;"  class="form-control" name="transport" id="transport" value="<?php echo $p[7]; ?>" onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th><input type="text" readonly style="color:black !important;"  class="form-control" name="oname" value="<?php echo $p[13]; ?>" style="width:150px;"/></th>
														<td> <div class="input-group">
															<input type="text"readonly style="color:black !important;"   class="form-control" name="other" id="other" value="<?php echo $p[9]; ?>" onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Round Off</th>
														<td> <div class="input-group">
															<input type="text" readonly style="color:black !important;"  class="form-control" name="roundoff" id="roundoff" value="<?php echo $p[10]; ?>" onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Grand Total</th>
														<td> <div class="input-group">
															<input type="text" readonly style="color:black !important;"  class="form-control" name="gtotal" id="gtotal" style="width:100px;" onkeyup="calc();"/>
														</div></td>
													</tr>
												</tbody>
											</table>
										</div>
										
										</div>
									</div>
									
                                </div>
                                <div class="panel-footer">
									<?php
										if(isset($_REQUEST['pur_id']))
										{
											echo "<script>calc();</script>";
									?>
									<button class="btn btn-primary" type="submit" name="s3">Modify</button>
									<?php
										}
										else
										{
									?>
									<button class="btn btn-primary" type="submit" name="s1">Save</button>
									<?php
										}
									?>
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