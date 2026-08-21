<?php
	ob_start();
	session_start();
	include_once("connect.php");
	if(!isset($_REQUEST['ledger_id']))
	{
		header("Location: viewledger.php"); die;
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
	 function gentransaction()
    {
		var val1 = document.frm2.dfrom.value;
		var val2 = document.frm2.dto.value;
		var val3 = document.frm2.ledger_id.value;
		$.ajax({
           url : 'gentransaction.php',
           type : 'POST',
           data : {dfrom : val1, dto : val2, ledger_id : val3},
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
    <body onload="gentransaction();">
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu3=true; $smenu3="2"; $ssmenu3="22"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Accounts Master</a></li>
                    <li><a href="#">Ledger Accounts</a></li>
					<li><a href="#">Ledger Details</a></li>
                    <li class="active">Transaction Details</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                     <h2> View Transaction Details</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                    
                    
                    <div class="row">
                        <div class="col-md-12">
							
							
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                
                                <div class="panel-body">
									 <form class="form-horizontal" method="post" action="#" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
											</div>
											<div class="row">
												 <div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dfrom" id="dfrom" onchange="gentransaction();"/>
												</div>
												 <div class="col-md-2 col-xs-12">  
													<input type="date" class="form-control" name="dto" id="dto" onchange="gentransaction();"/>
												</div>
												<input type="hidden" name="ledger_id" value="<?php echo $_REQUEST['ledger_id']; ?>"/>
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