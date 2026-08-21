<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg="";
	if(isset($_REQUEST['msg']))
	{
		$msg="Manufacturing Job Generated Successfully!!!";
	}
	if(isset($_REQUEST['s1']))
	{
		$mdate=$_REQUEST['mdate'];
		$pid1=mysqli_query($con,"select max(m_id) from manufacturejob");
		$pid=mysqli_fetch_row($pid1);
		$id=$pid[0]+1;
		
		$rid="M".$id;
		
		$count=count($_REQUEST['qty']);
		for($i=0;$i<$count;$i++)
		{
			$qty=$_REQUEST['qty'][$i];
			$rate=$_REQUEST['rate'][$i];
			$item_id=$_REQUEST['item_id'][$i];
			if($item_id!="" && $qty>0)
			{
				$item=explode("-",$item_id)[0];
				mysqli_query($con,"update variant set stock=stock-$qty where v_id='$item'");	

				mysqli_query($con,"insert into manu_fabric set m_id='$id',v_id='$item', qty='$qty', rate='$rate', remqty='$qty'");
			}
		}
		
		mysqli_query($con,"insert into manufacturejob set m_id='$id', mdate='$_REQUEST[mdate]', jobber='$_REQUEST[jobber]', remark='$_REQUEST[remark]', relatedwith='$rid', status=0, type='Manufacturing'");
		header("Location: addmanu.php?msg=set"); die;
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
		function getvalues(VAL)
		{
            
			var items = document.getElementsByName("item_id[]");	
			var rate = document.getElementsByName("rate[]");
			var qty = document.getElementsByName("qty[]");
			for(i=0;i<items.length;i++)
			{
				if(items[i].value==VAL)
				{
					var str = items[i].value;                    
					var ary = str.split('-');
					rate[i].value=ary[1];
					qty[i].setAttribute("max",ary[2]);
				}
			}
            calc();
		}
	function calc()
	{
		var qty = document.getElementsByName("qty[]");
		var rate = document.getElementsByName("rate[]");
		var cost = document.getElementsByName("cost[]");
		
		var total = 0;		
		for(var i=0; i<qty.length; i++)
		{
			var q = qty[i].value;
			var r = rate[i].value;
			var amt=0;
			if(q > 0 && r > 0)
			{
				amt=q*r;			
				cost[i].value=amt.toFixed(2);
                total=total *1 + amt*1;				
			}
		}
		document.getElementById("costtot").value = (total*1).toFixed(2);        
	}
</script>
<script>

var counter=1;
 function more() {
            counter++;

			var $table = $('#input_fields');
            var $tr = $table.find('tr').eq(1).clone();
            $tr.appendTo($table).find('input').val('');
			$tr.appendTo($table).find('select').eq(0).val('');
            $tr.appendTo($table).find('select').eq(0).attr('id',"item_id"+counter);
            $tr.appendTo($table).find('input').eq(0).attr('id',"qty"+counter);
            $("#input_fields").append($tr);
            $tr.appendTo($table).find('select').eq(0).focus();
  }
  </script>
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu15=true; $smenu15="1"; include_once("sidebar.php"); ?>
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
                    <li class="active">Generate Manufacturing Job</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                   <h2><span class="fa fa-lsit"></span>Generate Manufacturing Job</h2>
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
                            <form class="form-horizontal" method="post" action="addmanu.php" enctype="multipart/form-data" name='frm2'>
                            <div class="panel panel-default">
                                <div class="panel-body">   
									<div class="row">
                                        <div class="col-md-12">
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-actions">
												<tbody>  
													<tr>
														<th width="15%">Date</th>
														<td><div class="form-group">
															<input type="date" class="form-control" name="mdate" required value="<?php echo date("Y-m-d"); ?>"/>
														</div></td>
														
													</tr>
													<tr>
														<th width="15%">Jobber</th>
														<td><div class="form-group" style='width:89%; float:left;'>
															<select class="form-control" name="jobber" required id="party" >
																<option value="">--Select--</option>
																<?php
																	$f1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (33) order by name");
																	while($f=mysqli_fetch_row($f1))
																	{
																		echo "<option value='$f[0]'>$f[1]</option>";
																	}
																?>	
															</select>
															</div>
															<span style='position:relative; float:right; width:10%;'><button type="button" name="add_data1" id="add_data1" class="btn btn-success btn-xs"><i class="fa fa-plus"></i></button></span>
														</td>
													</tr>
													<tr>
														<td colspan='2'>
															<div class="table-responsive">
															<table class="table table-bordered table-striped table-actions" id="input_fields">
																<thead>
                                                                    <th>Fabric</th>
                                                                    <th>No. of MTR</th>
                                                                    <th>Rate</th>
                                                                    <th>Cost</th>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><select class="form-control" name="item_id[]" onchange="getvalues(this.value);" id="item_id1">
                                                                            <option value="">--Select--</option>
                                                                            <?php
                                                                                $f1=mysqli_query($con,"select * from variant where stock>0 and item_id IN (select item_id from item_details where ptype='Fabric' order by pcode)");
                                                                                while($f=mysqli_fetch_row($f1))
                                                                                {
																					$item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
                                                                                    echo "<option value='$f[0]-$f[5]-$f[6]'>$item[1]-$f[3]</option>";
                                                                                }
                                                                            ?>
                                                                        </select></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="qty[]" onkeyup="calc(); chk_qty(this);" min='1' id="qty1"/>
																		</div></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="rate[]" onkeyup="calc();"/>
																		</div></td>
                                                                        <td> <div class="form-group">
																			<input type="number" class="form-control" name="cost[]" onkeyup="calc();"/>
																		</div></td>
                                                                    </tr>
                                                                </tbody>
                                                                <tr>
                                                                    <td colspan='3' align='right'>Total</td>
                                                                    <td> <div class="form-group">
																		<input type="text" class="form-control" name="costtot" onkeyup="calc();" id="costtot"/>
																	</div></td>
                                                                </tr>
                                                            </table>
                                                            </div>
                                                            <button class="btn btn-primary" onClick="more();" type="button">Add More</button> 
                                                        </td>
                                                    </tr>
													<tr>
														<th>Remark</th>
														<td> <div class="input-group">
															<textarea class="form-control" rows="5" style="width:400px;" name="remark"></textarea>
														</div></td>
													</tr>
												</tbody>
											</table>
										</div>
										
										</div>
									</div>
									
                                </div>
                                <div class="panel-footer">							
									<button class="btn btn-primary" type="submit" name="s1">Generate Job</button>
									
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
 
 $('#add_data1').click(function(){
  var options = {
   ajaxPrefix:''
  };
  new Dialogify('addnewledger1.php', options)
   .title('Add New Jobber')
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
      form_data.append('cperson', $('#cperson').val());
	  form_data.append('mobile', $('#mobile').val());
	  form_data.append('email', $('#email').val());
	  form_data.append('address', $('#address').val());
	  
      $.ajax({
       method:"POST",
       url:'insert_data3.php',
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

function chk_qty(obj)
	{
		var id= obj.id.slice(3);
		var item_id='item_id'+id;
		var qty='qty'+id;
		var response = document.getElementById(item_id).value;
		var str=response.split("-");
		if(parseFloat(obj.value)>parseFloat(str[2]))
		{
		   alert("Qty is greater than available stock");
		   document.getElementById(qty).value='';
	    }
	}
</script>
        
    
         
    </body>

</html>