<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
	if(isset($_REQUEST['s1']))
	{
			$expdate=$_REQUEST['expdate'];
			$ledger_id=$_REQUEST['ledger_id'];
			$paidby=$_REQUEST['paidby'];
			$amt=$_REQUEST['amount'];
			$b1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$ledger_id'");
			$b=mysqli_fetch_row($b1);
			
			$cmp1=mysqli_query($con,"select max(inc_id) from income_detail");
			$cmp=mysqli_fetch_row($cmp1);
			$id=$cmp[0]+1;
			
			$rid="I".$id;
			mysqli_query($con,"insert into income_detail set inc_id=$id, incdate='$expdate', ledger_id='$ledger_id', paidby='$paidby', amount='$amt', remark='$_REQUEST[remark]', relatedwith='$rid', party='$_REQUEST[party]'");
			
			$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
			$cmp=mysqli_fetch_row($cmp1);
			$tid=$cmp[0]+1;
			if($_REQUEST['paidby']=="Credit")
			{
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$expdate."', ledger_id='".$_REQUEST['party']."', amount='".$amt."', particulars='Income for $b[0] $_REQUEST[remark]', type='Dr.', relatedto='$rid'");		
				$tid++;
				
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$expdate."', ledger_id='".$ledger_id."', amount='".$amt."', particulars='Income Received $_REQUEST[remark]', type='Cr.', relatedto='$rid'");
				$tid++;
			}
			else
			{
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$expdate."', ledger_id='".$paidby."', amount='".$amt."', particulars='Income Received from $b[0] $_REQUEST[remark]', type='Dr.', relatedto='$rid'");		
				$tid++;
				
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$expdate."', ledger_id='".$ledger_id."', amount='".$amt."', particulars='Income Received $_REQUEST[remark]', type='Cr.', relatedto='$rid'");
				$tid++;
			}	
			
			$msg="Income Entry Saved Successfully!!!";
	}
	if(isset($_REQUEST['s3']))
	{
			$pid1=mysqli_query($con,"select relatedwith from income_detail where inc_id=$_REQUEST[inc_id]");
			$pid=mysqli_fetch_row($pid1);
			$rid=$pid[0];
			$expdate=$_REQUEST['expdate'];
			$ledger_id=$_REQUEST['ledger_id'];
			$paidby=$_REQUEST['paidby'];
			$amt=$_REQUEST['amount'];
			mysqli_query($con,"update income_detail set incdate='$expdate', ledger_id='$ledger_id', paidby='$paidby', amount='$amt', remark='$_REQUEST[remark]', party='$_REQUEST[party]' where inc_id='$_REQUEST[inc_id]'");
			
			mysqli_query($con,"delete from transaction where relatedto='$rid'");
			
			$b1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$ledger_id'");
			$b=mysqli_fetch_row($b1);
			
			$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
			$cmp=mysqli_fetch_row($cmp1);
			$tid=$cmp[0]+1;
					
			if($_REQUEST['paidby']=="Credit")
			{
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$expdate."', ledger_id='".$_REQUEST['party']."', amount='".$amt."', particulars='Income for $b[0] $_REQUEST[remark]', type='Dr.', relatedto='$rid'");		
				$tid++;
				
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$expdate."', ledger_id='".$ledger_id."', amount='".$amt."', particulars='Income Received $_REQUEST[remark]', type='Cr.', relatedto='$rid'");
				$tid++;
			}
			else
			{
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$expdate."', ledger_id='".$paidby."', amount='".$amt."', particulars='Income Received from $b[0] $_REQUEST[remark]', type='Dr.', relatedto='$rid'");		
				$tid++;
				
				mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$expdate."', ledger_id='".$ledger_id."', amount='".$amt."', particulars='Income Received $_REQUEST[remark]', type='Cr.', relatedto='$rid'");
				$tid++;
			}	
			header("Location: viewincentry.php?msg=set"); die;
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
             <?php $menu6=true; $smenu6="2";  $ssmenu6="21"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Income Master</a></li>
					<li><a href="#">Income Entry</a></li>
                    <li class="active"><?php if(isset($_REQUEST['inc_id'])) {?>Modify Income Entry<?php } else { ?>Add Income Entry<?php }?></li>
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
                            <form class="form-horizontal" method="post"  name='frm2' action="addincentry.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									<?php
										if(isset($_REQUEST['inc_id']))
										{
											$d1=mysqli_query($con,"select * from income_detail where inc_id='$_REQUEST[inc_id]'");
											$d=mysqli_fetch_row($d1);
											echo "<input type='hidden' name='inc_id' value='$_REQUEST[inc_id]'/>";
									?>
                                    <h3 class="panel-title"><strong>Modify</strong> Income Entry</h3>
									<?php
										}
										else
										{
											$d[1]=$d[2]=$d[3]=$d[4]=$d[5]=$d[7]="";
											$d[1]=date("Y-m-d");
									?>
									 <h3 class="panel-title"><strong>Add New</strong> Income Entry</h3>
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
                                                <input type="date" class="form-control" name="expdate" value='<?php echo $d[1]; ?>' required />
                                            </div>                                            
                                        </div>
                                    </div>        
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Party</label>
                                        <div class="col-md-6 col-xs-12">           
                                           <select class="form-control" name="party" required>
											<option value="">--Select--</option>
												<?php
													$f1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (26,27) order by name");
													while($f=mysqli_fetch_row($f1))
													{
														if($d[7]==$f[0])
															echo "<option value='$f[0]' selected='selected'>$f[1]</option>";
														else
															echo "<option value='$f[0]'>$f[1]</option>";
													}
												?>	
											</select>
                                        </div>
                                    </div>									
                                     <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Income ledger</label>
                                        <div class="col-md-6 col-xs-12">
                                            <select class="form-control" name="ledger_id" required>
                                                <option value="">--Select--</option>
												<?php
													$c1=mysqli_query($con,"select * from ledger_accounts where status=1  and group_id in (select group_id from group_master where group_name in ('Direct Incomes','Indirect Incomes')) order by name");
													while($c=mysqli_fetch_row($c1))
													{
														if($d[2]==$c[0])
															echo "<option value='$c[0]' selected='selected'>$c[1]</option>";
														else
															echo "<option value='$c[0]'>$c[1]</option>";
													}
												?>
                                            </select>
                                        </div>
                                    </div>
									 <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Received By</label>
                                        <div class="col-md-6 col-xs-12">
                                            <select class="form-control" name="paidby" required>
												<option value="">--Select--</option>
												<option value="Credit" <?php if($d[3]=="Credit") { ?> selected='selected' <?php } ?>>Credit</option>
                                               <?php
													$list1=mysqli_query($con,"select ledger_id,name from ledger_accounts  where status=1 and (name like 'Cash Account' or group_id in (select group_id from group_master where group_name like 'Bank Accounts')) order by name");
													if($l=mysqli_fetch_row($list1))
													{
														do{
															if($d[3]==$l[0])
															echo "<option value='$l[0]' selected='selected'>$l[1]</option>";
															else
															echo "<option value='$l[0]'>$l[1]</option>";
														}while($l=mysqli_fetch_row($list1));
													}
												?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Amount</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="amount" value='<?php echo $d[4]; ?>' required/>
                                            </div>                                            
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Remarks</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group col-md-12">
                                                <textarea class="form-control" rows="5" placeholder="Your remark..." name="remark"><?php echo $d[5]; ?></textarea>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
									<?php
										if(isset($_REQUEST['inc_id']))
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