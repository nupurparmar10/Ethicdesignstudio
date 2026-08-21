<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	  if(isset($_REQUEST['msg']))
	 {
	 	$msg="Product Details Modified Successfully!!!";
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
        <link rel="stylesheet" href="css/lightbox.css">
        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->                
		
		<script>
		function selectall()
{
	var sall=document.getElementById("all");
	if(sall.checked==1)
	{
		var scholar=document.getElementsByName("item_id[]");
		for(i=0;i<scholar.length;i++)
		{
			scholar[i].checked=1;
		}
	}
	else
	{
		var scholar=document.getElementsByName("item_id[]");
		for(i=0;i<scholar.length;i++)
		{
			scholar[i].checked=0;
		}
	}
}
		function changestatus(item_id,status)
		{
			$.ajax({
			   url : 'changestatus.php',
			   type : 'POST',
			   data : {item_id : item_id, status : status},
				success : ajaxSuccess1,
				error : ajaxError
			  });
		}
		function ajaxSuccess1(response)
		{
			location.reload(false);
		}
		function ajaxError()
		{
			alert("Error");
		}
		</script>  
		<script src="js\jquery.min.js"></script>   
	<script src="js/jquery-1.11.0.min.js"></script>
	<script src="js/lightbox.js"></script>		
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu2=true; $smenu2="2"; $ssmenu2="22"; include_once("sidebar.php"); ?>
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
					 <li><a href="#">Masters</a></li>
                    <li><a href="#">Product Master</a></li>
                    <li class="active">Inventory (Group Wise)</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                     <h2>Inventory (Group Wise)</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                 
                    <div class="row">
                        <div class="col-md-12">
							<?php
								if($msg)
								{
							?>
							<div class="alert alert-info" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg; ?></strong>
							</div>
							<?php 
								}
							?>
							<?php
								if($msg1)
								{
							?>
							<div class="alert alert-danger" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg1; ?></strong>
							</div>
							<?php 
								}
							?>
							<?php
								if($msg2)
								{
							?>
							<div class="alert alert-warning" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg2; ?></strong>
							</div>
							<?php 
								}
							?>
							
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                
                                <div class="panel-body">
									 <form class="form-horizontal" method="post" action="viewproduct.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Product Code</label>
												<label class="col-md-2 col-xs-2">Description</label>
												<label class="col-md-2 col-xs-2">HSN Code</label>
												<label class="col-md-2 col-xs-2">Type</label>
											</div>
											<div class="row">
												<div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="pcode" id="pcode"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="desp" id="desp"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="hsn" id="hsn"/>
												</div>
												<div class="col-md-2 col-xs-12">  
													 <select class="form-control" name="ptype">
														<option value=''>--Select--</option>
														<?php
															$f1=mysqli_query($con,"select * from producttype order by ptname");
															while($f=mysqli_fetch_row($f1))
															{
																echo "<option value='$f[1]'>$f[1]</option>";
															}
														?>
													</select>
												</div>											
												<div class="col-md-2 col-xs-2"> 
													<button class="btn btn-primary" type="submit" name="open">Open</button>
												</div>
											</div>
									</div>
									 </form>
									 <br>
                                    <div class="table-responsive" id="display">
                                        <?php
  if(isset($_REQUEST['open']))
  {
 if($_REQUEST['ptype']!="") $ptype=" and ptype='$_REQUEST[ptype]'"; else $ptype="";
 
 $sql = "SELECT * FROM item_details where pcode like '%$_REQUEST[pcode]%' and (purdesp like '%$_REQUEST[desp]%' or saledesp like '%$_REQUEST[desp]%') and hsn like '%$_REQUEST[hsn]%' and status=1 ".$ptype." order by pcode";
	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Product Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' cellpadding='3' width='100%' style='border-collapse:collapse; font-size:12px;'>
			<tr>
				<th style='width:20px;'>S.<br>No.</th>
				<th>Code</th>
				<th>Product Type</th>
				<th>Purchase Description</th>
				<th>HSN</th>
				<th>Selling Description</th>
				<th>Unit</th>
				<th>Store Inventory</th>
				<th>Website Inventory</th>
				<th>Website</th>
			</tr>
			";
		
?>
<form action='addpurchase.php' method='post' target='_blank'>
<span style='float:right;'><button class="btn btn-warning" type="submit" name="s10">Generate Purchase Invoice</button></span>
<br><br><br>
		<table class="table datatable table-bordered table-actions">
			<thead>
				<tr>
					<th width="58"><span><input type='checkbox' name='all' onchange="selectall();" id='all'/></span></th>
					<th style='width:20px;'>S.<br>No.</th>
					<th>Code</th>
					<th>Product Type</th>
					<th>Purchase Description</th>
					<th>HSN</th>
					<th>Selling Description</th>
					<th>Unit</th>
					<th>Store Inventory</th>
					<th>Website Inventory</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($d = mysqli_fetch_row($result))
		{		
			$j=1;
			do
			{
				$table .= "<tr>";
		?>
				<tr id="<?php echo $d[0]; ?>">
		<?php				
				echo "<td><input type='checkbox' name='item_id[]' value='$d[0]'/></td>";
				echo "<td align='center'>$j</td>";	
		?>
			<td><?php echo $d[1]; ?></td>
			<td><?php echo $d[2]; ?></td>
			<td><?php echo htmlspecialchars("$d[3]"); ?></td>
			<td><?php echo $d[4]; ?></td>
			<td><?php echo htmlspecialchars("$d[5]"); ?></td>
			<td><?php echo $d[6]; ?></td>
			<?php
				$table .= "<td>$j</td>
						<td>$d[1]</td>
						<td>$d[2]</td>
						<td>".htmlspecialchars("$d[3]")."</td>
						<td>$d[4]</td>
						<td>".htmlspecialchars("$d[5]")."</td>
						<td>$d[6]</td>";
			
			$s=mysqli_fetch_row(mysqli_query($con,"select count(*),sum(stock),sum(webstock) from variant where item_id=$d[0]"));
			if($d[8]==1) $status="<span class='badge badge-success'>Active</span>"; else $status="<span class='badge badge-danger'>Deactive</span>";
			echo "<td align='right'>$s[1] in stock for $s[0] variants</td>
			<td align='right'>$s[2] in stock for $s[0] variants</td>
			<td align='right'>$status</td>";

			$table .= "<td align='right'>$s[1] in stock for $s[0] variants</td>
			<td align='right'>$s[2] in stock for $s[0] variants</td>
			<td align='right'>$status</td>";
			?>
				<td>
					<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('editproduct.php?item_id=<?php echo $d[0]; ?>','_blank');" type="button"  title="Edit"><span class="fa fa-pencil"></span></button>
					<button class="btn btn-success btn-rounded btn-condensed btn-sm" onClick="window.open('printproduct1.php?item_id=<?php echo $d[0]; ?>','_blank');" type="button" ><span class="fa fa-print"  title="Print Barcode"></span></button>
					<?php
						if($d[8]==1)
						{
					?>
					<button class="btn btn-warning btn-rounded btn-condensed btn-sm" onClick="changestatus('<?php echo $d[0]; ?>','0');" type="button" ><span class="fa fa-toggle-on"  title="Website Status ON"></span></button>
					<?php
						}
						else
						{
					?>
					<button class="btn btn-default btn-rounded btn-condensed btn-sm" onClick="changestatus('<?php echo $d[0]; ?>','1');" type="button" ><span class="fa fa-toggle-off"  title="Website Status OFF"></span></button>
					<?php
						}
					?>
				</td>
			</tr>
			<?php
				$j++;
			}while($d = mysqli_fetch_array($result));
		}
		$table .="</table>";
	?>	
	</tbody>
      </table>
	 		
		</form>
	<br></br>
	  		<div class="col-md-1 col-xs-1">
			  <form action="printlist.php" method="post" target="_blank">
				<input type="hidden" value="<?php echo $table; ?>" name="query"/>
				<button class="btn btn-primary" type="submit" name="s11">Print</button>		
				</form>
		</div>
		<div class="col-md-3 col-xs-3">
			<form action="excel.php" method="post">
				 <input type="hidden" name="query" value="<?php echo $table; ?>"/>
				 <input type="hidden" name="fn" value="Product Details (Group Wise)"/>
				 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
			 </form>
		</div>
            <?php
				}
			}
		 ?>	  										
                                    </div>
                                </div>
                            </div>
                            <!-- END DATATABLE EXPORT -->                            
                       
                        </div>
                    </div>

                </div>         
                <!-- END PAGE CONTENT WRAPPER -->
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->    

        <!-- MESSAGE BOX-->
        <div class="message-box animated fadeIn" data-sound="alert" id="mb-remove-row">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title"><span class="fa fa-times"></span> Remove <strong>Data</strong> ?</div>
                    <div class="mb-content">
                        <p>Are you sure you want to remove this row?</p>                    
                        <p>Press Yes if you sure.</p>
                    </div>
                    <div class="mb-footer">
                        <div class="pull-right">
                            <button class="btn btn-success btn-lg mb-control-yes">Yes</button>
                            <button class="btn btn-default btn-lg mb-control-close">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MESSAGE BOX-->        
        
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
	
        
        <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script>        
        <!-- END THIS PAGE PLUGINS-->  
        
        <!-- START TEMPLATE -->
        
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->     
    
    </body>

</html>