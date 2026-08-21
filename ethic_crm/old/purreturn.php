<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";
	if(isset($_REQUEST['msg']))
	{
		$msg="Purchase Bill Added Successfully!!!";
	}
	if(isset($_REQUEST['s1']))
	{
		$party=$_REQUEST['party'];
		$paidby=$_REQUEST['paidby'];
		$amt=$_REQUEST['gtotal'];
		$roundoff=$_REQUEST['roundoff'];
		$invdate=$_REQUEST['invdate'];
		$pid1=mysqli_query($con,"select max(pur_id) from purreturn");
		$pid=mysqli_fetch_row($pid1);
		$id=$pid[0]+1;
		
		$rid="PR".$id;		
		$count=count($_REQUEST['qty']);
		for($i=0;$i<$count;$i++)
		{
			$qty=$_REQUEST['qty'][$i];
			$v_id=$_REQUEST['v_id'][$i];
			if($v_id!="" && $qty>0)
			{
				$product=explode("-",$_REQUEST['v_id'][$i])[0];
				$purrate=$_REQUEST['purrate'][$i];
				$taxper=$_REQUEST['taxper'][$i];
				$dis=$_REQUEST['dis'][$i];
				mysqli_query($con,"insert into pr_items set v_id='$product', `stock`='$qty', `purrate`='$purrate',`tax`='$taxper', pur_id='$id', dis='$dis'");
				mysqli_query($con,"update variant set stock=stock-$qty  where v_id='$product'");
			}
		}
	

		mysqli_query($con,"insert into purreturn set pur_id=$id, party='".$party."', invno='".$_REQUEST['invno']."', invdate='".$invdate."', paidby='".$paidby."', roundoff='$_REQUEST[roundoff]', amount='".$amt."',  relatedwith='$rid', spdis='$_REQUEST[spdis]', freight='$_REQUEST[freight]', remark='$_REQUEST[remark]', transport='$_REQUEST[transport]', other='$_REQUEST[other]', oname='$_REQUEST[oname]',aginvno='$_REQUEST[aginvno]'");
		
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$party."', amount='".($amt+(float)$_REQUEST['spdis'])."', particulars='Purchase. Return Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		
		
		if($paidby!="Credit")
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$paidby."', amount='".$amt."', particulars='Purchase. Return Inv. No. :$_REQUEST[invno]', 	type='Dr.', relatedto='$rid'");	
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$party."', amount='".$amt."', particulars='Amount Paid Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
		}
		$tid++;		
		$amt=(float)$amt-+(float)$_REQUEST['spdis']-(float)$_REQUEST['freight']-(float)$_REQUEST['transport']-(float)$_REQUEST['other']-$_REQUEST['taxtot'];
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='1', amount='".($amt)."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
		
		$tid++;
		if($_REQUEST['taxtot']>0)
		{
			$tid++;
			$tax=round($_REQUEST['taxtot']/2,2);
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='7', amount='".$tax."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");			
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='8', amount='".$tax."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}

		if($_REQUEST['spdis']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='6', amount='".$_REQUEST['spdis']."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		if($_REQUEST['freight']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='9', amount='".$_REQUEST['freight']."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		if($_REQUEST['transport']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='12', amount='".$_REQUEST['transport']."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		if($_REQUEST['other']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='14', amount='".$_REQUEST['other']."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		$tid++;
		
		
		if($roundoff!=0.00)
		{
			if($_REQUEST['roundoff']<0)
			{
				$r=$roundoff-($roundoff*2);
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='13', amount='".$r."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");	
			}
			else
			{
				$r=$roundoff;
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='13', amount='".$r."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
			}
		}
		header("Location: purreturn.php?msg=set"); die;
	}
	if(isset($_REQUEST['s3']))
	{
		$old=mysqli_fetch_row(mysqli_query($con,"select relatedwith from purreturn where pur_id='$_REQUEST[pur_id]'"));
		
		$p1=mysqli_query($con,"select * from pr_items where pur_id='$_REQUEST[pur_id]'");
		while($p=mysqli_fetch_row($p1))
		{
			mysqli_query($con,"update variant set stock=stock+$p[1] where v_id='$p[0]'");
		}
		mysqli_query($con,"delete from pr_items where pur_id='$_REQUEST[pur_id]'");
		mysqli_query($con,"delete from transaction where relatedto='$old[0]'");
		
		$party=$_REQUEST['party'];
		$paidby=$_REQUEST['paidby'];
		$amt=$_REQUEST['gtotal'];
		$roundoff=$_REQUEST['roundoff'];
		$invdate=$_REQUEST['invdate'];
		$rid=$old[0];
		$id=$_REQUEST['pur_id'];
		
		$count=count($_REQUEST['qty']);
		for($i=0;$i<$count;$i++)
		{
			$qty=$_REQUEST['qty'][$i];
			$v_id=$_REQUEST['v_id'][$i];
			if($v_id!="" && $qty>0)
			{
                $product=explode("-",$_REQUEST['v_id'][$i])[0];
				$purrate=$_REQUEST['purrate'][$i];
				$taxper=$_REQUEST['taxper'][$i];
				$dis=$_REQUEST['dis'][$i];
				mysqli_query($con,"insert into pr_items set v_id='$product', `stock`='$qty', `purrate`='$purrate',`tax`='$taxper', pur_id='$id', dis='$dis'");
				mysqli_query($con,"update variant set stock=stock+$qty  where v_id='$product'");
			}
		}
		
        mysqli_query($con,"update purreturn set  party='".$party."', invno='".$_REQUEST['invno']."', invdate='".$invdate."', paidby='".$paidby."', roundoff='$_REQUEST[roundoff]', amount='".$amt."',  relatedwith='$rid', spdis='$_REQUEST[spdis]', freight='$_REQUEST[freight]', remark='$_REQUEST[remark]', transport='$_REQUEST[transport]', other='$_REQUEST[other]', oname='$_REQUEST[oname]',aginvno='$_REQUEST[aginvno]' where pur_id='$id'");
		
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$party."', amount='".($amt+(float)$_REQUEST['spdis'])."', particulars='Purchase. Return Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		
		
		if($paidby!="Credit")
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$paidby."', amount='".$amt."', particulars='Purchase. Return Inv. No. :$_REQUEST[invno]', 	type='Dr.', relatedto='$rid'");	
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$party."', amount='".$amt."', particulars='Amount Paid Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
		}
		$tid++;		
		$amt=(float)$amt-+(float)$_REQUEST['spdis']-(float)$_REQUEST['freight']-(float)$_REQUEST['transport']-(float)$_REQUEST['other']-$_REQUEST['taxtot'];
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='1', amount='".($amt)."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
		
		$tid++;
		if($_REQUEST['taxtot']>0)
		{
			$tid++;
			$tax=round($_REQUEST['taxtot']/2,2);
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='7', amount='".$tax."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");			
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='8', amount='".$tax."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}

		if($_REQUEST['spdis']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='6', amount='".$_REQUEST['spdis']."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		if($_REQUEST['freight']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='9', amount='".$_REQUEST['freight']."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		if($_REQUEST['transport']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='12', amount='".$_REQUEST['transport']."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		if($_REQUEST['other']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='14', amount='".$_REQUEST['other']."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		$tid++;
		
		
		if($roundoff!=0.00)
		{
			if($_REQUEST['roundoff']<0)
			{
				$r=$roundoff-($roundoff*2);
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='13', amount='".$r."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");	
			}
			else
			{
				$r=$roundoff;
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='13', amount='".$r."', particulars='Return Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
			}
		}
		header("Location: viewpurreturn.php?msg=set"); die;
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
		function getvalues(val)
        {
            var v_id = document.getElementsByName("v_id[]");	
            var purrate = document.getElementsByName("purrate[]");
            var taxper = document.getElementsByName("taxper[]");
            for(i=0;i<v_id.length;i++)
            {
                if(v_id[i].value==val)
                {
                    str=(v_id[i].value).split("-");
                    purrate[i].value=str[1];
                    taxper[i].value=str[2];
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
                    var val=amt*dis[i].value/100;
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
        function calc1()
        {
            var value = document.frm2.spdisvalue.value;
            var type = document.frm2.spdistype.value;
            if(type=="%")
            {
                var amt=document.getElementById("total").value*1 *value*1/100;			
            }
            else amt=value;
            document.getElementById("spdis").value=amt;
            calc();
        }
    </script>
    <script>
        var counter=1;  
    </script>
    <script>
        function more() 
        {
            var $table = $('#input_fields');
            var chk=$('#'+(counter-1)).find('select').eq(0).val();
            if(chk!="")
            {
                var $tr = $table.find('tr').eq(1).clone();
                $tr.attr("id",counter);
                $tr.find('select').eq(0).attr("id","v"+counter);
                $tr.appendTo($table).find('input').val('');
                $tr.appendTo($table).find('select').eq(0).val('');
                $("#input_fields").append($tr);
                counter++;
            }
        }
    </script>
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu9=true; $smenu9="7"; include_once("sidebar.php"); ?>
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
				   <li><a href="viewpurreturn.php">Purchase Master</a></li>
                   <li class="active">Purchase Return</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                   <h2><span class="fa fa-lsit"></span><?php if(isset($_REQUEST['pur_id'])) echo "Modify Purchase Return"; else echo "Add Purchase Return"; ?></h2>
				   <br><br><br>
				   <p style="color:red;">Please keep an eye on the stock...</p>
				   <span id="form_response"></span>
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
                            <form class="form-horizontal" method="post" action="purreturn.php" enctype="multipart/form-data" onsubmit="return confirm('Sure?');" name='frm2'>
                            <div class="panel panel-default">
                                <div class="panel-body">   
									<div class="row">
                                        <div class="col-md-12">
										<?php
											if(isset($_REQUEST['pur_id']))
											{
												$p1=mysqli_query($con,"select * from purreturn where pur_id='$_REQUEST[pur_id]'");
												$p=mysqli_fetch_row($p1);
												echo "<input type='hidden' name='pur_id' value='$_REQUEST[pur_id]'/>";
											}
											else
											{
												$p[1]=$p[2]=$p[3]=$p[4]=$p[5]=$p[6]=$p[7]=$p[8]=$p[9]=$p[10]=$p[11]=$p[12]=$p[13]=$p[14]=$p[15]=$p[16]=$p[17]=$p[18]=$p[19]=$p[21]=$p[22]="";
												$p[1]=date("Y-m-d");
												$p[20]="Other Charges";
											}
										?>
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-actions">
												<tbody>  
													<tr>
														<th width="10%">Return Invoice No.</th>
														<td><div class="form-group">
															<input type="text" class="form-control" name="invno" value="<?php echo $p[3]; ?>" required tabindex='1'/>
														</div></td>
														<th width="15%">Date</th>
														<td><div class="form-group">
															<input type="date" class="form-control" name="invdate" value="<?php  echo $p[1]; ?>" required tabindex='1'/>
														</div></td>														
													</tr>
													<tr>
														<th width="10%">Vendor</th>
														<td width='35%' colspan='3'><div class="form-group" style='width:89%; float:left;'>
															<select class="form-control" name="party" id="party" tabindex='1'>
																<option value="">--Select--</option>
																<?php
																	$f1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (26) order by name");
																	while($f=mysqli_fetch_row($f1))
																	{
																		if($p[2]==$f[0])
																			echo "<option value='$f[0]' selected='selected'>$f[1]</option>";
																		else
																			echo "<option value='$f[0]'>$f[1]</option>";
																	}
																?>	
															</select>
															</div>
														</td>
														
													</tr>
													<tr>
														<th width="10%">Payment Mode</th>
														<td><div class="form-group">
															<select class="form-control" name="paidby" required tabindex='1'>
																<option value="">--Select--</option>
																<option value="Credit" <?php if($p[5]=="Credit") { ?> selected='selected' <?php } ?>>Credit</option>
																<?php
																	$list1=mysqli_query($con,"select ledger_id,name from ledger_accounts where (group_id in (select group_id from group_master where group_name='Bank Accounts') or name='Cash Account') and status=1 order by name");
																	if($l=mysqli_fetch_row($list1))
																	{
																		do{
																			if($p[5]==$l[0])
																				echo "<option value='$l[0]' selected='selected'>$l[1]</option>";
																			else
																				echo "<option value='$l[0]'>$l[1]</option>";
																		}while($l=mysqli_fetch_row($list1));
																	}
																?>
															</select>
														</div></td>
                                                        <th>Against Invoice No.</th>
														<td><div class="form-group">
															<input type="text" class="form-control" name="aginvno" value="<?php echo $p[13]; ?>"/>
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
																		
																		<th></th>
																	</tr>
																</thead>
																<tbody>
																	<?php
																		if(isset($_REQUEST['pur_id']))
																		{
																			$k1=mysqli_query($con,"select * from pr_items where pur_id='$_REQUEST[pur_id]'");
																			if($k=mysqli_fetch_row($k1))
																			{
																			$a=-1;
																				do{
																					$a++;
																	?>
																	<tr id="<?php echo $a; ?>">
																		<td align="left" valign="middle">
																			<select class="form-control" name="v_id[]" onchange='getvalues(this.value); more();' style='width:160px;' id="v<?php echo $a; ?>" tabindex='1'>
																				<option value="">--Select--</option>
																				<?php
																					$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1)");
																					while($f=mysqli_fetch_row($f1))
																					{
																						$item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																						if($k[1]==$f[0])
																						{
																							$str="$f[0]-$f[4]-$item[7]-$f[5]";
																							echo "<option value='$f[0]-$f[4]-$item[7]-$f[5]' selected>$item[1] - $item[5] $f[2] $f[3]</option>";
																						}
																						else
																						echo "<option value='$f[0]-$f[4]-$item[7]-$f[5]'>$item[1] - $item[5] $f[2] $f[3]</option>";
																					}
																				?>	
																			</select>
																		</td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="qty[]" onkeyup="calc();" tabindex='1' value='<?php echo $k[2]; ?>'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purrate[]" onkeyup="calc();" tabindex='1' value='<?php echo $k[3]; ?>'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="dis[]" onkeyup="calc();" tabindex='1' value='<?php echo $k[6]; ?>'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxable[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxper[]" onkeyup="calc();" value='<?php echo $k[4]; ?>'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxamt[]" onkeyup="calc();" style='min-width:80px;'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purvalue[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="tpurrate[]" onkeyup="calc();"/>
																		</div></td>
																		
																		<td><a onclick='delete_row(0);'><i class='fa fa-times'></i></a></td>
																	</tr>
																	<?php
																			}while($k=mysqli_fetch_row($k1));
																			$a++;
																			echo "<script>counter=$a; more();</script>";
																			}
																			else
																				goto l1;
																		}
																		else if(isset($_REQUEST['v_id']))
																		{
																			for($i=0;$i<count($_REQUEST['v_id']);$i++)
																			{
																				$str="";
																	?>
																	<tr id="<?php echo $i; ?>">
																		<td align="left" valign="middle">
																			<select class="form-control" name="v_id[]" onchange='getvalues(this.value); more();' style='width:160px;' id="v<?php echo $i; ?>" tabindex='1'>
																				<option value="">--Select--</option>
																				<?php
																					$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1)");
																					while($f=mysqli_fetch_row($f1))
																					{
																						$item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																						if($_REQUEST['v_id'][$i]==$f[0])
																						{
																							$str="$f[0]-$f[4]-$item[7]-$f[5]";
																							echo "<option value='$f[0]-$f[4]-$item[7]-$f[5]' selected>$item[1] - $item[5] $f[2] $f[3]</option>";
																						}
																						else
																						echo "<option value='$f[0]-$f[4]-$item[7]-$f[5]'>$item[1] - $item[5] $f[2] $f[3]</option>";
																					}
																				?>	
																			</select>
																		</td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="qty[]" onkeyup="calc();" tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purrate[]" onkeyup="calc();" tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="dis[]" onkeyup="calc();" value='0' tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxable[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxper[]" onkeyup="calc();" value='5'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxamt[]" onkeyup="calc();" style='min-width:80px;'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purvalue[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="tpurrate[]" onkeyup="calc();"/>
																		</div></td>
																		
																		<td><a onclick='delete_row(0);'><i class='fa fa-times'></i></a></td>
																	</tr>
																	<?php
																				echo "<script>getvalues('$str');</script>";
																			}
																			$i++;
																			echo "<script>counter=$i; more();</script>";
																		}
																		else if(isset($_REQUEST['item_id']))
																		{
																			for($i=0,$j=0;$i<count($_REQUEST['item_id']);$i++)
																			{
																				$v1=mysqli_query($con,"select v_id from variant where item_id='".$_REQUEST['item_id'][$i]."'");
																				while($v=mysqli_fetch_row($v1))
																				{
																				$str="";
																	?>
																	<tr id="<?php echo $j; ?>">
																		<td align="left" valign="middle">
																			<select class="form-control" name="v_id[]" onchange='getvalues(this.value); more();' style='width:160px;' id="v<?php echo $j; ?>" tabindex='1'>
																				<option value="">--Select--</option>
																				<?php
																					$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1)");
																					while($f=mysqli_fetch_row($f1))
																					{
																						$item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																						if($v[0]==$f[0])
																						{
																							$str="$f[0]-$f[4]-$item[7]-$f[5]";
																							echo "<option value='$f[0]-$f[4]-$item[7]-$f[5]' selected>$item[1] - $item[5] $f[2] $f[3]</option>";
																						}
																						else
																						echo "<option value='$f[0]-$f[4]-$item[7]-$f[5]'>$item[1] - $item[5] $f[2] $f[3]</option>";
																					}
																				?>	
																			</select>
																		</td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="qty[]" onkeyup="calc();" tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purrate[]" onkeyup="calc();" tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="dis[]" onkeyup="calc();" value='0' tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxable[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxper[]" onkeyup="calc();" value='5'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxamt[]" onkeyup="calc();" style='min-width:80px;'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purvalue[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="tpurrate[]" onkeyup="calc();"/>
																		</div></td>
																		
																		<td><a onclick='delete_row(0);'><i class='fa fa-times'></i></a></td>
																	</tr>
																	<?php
																				$j++;
																				echo "<script>getvalues('$str');</script>";
																				}
																			}
																			$j++;
																			echo "<script>counter=$j; more();</script>";
																		}
																		else
																		{
																			l1:
																	?>
																	<tr id="0">
																		<td align="left" valign="middle">
																			<select class="form-control" name="v_id[]" onchange='getvalues(this.value); more();' style='width:160px;' id="v0" tabindex='1'>
																				<option value="">--Select--</option>
																				<?php
																					$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1)");
																					while($f=mysqli_fetch_row($f1))
																					{
																						$item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																						echo "<option value='$f[0]-$f[4]-$item[7]-$f[5]'>$item[1] - $item[5] $f[2] $f[3]</option>";
																					}
																				?>	
																			</select>
																		</td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="qty[]" onkeyup="calc();" tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purrate[]" onkeyup="calc();" tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="dis[]" onkeyup="calc();" value='0' tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxable[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxper[]" onkeyup="calc();" value='5'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxamt[]" onkeyup="calc();" style='min-width:80px;'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="purvalue[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="tpurrate[]" onkeyup="calc();"/>
																		</div></td>
																		
																		<td><a onclick='delete_row(0);'><i class='fa fa-times'></i></a></td>
																	</tr>
																	<?php
																		}
																	?>
																</tbody>
																<tr>
																	<td align='right'>Total</td>
																	<td> <div class="form-group">
																			<input type="text" class="form-control" name="qtytot" onkeyup="calc();" id="qtytot"/>
																	</div></td>
																	<td> </td>
																	<td> </td>
																	<td> <div class="form-group">
																		<input type="text" class="form-control" name="taxabletot" onkeyup="calc();" id="taxabletot"/>
																	</div></td>
																	<td></td>
																	<td> <div class="form-group">
																		<input type="text" class="form-control" name="taxtot" onkeyup="calc();" id="taxtot"/>
																	</div></td>
																	<td> <div class="form-group">
																			<input type="text" class="form-control" name="total" id="total" onkeyup="calc();"/>
																	</div></td>
																	<td colspan='4'></td>
																</tr>
															</tbody>
															</table>
															</div>														
														</td>
													</tr>
													<tr>
														<th rowspan='6'>Remark</th>
														<td rowspan='6'> <div class="input-group">
															<textarea class="form-control" rows="5" style="width:400px;" name="remark" tabindex='1'><?php echo $p[5]; ?></textarea>
														</div></td>
														<th  style="width:300px;">Special Discount</th>
														<td><div class="input-group">
															<input type="text" tabindex='1' class="form-control" name="spdisvalue" id="spdisvalue" onkeyup="calc1();" style="width:50px;" value="<?php echo $p[6]; ?>"/>
															<select class="form-control" name="spdistype" id="spdistype" onchange="calc1();" style="width:50px;" tabindex='1'>
																<option>Cash</option>
																<option>%</option>
															</select>
														</div> 
															<div class="input-group">
															<input type="text" class="form-control" name="spdis" id="spdis" value="<?php echo $p[9]; ?>" onkeyup="calc1();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Freight Charges</th>
														<td> <div class="input-group">
															<input type="text" class="form-control" name="freight" id="freight" value="<?php echo $p[8]; ?>" tabindex='1' onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Transport Charges</th>
														<td> <div class="input-group">
															<input type="text" class="form-control" name="transport" id="transport" value="<?php echo $p[7]; ?>" tabindex='1' onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th><input type="text" class="form-control" name="oname" value="<?php echo $p[13]; ?>" style="width:150px;" tabindex='1'/></th>
														<td> <div class="input-group">
															<input type="text" class="form-control" name="other" id="other" value="<?php echo $p[9]; ?>" tabindex='1' onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Round Off</th>
														<td> <div class="input-group">
															<input type="text" class="form-control" name="roundoff" id="roundoff" value="<?php echo $p[10]; ?>" onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Grand Total</th>
														<td> <div class="input-group">
															<input type="text" class="form-control" name="gtotal" id="gtotal" style="width:100px;" onkeyup="calc();"/>
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
									<button class="btn btn-primary" type="submit" name="s3" tabindex='1'>Modify</button>
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
		<script src="js/model.js"></script>
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->     
    <style>
		.dialogify.fixed
		{
			width:80%;
		}
		.dialogify .dialogify__fixedwidth
		{
			max-width:100%;
		}
		</style>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 $('#add_data').click(function(){
  var options = {
   ajaxPrefix:''
  };
  new Dialogify('addnewproduct.php', options)
   .title('Add New Product')
   .buttons([
    {
     text:'Cancel',
     click:function(e){
      this.close();
     }
    },
    {
     text:'Add',
     type:Dialogify.BUTTON_PRIMARY,
     click:function(e)
     {
		if($('#ptype').val() == "Garments")
		{
			var val1=val2=val3=val4=val5=val6=val7=val8=val9=val10=val11="";
			var color=document.getElementsByName("color[]");
			var size1qty=document.getElementsByName("size1qty[]");
			var size2qty=document.getElementsByName("size2qty[]");
			var size3qty=document.getElementsByName("size3qty[]");
			var size4qty=document.getElementsByName("size4qty[]");
			var size5qty=document.getElementsByName("size5qty[]");
			var size6qty=document.getElementsByName("size6qty[]");
			var size7qty=document.getElementsByName("size7qty[]");
			var size8qty=document.getElementsByName("size8qty[]");
			var size9qty=document.getElementsByName("size9qty[]");
			var size10qty=document.getElementsByName("size10qty[]");			
			for(i=0;i<color.length;i++)
			{
				val1 += color[i].value + ";";
				val2 += size1qty[i].value + ";";
				val3 += size2qty[i].value + ";";
				val4 += size3qty[i].value + ";";
				val5 += size4qty[i].value + ";";
				val6 += size5qty[i].value + ";";
				val7 += size6qty[i].value + ";";
				val8 += size7qty[i].value + ";";
				val9 += size8qty[i].value + ";";
				val10 += size9qty[i].value + ";";
				val11 += size10qty[i].value + ";";
			}		
			var form_data = new FormData();
			form_data.append('ptype', $('#ptype').val());
			form_data.append('purdesp', $('#purdesp').val());
			form_data.append('saledesp', $('#saledesp').val());
			form_data.append('hsn', $('#hsn').val());      
			form_data.append('color', val1);
			form_data.append('size1qty', val2);
			form_data.append('size2qty', val3);
			form_data.append('size3qty', val4);
			form_data.append('size4qty', val5);
			form_data.append('size5qty', val6);
			form_data.append('size6qty', val7);
			form_data.append('size7qty', val8);
			form_data.append('size8qty', val9);
			form_data.append('size9qty', val10);
			form_data.append('size10qty', val11);			
			form_data.append('npurrate', $('#npurrate').val());	
			form_data.append('tax', $('#tax').val());
			form_data.append('new_sub', $('#new_sub').val());
			form_data.append('s_id', $('#s_id').val());
		}
		else
		{
			var val1=val2="";
			var color=document.getElementsByName("color[]");
			var nqty=document.getElementsByName("nqty[]");
			for(i=0;i<color.length;i++)
			{
				val1 += color[i].value + ";";
				val2 += nqty[i].value + ";";
			}		
			var form_data = new FormData();
			form_data.append('ptype', $('#ptype').val());
			form_data.append('purdesp', $('#purdesp').val());
			form_data.append('saledesp', $('#saledesp').val());
			form_data.append('hsn', $('#hsn').val());      
			form_data.append('color', val1);
			form_data.append('nqty', val2);
			form_data.append('npurrate', $('#npurrate').val());
			form_data.append('tax', $('#tax').val());
			form_data.append('new_sub', $('#new_sub').val());
			form_data.append('s_id', $('#s_id').val());
		}
      	$.ajax({
       method:"POST",
       url:'insert_data.php',
       data:form_data,
       dataType:'json',
       contentType:false,
       cache:false,
       processData:false,
       success:function(data)
       {
        if(data.error != '')
        {
         $('#form_response').html('<div class="alert alert-danger">'+data.error+'</div>');
        }
        else
        {
         //$('#form_response').html('<div class="alert alert-success">'+data.success+'</div>');
		 console.log(data.query);
		 	var v_id=document.getElementsByName("v_id[]");
			var str=data.success;
			var lastvalue=(data.last).split(";");
			var taxvalue=(data.tax).split(":");
			for(i=0;i<v_id.length;i++)
			{
				var id=v_id[i].id;
				$('#'+id).append(str);
			}
			for(i=0;i<lastvalue.length;i++)
			{
				last=counter-1;				
				newvalues=lastvalue[i].split(":");
				$('#v'+last).val(newvalues[0]);
				$('#'+last).find("input").eq(0).val(newvalues[1]);
				$('#'+last).find("input").eq(1).val(newvalues[2]);
				$('#'+last).find("input").eq(4).val(taxvalue[1]);
				$('#'+last).find("input").eq(2).val('0');
				$('#v'+last).focus();
				if(i<(lastvalue.length-1)) more();
			}
			calc();
        }
       }
      });
     }
    }
   ]).showModal();
 });

$('#add_data1').click(function(){
  var options = {
   ajaxPrefix:''
  };
  new Dialogify('addnewledger.php', options)
   .title('Add New Vendor')
   .buttons([
    {
     text:'Cancel',
     click:function(e){
      this.close();
     }
    },
    {
     text:'Add',
     type:Dialogify.BUTTON_PRIMARY,
     click:function(e)
     {
      var form_data = new FormData();
      form_data.append('lname', $('#lname').val());
      form_data.append('tinno', $('#tinno').val());
      form_data.append('cperson', $('#cperson').val());
	  form_data.append('mobile', $('#mobile').val());
	  form_data.append('email', $('#email').val());
	  form_data.append('address', $('#address').val());
	  
      $.ajax({
       method:"POST",
       url:'insert_data1.php',
       data:form_data,
       dataType:'json',
       contentType:false,
       cache:false,
       processData:false,
       success:function(data)
       {
        if(data.error != '')
        {
         $('#form_response').html('<div class="alert alert-danger">'+data.error+'</div>');
        }
        else
        {
         //$('#form_response').html('<div class="alert alert-success">'+data.success+'</div>');
		 	$('#party').append(data.success);
			$('#party').val(data.last);
			$('#party').focus();
        }
       }
      });
     }
    }
   ]).showModal();
 });
 
});
</script>
    </body>

</html>