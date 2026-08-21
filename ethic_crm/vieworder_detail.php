<?php
ob_start();
session_start();
include_once("connect.php");
$msg = "";
if (isset($_REQUEST['msg'])) {
	$msg = "Order item retrieved successfully!!!";
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
	<link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css" />
	<!-- EOF CSS INCLUDE -->
</head>

<body>
	<!-- START PAGE CONTAINER -->
	<div class="page-container">
		<!-- START PAGE SIDEBAR -->
		<?php $menu7 = true;
		$smenu7 = "";
		include_once("sidebar.php"); ?>
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
				<li class="active">View Order Items</li>
			</ul>
			<!-- END BREADCRUMB -->

			<!-- PAGE TITLE -->
			<div class="page-title">
				<h2>View Order Items</h2>
			</div>
			<!-- END PAGE TITLE -->

			<!-- PAGE CONTENT WRAPPER -->
			<div class="page-content-wrap">
				<div class="row">
					<div class="col-md-12">
						<?php
						if ($msg) {
						?>
							<div class="alert alert-info" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg; ?></strong>
							</div>
						<?php
						}
						?>

						<!-- START DATATABLE EXPORT -->
						<div class="panel panel-default">
							<div class="panel-body">
								<br>
								<div class="table-responsive" id="display">
									<?php
									if (!isset($_GET['check_id'])) {
										echo "No check ID provided!";
										exit;
									}
									$check_id = mysqli_real_escape_string($con, $_GET['check_id']);
									$sql = "SELECT * FROM order_item WHERE check_id = '$check_id'";

									$result = mysqli_query($con, $sql);

									$table = "";
									if (mysqli_num_rows($result) == 0) {
										echo "No Order Items Available for this Check ID!";
									} else {
										$table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
                                                    <caption><h1>Order Items</h1></caption>
                                                    <tr>
                                                        <th width='58'><span>S. No.</span></th>
                                                        
                                                        <th width='150'><span>Color</span></th>
                                                        <th width='150'><span>Size</span></th>
                                                        <th width='150'><span>Quantity</span></th>
                                                        <th width='150'><span>Base Price</span></th>
                                                    </tr>";
									?>
										<table class="table datatable table-bordered table-striped">
											<thead>
												<tr>
													<th width='58'><span>S. No.</span></th>
													
													<th width='150'><span>Color</span></th>
													<th width='150'><span>Size</span></th>
													<th width='150'><span>Quantity</span></th>
													<th width='150'><span>Base Price</span></th>
												</tr>
											</thead>
											<tbody>
												<?php
												$j = 1;
												while ($row = mysqli_fetch_array($result)) {
												?>
													<tr id="<?php echo $row['item_id']; ?>">
														<?php
														echo "<td>$j</td>";
														
														echo "<td>" . htmlspecialchars($row['color']) . "</td>";
														echo "<td>" . htmlspecialchars($row['size']) . "</td>";
														echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
														echo "<td>" . htmlspecialchars($row['base_price']) . "</td>";
														$table .= "<tr>
                                                                    <td style='padding-left:10px;'>$j</td>
                                                                    <td style='padding-left:10px;'>" . htmlspecialchars($row['item_id']) . "</td>
                                                                    <td style='padding-left:10px;'>" . htmlspecialchars($row['check_id']) . "</td>
                                                                    <td style='padding-left:10px;'>" . htmlspecialchars($row['v_id']) . "</td>
                                                                    <td style='padding-left:10px;'>" . htmlspecialchars($row['color']) . "</td>
                                                                    <td style='padding-left:10px;'>" . htmlspecialchars($row['size']) . "</td>
                                                                    <td style='padding-left:10px;'>" . htmlspecialchars($row['quantity']) . "</td>
                                                                    <td style='padding-left:10px;'>" . htmlspecialchars($row['base_price']) . "</td>
                                                                </tr>";
														?>
													</tr>
												<?php
													$j++;
												}
												$table .= "</table>";
												?>
											</tbody>
										</table>
										<br><br>
										<div class="col-md-1 col-xs-1">
											<form action="printlist.php" method="post" target="_blank">
												<input type="hidden" value="<?php echo $table; ?>" name="query" />
												<button class="btn btn-primary" type="submit" name="s11">Print</button>
											</form>
										</div>
										<div class="col-md-3 col-xs-3">
											<form action="excel.php" method="post">
												<input type="hidden" name="query" value="<?php echo $table; ?>" />
												<input type="hidden" name="fn" value="OrderItems" />
												<button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
											</form>
										</div>
									<?php
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