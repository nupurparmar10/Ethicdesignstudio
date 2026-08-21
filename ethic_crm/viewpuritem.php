<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
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
             <?php  $menu9=true; $smenu9="6"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Purchase Master</a></li>
                    <li class="active">Purchase Details (Item Wise)</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2> View Purchase Details (Item Wise)</h2>
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
                                   <form class="form-horizontal" method="post" action="viewpuritem.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Party Name</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
												<label class="col-md-2 col-xs-2">Invoice No.</label>
												<label class="col-md-2 col-xs-2">Product Code</label>
												<label class="col-md-2 col-xs-2">Product Type</label>
											</div>
											<div class="row">
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="party">
														<option value=''>--Select--</option>
														<?php
															$l1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (26,27) order by name");
															while($l=mysqli_fetch_row($l1))
															{
																echo "<option value='$l[0]'>$l[1]</option>";
															}
														?>
													</select></div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dfrom"/></div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dto"/></div>
												<div class="col-md-2 col-xs-2"><input type="text" class="form-control" name="invno"/></div>
												<div class="col-md-2 col-xs-2"><input type="text" class="form-control" name="pcode"/></div>
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="ptype">
														<option value=''>--Select--</option>
														<?php
															$l1=mysqli_query($con,"select * from producttype order by ptname");
															while($l=mysqli_fetch_row($l1))
															{
																echo "<option value='$l[1]'>$l[1]</option>";
															}
														?>
													</select></div>
											</div>
											<div class="row">
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
	if($_REQUEST['dfrom']!="") $dfrom=" and invdate>='$_REQUEST[dfrom]'"; else $dfrom="";
	if($_REQUEST['dto']!="") $dto=" and invdate<='$_REQUEST[dto]'"; else $dto="";
	if($_REQUEST['party']!="") $party=" and party='$_REQUEST[party]'"; else $party="";
	if($_REQUEST['ptype']!="") $ptype=" and ptype='$_REQUEST[ptype]'"; else $ptype="";
	
	$sql = "select * from pur_items where pur_id in (select pur_id from purbook where invno like '%%' ".$dfrom." ".$dto.") and v_id in (select v_id from variant where item_id IN (select item_id from item_details where pcode like '%$_REQUEST[pcode]%' ".$ptype.")) order by v_id";
	$result = mysqli_query($con,$sql);
	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Purchase Bill Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' cellpadding='3' width='100%' style='border-collapse:collapse; font-size:12px;'>
			<tr>
				<th style='width:20px;'>S.<br>No.</th>
				<th>Code</th>
				<th>Type</th>
				<th>Description</th>
				<th>HSN</th>
				<th>Unit</th>
				<th>Size</th>
				<th>Color</th>
				<th>Qty</th>
				<th>Purchase Rate</th>
				<tH>Dis %</tH>
				<th>Taxable Value</th>
				<th>Tax</th>
				<th>Total Purchase Value</th>
				<th>Total Purchase Rate</th>
				<th>Selling Price</th>
				<th>Ethic Selling Price</th>
			</tr>";
?>
		<table class="table table-bordered table-actions">
			<thead>
			<tr>
				<th style='width:20px;'>S.<br>No.</th>
				<th>Code</th>
				<th>Type</th>
				<th>Description</th>
				<th>HSN</th>
				<th>Unit</th>
				<th>Size</th>
				<th>Color</th>
				<th>Qty</th>
				<th>Purchase Rate</th>
				<tH>Dis %</tH>
				<th>Taxable Value</th>
				<th>Tax</th>
				<th>Total Purchase Value</th>
				<th>Total Purchase Rate</th>
				<th>Selling Price</th>
				<th>Ethic Selling Price</th>
				<th >Action</th>
			</tr>
			</thead>
			<tbody>
 <?php	
		if($d = mysqli_fetch_row($result))
		{		
			$j=1;
			do
			{			
				$item1=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id ='$d[0]' "));
				$item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$item1[1]'"));
				
				$amt=$d[1]*$d[2];
				$amt=$amt- ($amt*$d[6]/100);
				$tax=round($amt*$d[3]/100,2);

				$amt1=$amt+$tax;
				$rate=round($amt1/$d[1],2);
				
				$sell=round($rate + ($rate * 60/100),2);
				$table .= "<tr>";
			?>
				 <tr id="<?php echo $p[0]; ?>">
					<td><?php echo $j;?></td>
					<td><?php echo $item[1]; ?></td>
					<td><?php echo $item[2]; ?></td>
					<td><?php echo htmlspecialchars("$item[5]"); ?></td>
					<td><?php echo $item[4]; ?></td>
					<td><?php echo htmlspecialchars("$item[6]"); ?></td>
					<td><?php echo $item1[2]; ?></td>
					<td><?php echo $item1[3]; ?></td>
					<td><?php echo $d[1]; ?></td>
					<td><?php echo $d[2]; ?></td>
					<td><?php echo $d[6]; ?></td>
					<td><?php echo $amt; ?></td>
					<td><?php echo $tax; ?></td>
					<td><?php echo $amt1; ?></td>
					<td><?php echo $rate; ?></td>
					<td><?php echo $sell; ?></td>
					<td><?php echo $d[4]; ?></td>
					<?php
						$table .= "<td>$j</td>
								<td>$item[1]</td>
								<td>$item[2]</td>
								<td>".htmlspecialchars("$item[5]")."</td>
								<td>$item[4]</td>
								<td>".htmlspecialchars("$d[5]")."</td>
								<td>$item1[2]</td>
								<td>$item1[3]</td>
								<td>$d[1]</td>
								<td>$d[2]</td>
								<td>$d[6]</td>
								<td>$amt</td>
								<td>$tax</td>
								<td>$amt1</td>
								<td>$rate</td>
								<td>$sell</td>
								<td>$d[4]</td>";
					?>
					<td>
						<button class="btn btn-warning btn-rounded btn-condensed btn-sm" onClick="window.open('purdet.php?pur_id=<?php echo $d[5]; ?>','_self');"><span class="fa fa-list"></span></button>
					</td>
				</tr>
			<?php
					$table .= "</tr>";
				$j++;
			}while($d = mysqli_fetch_array($result));
		}
		
	?>			
		</tbody>
      </table> 
	
		<div class="col-md-1 col-xs-1">
			  <form action="printlist.php" method="post" target="_blank">
				<input type="hidden" value="<?php echo $table; ?>" name="query"/>
				<button class="btn btn-primary" type="submit" name="s10">Print</button>		
				</form>
		</div>
		<div class="col-md-3 col-xs-3">
			<form action="excel.php" method="post">
				 <input type="hidden" name="query" value="<?php echo $table; ?>"/>
				 <input type="hidden" name="fn" value="Purchase Bill Details (Item Wise)"/>
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