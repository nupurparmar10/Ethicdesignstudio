<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = $msg2 = "";


if (isset($_GET['val'])) {
    $v_id = (int)$_GET['val'];

    $variant_sql = "SELECT item_id FROM variant WHERE v_id = $v_id";
    $variant_res = $con->query($variant_sql);
    if ($variant_res->num_rows > 0) {
        $row = $variant_res->fetch_assoc();
        $item_id = $row['item_id'];

        $pic_sql = "SELECT pic FROM variant_pic WHERE v_id = $v_id";
        $pic_res = $con->query($pic_sql);
        if ($pic_res->num_rows > 0) {
            while ($pic_row = $pic_res->fetch_assoc()) {
                $pic_path = $pic_row['pic'];
                if (file_exists($pic_path)) {
                    unlink($pic_path);
                }
            }
        }

        $con->query("DELETE FROM variant_pic WHERE v_id = $v_id");
        $con->query("DELETE FROM variant WHERE v_id = $v_id");
        $check_sql = "SELECT COUNT(*) AS cnt FROM variant WHERE item_id = $item_id";
        $check_res = $con->query($check_sql);
        $check_row = $check_res->fetch_assoc();
        if ((int)$check_row['cnt'] === 0) {
            $con->query("DELETE FROM item_details WHERE item_id = $item_id");
        }

        $msg =  "Variant deleted successfully!";
    } 
} 

if (isset($_REQUEST['msg'])) {
	$msg = "Product Details Modified Successfully!!!";
}
if (isset($_REQUEST['msg1'])) {
	$msg = "Product Details Added Successfully!!!";
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
	<link rel="stylesheet" href="css/lightbox.css">
	<!-- CSS INCLUDE -->
	<link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css" />
	<!-- EOF CSS INCLUDE -->

	<script>
		function selectall() {
			var sall = document.getElementById("all");
			if (sall.checked == 1) {
				var scholar = document.getElementsByName("v_id[]");
				for (i = 0; i < scholar.length; i++) {
					scholar[i].checked = 1;
				}
			} else {
				var scholar = document.getElementsByName("v_id[]");
				for (i = 0; i < scholar.length; i++) {
					scholar[i].checked = 0;
				}
			}
		}
	</script>
	<script src="js\jquery.min.js"></script>
	<script src="js/jquery-1.11.0.min.js"></script>
	<script src="js/lightbox.js"></script>
</head>

<body>
	<!-- START PAGE CONTAINER -->
	<div class="page-container">

		<!-- START PAGE SIDEBAR -->
		<?php $menu2 = true;
		$smenu2 = "2";
		$ssmenu2 = "23";
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
				<li><a href="#">Masters</a></li>
				<li><a href="#">Product Master</a></li>
				<li class="active">Inventory </li>
			</ul>
			<!-- END BREADCRUMB -->

			<!-- PAGE TITLE -->
			<div class="page-title">
				<h2> Inventory </h2>
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
								<button type="button" class="close" data-dismiss="alert"><span
										aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg; ?></strong>
							</div>
							<?php
						}
						?>
						<?php
						if ($msg1) {
							?>
							<div class="alert alert-danger" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span
										aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg1; ?></strong>
							</div>
							<?php
						}
						?>
						<?php
						if ($msg2) {
							?>
							<div class="alert alert-warning" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span
										aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg2; ?></strong>
							</div>
							<?php
						}
						?>

						<!-- START DATATABLE EXPORT -->
						<div class="panel panel-default">

							<div class="panel-body">
								<form class="form-horizontal" method="post" action="viewproduct1.php" name='frm2'
									enctype="multipart/form-data">
									<div class="form-group">
										<div class="row">
											<div class="col-md-3 col-xs-12" style="margin-top: 20px;">
												<input type="text" class="form-control" name="pcode" id="pcode"
													placeholder="Product Code" />
											</div>
											<div class="col-md-3 col-xs-12" style="margin-top: 20px;">
												<input type="text" class="form-control" name="desp" id="desp"
													placeholder="Description" />
											</div>
											<div class="col-md-3 col-xs-12" style="margin-top: 20px;">
												<select class="form-control" name="ptype"
													onchange='getsubcategory(this.value);'>
													<option value=''>--Select Product Type--</option>
													<?php
													$f1 = mysqli_query($con, "select * from producttype order by ptname");
													while ($f = mysqli_fetch_row($f1)) {
														echo "<option value='$f[1]'>$f[1]</option>";
													}
													?>
												</select>
											</div>
											<div class="col-md-3 col-xs-12" style="margin-top: 20px;" id="subcategory">
												<select class="form-control" name="s_id" id="s_id">
													<option value=''>--Select Sub-Category--</option>
												</select>
											</div>

											<div class="col-md-3 col-xs-12" style="margin-top: 20px;">
												<select class="form-control" name="size">
													<option value=''>--Select Size--</option>
													<?php
													$f1 = mysqli_query($con, "select distinct(size) from variant where size!='' order by size");
													while ($f = mysqli_fetch_row($f1)) {
														echo "<option value='$f[0]'>$f[0]</option>";
													}
													?>
												</select>
											</div>
											<div class="col-md-3 col-xs-12" style="margin-top: 20px;">
												<select class="form-control" name="material_type">
													<option value=''>--Select Material Type--</option>
													<?php
													$f1 = mysqli_query($con, "SELECT m_id, type FROM material_type WHERE type != '' ORDER BY type");
													while ($f = mysqli_fetch_row($f1)) {
														echo "<option value='$f[0]' style='color: black;'>$f[1]</option>";
													}

													?>
												</select>
											</div>
											<div class="col-md-3 col-xs-12" style="margin-top: 20px;">
												<select class="form-control" name="collection">
													<option value=''>--Select Collection--</option>
													<?php
													$f1 = mysqli_query($con, "SELECT c_id, name FROM collection WHERE name != '' ORDER BY name");
													while ($f = mysqli_fetch_row($f1)) {
														echo "<option value='$f[0]' style='color: black;'>$f[1]</option>";
													}

													?>
												</select>
											</div>

											<div class="col-md-3 col-xs-12" style="margin-top: 20px;">
												<input type="text" class="form-control" name="color" id="color"
													placeholder="Color" />
											</div>
										</div>
										<div class="row">
											<div class="col-md-3 col-xs-2" style="margin-top: 20px;">
												<button class="btn btn-primary" type="submit" name="open">Open</button>
											</div>
										</div>
									</div>
								</form>
								<br>
								<div class="table-responsive" id="display">
									<?php
									if (isset($_REQUEST['open'])) {
										if ($_REQUEST['ptype'] != "")
											$ptype = " and ptype='$_REQUEST[ptype]'";
										else
											$ptype = "";
										if ($_REQUEST['s_id'] != "")
											$s_id = " and s_id='$_REQUEST[s_id]'";
										else
											$s_id = "";
										if ($_REQUEST['size'] != "")
											$size = " and size='$_REQUEST[size]'";
										else
											$size = "";
										if ($_REQUEST['color'] != "")
											$color = " and color='$_REQUEST[color]'";
										else
											$color = "";

										$material_type = isset($_REQUEST['material_type']) ? $_REQUEST['material_type'] : '';
										$collection = isset($_REQUEST['collection']) ? $_REQUEST['collection'] : '';
										$pcode = isset($_REQUEST['pcode']) ? $_REQUEST['pcode'] : '';
										$desp = isset($_REQUEST['desp']) ? $_REQUEST['desp'] : '';

										$sql = "SELECT v.*
                                            FROM variant v
                                            INNER JOIN item_details i ON v.item_id = i.item_id
                                            WHERE 1
                                            $size
                                            $color
                                            AND i.pcode LIKE '%$pcode%'
                                            AND i.material_type LIKE '%$material_type%'
                                            AND i.collection LIKE '%$collection%'
                                            AND (i.purdesp LIKE '%$desp%' OR i.saledesp LIKE '%$desp%')
                                            AND i.status = 1
                                            $ptype
                                            $s_id
                                             ORDER BY CAST(SUBSTRING(i.pcode, 3) AS UNSIGNED) ASC, v.v_id ASC";
										$result = mysqli_query($con, $sql);

										$table = "";
										if (mysqli_num_rows($result) == 0) {
											echo "There is no Product Available!!!";
										} else {
											$table .= "<table align='center' border='1' cellpadding='3' width='100%' style='border-collapse:collapse; font-size:12px;'>
												<tr>
													<th style='width:20px;'>S.<br>No.</th>
													<th>Code</th>
													<th>Type</th>
													<th>Sub-Category</th>
													<th>Description</th>
													<th>HSN</th>
													<th>Unit</th>
													<th>Size</th>
													<th>Color</th>
													<th>Stock in Store</th>
													<th>Stock in Website</th>
													<th>Purchase Rate</th>
													<th>Taxable Value</th>
													<th>Tax</th>
													<th>Total Purchase Value</th>
													<th>Total Purchase Rate</th>
													<th>Selling Price</th>
													<th>Ethic Selling Price</th>
													<th>Status</th>
												</tr>
												";

											?>
											<form action='addpurchase.php' method='post' target='_blank'>
												<span style='float:right; '><button class="btn btn-info" type="button" onclick="getCheckedVIds()" >Generate Barcode</button> </span>

												<span style='float:right;padding-right: 10px;'><button class="btn btn-warning" type="submit"
														name="s10">Generate Purchase Invoice</button> </span>
												<br><br>
												<span style='color:red;'>**Double click on row to upload/edit photos</span>
												<table id="viewproduct-display-table" class="table datatable table-bordered table-actions">
													<thead>
														<tr>
															<th width="58"><span><input type='checkbox' name='all'
																		onchange="selectall();" id='all' /></span></th>
															<th style='width:20px;'>S.<br>No.</th>
															<th>Action</th>
															<th>Pic</th>
															<th>Material</th>
															<th>Collection</th>
															<th>Code</th>
															<th>Type</th>
															<th>Sub-Category</th>
															<th>Description</th>
															<th>HSN</th>
															<th>Unit</th>
															<th>Size</th>
															<th>Color</th>
															<th>Stock in<br>Store</th>
															<th>Stock in<br>Website</th>
															<th>Purchase<br>Rate</th>
															<th>Taxable<br>Value</th>
															<th>Tax</th>
															<th>Total <br>Pur. Value</th>
															<th>Total <br>Pur. Rate</th>
															<th>Selling<br>Price</th>
															<th>Ethic<br>Price</th>
															<th>Status</th>

														</tr>
													</thead>
													<tbody>
														<?php
														if ($d = mysqli_fetch_row($result)) {
															$j = 1;
															$tot = array('0', '0', '0', '0', '0');
															do {
																$table .= "<tr>";
																?>
																<tr id="<?php echo $d[0]; ?>"
																	ondblclick="uploadpic('<?php echo $d[0]; ?>');">
																	<?php
																	$pic1 = mysqli_query($con, "select pic from variant_pic where v_id='$d[0]' order by rand() limit 1");

																	$pic = '';

																	if ($pic1 && mysqli_num_rows($pic1) > 0) {
																		$row = mysqli_fetch_assoc($pic1);
																		$pic = $row['pic'] ?? '';
																	}
																	echo "<td><input type='checkbox' name='v_id[]' value='$d[0]'/></td>";
																	echo "<td align='center'>$j</td>";
																	$item = mysqli_fetch_row(mysqli_query($con, "select * from item_details where item_id='$d[1]'"));
																	$sub_cat = mysqli_fetch_row(mysqli_query($con, "select * from pro_subcategory where s_id='$item[10]'"));
																	$sub_cat = $sub_cat ?? 'N/A';
																	if ($item[8] == 1)
																		$status = "<span class='badge badge-success'>Active</span>";
																	else
																		$status = "<span class='badge badge-danger'>Deactive</span>"; ?>
																	<td>
																		<button class="btn btn-info btn-rounded btn-condensed btn-sm"
																			onClick="window.open('editproduct.php?item_id=<?php echo $d[1]; ?>','_blank');"
																			type="button" title="Edit"><span
																				class="fa fa-pencil"></span></button>
																		<button class="btn btn-success btn-rounded btn-condensed btn-sm"
																			onClick="window.open('printproduct1.php?item_id=<?php echo $d[1]; ?>','_blank');"
																			type="button"><span class="fa fa-print"
																				title="Print Barcode"></span></button>

																		<button class="btn btn-danger btn-rounded btn-condensed btn-sm"
																			type="button"
																			onclick="if(confirm('Are you sure you want to delete this variant?')) window.location.href='viewproduct1.php?val=<?php echo $d[0]; ?>';">
																			<span class="fa fa-trash" title="Delete variant"></span>
																		</button>

																		<?php
																		if ($item[8] == 1) {
																			?>
																			<button class="btn btn-warning btn-rounded btn-condensed btn-sm"
																				onClick="changestatus('<?php echo $d[1]; ?>','0');"
																				type="button"><span class="fa fa-toggle-on"
																					title="Website Status ON"></span></button>
																			<?php
																		} else {
																			?>
																			<button class="btn btn-default btn-rounded btn-condensed btn-sm"
																				onClick="changestatus('<?php echo $d[1]; ?>','1');"
																				type="button"><span class="fa fa-toggle-off"
																					title="Website Status OFF"></span></button>
																			<?php
																		}
																		?>
																	</td>
																	<?php echo "<td>";
																	if (!empty($pic)) {
																		echo "<img src='$pic' height='80px' width='80px'/>";
																	}
																	echo "</td>";


																	?>
																	<?php
																	// Material type
																	$mat_id = $item[11];
																	$mat_type = '';
																	$mat_q = $con->query("SELECT type FROM material_type WHERE m_id = '$mat_id' LIMIT 1");
																	if ($mat_q && $mat_q->num_rows > 0) {
																		$mat_type = $mat_q->fetch_assoc()['type'];
																	}

																	// Collection name
																	$col_id = $item[12];
																	$col_name = '';
																	$col_q = $con->query("SELECT name FROM collection WHERE c_id = '$col_id' LIMIT 1");
																	if ($col_q && $col_q->num_rows > 0) {
																		$col_name = $col_q->fetch_assoc()['name'];
																	}

																	?>

																	<td><?= htmlspecialchars($mat_type) ?></td>
																	<td><?= htmlspecialchars($col_name) ?></td>



																	<td><?php echo $item[1]; ?></td>
																	<td><?php echo htmlspecialchars($item[2]); ?></td>
																	<td><?php echo $sub_cat[2]; ?></td>
																	<td><?php echo htmlspecialchars("$item[5]"); ?></td>
																	<td><?php echo $item[4]; ?></td>
																	<td><?php echo $item[6]; ?></td>
																	<?php
																	$table .= "<td>$j</td>
																				<td>$item[1]</td>
																				<td>$item[2]</td>
																				<td>$sub_cat[2]</td>
																				<td>" . htmlspecialchars("$item[5]") . "</td>
																				<td>$item[4]</td>
																				<td>$item[6]</td>";

																	echo "<td align='right'>$d[2]</td>
																			<td align='right'>$d[3]</td>
																			<td align='right'>$d[6]</td>
																			<td align='right'>$d[7]</td>
																			<td align='right'>$d[4]</td>
																			
																			";

																	$table .= "<td align='right'>$d[2]</td>
																				<td align='right'>$d[3]</td>
																				<td align='right'>$d[6]</td>
																				<td align='right'>$d[7]</td>
																				<td align='right'>$d[4]</td>
																				";

																	$tot[0] += $d[6];
																	$tot[1] += $d[7];

																	$amt = $d[6] * $d[4];
																	echo "<td align='right'>$amt</td>";
																	$table .= "<td align='right'>$amt</td>";
																	$tot[2] += $amt;
																	$tax = round($amt * $item[7] / 100, 2);
																	$tot[3] += $tax;
																	echo "<td align='right'>" . ($tax) . "</td>";
																	$table .= "<td align='right'>" . ($tax) . "</td>";

																	$amt = $amt + $tax;
																	$tot[4] += $amt;
																	echo "<td align='right'>$amt</td>";
																	$table .= "<td align='right'>$amt</td>";
																	if ($d[6] != 0)
																		$rate = round($amt / $d[6], 2);
																	else
																		$rate = 0;
																	echo "<td align='right'>$rate</td>";
																	$table .= "<td align='right'>$rate</td>";
																	$sell = round($rate + ($rate * 60 / 100), 2);
																	echo "<td align='right'>$sell</td>";
																	$table .= "<td align='right'>$sell</td>";
																	echo "<td align='right'>$d[5]</td>";
																	$table .= "<td align='right'>$d[5]</td>"; ?>

																	<?php
																	echo "<td align='right'>$status";

																	// Fetch labels only once
																	$label_query = "SELECT l_id, name FROM label";
																	$label_result = mysqli_query($con, $label_query);

																	$current_label = $d[8];
																	$labels = [];

																	if ($label_result && mysqli_num_rows($label_result) > 0) {
																		while ($row = mysqli_fetch_assoc($label_result)) {
																			$labels[] = $row;
																		}
																	}
																	?>

																	<?php if (!empty($labels)): ?>
																		<select class="label-select"
																			data-vid="<?= htmlspecialchars($d[0]) ?>">
																			<?php foreach ($labels as $label): ?>
																				<option value="<?= htmlspecialchars($label['l_id']) ?>"
																					<?= ($label['l_id'] == $current_label) ? 'selected' : '' ?>>
																					<?= htmlspecialchars($label['name']) ?>
																				</option>
																			<?php endforeach; ?>
																		</select>
																	<?php endif; ?>

																	<?php
																	echo "</td>";
																	$table .= "<td align='right'>$status</td>";
																	?>


																</tr>
																<?php
																$j++;
															} while ($d = mysqli_fetch_array($result));
														}
														$table .= "<tr>
																<td colspan='14'>Total</td>
																<td align='right'>" . round($tot[0], 2) . "</td>
																<td align='right'>" . round($tot[1], 2) . "</td>
																<td></td>
																<td align='right'>" . round($tot[2], 2) . "</td>
																<td align='right'>" . round($tot[3], 2) . "</td>
																<td align='right'>" . round($tot[4], 2) . "</td>
																<td colspan='3'></td>
															</tr></table>";

														echo "</tbody>
																<tr style='font-weight:bold;'>
																<td colspan='14'>Total</td>
																<td align='right'>" . round($tot[0], 2) . "</td>
																<td align='right'>" . round($tot[1], 2) . "</td>
																<td></td>
																<td align='right'>" . round($tot[2], 2) . "</td>
																<td align='right'>" . round($tot[3], 2) . "</td>
																<td align='right'>" . round($tot[4], 2) . "</td>
																<td colspan='3'></td>
																</tr>";
														?>

												</table>
											</form>
											<br></br>
											<div class="col-md-1 col-xs-1">
												<form action="printlist.php" method="post" target="_blank">
													<input type="hidden" value="<?php echo $table; ?>" name="query" />
													<button class="btn btn-primary" type="submit" name="s11">Print</button>
												</form>
											</div>
											<div class="col-md-3 col-xs-3">
												<form action="excel.php" method="post">
													<input type="hidden" name="query" value="<?php echo $table; ?>" />
													<input type="hidden" name="fn" value="Product Details " />
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

	<script type="text/javascript">
		$(function () {
			$("#viewproduct-display-table").dataTable({
				"pageLength": 10,
				"lengthMenu": [[10, 30, 65, 130], [10, 30, 65, 130]]
			});
		});
	</script>

	<script type="text/javascript" src="js/plugins.js"></script>
	<script type="text/javascript" src="js/actions.js"></script>
	<!-- END TEMPLATE -->
	<!-- END SCRIPTS -->
	<script src="js/model.js"></script>
	<!-- END TEMPLATE -->
	<!-- END SCRIPTS -->
	<style>
		.dialogify.fixed {
			width: 80%;
		}

		.dialogify .dialogify__fixedwidth {
			max-width: 100%;
		}
	</style>

	<script type="text/javascript" language="javascript">
		function uploadpic(v_id) {
			var options = {
				ajaxPrefix: ''
			};
			new Dialogify('addpic.php?v_id=' + v_id, options)
				.title('Upload/Edit Photos')
				.buttons([{
					text: 'Cancel',
					click: function (e) {
						this.close();
					}
				},
				{
					text: 'Add',
					type: Dialogify.BUTTON_PRIMARY,
					click: function (e) {
						var form_data = new FormData();
						var fileList = $('#pic').prop("files");

						// Convert the FileList to an array
						var val = val1 = "";
						for (i = 0; i < fileList.length; i++) {
							form_data.append('pic[]', fileList[i]);
						}
						var remove = document.getElementsByName("remove[]");
						for (i = 0; i < remove.length; i++) {
							if (remove[i].checked == true)
								val1 += remove[i].value + ";";
						}
						form_data.append('remove', val1);
						form_data.append('v_id', $('#v_id').val());
						$.ajax({
							method: "POST",
							url: 'insert_data4.php',
							data: form_data,
							dataType: 'json',
							contentType: false,
							cache: false,
							processData: false,
							success: function (data) {
								console.log(data.tmp);

								if (data.error != '') {
									console.log(data.error);
								} else {
									location.reload(false);
								}
							},
							error: function () {
								console.log("error");
							}
						});
					}
				}
				]).showModal();
		}

		function getsubcategory(val) {
			$.ajax({
				url: 'insert_data.php',
				type: 'POST',
				data: {
					subcategory: val
				},
				success: ajaxSuccess1,
				error: ajaxError
			});
		}

		function ajaxSuccess1(response) {
			$('#subcategory').html(response);
		}

		function changestatus(item_id, status) {
			$.ajax({
				url: 'changestatus.php',
				type: 'POST',
				data: {
					item_id: item_id,
					status: status
				},
				success: ajaxSuccess2,
				error: ajaxError
			});
		}

		function ajaxSuccess2(response) {
			location.reload(false);
		}

		function ajaxError() {
			alert("error");
		}
	</script>
	<script>
		$(document).on('change', '.label-select', function () {
			var v_id = $(this).data('vid');
			var label_id = $(this).val();

			$.ajax({
				url: 'label_ajax.php',
				type: 'POST',
				data: {
					v_id: v_id,
					label_id: label_id
				},
				success: function (response) {

				},
				error: function (xhr, status, error) {
					console.log('Something went wrong: ' + error);

				}
			});
		});
		let selectedVIds = [];
		$(document).on('change', 'input[name="v_id[]"]', function () 
		{
			let value = $(this).val();
			if ($(this).is(':checked')) 
			{
				if (!selectedVIds.includes(value)) {
					selectedVIds.push(value);
				}

			} else {
				selectedVIds = selectedVIds.filter(item => item !== value);
			}

		});
		function getCheckedVIds() 
		{
			// Recheck current page selected items also
			$('input[name="v_id[]"]:checked').each(function () 
			{
				let value = $(this).val();
				if (!selectedVIds.includes(value)) {
					selectedVIds.push(value);
				}
			});

			// Open in new tab and pass data
			let form = document.createElement('form');

			form.method = 'POST';
			form.action = 'barcodeproduct2.php';
			form.target = '_blank';

			// Pass all selected v_id[]
			selectedVIds.forEach(function(id) {

				let input = document.createElement('input');

				input.type = 'hidden';
				input.name = 'v_id[]';
				input.value = id;

				form.appendChild(input);

			});

			document.body.appendChild(form);

			form.submit();

			document.body.removeChild(form);
			console.log(selectedVIds);
		}
	</script>

</body>

</html>
