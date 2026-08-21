<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	$flag=0;
	if(isset($_REQUEST['s5']))
	{
		$chk1=mysqli_query($con,"select * from salary where month='$_REQUEST[month]' and ledger_id='$_REQUEST[ledger_id]'");
		if($chk=mysqli_fetch_row($chk1))
		{
			$msg1="Salary Already Given!!!";
		}
		else $flag=1;
	}
	if(isset($_REQUEST['s1']))
	{
		$sdate=$_REQUEST['sdate'];
		$cmp1=mysqli_query($con,"select max(s_id) from salary");
		$cmp=mysqli_fetch_row($cmp1);
		$id=$cmp[0]+1;
		
		$rid="SAL".$id;
		mysqli_query($con,"insert into salary set s_id=$id, sdate='$sdate', ledger_id='$_REQUEST[ledger_id]', paidby='$_REQUEST[paidby]', month='$_REQUEST[month]', salary='$_REQUEST[salary]', present='$_REQUEST[present]', absent='$_REQUEST[absent]', erpsalary='$_REQUEST[erpsalary]', remark='$_REQUEST[remark]', comm='$_REQUEST[comm]', cremark='$_REQUEST[cremark]', deduction='$_REQUEST[deduction]', dremark='$_REQUEST[dremark]', amount='$_REQUEST[amount]',  relatedwith='$rid', advded='$_REQUEST[advance]', year='$_REQUEST[year]'");
		
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
		
		$date= DateTime::createFromFormat('m', $_REQUEST['month']);
		$m=$date->format('M');
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$_REQUEST['sdate']."', ledger_id='".$_REQUEST['ledger_id']."', amount='".$_REQUEST['amount']."', particulars='Salary Paid for the Month $m', type='Dr.', relatedto='$rid'");
		$tid++;
		
		$emp=mysqli_fetch_row(mysqli_query($con,"select name from ledger_accounts where ledger_id='$_REQUEST[ledger_id]'"));
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$_REQUEST['sdate']."', ledger_id='".$_REQUEST['paidby']."', amount='".$_REQUEST['amount']."', particulars='Salary Paid for the Month $m to $emp[0]', type='Cr.', relatedto='$rid'");
		
		mysqli_query($con,"update empdet set advance=advance-$_REQUEST[advance] where  ledger_id='$_REQUEST[ledger_id]'");
		$msg="Salary Entry Saved Successfully!!!";
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
		<Script>
			function calc()
			{
				var erpsalary=document.frm2.erpsalary.value*1;
				var comm=document.frm2.comm.value*1;
				var deduction=document.frm2.deduction.value*1;
				var advance=document.frm2.advance.value*1;
				var amount=erpsalary + comm - deduction - advance;
				document.frm2.amount.value=amount.toFixed(2);
			}
		</script>
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu8=true; $smenu8="4"; $ssmenu8="41"; include_once("sidebar.php"); ?>
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
                    <li class="active">Pay Salary</li>
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
							<div class="alert alert-warning" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg1; ?></strong>
							</div>
							<?php 
								}
							?>
                            <form class="form-horizontal" method="post" action="paysalary.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									
									 <h3 class="panel-title"><strong>Pay </strong> Salary</h3>
                                </div>
								<?php
									$date=date("Y-m-d");
								?>
                                <div class="panel-body">   
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Date</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                <input type="date" class="form-control" name="sdate" value='<?php echo $date; ?>' required />
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Employee</label>
                                        <div class="col-md-6 col-xs-12">   
										<select class='form-control' name='ledger_id' required>
											<option value="">--Select--</option>
										<?php
												$list1=mysqli_query($con,"select ledger_id,empname from empdet where status=1 order by empname");
												while($list=mysqli_fetch_row($list1))
												{
													echo "<option value='$list[0]'>$list[1]</option>";
												}
												?>       
										</select>												
										</div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Month</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                           <select class="form-control" name="month" required>
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
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Year</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                           <input class="form-control" name="year" required type='number' value='<?php echo date("Y"); ?>'/>
                                        </div>
                                    </div> 
                                </div>
                                <div class="panel-footer">
									<button class="btn btn-primary" type="submit" name="s5">Proceed</button>
                                    <button class="btn btn-default">Clear Form</button>                                    
                                </div>
                            </div>
                            </form>
                            <br>
							<br>
							<?php
								if($flag==1)
								{
							?>
							 <form class="form-horizontal" method="post" action="paysalary.php" enctype="multipart/form-data" name='frm2'>
                            <div class="panel panel-default">
                                <div class="panel-heading">
									
									 <h3 class="panel-title"><strong>Pay </strong> Salary</h3>
                                </div>
								<?php
									echo "<input type='hidden' name='sdate' value='$_REQUEST[sdate]'/>";
									echo "<input type='hidden' name='ledger_id' value='$_REQUEST[ledger_id]'/>";
									echo "<input type='hidden' name='month' value='$_REQUEST[month]'/>";
									echo "<input type='hidden' name='year' value='$_REQUEST[year]'/>";
								?>
                                <div class="panel-body">   
									<div class="form-group">
										<div class="col-md-12 col-xs-12 table-responsive">
											<table class="table table-bordered">
												<tr>
													<th>Date</th>
													<th>Employee</th>
													<th>Month</th>
													<th>Salary</th>
													<th>Total Present</th>
													<th>Total Absent</th>
												</tr>
												<tr>
													<td><?php echo $_REQUEST['sdate']; ?></td>
													<td>
													<?php
														$emp1=mysqli_query($con,"select empname,salary,daysallowed,advance from empdet where ledger_id='$_REQUEST[ledger_id]'");
														$emp=mysqli_fetch_row($emp1);
														echo "$emp[0]";
													?></td>
													<td><?php $month=$_REQUEST['month']; ?> 
													<?php $date= DateTime::createFromFormat('m', $month);
													echo $date->format('M')." ".$_REQUEST['year']; ?></td>
													<td><?php												
														echo "$emp[1]";
														echo "<input type='hidden' name='salary' value='$emp[1]'/>";
													?> </td>
													<td> <?php
														$year=$_REQUEST['year'];
														$dfrom=$year."-".$month."-01";
														$f_d_t = mktime(0,0,0,$month,1,$year);
														$no_days=date("t",$f_d_t);
														$dto=$year."-".$month."-".$no_days;
														$days1=mysqli_fetch_row(mysqli_query($con,"select count(*) from empattendance where ledger_id='$_REQUEST[ledger_id]' and adate>='$dfrom' and adate<='$dto' and att='P'"));
														$days2=mysqli_fetch_row(mysqli_query($con,"select count(*) from empattendance where ledger_id='$_REQUEST[ledger_id]' and adate>='$dfrom' and adate<='$dto' and att='H'"));
														$days3=mysqli_fetch_row(mysqli_query($con,"select count(*) from empattendance where ledger_id='0' and adate>='$dfrom' and adate<='$dto' and att=''"));
														$days=$days1[0]+$days3[0]+($days2[0]/2);
														echo $days;
														echo "<input type='hidden' name='present' value='$days'/>";
														$comm=0;
														$com1=mysqli_query($con,"select * from billbook where invdate>='$dfrom' and invdate<='$dto' and emp_id='$_REQUEST[ledger_id]'");
														while($com=mysqli_fetch_row($com1))
														{
															$comm +=$com[19]*($com[14]+$com[9]-$com[10]-$com[11]-$com[12]+$com[13])/100;
														}														
														$comm=round($comm,2);
													?> </td>
													<td> <?php
														$abs=$no_days-$days;
														echo $abs;
														echo "<input type='hidden' name='absent' value='$abs'/>";
													?> </td>
												</tr>
											</table>
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-12 col-xs-12 table-responsive">
											<table class="table table-bordered">
												<tr>
													<th>Particulars</th>
													<th>Amount</th>
													<th>Remarks</th>
												</tr>
												<tr>
													<th>Salary by Attendance</th>
													<td> <?php
														$lt=$no_days-$days;
														if($lt>$emp[2])
														{
															$per=$emp[1]/30;
															$salary=round(($days+$emp[2])*$per,2);
														}
														else $salary=$emp[1];
														echo $salary;
														echo "<input type='hidden' name='erpsalary' value='$salary'/>";
													?></td>
													<td><textarea class="form-control" rows="3" name="remark"></textarea></td>
												</tr>
												<tr>
													<th>Commission</th>
													<td><input type="text" class="form-control" name="comm" onkeyup='calc();'value='<?php echo $comm; ?>'/></td>
													<td><textarea class="form-control" rows="3" name="cremark"></textarea></td>
												</tr>
												<tr>
													<th>Advance Balance</th>
													<td><input type="text" class="form-control" name="advance" onkeyup='calc();' value="<?php echo $emp[3]; ?>"/></td>
													<td>Amount : <?php echo $emp[3]; ?></td>
												</tr>
												<tr>
													<th>Deductions (if any)</th>
													<td><input type="text" class="form-control" name="deduction" onkeyup='calc();'/></td>
													<td><textarea class="form-control" rows="3" name="dremark"></textarea></td>
												</tr>
												<tr>
													<th>Total Salary</th>
													<td><input type="text" class="form-control" name="amount" value='<?php echo ($salary+$comm-$emp[3]); ?>' onkeyup='calc();'/></td>
													<td>Paid By <select class="form-control" name="paidby" required>
														<option value=''>--Select--</option>
														<?php
															$list1=mysqli_query($con,"select ledger_id,name from ledger_accounts  where status=1 and (name like 'Cash Account' or group_id in (select group_id from group_master where group_name like 'Bank Accounts')) order by name");
															if($l=mysqli_fetch_row($list1))
															{
																do{
																	echo "<option value='$l[0]'>$l[1]</option>";
																}while($l=mysqli_fetch_row($list1));
															}
														?>
													</select></td>
												</tr>
											</table>
										</div>
									</div>
                                </div>
                                <div class="panel-footer">
									<button class="btn btn-primary" type="submit" name="s1">Pay</button>
                                </div>
                            </div>
                            </form>
							<?php
								}
							?>
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