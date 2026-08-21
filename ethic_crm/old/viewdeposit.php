<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	if(isset($_REQUEST['msg']))
	{
		$msg="Deposit Entry Modified Successfully!!!";
	}
	if(isset($_REQUEST['d_id']))
	{
		$pid1=mysqli_query($con,"select relatedwith from deposit where d_id=$_REQUEST[d_id]");
		$pid=mysqli_fetch_row($pid1);
		$rid=$pid[0];
		mysqli_query($con,"delete from deposit where d_id=$_REQUEST[d_id]");
		mysqli_query($con,"delete from transaction where relatedto='$rid'");
		$msg1="Deposit Entry Deleted Successfully!!!";
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
			var path="viewdeposit.php?d_id="+row;
			window.open(path,"_self");
	}
	function gendeposit()
    {
		var val2 = document.frm2.bank.value;
		var val3 = document.frm2.dfrom.value;
		var val4 = document.frm2.dto.value;
		var val1 = document.frm2.amt.value;
		var val5 = document.frm2.remark.value;
        $.ajax({
           url : 'gendeposit.php',
           type : 'POST',
           data : {bank : val2, dfrom : val3, dto : val4, amt : val1, remark : val5},
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
    <body onload="gendeposit();">
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu4=true; $smenu4="2";  $ssmenu4="22"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Bank Master</a></li>
					<li><a href="#">Deposit Entries</a></li>
                    <li class="active">View Deposit Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2> View Deposit Details</h2>
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
							
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                
                                <div class="panel-body">
									 <form class="form-horizontal" method="post" action="#" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Bank Account</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
												<label class="col-md-2 col-xs-2">Amount</label>
												<label class="col-md-2 col-xs-2">Remark</label>
											</div>
											<div class="row">
												 <div class="col-md-2 col-xs-12">  
													 <select class="form-control" name="bank" onchange="gendeposit();">
													   <?php
															$list1=mysqli_query($con,"select * from ledger_accounts where ledger_id in (select ledger_id from bank_details) and status=1 order by name");
															echo "<option value=''>--Select--</option>";
															if($p=mysqli_fetch_row($list1))
															{
																do{
																	echo "<option value='$p[0]'>$p[1]</option>";
																}while($p=mysqli_fetch_row($list1));
															}
														?>
													</select>
												</div>
												<div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dfrom" id="dfrom" onchange="gendeposit();"/>
												</div>
												<div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dto" id="dto" onchange="gendeposit();"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="amt" id="amt" onkeyup="gendeposit();"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="text" class="form-control" name="remark" id="remark" onkeyup="gendeposit();"/>
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