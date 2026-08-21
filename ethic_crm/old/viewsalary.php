<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	 if(isset($_REQUEST['s_id']))
	 {
	 	$pid1=mysqli_query($con,"select relatedwith,amount,ledger_id,advded from salary where s_id=$_REQUEST[s_id]");
		$pid=mysqli_fetch_row($pid1);
		$rid=$pid[0];
		mysqli_query($con,"update empdet set advance=advance+$pid[3] where  ledger_id='$pid[2]'");
		mysqli_query($con,"delete from salary where s_id=$_REQUEST[s_id]");
		mysqli_query($con,"delete from transaction where relatedto='$rid'");
		$msg="Salary Entry Deleted Successfully!!!";
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
			var path="viewsalary.php?s_id="+row;
			window.open(path,"_self");
	}

 function gensalary()
    {
		var val1 = document.frm2.ledger_id.value;
		var val2 = document.frm2.dfrom.value;
		var val3 = document.frm2.dto.value;
		var val4 = document.frm2.remark.value;
		var val5 = document.frm2.month.value;
        var val6 = document.frm2.year.value;
        $.ajax({
           url : 'gensalary.php',
           type : 'POST',
           data : {ledger_id : val1, dfrom : val2, dto : val3, remark : val4, month : val5, year : val6},
           success : ajaxSuccess,
           error : ajaxError
          });
    }
 function ajaxSuccess(response)
    {
             $('#display').html(response);
    }
 function ajaxError()
    {
		alert("error");
	}
		</script>     
		<script src="js\jquery.min.js"></script>                 
    </head>
    <body onload="gensalary();">
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu8=true; $smenu8="4"; $ssmenu8="42"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Employee Master</a></li>
					<li><a href="#">Salary Master</a></li>
                    <li class="active">View Salary Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                     <h2> View Salary Details</h2>
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
									 <form class="form-horizontal" method="post" action="#" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Employee</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
												<label class="col-md-2 col-xs-2">Remark</label>
												<label class="col-md-2 col-xs-2">Paid for The Month</label>
                                                <label class="col-md-2 col-xs-2">Paid for The Year</label>
											</div>
											<div class="row">
												 <div class="col-md-2 col-xs-12">
												 <select class="form-control" name="ledger_id" onchange="gensalary();" id="ledger_id">
													<option value="">--Select--</option>
													<?php
														$d1=mysqli_query($con,"select ledger_id,empname from empdet order by empname");
														while($d=mysqli_fetch_row($d1))
														{
															echo "<option value='$d[0]'>$d[1]</option>";
														}
													?>
												</select></div>
												 <div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dfrom" id="dfrom" onchange="gensalary();"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dto" id="dto" onchange="gensalary();"/>
												</div>
												<div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="remark" id="remark" onkeyup="gensalary();"/>
												</div>
												<div class="col-md-2 col-xs-12">  
													<select class="form-control" name="month" onchange="gensalary();">
														<option value="">--Select Month--</option>
														<option value="01">January</option>
														<option value="02">Febuary</option>
														<option value="03">March</option>
														<option value="04">April</option>
														<option value="05">May</option>
														<option value="06">June</option>
														<option value="07">July</option>
														<option value="08">August</option>
														<option value="09">September</option>
														<option value="10">October</option>
														<option value="11">November</option>
														<option value="12">December</option>
													</select>     
												</div>
                                                <div class="col-md-2 col-xs-12">  
													<input type="number" class="form-control" name="year" id="year" value="<?php echo date("Y"); ?>" onkeyup="gensalary();"/>
												</div>
											</div>
									</div>
									 </form>
									 <br>
                                    <div class="table-responsive" id="display">
                                                                      
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