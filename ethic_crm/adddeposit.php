<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	if(isset($_REQUEST['s1']))
	{
		$wdate=$_REQUEST['wdate'];
		$bank=$_REQUEST['bank'];
		$paidby=$_REQUEST['paidby'];
		$amt=$_REQUEST['amount'];
		$remark=$_REQUEST['remarks'];
		$cmp1=mysqli_query($con,"select max(d_id) from deposit");
		$cmp=mysqli_fetch_row($cmp1);
		$did=$cmp[0]+1;
		
		$rid="D".$did;
	
		mysqli_query($con,"insert into deposit set d_id=$did, d_date='$wdate', amt='$amt', bank_acc='$bank', remark='$remark', relatedwith='$rid'");
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
		
		$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$bank'");
		$a=mysqli_fetch_row($a1);
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$wdate."', ledger_id='".$paidby."', amount='".$amt."', particulars='Cash Deposit to $a[0]', type='Cr.', relatedto='$rid'");
		$tid++;
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$wdate."', ledger_id='".$bank."', amount='".$amt."', particulars='Cash Deposited', type='Dr.', relatedto='$rid'");
	
		$msg="Cash Deposit Entry Saved Successfully!!!";
	}	
	else if(isset($_REQUEST['s3']))
	{
		$wdate=$_REQUEST['wdate'];
		$bank=$_REQUEST['bank'];
		$paidby=$_REQUEST['paidby'];
		$amt=$_REQUEST['amount'];
		$remark=$_REQUEST['remarks'];
		$did=$_REQUEST['d_id'];
		$rid=$_REQUEST['rid'];
		
		mysqli_query($con,"delete from transaction where relatedto='$rid'");
		
		mysqli_query($con,"update deposit set d_date='$wdate', amt='$amt', bank_acc='$bank', remark='$remark' where d_id=$did");
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
		
		$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$bank'");
		$a=mysqli_fetch_row($a1);
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$wdate."', ledger_id='".$paidby."', amount='".$amt."', particulars='Cash Deposit to $a[0]', type='Cr.', relatedto='$rid'");
		$tid++;
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$wdate."', ledger_id='".$bank."', amount='".$amt."', particulars='Cash Deposited', type='Dr.', relatedto='$rid'");
		header("Location: viewdeposit.php?msg=set"); die;
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
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu4=true; $smenu4="2";  $ssmenu4="21"; include_once("sidebar.php"); ?>
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
                    <li class="active"><?php if(isset($_REQUEST['d_id'])) {?>Modify Entry<?php } else { ?>Add Entry<?php }?></li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
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
                            <form class="form-horizontal" method="post" action="adddeposit.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									<?php
										if(isset($_REQUEST['d_id']))
										{
											$list1=mysqli_query($con,"select * from deposit where d_id='$_REQUEST[d_id]'");
											$l=mysqli_fetch_row($list1);
											echo "<input type='hidden' name='d_id' value='$_REQUEST[d_id]'/>";
											echo "<input type='hidden' name='rid' value='$l[5]'/>";
											
									?>
                                    <h3 class="panel-title"><strong>Modify</strong> Entry</h3>
									<?php
										}
										else
										{
											$l[1]=$l[2]=$l[3]=$l[4]="";
											$l[1]=date("Y-m-d");
									?>
									 <h3 class="panel-title"><strong>Add New</strong> Entry</h3>
									<?php
										}
									?>
                                </div>
                                <div class="panel-body">   
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Date</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="date" class="form-control" name="wdate" value='<?php echo $l[1]; ?>' required />
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Amount</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="amount" value='<?php echo $l[2]; ?>' required/>
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">From</label>
                                        <div class="col-md-6 col-xs-12">       
                                            <select class="form-control" name="paidby" required>
                                               <?php
													$a1=mysqli_query($con,"select ledger_id from ledger_accounts where name='Cash Account' and status=1");
													$a=mysqli_fetch_row($a1);
													echo "<option value='$a[0]'>Cash Account</option>";
												?>
                                            </select>
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">To Bank Account</label>
                                        <div class="col-md-6 col-xs-12">       
                                            <select class="form-control" name="bank" required>
                                               <?php
													$list1=mysqli_query($con,"select * from ledger_accounts where ledger_id in (select ledger_id from bank_details) and status=1 order by name");
													echo "<option value=''>--Select--</option>";
													if($p=mysqli_fetch_row($list1))
													{
														do{
															if($l[3]==$p[0])
															echo "<option value='$p[0]' selected='selected'>$p[1]</option>";
															else
															echo "<option value='$p[0]'>$p[1]</option>";
														}while($p=mysqli_fetch_row($list1));
													}
												?>
                                            </select>
                                        </div>
                                    </div>
									
                                     <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Remarks</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group col-md-12">
                                                <textarea class="form-control" rows="5" name="remarks"><?php echo $l[4]; ?></textarea>
                                            </div>                                            
                                        </div>
                                    </div>
									
                                    
                                </div>
                                <div class="panel-footer">
									<?php
										if(isset($_REQUEST['d_id']))
										{
									?>
									<button class="btn btn-primary" type="submit" name="s3">Modify</button>
									<?php
										}
										else
										{
									?>
									<button class="btn btn-primary" type="submit" name="s1">Add</button>
									<?php
										}
									?>
                                    <button class="btn btn-default">Clear Form</button>                                    
                                </div>
                            </div>
                            </form>
                            
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