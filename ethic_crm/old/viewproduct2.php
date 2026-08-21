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

if (isset($_GET['variant_info_item_id'])) {
	$item_id = (int)$_GET['variant_info_item_id'];
	$item_q = mysqli_query($con, "SELECT * FROM item_details WHERE item_id='$item_id' LIMIT 1");
	$item = $item_q ? mysqli_fetch_row($item_q) : null;

	if (!$item) {
		echo "<div class='alert alert-warning'>Product details not found.</div>";
		exit;
	}

	$sub_cat = mysqli_fetch_row(mysqli_query($con, "SELECT * FROM pro_subcategory WHERE s_id='$item[10]' LIMIT 1"));
	$mat_type = "";
	$col_name = "";

	$mat_q = $con->query("SELECT type FROM material_type WHERE m_id = '$item[11]' LIMIT 1");
	if ($mat_q && $mat_q->num_rows > 0) {
		$mat_type = $mat_q->fetch_assoc()['type'];
	}

	$col_q = $con->query("SELECT name FROM collection WHERE c_id = '$item[12]' LIMIT 1");
	if ($col_q && $col_q->num_rows > 0) {
		$col_name = $col_q->fetch_assoc()['name'];
	}

	$variants = mysqli_query($con, "SELECT * FROM variant WHERE item_id='$item_id' ORDER BY v_id");
	?>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-actions" style="font-size:12px;">
			<thead>
				<tr>
					<th>S.No.</th>
					<th>Pic</th>
					<th>Size</th>
					<th>Color</th>
					<th>Stock</th>
					<th>Stock on Website</th>
					<th>Ethic Price</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ($variants && mysqli_num_rows($variants) > 0) {
					$j = 1;
					while ($v = mysqli_fetch_row($variants)) {
						$pic = "";
						$pic_q = mysqli_query($con, "SELECT pic FROM variant_pic WHERE v_id='$v[0]' ORDER BY rand() LIMIT 1");
						if ($pic_q && mysqli_num_rows($pic_q) > 0) {
							$pic_row = mysqli_fetch_assoc($pic_q);
							$pic = $pic_row['pic'] ?? "";
						}

						$purchase_value = $v[6] * $v[4];
						$tax_value = round($purchase_value * $item[7] / 100, 2);
						$total_purchase_value = $purchase_value + $tax_value;
						$total_purchase_rate = ($v[6] != 0) ? round($total_purchase_value / $v[6], 2) : 0;
						$selling_price = round($total_purchase_rate + ($total_purchase_rate * 60 / 100), 2);
						$status = ($item[8] == 1) ? "<span class='badge badge-success'>Active</span>" : "<span class='badge badge-danger'>Deactive</span>";
						?>
						<tr>
							<td align="center"><?php echo $j; ?></td>
							<td><?php if (!empty($pic)) { ?><img src="<?php echo htmlspecialchars($pic); ?>" height="60px" width="60px" /><?php } ?></td>
							
							<td align="right"><?php echo htmlspecialchars($v[2]); ?></td>
							<td align="right"><?php echo htmlspecialchars($v[3]); ?></td>
							<td align="right"><?php echo htmlspecialchars($v[6]); ?></td>
							<td align="right"><?php echo htmlspecialchars($v[7]); ?></td>
							<td align="right"><?php echo htmlspecialchars($v[5]); ?></td>
							<td><?php echo $status; ?></td>
						</tr>
						<?php
						$j++;
					}
				} else {
					echo "<tr><td colspan='23' align='center'>No variants found.</td></tr>";
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
	exit;
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
		$ssmenu2 = "24";
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
				<li class="active">Product List </li>
			</ul>
			<!-- END BREADCRUMB -->

			<!-- PAGE TITLE -->
			<div class="page-title">
				<h2> Product List </h2>
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
								<form class="form-horizontal" method="post" action="viewproduct2.php" name='frm2'
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
										WHERE v.v_id IN (
											SELECT MIN(v_id)
											FROM variant
											WHERE 1 $size $color
											GROUP BY item_id
										)
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
				
			</tr>
			";

											?>
											<form action='addpurchase.php' method='post' target='_blank'>
												<table class="table datatable table-bordered table-actions">
													<thead>
														<tr>
															<th width="25"><span><input type='checkbox' name='all'
																		onchange="selectall();" id='all' /></span></th>
															<th style='width:10px;'>S.<br>No.</th>
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
																<tr id="<?php echo $d[0]; ?>">
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
																		<button class="btn btn-warning btn-rounded btn-condensed btn-sm"
																			onClick="showVariantInfo('<?php echo $d[1]; ?>');"
																			type="button" title="Detail"><span
																				class="fa fa-info"></span></button>
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
																	?>
																</tr>
																<?php
																$j++;
																	} while ($d = mysqli_fetch_array($result));
																}
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

	<div class="modal fade" id="variantInfoModal" tabindex="-1" role="dialog" aria-labelledby="variantInfoModalLabel">
		<div class="modal-dialog modal-lg" role="document" style="width:95%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="variantInfoModalLabel">Variant Details</h4>
				</div>
				<div class="modal-body" id="variantInfoContent">
					<div class="text-center">Loading...</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

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

		function showVariantInfo(item_id) {
			$('#variantInfoContent').html('<div class="text-center">Loading...</div>');
			$('#variantInfoModal').modal('show');

			$.ajax({
				url: 'viewproduct2.php',
				type: 'GET',
				data: {
					variant_info_item_id: item_id
				},
				success: function (response) {
					$('#variantInfoContent').html(response);
				},
				error: function () {
					$('#variantInfoContent').html('<div class="alert alert-danger">Unable to load variant details.</div>');
				}
			});
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
		
	</script>

</body>

</html>
