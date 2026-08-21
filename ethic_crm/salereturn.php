<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";
	if(isset($_REQUEST['msg']))
	{
		$msg="Sales Bill Added Successfully!!!";
	}
	if(isset($_REQUEST['s1']) || isset($_REQUEST['s2']) || isset($_REQUEST['s3']))
	{
        $party=$_REQUEST['party'];
		$paidby=$_REQUEST['paidby'];
		$amt=$_REQUEST['gtotal'];
		$roundoff=$_REQUEST['roundoff'];
		$invdate=$_REQUEST['invdate'];
		$id=$_REQUEST['sale_id'];
		
		$rid="SR".$id;
		$cheque=$_REQUEST['chequeno'];
		
		$count=count($_REQUEST['item_id']);
		for($i=0;$i<$count;$i++)
		{
			$item=explode("-",$_REQUEST['item_id'][$i])[0];
			$qty=$_REQUEST['qty'][$i];
			if($item!="" && $qty>0)
			{
				$rate=$_REQUEST['rate'][$i];
				$disper=$_REQUEST['disper'][$i];
				$taxper=$_REQUEST['taxper'][$i];
				$distype=$_REQUEST['distype'][$i];
				$mrp=$_REQUEST['mrp'][$i];
                mysqli_query($con,"insert into sr_items set sale_id='$id', v_id='$item', qty='$qty', rate='$rate', dis='$disper', gst='$taxper', mrp='$mrp', distype='$distype'");
				mysqli_query($con,"update variant set stock=stock+$qty, webstock=webstock+$qty where v_id='$item'");
			}
		}
		
        mysqli_query($con,"insert into billreturn set sale_id=$id, party='".$party."', invno='".$_REQUEST['invno']."', invdate='".$invdate."', paidby='".$paidby."', roundoff='$_REQUEST[roundoff]', amount='".$amt."', chequeno='".$cheque."', relatedwith='$rid', spdis='$_REQUEST[spdis]', freight='$_REQUEST[freight]', remark='$_REQUEST[remark]', transport='$_REQUEST[transport]', other='$_REQUEST[other]', taxtype='$_REQUEST[taxtype]',aginvno='$_REQUEST[aginvno]', emp_id='$_REQUEST[emp_id]', comm='$_REQUEST[comm]', oname='$_REQUEST[oname]'");
        
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
		
        mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$party."', amount='".$amt."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		
		if($paidby!="Credit")
		{            
            $tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$paidby."', amount='".$amt."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$party."', amount='".$amt."', particulars='Amount Given Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		}
		
		if($_REQUEST['taxtot']>0)
		{
			$tid++;
			if($_REQUEST['taxtype']=="GST")
			{
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='8', amount='".($_REQUEST['taxtot']/2)."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");			
				$tid++;
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='7', amount='".($_REQUEST['taxtot']/2)."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");			
				$tid++;
			}
			else
			{
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='4', amount='".$_REQUEST['taxtot']."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");			
				$tid++;
			}
	
		}
		if($_REQUEST['distot']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='6', amount='".$_REQUEST['distot']."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		if($_REQUEST['spdis']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='6', amount='".$_REQUEST['spdis']."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		}
		if($_REQUEST['freight']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='9', amount='".$_REQUEST['freight']."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		}
		if($_REQUEST['transport']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='12', amount='".$_REQUEST['transport']."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		}
		if($_REQUEST['other']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='14', amount='".$_REQUEST['other']."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		}
		$tid++;
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='2', amount='".($_REQUEST['gtotal'] - $_REQUEST['taxtot'])."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
		
		$tid++;
		
		if($roundoff!=0.00)
		{
			if($_REQUEST['roundoff']<0)
			{
				$r=$roundoff-($roundoff*2);
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='13', amount='".$r."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");	
			}
			else
			{
				$r=$roundoff;
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='13', amount='".$r."', particulars='Sale Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
			}
		}
		if(isset($_REQUEST['s2']))
		{
		if($_REQUEST['taxtype']=="GST")
			echo "<script language=\"javascript\">window.open(\"printcrinvoice.php?sale_id=$id\",\"_blank\");</script>";
		else
			echo "<script language=\"javascript\">window.open(\"printcrinvoice1.php?sale_id=$id\",\"_blank\");</script>";
		}
		if(isset($_REQUEST['s3']))
		{
			//message api

			//end
		}
		echo "<script language=\"javascript\">window.open(\"salereturn.php?msg=set\",\"_self\");</script>";
	}
	if(isset($_REQUEST['s4']))
	{
		$old=mysqli_fetch_row(mysqli_query($con,"select relatedwith from billreturn where sale_id='$_REQUEST[sale_id]'"));
		$p1=mysqli_query($con,"select * from bill_items where sale_id='$_REQUEST[sale_id]'");
		while($p=mysqli_fetch_row($p1))
		{
			mysqli_query($con,"update variant set stock=stock-$p[2], webstock=webstock-$p[2] where v_id='$p[1]'");
		}
        mysqli_query($con,"delete from sr_items where sale_id='$_REQUEST[sale_id]'");
		mysqli_query($con,"delete from transaction where relatedto='$old[0]'");
		
		$party=$_REQUEST['party'];
		$paidby=$_REQUEST['paidby'];
		$amt=$_REQUEST['gtotal'];
		$roundoff=$_REQUEST['roundoff'];
		$invdate=$_REQUEST['invdate'];
		$rid=$old[0];
		$cheque=$_REQUEST['chequeno'];
		$id=$_REQUEST['sale_id'];
		mysqli_query($con,"update ledger_details set mobile='$cheque' where ledger_id='$party'");
		$count=count($_REQUEST['item_id']);
		for($i=0;$i<$count;$i++)
		{
			$item=explode("-",$_REQUEST['item_id'][$i])[0];
			$qty=$_REQUEST['qty'][$i];
			if($item!="" && $qty>0)
			{
				$rate=$_REQUEST['rate'][$i];
				$disper=$_REQUEST['disper'][$i];
				$taxper=$_REQUEST['taxper'][$i];
				$mrp=$_REQUEST['mrp'][$i];
				$distype=$_REQUEST['distype'][$i];
				mysqli_query($con,"insert into sr_items set sale_id='$id', v_id='$item', qty='$qty', rate='$rate', dis='$disper', gst='$taxper', mrp='$mrp', distype='$distype'");
				mysqli_query($con,"update variant set stock=stock+$qty, webstock=webstock+$qty where v_id='$item'");				
			}
		}
		
        mysqli_query($con,"update billreturn set party='".$party."', invno='".$_REQUEST['invno']."', invdate='".$invdate."', paidby='".$paidby."', roundoff='$_REQUEST[roundoff]', amount='".$amt."', chequeno='".$cheque."', relatedwith='$rid', spdis='$_REQUEST[spdis]', freight='$_REQUEST[freight]', remark='$_REQUEST[remark]',  transport='$_REQUEST[transport]', other='$_REQUEST[other]', taxtype='$_REQUEST[taxtype]', aginvno='$_REQUEST[aginvno]', emp_id='$_REQUEST[emp_id]', comm='$_REQUEST[comm]', oname='$_REQUEST[oname]' where sale_id='$id'");
		
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$party."', amount='".$amt."', particulars='Sales. Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		
		
		if($paidby!="Credit")
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$paidby."', amount='".$amt."', particulars='Sales. Return Inv. No. :$_REQUEST[invno]', 	type='Cr.', relatedto='$rid'");	
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='".$party."', amount='".$amt."', particulars='Amount Given. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");			
		}
		
		if($_REQUEST['taxtot']>0)
		{
			$tid++;
			if($_REQUEST['taxtype']=="GST")
			{
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='8', amount='".($_REQUEST['taxtot']/2)."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");			
				$tid++;
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='7', amount='".($_REQUEST['taxtot']/2)."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");			
				$tid++;
			}
			else
			{
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='4', amount='".$_REQUEST['taxtot']."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");			
				$tid++;
			}
		}
		if($_REQUEST['distot']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='6', amount='".$_REQUEST['distot']."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		if($_REQUEST['spdis']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='6', amount='".$_REQUEST['spdis']."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");
		}
		if($_REQUEST['freight']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='9', amount='".$_REQUEST['freight']."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		}
		if($_REQUEST['transport']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='12', amount='".$_REQUEST['transport']."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		}
		if($_REQUEST['other']>0)
		{
			$tid++;
			mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='14', amount='".$_REQUEST['other']."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");
		}
		$tid++;
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='2', amount='".($_REQUEST['gtotal'] - $_REQUEST['taxtot'])."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
		
		$tid++;
		
		if($roundoff!=0.00)
		{
			if($_REQUEST['roundoff']<0)
			{
				$r=$roundoff-($roundoff*2);
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='13', amount='".$r."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Dr.', relatedto='$rid'");	
			}
			else
			{
				$r=$roundoff;
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$invdate."', ledger_id='13', amount='".$r."', particulars='Sales Return. Inv. No. :$_REQUEST[invno]', type='Cr.', relatedto='$rid'");	
			}
		}
		header("Location: viewsalereturn.php?msg=set"); die;
	}
?>
<?php
	$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1) and stock>0 order by v_id");
	$query="";																		
	while($f=mysqli_fetch_row($f1))
	{
		$c=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
		$query .= "<option value='$f[0]-$f[5]-$c[7]-$f[6]'>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
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
			var rate = document.getElementsByName("rate[]");
			var taxper = document.getElementsByName("taxper[]");
			var mrp = document.getElementsByName("mrp[]");
			var disper = document.getElementsByName("disper[]");
			var qty = document.getElementsByName("qty[]");
			for(i=0;i<items.length;i++)
			{
				if(items[i].value==VAL)
				{
					var str = items[i].value;
					var ary = str.split('-');
					taxper[i].value=ary[2];
					if(ary[1]>0)
						val= (ary[1]*100/(100+ary[2]*1)).toFixed(3);
					else val=0;
					mrp[i].value=val;
					qty[i].value=1;
					rate[i].value=val;
				}
			}
			calc();
		}

	
	function calc()
	{
		var items = document.getElementsByName("item_id[]");	
		var qty = document.getElementsByName("qty[]");
		var rate = document.getElementsByName("rate[]");
		var disper = document.getElementsByName("disper[]");
		var distype = document.getElementsByName("distype[]");
		var disamt = document.getElementsByName("disamt[]");
		var taxper = document.getElementsByName("taxper[]");
		var taxamt = document.getElementsByName("taxamt[]");
		var mrp = document.getElementsByName("mrp[]");
		var amount = document.getElementsByName("amount[]");
		var comm = document.frm2.comm.value;
		var total = 0;
		var distot=0;
		var taxtot=0;
		for(var i=0; i<qty.length; i++)
		{
			var q = qty[i].value;
			var r = rate[i].value;
			var amt=0;
			var val=0;
			if(q > 0)
			{
				if(distype[i].value=='P')
				{
					val=q*mrp[i].value*disper[i].value/100;
					r=mrp[i].value-(mrp[i].value*disper[i].value/100)*1;
				}
				else
				{ 
					val=q*disper[i].value;
					r=mrp[i].value-(disper[i].value)*1;
				}
				rate[i].value=r.toFixed(2);
				disamt[i].value=val.toFixed(2);
				distot+=val*1;
				
				amt=q*r;
				val=amt*taxper[i].value/100;
				taxamt[i].value=val.toFixed(2);
				taxtot+=val*1;
				amt+=val*1;
				amt=amt.toFixed(2);
				amount[i].value=(amt*1);
				total += (amt*1);
			}
		}
		total1 = (total*comm*1/100)*1;
		document.getElementById("distot").value = (distot*1).toFixed(2);
		document.getElementById("taxtot").value = (taxtot*1).toFixed(2);
		document.getElementById("total").value = (total*1).toFixed(2);
		document.getElementById("totcomm").value = (total1*1).toFixed(2);
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
	function chk_qty(val,val1)
	{
		var id= val1.slice(3);
		var item_id='item_id'+id;
		var qty='qty'+id;
		var response = document.getElementById(item_id).value;
		var str=response.split("-");

		var availableStock = parseInt(str[3], 10);
    	var enteredQty = parseInt(val, 10);

		if (enteredQty > availableStock) 
		{
        alert("Qty is greater than available stock");
        document.getElementById(qty).value = '';
    	}
	}
</script>
<script>
	var counter=1;
   var query="<?php echo $query; ?>";
</script>
  <script>

 function more() {
            var $table = $('#input_fields');			
			var chk=$('#'+counter).find('select').eq(0).val();
			var item_id='item_id'+counter;
			var qty='qty'+counter;
			if(chk!="")
			{
				counter++;
				var str1="<tr id='"+counter+"'><td align='left' valign='middle'><div class='form-group'><select id='"+item_id+"'class='form-control' name='item_id[]' onchange='getValues1(this.value); more();' tabindex='1'><option value=''>--Select--</option>"+query+"</select></div></td><td> <div class='form-group'><input type='text' class='form-control' name='mrp[]' onkeyup='calc();'/></div></td><td> <div class='form-group'><input type='text' class='form-control' name='qty[]' id='"+qty+"'  onkeyup=\"calc(); chk_qty(this.value,'"+qty+"');\" tabindex='1'/></div></td><td> <div class='form-group'><input type='text' class='form-control' name='disper[]' value='0' onkeyup='calc();'/></div></td><td> <div class='form-group'><select class='form-control' name='distype[]' onchange='calc();'><option>C</option><option value='P'>%</option></select></div></td><td> <div class='form-group'><input type='text' class='form-control' name='disamt[]' onkeyup='calc();'/></div></td><td> <div class='form-group'><input type='text' class='form-control' name='rate[]' onkeyup='calc();'/></div></td><td> <div class='form-group'><input type='text' class='form-control' name='taxper[]' onkeyup='calc();'/></div></td><td> <div class='form-group'><input type='text' class='form-control' name='taxamt[]' onkeyup='calc();'/></div></td><td> <div class='form-group'><input type='text' class='form-control' name='amount[]' onkeyup='calc();'/></div></td><td><a onclick='delete_row("+counter+");'><i class='fa fa-times'></i></a></td></tr>";
				
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
  </script>
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu10=true; $smenu10="7"; include_once("sidebar.php"); ?>
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
                    <li class="active">Add Sales Return</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                   <h2><span class="fa fa-lsit"></span><?php if(isset($_REQUEST['sale_id'])) echo "Modify Sales Return"; else echo "Add New Return"; ?></h2>
				   <br><br><br>
				   <p style="color:red;">Please keep an eye on the stock...</p>
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
                            <form class="form-horizontal" method="post" action="salereturn.php" name="frm2" enctype="multipart/form-data" onsubmit="return chk();">
                            <div class="panel panel-default">
                                <div class="panel-body">   
									<div class="row">
                                        <div class="col-md-12">
										<?php
											if(isset($_REQUEST['sale_id']))
											{
												$p1=mysqli_query($con,"select * from billreturn where sale_id='$_REQUEST[sale_id]'");
												$p=mysqli_fetch_row($p1);
												echo "<input type='hidden' name='sale_id' value='$_REQUEST[sale_id]'/>";
											}
											else
											{
												$p[1]=$p[2]=$p[3]=$p[4]=$p[5]=$p[6]=$p[7]=$p[8]=$p[9]=$p[10]=$p[11]=$p[12]=$p[13]=$p[14]=$p[15]=$p[16]=$p[17]=$p[18]=$p[19]=$p[20]=$p[21]="";
												$p[1]=date("Y-m-d");
												$p[22]="Other Charges";
											}
										?>
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-actions">
												<tbody>  
													<tr>
														<th width="10%">Return Invoice No.</th>
														<td><?php
															if(isset($_REQUEST['sale_id']))
															{
																	$no=$p[3];
																	$sid=$p[0];
															}
															else
															{
																$invno=mysqli_query($con,"select invno,sale_id from billreturn order by sale_id desc limit 1");
																
																if($i=mysqli_fetch_row($invno))
																	$i[0]=explode("/",$i[0])[2];
																else $i[0]=$i[1]=0;
																$i[0]++;
																if($i[0]<10) $no="EDSCR/".date("Y-m")."/00".$i[0];
																else if($i[0]<100) $no="EDSCR/".date("Y-m")."/0".$i[0];
																else $no="EDS/".date("Y-m")."/".$i[0];
																$sid=++$i[1];
															}
															?>
															<input type="hidden" class="form-control" name="sale_id" value="<?php echo $sid;?>"/>
															<input type="hidden" class="form-control" name="invno" value="<?php echo $no; ?>"/>
															<b><?php echo $no; ?></b></td>
														<th width="15%">Date</th>
														<td><div class="form-group">
															<input type="date" class="form-control" name="invdate" value="<?php  echo $p[1]; ?>" required tabindex='1'/>
														</div></td>
													</tr>
													<tr>
														<th width="10%">Party Name</th>
														<td width='35%'>
														<div class="ui-widget form-group">
															<select class="form-control" name="party" tabindex='1'>
																<option value="">--Select--</option>
																<?php
																	$f1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (27) order by name");
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
														<th width="10%">GST Type</th>
														<td><div class="form-group">
															<select class="form-control" name="taxtype" required tabindex='1'>
																<option <?php if($p[14]=="GST") { ?> selected='selected' <?php } ?>>GST</option>
																<option <?php if($p[14]=="IGST") { ?> selected='selected' <?php } ?>>IGST</option>
															</select>
														</div></td>
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
														<th>Mobile No.</th>
														<td><div class="form-group">
															<input type="text" tabindex='1' class="form-control" name="chequeno" value="<?php echo $p[6]; ?>" onkeyup="return allowOnly10Numeric(this);" oninput="allowOnly10Numeric(this);"/>
															<span id="mobileError" style="color: red; font-size: 14px;"></span>  
														</div></td>
													</tr>
													<tr>
                                                        <th>Against Invoice No.</th>
														<td><div class="form-group">
															<input type="text" class="form-control" name="aginvno" value="<?php echo $p[15]; ?>"/>
														</div></td>
														<th>Sales Person</th>
														<td> <div class="form-group">
															<select class="form-control" name="emp_id" required tabindex='1'>
																<option value=''>--Select--</option>
																<option value='0' <?php if($p[16]=='0') { ?> selected='selected'<?php } ?>>None</option>
																<?php
																	$e1=mysqli_query($con,"select * from empdet where status=1 order by empname");
																	while($e=mysqli_fetch_row($e1))
																	{
																		if($p[16]==$e[0])
																			echo "<option value='$e[0]' selected='selected'>$e[1]</option>";
																		else
																			echo "<option value='$e[0]'>$e[1]</option>";
																	}
																?>
															</select>
														</div></td>
													</tr>
                                                    <Tr>
                                                        <th width="10%">Commission %</th>
														<td><div class="form-group">
															<input type="text" class="form-control" name="comm" value="<?php echo $p[17]; ?>" onkeyup="calc();" tabindex='1'/>
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
																		<th colspan='3' style='text-align:center;'>Discount</th>
																		<th>Rate</th>
																		<th colspan='2'>GST</th>
																		<th>Amount</th>
																	</tr>
																</thead>
																<tbody>
																	<?php
																		if(isset($_REQUEST['sale_id']))
																		{
																			$k1=mysqli_query($con,"select * from sr_items where sale_id='$p[0]'");
                                                                          
																			if($k=mysqli_fetch_row($k1))
																			{
																			$a=0;
																				do{
																					$a++;
																	?>
																	<tr id="<?php echo $a; ?>">
																		<td align="left" valign="middle">
																			<div class="form-group">
																					<select class="form-control" name="item_id[]" id="item_id<?php echo $a; ?>" onchange='getValues1(this.value); more();' tabindex='1'>
																								<option value="">--Select--</option>
																						<?php
																							$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1) order by v_id");
																							while($f=mysqli_fetch_row($f1))
																							{
																								$c=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																								if($k[1]==$f[0])
																								echo "<option value='$f[0]-$f[5]-$c[7]-$f[6]' selected>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
																								else
																								echo "<option value='$f[0]-$f[5]-$c[7]-$f[6]'>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
																							}
																						?>	
																					</select>
																			</div>
																		</td>
																		
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="mrp[]" onkeyup="calc();" value="<?php echo $k[6]; ?>"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="qty[]" id="qty<?php echo $a; ?>" value="<?php echo $k[2]; ?>" onkeyup="calc();  chk_qty(this.value,'<?php echo 'qty'.$a; ?>');" tabindex='1'/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="disper[]" value="<?php echo $k[4]; ?>" tabindex='1' onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<select class="form-control" name="distype[]" onchange="calc();" tabindex='1'>
																				<option <?php if($k[7]=="C") { ?> selected <?php } ?>>C</option>
																				<option value='P' <?php if($k[7]=="P") { ?> selected <?php } ?>>%</option>
																			</select>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="disamt[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="rate[]" value="<?php echo $k[3]; ?>" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxper[]" value="<?php echo $k[5]; ?>" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxamt[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="amount[]" onkeyup="calc();"/>
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
																				<select class="form-control" name="item_id[]" id="item_id<?php echo $qty; ?>" onchange='getValues1(this.value); more();' tabindex='1'>
																							<option value="">--Select--</option>
																					<?php
																						$f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1) and stock>0 order by v_id");										
																						while($f=mysqli_fetch_row($f1))
																						{
																							$c=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
																							
																							echo "<option value='$f[0]-$f[5]-$c[7]-$f[6]'>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
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
																			<input type="text" class="form-control" name="disper[]" onkeyup="calc();" tabindex='1' value='0'/>
																		</div></td>
																		<td> <div class="form-group">
																			<select class="form-control" name="distype[]" onchange="calc();" tabindex='1'>
																				<option>C</option>
																				<option value='P'>%</option>
																			</select>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="disamt[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="rate[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxper[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="taxamt[]" onkeyup="calc();"/>
																		</div></td>
																		<td> <div class="form-group">
																			<input type="text" class="form-control" name="amount[]" onkeyup="calc();"/>
																		</div></td>
																		<td><a onclick='delete_row(0);'><i class='fa fa-times'></i></a></td>
																	</tr>
																	<?php
																	echo "<script>counter++;</script>";
																		}
																	?>
																</tbody>
																<tr>
																	<td rowspan='2'></td>
																	<td colspan='4'></td>
																	<th> Dis. Total</th>
																	<td colspan='2'> </td>
																	<th>Tax Total</th>
																	<th>Amt Total</th>
																	<td style='width:20px;'></td>
																</tr>
																<tr>
																	<td colspan='4' align='right'>Total</td>
																	<td> <div class="form-group">
																			<input type="text" class="form-control" name="distot" onkeyup="calc();" id="distot"/>
																	</div></td>
																	<td colspan='2'> </td>
																	<td> <div class="form-group">
																		<input type="text" class="form-control" name="taxtot" onkeyup="calc();" id="taxtot"/>
																	</div></td>
																	<td> <div class="form-group">
																			<input type="text" class="form-control" name="total" id="total" onkeyup="calc();"/>
																	</div></td>
																	<td style='width:20px;'></td>
																</tr>
															</table>
															</div>
														</td>
													</tr>
													<tr>
														<th rowspan='5'>Remark</th>
														<td rowspan='5'> <div class="input-group">
															<textarea class="form-control" rows="5" style="width:400px;" name="remark" tabindex='1'><?php echo $p[4]; ?></textarea>
														</div></td>
														<th  style="width:300px;">Special Discount</th>
														<td><div class="input-group">
															<input type="text" class="form-control" name="spdisvalue" id="spdisvalue" onkeyup="calc1();" tabindex='1' style="width:50px;" value="<?php echo $p[7]; ?>"/>
															<select class="form-control" name="spdistype" id="spdistype" onchange="calc1();" style="width:50px;" tabindex='1'>
																<option>Cash</option>
																<option>%</option>
															</select>
														</div> 
															<div class="input-group">
															<input type="text" class="form-control" name="spdis" id="spdis" value="<?php echo $p[7]; ?>" onkeyup="calc1();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Freight Charges</th>
														<td> <div class="input-group">
															<input type="text" tabindex='1' class="form-control" name="freight" id="freight" value="<?php echo $p[9]; ?>" onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Transport Charges</th>
														<td> <div class="input-group">
															<input type="text" tabindex='1' class="form-control" name="transport" id="transport" value="<?php echo $p[8]; ?>" onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th><input type="text" tabindex='1' class="form-control" name="oname" value="<?php echo $p[18]; ?>" style="width:150px;"/></th>
														<td> <div class="input-group">
															<input type="text" tabindex='1' class="form-control" name="other" id="other" value="<?php echo $p[10]; ?>" onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Round Off</th>
														<td> <div class="input-group">
															<input type="text" class="form-control" name="roundoff" id="roundoff" value="<?php echo $p[11]; ?>" onkeyup="calc();" style="width:100px;"/>
														</div></td>
													</tr>
													<tr>
														<th>Total Commision</th>
														<td> <div class="input-group">
															<input type="text" class="form-control" name="totcomm" id="totcomm" onkeyup="calc();"/>
														</div></td>
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
										if(isset($_REQUEST['sale_id']))
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
									<button class="btn btn-primary" type="submit" name="s2" tabindex='1'>Save & Print</button>
									<button class="btn btn-primary" type="submit" name="s3" tabindex='1'>Save & Message</button>
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