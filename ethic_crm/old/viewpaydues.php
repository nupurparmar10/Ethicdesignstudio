<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	 if(isset($_REQUEST['pay_id']))
	 {
	 	$pid1=mysqli_query($con,"select relatedwith from paydues where pay_id=$_REQUEST[pay_id]");
		$pid=mysqli_fetch_row($pid1);
		$rid=$pid[0];
		mysqli_query($con,"delete from paydues where pay_id=$_REQUEST[pay_id]");
		mysqli_query($con,"delete from transaction where relatedto='$rid'");
		$msg="Pay Dues Entry Deleted Successfully!!!";
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
		<script type="text/javascript" language="javascript">
	function delete_row(row)
	{
        var box = $("#mb-remove-row");
        box.addClass("open");
        box.find(".mb-control-yes").on("click",function(){
            box.removeClass("open");
			delete_row1(row);
            $("#"+row).hide("slow",function(){
                $(this).remove();
            });
			

        });
    }
	function delete_row1(row)
	{
			var path="viewpaydues.php?pay_id="+row;
			window.open(path,"_self");
	}
		</script>     
		<script src="js\jquery.min.js"></script>                 
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu11=true; $smenu11="2"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Pay Dues Master</a></li>
                    <li class="active">View Pay Dues Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                     <h2> View Pay Dues Details</h2>
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
							<div class="alert alert-danger" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg; ?></strong>
							</div>
							<?php 
								}
							?>
							
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                
                                <div class="panel-body">
									 <form class="form-horizontal" method="post" action="viewpaydues.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Party</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
											</div>
											<div class="row">
												 <div class="col-md-2 col-xs-12">
												 <select class="form-control" name="party" id="party">
													<option value="">--Select--</option>
													<?php
														$d1=mysqli_query($con,"select * from ledger_accounts where group_id in (26,27) order by name");
														while($d=mysqli_fetch_row($d1))
														{
															echo "<option value='$d[0]'>$d[1]</option>";
														}
													?>
												</select></div>
												 <div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dfrom" id="dfrom"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dto" id="dto"/>
												</div>
												<div class="col-md-2 col-xs-12"> 
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
   if($_REQUEST['party']!="") $party=" and party='$_REQUEST[party]'"; else $party="";
  if($_REQUEST['dfrom']!="" && $_REQUEST['dto']!="")
{
  $sql = "SELECT * FROM paydues where true ".$party." and rdate>='$_REQUEST[dfrom]' and rdate<='$_REQUEST[dto]' order by rdate desc";
} 	
else if($_REQUEST['dfrom']!="")
{
	$sql = "SELECT * FROM paydues where true ".$party." and rdate>='$_REQUEST[dfrom]' order by rdate desc";
}
else if($_REQUEST['dto']!="")
{
	$sql = "SELECT * FROM paydues where true ".$party." and rdate<='$_REQUEST[dto]' order by rdate desc";
}
else
{
	$sql = "SELECT * FROM paydues where true ".$party." order by rdate desc";
} 

	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Pay Dues Detail Available!!!";
	}
	else
	{
		$table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
			<caption><h1>Pay Dues Details</h1></caption>
			<tr>
				<th width='65'><span>S. No.</span></th>
				<th width='200'><span>Date</span></th>	
				<th width='200'><span>Party</span></th>	
				<th width='200'><span>Amount</span></th>	
				<th width='200'><span>Paid By</span></th>		
				<th width='200'><span>Cheque</span></th>	
				<th width='200'><span>Remark</span></th>	
			</tr>";
?>
		<table class="table table-bordered table-striped table-actions">
			<thead>
				<tr>
					<th width='65'><span>S. No.</span></th>
					<th width='200'><span>Date</span></th>	
					<th width='200'><span>Party</span></th>	
					<th width='200'><span>Amount</span></th>	
					<th width='200'><span>Paid By</span></th>		
					<th width='200'><span>Cheque</span></th>	
					<th width='200'><span>Remark</span></th>
					<th width="120">Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($row = mysqli_fetch_row($result))
		{		
			$j=1;			
			do
			{
		?>
				<tr id="<?php echo $row[0]; ?>">
		<?php				
				$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$row[2]");
				$a=mysqli_fetch_row($a1);
				echo "<td>$j</td>";
				$table .=  "<tr>
					<td style='padding-left:10px;'>$j</td>";
				if($row[1]!="0000-00-00")
				{
					$date= DateTime::createFromFormat('Y-m-d', $row[1]);
					echo "<td>";
					echo $date->format('d-m-Y');
					echo"</td>";
					$table .="<td style='text-align:left;'>".$date->format('d-m-Y')."</td>";
				}
				else 
				{
					echo "<td>&nbsp;</td>";
					$table .="<td></td>";
				}
				echo "<td>".htmlspecialchars($a[0])."</td>";
				echo "<td>".htmlspecialchars($row[3])."</td>";
				
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($a[0])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[3])."</td>";
					
				$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$row[4]");
				$a=mysqli_fetch_row($a1);
				echo "<td>".htmlspecialchars($a[0])."</td>";
				$table .="<td style='padding-left:10px;'>".htmlspecialchars($a[0])."</td>";
				
				echo "<td>".htmlspecialchars($row[5])."</td>";
				echo "<td>".htmlspecialchars($row[7])."</td>";
				$table .=  "<td style='padding-left:10px;'>".htmlspecialchars($row[5])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[7])."</td></tr>";
				?>
				<td>
					<button class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $row[0]; ?>');"><span class="fa fa-times" title="Delete"></span></button>
				</td>
			</tr>
			<?php
				$j++;
			}while($row = mysqli_fetch_array($result));
		}
		$table .= "</table>";
	?>	
		</tbody>
      </table> 
	  		<div class="col-md-1 col-xs-1">
			  <form action="printlist.php" method="post" target="_blank">
				<input type="hidden" value="<?php echo $table; ?>" name="query"/>
				<button class="btn btn-primary" type="submit" name="s11">Print</button>		
				</form>
		</div>
		<div class="col-md-3 col-xs-3">
			<form action="excel.php" method="post">
				 <input type="hidden" name="query" value="<?php echo $table; ?>"/>
				 <input type="hidden" name="fn" value="Pay Dues Details"/>
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