<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";
	if(isset($_REQUEST['msg']))
	{
		$msg="Damage Entry Added Successfully!!!";
	}
	if(isset($_REQUEST['s1']) )
	{
        $id=mysqli_fetch_row(mysqli_query($con,"select max(d_id) from damage_master"));
		$id[0]++;
        mysqli_query($con,"insert into damage_master set d_id='$id[0]', ddate='$_REQUEST[ddate]' , remark='$_REQUEST[remark]'");
        $count=count($_REQUEST['item_id']);
        for($i=0;$i<$count;$i++)
        {
            $item=explode("-",$_REQUEST['item_id'][$i])[0];
            $qty=$_REQUEST['qty'][$i];
            if($item!="" && $qty>0)
			{
                $pur_rate=$_REQUEST['mrp'][$i];
                $qty=$_REQUEST['qty'][$i];
                mysqli_query($con,"insert into damage_particular set d_id='$id[0]', v_id='$item', qty='$qty', pur_rate='$pur_rate'");
                mysqli_query($con,"update variant set stock=stock-$qty, webstock=webstock-$qty where v_id='$item'");
            }
        }
        header("Location:adddamage.php?msg=set"); die;
	}
	if(isset($_REQUEST['s4']))
	{
		$p1=mysqli_query($con,"select * from damage_particular where d_id='$_REQUEST[d_id]'");
		while($p=mysqli_fetch_row($p1))
		{
			mysqli_query($con,"update variant set stock=stock+$p[2], webstock=webstock+$p[2] where v_id='$p[2]'");
		}
		mysqli_query($con,"delete from damage_particular where d_id='$_REQUEST[d_id]'");
		
		$count=count($_REQUEST['item_id']);
        for($i=0;$i<$count;$i++)
        {
            $item=explode("-",$_REQUEST['item_id'][$i])[0];
            $qty=$_REQUEST['qty'][$i];
            if($item!="" && $qty>0)
			{
                $pur_rate=$_REQUEST['mrp'][$i];
                $qty=$_REQUEST['qty'][$i];
                mysqli_query($con,"insert into damage_particular set d_id='$_REQUEST[d_id]', v_id='$item', qty='$qty', pur_rate='$pur_rate'");
                mysqli_query($con,"update variant set stock=stock-$qty, webstock=webstock-$qty where v_id='$item'");
            }
        }
		header("Location: viewdamage.php?msg=set"); die;
	}
?>
<?php
	$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1) and stock>0 order by v_id");
	$query="";																		
	while($f=mysqli_fetch_row($f1))
	{
		$c=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
		$query .= "<option value='$f[0]-$f[5]-$c[7]-$f[6]-$f[4]'>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
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
	function delete_row(row)
	{
		$("#"+row).remove();
		calc();
	}
</script>
    <script>
    function getValues1(VAL)
    {
        var items = document.getElementsByName("item_id[]");	
        var mrp = document.getElementsByName("mrp[]");
        var qty = document.getElementsByName("qty[]");
        for(i=0;i<items.length;i++)
        {
            if(items[i].value==VAL)
            {
                var str = items[i].value;
                var ary = str.split('-');
                mrp[i].value=ary[4];
                qty[i].value=1;
                                    
            }
        }
        calc();
    }
   
        
    function calc()
    {
        var flag=0;
        var items = document.getElementsByName("item_id[]");	
        var qty = document.getElementsByName("qty[]");
        var mrp = document.getElementsByName("mrp[]");
        var amount = document.getElementsByName("amount[]");
        var total = 0;
        for(var i=0; i<qty.length; i++)
        {
            var q = qty[i].value;
            var m = mrp[i].value;
            var amt=0; 
            if(q > 0)
            {
                amt=q*mrp[i].value;
                amount[i].value=amt.toFixed(2);
                total+=amt;

            }
        }
        
        document.getElementById("total").value = (total*1).toFixed(2);
        
    }
	
	function chk_qty(val,val1)
	{
		var id= val1.slice(3);
		var item_id='item_id'+id;
		var qty='qty'+id;
        var amt='amt'+id;
		var response = document.getElementById(item_id).value;
		var str=response.split("-");

		var availableStock = parseInt(str[3], 10);
    	var enteredQty = parseInt(val, 10);

		if (enteredQty > availableStock) 
		{
            alert("Qty is greater than available stock");
            document.getElementById(qty).value = '0';
            document.getElementById(amt).value = '';
    	}
	}
</script>
    <script>
        var counter=1;
    var query="<?php echo $query; ?>";
    </script>
  <script>

 function more() 
 {
    var $table = $('#input_fields');			
    var chk=$('#'+counter).find('select').eq(0).val();
    var item_id='item_id'+counter;
    var qty='qty'+counter;
    var amt='amt'+counter;
    if(chk!="")
    {
        counter++;
        var str1="<tr id='"+counter+"'><td align='left' valign='middle'><div class='form-group'><select id='"+item_id+"'class='form-control' name='item_id[]' onchange=' getValues1(this.value); more(); ' tabindex='1'><option value=''>--Select--</option>"+query+"</select></div></td><td> <div class='form-group'><input type='text' class='form-control' name='mrp[]' onkeyup='calc();'/></div></td><td> <div class='form-group'><input type='text' class='form-control' name='qty[]' id='"+qty+"'  onkeyup=\"calc();  chk_qty(this.value,'"+qty+"');\" tabindex='1'/></div></td><td> <div class='form-group'><input type='text' class='form-control' name='amount[]'id='"+amt+"' onkeyup='calc();'/></div></td><td><a onclick='delete_row("+counter+");'><i class='fa fa-times'></i></a></td></tr>";
        
        $("#input_fields").append(str1);
    }
  }
   function chk()
   {
		if(document.frm2.party.value=="" && document.frm2.party1.value=="")
		{
			alert("Please select a party or add new party!!!");
			return false;
		}
		return confirm('Sure?');
   }
   function chk_aval(val)
   {
        // console.log(v_id);
        // console.log(stock);
   }
  </script>
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu2=true; $smenu2="7";  $ssmenu2="71"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Master</a></li>
					<li><a href="#">Damage Master</a></li>
                    <li class="active"><?php if(isset($_REQUEST['d_id'])) {?>Modify Damage Entry<?php } else { ?>Add Damage Entry<?php }?></li>
                </ul>
                <!-- END BREADCRUMB -->
                             
                
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
                            <form class="form-horizontal" method="post" action="adddamage.php" name="frm2" enctype="multipart/form-data" onsubmit="return calc();">
                            <div class="panel panel-default">
                                <div class="panel-body"> 
                                    <div class="panel-heading">
                                        <?php
                                            if(isset($_REQUEST['d_id']))
                                            {
                                                $d1=mysqli_query($con,"select * from damage_master where d_id='$_REQUEST[d_id]'");
                                                $d=mysqli_fetch_row($d1);
                                                echo "<input type='hidden' name='d_id' value='$_REQUEST[d_id]'/>";
                                        ?>
                                        <h3 class="panel-title"><strong>Modify</strong> Damage Entry</h3>
                                        <?php
                                            }
                                            else
                                            {
                                                $d[1]=$d[2]=$d[3]=$d[4]=$d[5]=$d[6]=$d[7]="";
                                                $d[1]=date("Y-m-d");
                                        ?>
                                        <h3 class="panel-title"><strong>Add New</strong> Damage Entry</h3>
                                        <?php
                                            }
                                        ?>
                                    </div>  
									<div class="row">
                                        <div class="col-md-12">
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-actions">
												<tbody>  
													<tr>
														<th width="15%">Date</th>
														<td><div class="form-group">
                                                            <input type="date" class="form-control" name="ddate" value='<?php echo $d[1]; ?>' required />
														</div></td>
                                                        <th width="10%">Remarks</th>
														<td><div class="form-group">
                                                            <textarea class="form-control" rows="2" placeholder="Your remark..." name="remark"><?php echo $d[2]; ?></textarea>
														</div></td>
													</tr>
													
													<tr>
														<td colspan='4'>
															<div class="table-responsive">
															<table class="table table-bordered table-striped table-actions" id="input_fields">
																<thead>
																	<tr>
																		<th>Product Name<br>(Code - Desp - Variant)</th>
																		<th>MRP</th>
																		<th>Qty</th>
																		<th>Amount</th>
																	</tr>
																</thead>
																<tbody>
																	<?php
																		if(isset($_REQUEST['d_id']))
																		{
																			$k1=mysqli_query($con,"select * from damage_particular where d_id='$_REQUEST[d_id]'");
																			if($k=mysqli_fetch_row($k1))
																			{
																			$a=0;
																				do{
																					$a++;
																	?>
																	<tr id="<?php echo $a; ?>">
																		<td align="left" valign="middle">
																			<div class="form-group">
																					<select class="form-control" name="item_id[]" id="item_id<?php echo $a; ?>" onchange=' getValues1(this.value); more(); ' tabindex='1'>
																								<option value="">--Select--</option>
																						<?php
																							$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1) order by v_id");
																							while($f=mysqli_fetch_row($f1))
																							{
																								$c=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																								if($k[2]==$f[0])
																								echo "<option value='$f[0]-$f[5]-$c[7]-$f[6]-$f[4]' selected>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
																								else
																								echo "<option value='$f[0]-$f[5]-$c[7]-$f[6]-$f[4]'>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
																							}
																						?>	
																					</select>
																			</div>
																		</td>
																		
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="mrp[]" onkeyup="calc();" value="<?php echo $k[4]; ?>"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="qty[]" id="qty<?php echo $a; ?>" value="<?php echo $k[3]; ?>" onkeyup="calc();  chk_qty(this.value,'<?php echo 'qty'.$a; ?>');" tabindex='1'/>
																		</div></td>
																		
																		<td> <div class="form-group">
																			<input type="text" class="form-control" id="amt<?php echo $a; ?>" name="amount[]" onkeyup="calc();"/>
																		</div></td>
																		<td><a onclick='delete_row(<?php echo $a; ?>);'><i class='fa fa-times'></i></a></td>
																	</tr>
																	<?php
																			}while($k=mysqli_fetch_row($k1));
																			echo "<script>counter=$a;</script>";
																			}
																			else
																				goto l1;
																		}
																		else
																		{
																			$qty=1;
																			l1:
																	?>
																	<tr id='0'>
																		<td align="left" valign="middle">
																			<div class="form-group">
																				<select class="form-control" name="item_id[]" id="item_id<?php echo $qty; ?>" onchange=' getValues1(this.value); more(); ' tabindex='1'>
																							<option value="">--Select--</option>
																					<?php
																						$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1) and stock>0 order by v_id");										
																						while($f=mysqli_fetch_row($f1))
																						{
																							$c=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																							
																							echo "<option value='$f[0]-$f[5]-$c[7]-$f[6]-$f[4]'>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
																						}
																					?>	
																				</select>
																			</div>
																		</td>
																		
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="mrp[]" onkeyup="calc();" />
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="qty[]" id="qty<?php echo $qty; ?>" onkeyup="calc(); chk_qty(this.value,'<?php echo 'qty'.$qty; ?>');" tabindex='1'/>
																		</div></td>
																		
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="amount[]" onkeyup="calc();" id="amt<?php echo $qty; ?>"/>
																		</div></td>
																		<td><a onclick='delete_row(0);'><i class='fa fa-times'></i></a></td>
																	</tr>
																	<?php
																	echo "<script>counter++;</script>";
																		}
																	?>
																</tbody>
																<tr>
                                                                    <td colspan='2'></td>
                                                                    <th>Total</th>
                                                                    <td><div class="form-group">
																			<input type="text" class="form-control" id="total" name="total" onkeyup="calc();" />
																		</div></td>
                                                                </tr>
															</table>
															</div>
														</td>
													</tr>
													
												</tbody>
											</table>
										</div>
										
										</div>
									</div>
									
                                </div>
                                <div class="panel-footer">
									<?php
										if(isset($_REQUEST['d_id']))
										{
											echo "<script>calc()</script>";
									?>
									<button class="btn btn-primary" type="submit" name="s4" tabindex='1'>Modify</button>
									<?php
										}
										else
										{
									?>
									<button class="btn btn-primary" type="submit" name="s1" tabindex='1'>Save</button>
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