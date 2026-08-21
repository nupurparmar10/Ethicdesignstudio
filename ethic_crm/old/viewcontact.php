<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	 if(isset($_REQUEST['msg']))
	 {
	 	$msg="Contact edited successfully!!!";
	 }
	 if(isset($_REQUEST['c_id']))
	 {
	 	mysqli_query($con,"delete from contact where c_id='$_REQUEST[c_id]'");
	 	$msg1="Contact Deleted successfully!!!";
	 }
	if(isset($_REQUEST['msg1']))
	 {
	 	$msg2="Message Send successfully!!!";
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
			var path="viewcontact.php?c_id="+row;
			window.open(path,"_self");
	}
	function selectall()
{
	var sall=document.getElementById("all");
	if(sall.checked==1)
	{
		var scholar=document.getElementsByName("contacts[]");
		for(i=0;i<scholar.length;i++)
		{
			scholar[i].checked=1;
		}
	}
	else
	{
		var scholar=document.getElementsByName("contacts[]");
		for(i=0;i<scholar.length;i++)
		{
			scholar[i].checked=0;
		}
	}
}
		</script>     
		<script src="js\jquery.min.js"></script>                 
    </head>
    <body onload="gencontact();">
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            <!-- START PAGE SIDEBAR -->
             <?php $menu7=true; $smenu7="2"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Contact Master</a></li>
                    <li class="active">View Contact Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                     <h2> View Contact Details</h2>
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
									 <form class="form-horizontal" method="post" action="viewcontact.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Name</label>
												<label class="col-md-2 col-xs-2">Address</label>
												<label class="col-md-2 col-xs-2">Mobile</label>
												<label class="col-md-2 col-xs-2">Email</label>
												<label class="col-md-2 col-xs-2">Website</label>
												<label class="col-md-2 col-xs-2">Firm Name</label>
											</div>
											<div class="row">
												 <div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="cname" id="cname" onkeypress="return onlyCharacters(event);"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="address" id="address"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="mobile" id="mobile"  onkeypress="return allowOnlyNumbers(event);" oninput="allowOnlyNumbers(this, true);"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="email" class="form-control" name="email" id="email" />
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="website" id="website" />
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="fname" id="fname"/>
												</div>
											</div>
											<div class="row">
												<label class="col-md-2 col-xs-2">Group</label>
											</div>
											<div class="row">
												 <div class="col-md-2 col-xs-12">  
													<select class="form-control" name="group">
														<option value=''>--Select--</option>
														<?php
															$c1=mysqli_query($con,"select distinct(groupname) from contact");
															while($c=mysqli_fetch_row($c1))
															{
																echo "<option>$c[0]</option>";
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
 if($_REQUEST['group']!="") $group=" and groupname='$_REQUEST[group]'"; else $group="";
 $sql = "SELECT * FROM contact where cname like '%$_REQUEST[cname]%' and (mob1 like '%$_REQUEST[mobile]%' or mob2 like '%$_REQUEST[mobile]%' or mob3 like '%$_REQUEST[mobile]%') and email like '%$_REQUEST[email]%' and website like '%$_REQUEST[website]%' and firmname like '%$_REQUEST[fname]%' and address like '%$_REQUEST[address]%' ".$group." order by cname";

	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Contact Available!!!";
	}
	else
	{
		$table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
			<caption><h1>Contact Details</h1></caption>
			<tr>
				<th width='58'><span>S. No.</span></th>
				<th width='189'><span>Name</span></th>	
				<th width='189'><span>Address</span></th>	
				<th width='169'><span>Firm Name</span></th>	
				<th width='141'><span>Mobile 1</span></th>	
				<th width='146'><span>Mobile 2</span></th>	
				<th width='124'><span>Mobile 3</span></th>	
				<th width='155'><span>Email ID</span></th>	
				<th width='150'><span>Website</span></th>	
				<th width='150'><span>Remark</span></th>	
				<th width='150'><span>Group</span></th>	
			</tr>";
?>
<form action='messageall.php' method='post'>
		<table class="table datatable table-bordered table-striped table-actions">
			<thead>
				<tr>
					<th width="58"><span>Select <input type='checkbox' name='all' onchange="selectall();" id='all'/></span></th>
					<th width="58"><span>S. No.</span></th>
					<th width="189"><span>Name</span></th>	
					<th width="189"><span>Address</span></th>	
					<th width="169"><span>Firm Name</span></th>	
					<th width="141"><span>Mobile 1</span></th>	
					<th width="146"><span>Mobile 2</span></th>	
					<th width="124"><span>Mobile 3</span></th>	
					<th width="155"><span>Email ID</span></th>		
					<th width='150'><span>Website</span></th>	
					<th width='150'><span>Remark</span></th>	
					<th width='150'><span>Group</span></th>
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
				echo "<td><input type='checkbox' name='contacts[]' value='$row[0]'/></td>";
				echo "<td>$j</td>";
				echo "<td>".htmlspecialchars($row[1])."</td>";
				echo "<td>".htmlspecialchars($row[8])."</td>";
				echo "<td>".htmlspecialchars($row[7])."</td>";
				echo "<td>$row[2]</td>";
				echo "<td>$row[3]</td>";
				echo "<td>$row[4]</td>";
				echo "<td>".htmlspecialchars($row[5])."</td>";
				echo "<td>".htmlspecialchars($row[6])."</td>";
				echo "<td>".htmlspecialchars($row[9])."</td>";
				echo "<td>".htmlspecialchars($row[10])."</td>";
				$table .=  "<tr>
					<td style='padding-left:10px;'>$j</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[1])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[8])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[7])."</td>
					<td style='padding-left:10px;'>$row[2]</td>
					<td style='padding-left:10px;'>$row[3]</td>
					<td style='padding-left:10px;'>$row[4]</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[5])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[6])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[9])."</td>
					<td style='padding-left:10px;'>".htmlspecialchars($row[10])."</td>
					</tr>";
			?>
				<td>
					<button type="button" class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('addcontact.php?c_id=<?php echo $row[0]; ?>','_self');" title="Edit"><span class="fa fa-pencil"></span></button>
					<button type="button" class="btn btn-danger btn-rounded btn-condensed btn-sm" onClick="delete_row('<?php echo $row[0]; ?>');"><span class="fa fa-times" title="Delete"></span></button>
				</td>
			</tr>
			<?php
				$j++;
			}while($row = mysqli_fetch_array($result));
		}
		$table .="</table>";
	?>							
		</tbody>
      </table> 
	  <button class="btn btn-warning" type="submit" name="s10">Message Selected</button>		
		</form>
		<br><br>
		<div class="col-md-1 col-xs-1">
			  <form action="printlist.php" method="post" target="_blank">
				<input type="hidden" value="<?php echo $table; ?>" name="query"/>
				<button class="btn btn-primary" type="submit" name="s11">Print</button>		
				</form>
		</div>
		<div class="col-md-3 col-xs-3">
			<form action="excel.php" method="post">
				 <input type="hidden" name="query" value="<?php echo $table; ?>"/>
				 <input type="hidden" name="fn" value="Contact Details"/>
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