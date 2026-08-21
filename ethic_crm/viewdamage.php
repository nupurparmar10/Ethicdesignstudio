<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	if(isset($_REQUEST['msg']))
	{
		$msg="Damage Entry Modified Successfully!!!";
	}
	if(isset($_REQUEST['d_id']))
	{
       
		$p1=mysqli_query($con,"select * from damage_particular where d_id='$_REQUEST[d_id]'");
		while($p=mysqli_fetch_row($p1))
		{
			mysqli_query($con,"update variant set stock=stock+$p[2], webstock=webstock+$p[2] where v_id='$p[2]'");
		}
		mysqli_query($con,"delete from damage_particular where d_id='$_REQUEST[d_id]'");
		mysqli_query($con,"delete from damage_master where d_id='$_REQUEST[d_id]'");
		$msg1="Damage Entry Deleted Successfully!!!";
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
            var path="viewdamage.php?d_id="+row;
            window.open(path,"_self");
        }
		</script>  
		<script src="js\jquery.min.js"></script>           
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu2=true; $smenu2="7";  $ssmenu2="72"; include_once("sidebar.php"); ?>
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
                    <li class="active">Damage Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2> View Damage Details</h2>
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
                                   <form class="form-horizontal" method="post" action="viewdamage.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
                                                <label class="col-md-2 col-xs-2">Product</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
											</div>
											<div class="row">
                                                <div class="col-md-2 col-xs-2">
                                                    <select class="form-control" name="v_id" >
                                                        <option value="">--Select--</option>
                                                        <?php
                                                            $f1=mysqli_query($con,"select * from variant where item_id in (select item_id from item_details where status=1) and stock>0 order by v_id");										
                                                            while($f=mysqli_fetch_row($f1))
                                                            {
                                                                $c=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
                                                                
                                                                echo "<option value='$f[0]'>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
                                                            }
                                                        ?>	
                                                    </select>
                                                </div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dfrom"/></div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dto"/></div>
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
	if($_REQUEST['dfrom']!="") $dfrom=" and ddate>='$_REQUEST[dfrom]'"; else $dfrom="";
	if($_REQUEST['dto']!="") $dto=" and ddate<='$_REQUEST[dto]'"; else $dto="";
	if($_REQUEST['v_id']!="") $v_id=" and v_id='$_REQUEST[v_id]'"; else $v_id="";

    $sql="select * from damage_particular where true ".$v_id." and d_id IN( select d_id from damage_master where true ".$dfrom." ".$dto." )";
	$result = mysqli_query($con,$sql);
	$table ="";

	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Damage Entry Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' cellpadding='3' width='100%' style='border-collapse:collapse; font-size:12px;'>
			<tr>
				<th>S.<br>No.</th>
				<th>Date</th>
				<th>Product</th>
				<th>Quantity</th>
				<th>Purchase Rate</th>
				<th>Amount</th>
                <th>Remark</th>
			</tr>";
?>
		<table class="table table-bordered table-actions">
			<thead>
				<tr>
					<th style="width:20px;">S.<br>No.</th>
					<th width='80'>Date</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Purchase Rate</th>
                    <th>Amount</th>
                    <th>Remark</th>
					<th width='20'>Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($d = mysqli_fetch_row($result))
		{		
			$j=1;
			$tot=0;
			$tot1=0;
			do
			{
                $p=mysqli_fetch_row(mysqli_query($con,"select * from damage_master  where d_id='$d[1]'"));
                $v1=mysqli_query($con,"select * from variant  where v_id='$d[2]'");
                if($v=mysqli_fetch_row($v1))
                {
                    $item=mysqli_fetch_row(mysqli_query($con,"select * from item_details  where item_id='$v[1]'"));
                    $variant=$item[1].' '.$item[5].' '.$v[2].' '.$v[3];
                }
                else
                {
                    $variant='';
                }
                $amt=$d[3]*$d[4];
				$table .= "<tr>";
			?>
				 <tr id="<?php echo $d[0]; ?>">
					<td><?php echo $j;?></td>
					<?php
						$table .= "<td>$j</td>";
						if($p[1]!="0000-00-00")
						{
							$date= DateTime::createFromFormat('Y-m-d', $p[1]);
							echo "<td>".$date->format('d-m-Y')."</td>";
							$table .="<td>".$date->format('d-m-Y')."</td>";
						}
						else
						{
							echo "<td>&nbsp;</td>";
							$table .= "<td>&nbsp;</td>";
						}
					?>
					<td><?php echo $variant; ?></td>
					<td><?php echo $d[3]; ?></td>
					<td><?php echo $d[4]; ?></td>
					<td align='right'><?php echo number_format($amt,2); ?></td>
					<td><?php echo $p[2]; ?></td>
					<?php
						$table .= "<td>$variant</td>
								<td>$d[3]</td>
								<td>$d[4]</td>
								<td align='right'>".number_format($amt,2)."</td>
								<td>$p[2]</td>";
						$tot+=$d[4];
						$tot1+=$amt;
					?>
					<td>
						<button class="btn btn-info btn-rounded btn-condensed btn-sm" onClick="window.open('adddamage.php?d_id=<?php echo $d[1]; ?>','_self');"><span class="fa fa-pencil"></span></button>
						
						<button class="btn btn-danger btn-rounded btn-condensed btn-sm" title="Delete Damage Entry" onClick="delete_row('<?php echo $d[1]; ?>');"><span class="fa fa-times"></span></button>
					</td>
				</tr>
			<?php
					$table .= "</tr>";
				$j++;
			}while($d = mysqli_fetch_array($result));
		}
		$table .="<tr>
			<td colspan='4'>Total</td>
			<td align='right'>".number_format($tot,2)."</td>
			<td align='right'>".number_format($tot1,2)."</td>
		</tr></table>";
	?>			
		<tr>
			<td colspan='4'  style="font-weight:bolder;">Total</td>
			<td align='right' style="font-weight:bolder;"><?php echo number_format($tot,2); ?></td>
			<td align='right'  style="font-weight:bolder;"><?php echo number_format($tot1,2); ?></td>
			<td></td>
		</tr>
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
				 <input type="hidden" name="fn" value="Sale Bill Details"/>
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
        
        <!-- THIS PAGE PLUGINS -->
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>                
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
        <script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
        <!-- END THIS PAGE PLUGINS -->       
        
        <!-- START TEMPLATE -->
        
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->   
    </body>
</html>