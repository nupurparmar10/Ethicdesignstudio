<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";
    if(!isset($_REQUEST['m_id']))
    {
        header("Location: viewmanu.php"); die;
    }
	if(isset($_REQUEST['s1']))
	{
        $m_id=$_REQUEST['m_id'];

        $pid1=mysqli_query($con,"select max(item_id) from item_details");
        $pid=mysqli_fetch_row($pid1);

        for($new=1;$new<=$_REQUEST['newitem'];$new++)
        {
            $itemid=++$pid[0];       
            $pcode="ED";
            if($itemid<10) $pcode.="0".$itemid; else $pcode.=$itemid;

            $ptype=$_REQUEST['ptype'.$new];
            $hsn1=mysqli_fetch_row(mysqli_query($con,"select hsn from producttype where ptname = '$ptype'"));
            $hsn=$hsn1[0];
            $saledesp=$_REQUEST['saledesp'.$new];
            $tax=$_REQUEST['tax'.$new][0];
            $s_id=$_REQUEST['s_id'.$new];
            mysqli_query($con,"insert into item_details set item_id='$itemid', pcode='$pcode',ptype='$ptype',purdesp='',hsn='$hsn',saledesp='$saledesp',unit='PCS',tax='".$tax."',website='0',status='1', s_id='$s_id'");

            $count=count($_REQUEST['qty'.$new]);
            for($i=0;$i<$count;$i++)
            {
                $qty=$_REQUEST['qty'.$new][$i];
                if($qty!='' && $qty>0)
                {
                    $size = $_REQUEST['size'.$new][$i];
                    $color = $_REQUEST['color'.$new][$i];
                    $edsellrate= $_REQUEST['edsellrate'.$new][$i];
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='$size', color='$color',stock='$qty',webstock='0',purrate='0',edsellrate='$edsellrate'");

                    $v=mysqli_fetch_row(mysqli_query($con,"select v_id from variant where item_id =$itemid and size='$size' and color='$color'"));
                    $barcode = encryptId($v[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$v[0]'");
                    $fabric_cost=$_REQUEST['fabric_cost'.$new][$i];
                    $manu_cost=$_REQUEST['manu_cost'.$new][$i];
                    $tax=$_REQUEST['tax'.$new][$i];
                    mysqli_query($con,"insert into manu_item set m_id =$m_id, v_id='$v[0]', qty='$qty', fabric_cost='$fabric_cost', manu_cost='$manu_cost',tax='$tax',edsellrate='$edsellrate'");
                }
            }
            $count=count($_REQUEST['fqty']);
            for($i=0;$i<$count;$i++)
            {
                $v_id=$_REQUEST['v_id'][$i];
                $qty=(float)$_REQUEST['fqty'.$new][$i];
                mysqli_query($con,"insert into manu_itemdesp set m_id='$m_id', item_id='$itemid', v_id='$v_id', qty='$qty'");
                mysqli_query($con,"update manu_fabric set remqty=remqty-$qty where m_id='$m_id' and v_id='$v_id'");
            }
        }
		
        $date=date("Y-m-d");

        $chk1=mysqli_query($con,"select * from manu_fabric where m_id='$m_id' and remqty!=0");
        if(!$chk=mysqli_fetch_row($chk1))
        {
		    mysqli_query($con,"update manufacturejob set status=1, completed='$date' where m_id='$m_id'");
        }
        $tid=mysqli_fetch_row(mysqli_query($con,"select max(trans_id) from transaction"));
        $tid[0]++;
        $cost=$_REQUEST['totalcost'];
        $m=mysqli_fetch_row(mysqli_query($con,"select * from manufacturejob where m_id='$m_id'"));
        mysqli_query($con,"insert into transaction set trans_id='$tid[0]', tdate='$date', ledger_id='$m[2]', amount='$cost', particulars='Job Amount Due', type='Cr.', relatedto='$m[4]'");
		header("Location: viewmanu.php?msg=set"); die;
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
    function chkqty()
    {
        var newitem=document.frm2.newitem.value;
        var totqty=document.getElementsByName("fqty[]");
        var usedqty = [];
        for(var j=0; j<totqty.length; j++)
        {
            usedqty[j]=0;
        }
        for(var i=1; i<=newitem; i++)
		{
            var fqty = document.getElementsByName("fqty"+i+"[]");  
            for(var j=0; j<fqty.length; j++)
            {
                usedqty[j]+=fqty[j].value*1;
            }
        }
        for(var j=0; j<totqty.length; j++)
        {
            if(usedqty[j]>totqty[j].value)
            {
                alert("Fabric "+(j+1)+" Qty Mismatch!!! Plz Chk");
                return false;
            }   
        }
        return true;
    }
</script>
<script>
    
 function more(id) {
			var $table = $('#input_fields'+id);
            var $tr = $table.find('tr').eq(1).clone();
            $tr.appendTo($table).find('input').val('');
            $tr.appendTo($table).find('input').eq(5).val('5');
			$tr.appendTo($table).find('select').eq(0).val('');
            $("#input_fields"+id).append($tr);
            $tr.appendTo($table).find('select').eq(0).focus();
            calc1(id);
  }
  function getsubcategory(ptype,id)
    {
        $.ajax({
        url : 'getsubcategory.php',
        type : 'POST',
        data : {ptype : ptype, id : id},
        success : ajaxSuccess1,
        error : ajaxError
        });
    }
    function ajaxSuccess1(response)
    {
        var str=response.split(";;");
        $('#subcategory'+str[1]).html(str[0]);
    }
    function ajaxError()
        {
        alert("error");
        }

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
                            <form class="form-horizontal" method="post" action="submit_manu3.php" enctype="multipart/form-data" name='frm2' onsubmit="return chkqty();">
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
                                                                        echo "<input type='hidden' value='$d[2]' name='v_id[]'/>";
                                                                        echo "<input type='hidden' value='$d[5]' name='fqty[]'/>";
                                                                        echo "<input type='hidden' value='$d[4]' name='rate[]'/>";
                                                                        }
                                                                    ?>
                                                                </tbody>
                                                                <tr>
                                                                    <td colspan='4' align='right'>Total</td>
                                                                    <th> <?php echo $tot; ?></th>
                                                                </tr>
                                                            </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        echo "<input type='hidden' name='newitem' value='$_REQUEST[newitem]'/>";
                                                        for($i=1;$i<=$_REQUEST['newitem'];$i++)
                                                        {
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
                                                                                <td><div class="form-group">
                                                                                    <select class="form-control"  name="ptype<?php echo $i; ?>" required id="ptype<?php echo $i; ?>" onchange="getsubcategory(this.value,'<?php echo $i; ?>');">
                                                                                        <option value=''>--Select--</option>
                                                                                        <?php
                                                                                            $f1=mysqli_query($con,"select * from producttype");
                                                                                            while($f=mysqli_fetch_row($f1))
                                                                                            {
                                                                                            echo "<option value='$f[1]'>$f[1]</option>";
                                                                                            }
                                                                                        ?>	
                                                                                    </select>
                                                                                </div></td>
                                                                                <td id="subcategory<?php echo $i; ?>"><div class="form-group">
                                                                                    <select class="form-control"  name="s_id<?php echo $i; ?>" id="s_id<?php echo $i; ?>" >
                                                                                        <option value=''>--Select--</option>
                                                                                    </select>
                                                                                </div></td>
                                                                                <td><div class="form-group">
                                                                                    <input type="text" class="form-control" name="saledesp<?php echo $i; ?>" required/>
                                                                                </div></td>
                                                                            </tr>
                                                                            <tr><td colspan='3'><table class="table table-bordered">
                                                                            <thead>
                                                                                <th>Fabric</th>
                                                                                <th>Total MTR Consumed</th>
                                                                            </thead>
                                                                            <tbody>
                                                                            <?php
                                                                                    $d1=mysqli_query($con,"select * from manu_fabric where m_id='$_REQUEST[m_id]' order by id");
                                                                                    while($d=mysqli_fetch_row($d1))
                                                                                    {
                                                                                        $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$d[2]'"));
                                                                                        $item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
                                                                                ?>
                                                                                <tr>
                                                                                    <td><?php echo "$item[1]-$f[3]"; ?></td>
                                                                                    <td><div class="form-group">
                                                                                    <input type="text" class="form-control" name="fqty<?php echo $i; ?>[]" onkeyup="calc1(<?php echo $i; ?>);"/>
                                                                                </div></td>
                                                                                </tr>
                                                                                <?php
                                                                                    }
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
                                                                                <tr>
                                                                                <td align="left" valign="middle">
                                                                                        <select class="form-control" name="size<?php echo $i; ?>[]">
                                                                                            <option value="">--Select--</option>
                                                                                            <option value="XS">XS</option>
                                                                                            <option value="S">S</option>
                                                                                            <option value="M">M</option>
                                                                                            <option value="L">L</option>
                                                                                            <option value="XL">XL</option>
                                                                                            <option value="2XL">2XL</option>
                                                                                            <option value="3XL">3XL</option>
                                                                                            <option value="4XL">4XL</option>
                                                                                            <option value="5XL">5XL</option>
                                                                                            <option value="6XL">6XL</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                        <input type="text" class="form-control" name="color<?php echo $i; ?>[]"/>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                        <input type="text" class="form-control" name="qty<?php echo $i; ?>[]" onkeyup="calc1(<?php echo $i; ?>); getcost();"/>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" name="fabric_cost<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);" readonly style='color:black;'/>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" name="manu_cost<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>); getcost();"/>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" name="newcost<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);"/>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> 
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" name="tax<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);" value='5'/>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td> <div class="form-group">
                                                                                        <input type="text" class="form-control" name="taxval<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);"/></div></td>
                                                                                    <td> <div class="form-group">
                                                                                        <input type="text" class="form-control" name="sellcost<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);"/>
                                                                                    </div></td>
                                                                                    <td> <div class="form-group">
                                                                                        <input type="number" class="form-control" name="edsellrate<?php echo $i; ?>[]" onkeyup="calc(<?php echo $i; ?>);"/>
                                                                                    </div></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <button class="btn btn-primary" onClick="more(<?php echo $i; ?>);" type="button">Add More</button> 
                                                                        </td></tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            </div>
                                                        </td>
                                                    </tr>	
                                                    <?php
                                                        }
                                                    ?>									
												</tbody>
											</table>
										</div>
										<div class="form-group">
                                            <label><b style='color:red;'>Total Manufacturing Cost</b></label>
                                            <input type="text" class="form-control" name="totalcost" onkeyup="getcost();" style='max-width:150px;'/>
                                        </div>
										</div>
									</div>
                                </div>
                                <div class="panel-footer">							
									<button class="btn btn-primary" type="submit" name="s1">Complete Job</button>
									
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