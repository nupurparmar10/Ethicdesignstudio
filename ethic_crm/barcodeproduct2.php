<?php
ob_start();
session_start();
include_once("connect.php");

if (!isset($_POST['v_id']) || !is_array($_POST['v_id']) || count($_POST['v_id']) == 0) {
	header("Location: viewproduct1.php");
	die;
}

$v_ids = array_values(array_unique(array_map('intval', $_POST['v_id'])));
$id_list = implode(',', $v_ids);
$variants = mysqli_query($con, "SELECT * FROM variant WHERE v_id IN ($id_list) ORDER BY FIELD(v_id, $id_list)");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Ethic Design Studio</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="icon" href="logo3.png" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css" />
</head>
<body>
	<div class="page-container">
		<?php $menu2 = true; $smenu2 = "2"; $ssmenu2 = "23"; include_once("sidebar.php"); ?>
		<div class="page-content">
			<?php include_once("topheader.php"); ?>
			<ul class="breadcrumb">
				<li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="#">Masters</a></li>
				<li><a href="#">Product Master</a></li>
				<li class="active">Print Barcode</li>
			</ul>
			<div class="page-title">
				<h2>Print Barcode</h2>
			</div>
			<div class="page-content-wrap">
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Selected Variants</h3>
							</div>
							<form action="printproduct2.php" method="post" target="_blank">
								<div class="panel-body">
									<button type="submit" class="btn btn-info" style="margin-bottom:15px;">Print Barcode</button>
									<table class="table table-bordered table-actions">
										<thead>
											<tr>
												<th style="width:90px;">Product Image</th>
												<th>Size</th>
												<th>Color</th>
												<th style="width:180px;">Stock in Store</th>
											</tr>
										</thead>
										<tbody>
											<?php while ($d = mysqli_fetch_row($variants)) {
												$pic = '';
												$pic1 = mysqli_query($con, "SELECT pic FROM variant_pic WHERE v_id='$d[0]' ORDER BY rand() LIMIT 1");
												if ($pic1 && mysqli_num_rows($pic1) > 0) {
													$row = mysqli_fetch_assoc($pic1);
													$pic = $row['pic'] ?? '';
												}
											?>
											<tr>
												<td>
													<input type="hidden" name="v_id[]" value="<?php echo htmlspecialchars($d[0]); ?>">
													<?php if (!empty($pic)) { ?>
														<img src="<?php echo htmlspecialchars($pic); ?>" height="80" width="80" />
													<?php } ?>
												</td>
												<td><?php echo htmlspecialchars($d[2]); ?></td>
												<td><?php echo htmlspecialchars($d[3]); ?></td>
												<td>
													<input type="number" class="form-control" name="stock[<?php echo htmlspecialchars($d[0]); ?>]" value="<?php echo htmlspecialchars($d[6]); ?>" min="0" step="1">
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php include_once("footer.php"); ?>
	<script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/plugins.js"></script>
	<script type="text/javascript" src="js/actions.js"></script>
</body>
</html>
