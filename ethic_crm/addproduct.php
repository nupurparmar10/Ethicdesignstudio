<?php
ob_start();
session_start();
include_once("connect.php");

$msg = $msg1 = "";

// Handle Delete Variant
if (isset($_POST['delete_variant']) && isset($_POST['itemid']) && isset($_POST['v_id'])) {
	$itemid = $_POST['itemid'];
	$v_id = $_POST['v_id'];

	$itemid = filter_var($itemid, FILTER_SANITIZE_STRING);
	$v_id = filter_var($v_id, FILTER_SANITIZE_STRING);

	if (isset($_SESSION['temp_products'][$itemid])) {
		$found = false;
		foreach ($_SESSION['temp_products'][$itemid]['variants'] as $key => $variant) {
			if ($variant['v_id'] === $v_id) {
				unset($_SESSION['temp_products'][$itemid]['variants'][$key]);
				$_SESSION['temp_products'][$itemid]['variants'] = array_values($_SESSION['temp_products'][$itemid]['variants']);
				$found = true;

				// Remove product if no variants left
				if (empty($_SESSION['temp_products'][$itemid]['variants'])) {
					unset($_SESSION['temp_products'][$itemid]);
					unset($_SESSION['tax_value'][$itemid]);
					unset($_SESSION['new_materials'][$itemid]);
					unset($_SESSION['new_collections'][$itemid]);
					unset($_SESSION['new_subcategories'][$itemid]);
				}

				header("Location: addproduct.php?msg=Variant deleted");
				exit;
			}
		}
		if (!$found) {
			$msg1 = "Variant not found";
		}
	} else {
		$msg1 = "Item not found";
	}
	header("Location: addproduct.php?msg1=" . urlencode($msg1));
	exit;
}

// Handle Add Product
if (isset($_POST['add_product'])) {
	$error = '';
	$msg1 = '';

	if (
		empty($_POST['ptype']) ||
		empty($_POST['tax']) ||
		empty($_POST['saledesp']) ||

		(empty($_POST['s_id']) && empty($_POST['new_sub']))
	) {
		$msg1 = 'Required fields are missing or sub-category not selected';
		goto end;
	}

	$x = explode(":", $_POST['tax']);
	$itemid = time() . '_' . rand(1000, 9999);
	$pcode = "ED" . substr($itemid, -6);

	if ($_POST['ptype'] == "Fabric") {
		$unit = "MTR";
	} else {
		$unit = "PCS";
	}

	$stmt = mysqli_prepare($con, "SELECT hsn FROM producttype WHERE ptname = ?");
	mysqli_stmt_bind_param($stmt, "s", $_POST['ptype']);
	mysqli_stmt_execute($stmt);
	$hsn1 = mysqli_stmt_get_result($stmt);
	$hsn1 = mysqli_fetch_row($hsn1);
	$hsn = $hsn1[0] ?? '';
	mysqli_stmt_close($stmt);

	$edsellrate = intval($_POST['edsellrate']);
	$saledesp = mysqli_real_escape_string($con, $_POST['saledesp']);
	$product_desp=mysqli_real_escape_string($con, $_POST['product_desp']);

	// Handle new material type
	$material_id = $_POST['material_id'];
	$new_material = trim($_POST['new_material_type']);
	if ($new_material !== '') {
		$safe_new_material = mysqli_real_escape_string($con, $new_material);
		$check_material = mysqli_query($con, "SELECT m_id FROM material_type WHERE LOWER(type) = LOWER('$safe_new_material') AND status = 1 LIMIT 1");
		if (mysqli_num_rows($check_material) > 0) {
			$msg1 = 'This Material Type already exists';
			goto end;
		} else {
			$_SESSION['new_materials'][$itemid] = $safe_new_material;
			$material_id = 'temp_' . $itemid;
		}
	}

	// Handle new collection
	$collection_id = $_POST['collection_id'];
	$new_collection = trim($_POST['new_collection_name']);
	if ($new_collection !== '') {
		$safe_new_collection = mysqli_real_escape_string($con, $new_collection);
		$check_collection = mysqli_query($con, "SELECT c_id FROM collection WHERE LOWER(name) = LOWER('$safe_new_collection') AND status = 1 LIMIT 1");
		if (mysqli_num_rows($check_collection) > 0) {
			$msg1 = 'This Collection already exists';
			goto end;
		} else {
			$_SESSION['new_collections'][$itemid] = $safe_new_collection;
			$collection_id = 'temp_' . $itemid;
		}
	}

	// Handle new subcategory
	if ($_POST['new_sub'] != '') {
		$sname = mysqli_real_escape_string($con, $_POST['new_sub']);
		$stmt = mysqli_prepare($con, "SELECT pt_id FROM producttype WHERE ptname = ?");
		mysqli_stmt_bind_param($stmt, "s", $_POST['ptype']);
		mysqli_stmt_execute($stmt);
		$prod_type = mysqli_stmt_get_result($stmt);
		$prod_type = mysqli_fetch_row($prod_type);
		$pt_id = $prod_type[0] ?? 0;
		mysqli_stmt_close($stmt);

		$check_sub = mysqli_query($con, "SELECT s_id FROM pro_subcategory WHERE LOWER(sname) = LOWER('$sname') AND pt_id='$pt_id' LIMIT 1");
		if (mysqli_num_rows($check_sub) > 0) {
			$msg1 = 'This Subcategory already exists';
			goto end;
		}
		$_SESSION['new_subcategories'][$itemid] = ['sname' => $sname, 'pt_id' => $pt_id];
		$s_id = 'temp_' . $itemid;
	} else {
		$s_id = $_POST['s_id'];
	}

	// Prepare product data for session
	$product = [
		'temp_id' => $itemid,
		'pcode' => $pcode,
		'ptype' => $_POST['ptype'],
		'product_desp' => $product_desp,
		'hsn' => $hsn,
		'saledesp' => $saledesp,
		'unit' => $unit,
		'tax' => $x[0],
		'tax_percent' => $x[1],
		's_id' => $s_id,
		'material_id' => $material_id,
		'collection_id' => $collection_id,
		'edsellrate' => $edsellrate,
		'variants' => []
		
	];

	// Handle variants with limit
	$variant_count = 0;
	$max_variants = 100; // Prevent memory explosion
	if ($_POST['ptype'] == "Garments") 
	{
		$color = array_slice($_POST['color'] ?? [], 0, 50);
		$standard_color = $_POST['standard_color'] ?? [];
		$size1qty = $_POST['size1qty'] ?? [];
		$size2qty = $_POST['size2qty'] ?? [];
		$size3qty = $_POST['size3qty'] ?? [];
		$size4qty = $_POST['size4qty'] ?? [];
		$size5qty = $_POST['size5qty'] ?? [];
		$size6qty = $_POST['size6qty'] ?? [];
		$size7qty = $_POST['size7qty'] ?? [];
		$size8qty = $_POST['size8qty'] ?? [];
		$size9qty = $_POST['size9qty'] ?? [];
		$size10qty = $_POST['size10qty'] ?? [];
		$sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL'];
		$quantities = [$size1qty, $size2qty, $size3qty, $size4qty, $size5qty, $size6qty, $size7qty, $size8qty, $size9qty, $size10qty];
		for ($i = 0; $i < count($color) && $variant_count < $max_variants; $i++) 
		{
			$c = mysqli_real_escape_string($con, $color[$i]);
			$sc = mysqli_real_escape_string($con, $standard_color[$i]);
			if ($c != '') {
				for ($j = 0; $j < count($sizes); $j++) {
					if (!empty($quantities[$j][$i]) && (int) $quantities[$j][$i] > 0) {
						$v_id = 'temp_' . $itemid . '_' . $j . '_' . $i;
						$product['variants'][] = [
							'v_id' => $v_id,
							'color' => $c,
							'standard_color' => $sc,
							'size' => $sizes[$j],
							'stock' => (int) $quantities[$j][$i],
							'purrate' => 0,
							'edsellrate' => $edsellrate,
							'webstock' => 0
						];
						$variant_count++;
					}
				}
			}
		}
	} else {
		$color = array_slice($_POST['color'] ?? [], 0, 50);
		$standard_color = $_POST['standard_color'] ?? [];
		$nqty = array_slice($_POST['nqty'] ?? [], 0, 50);
		for ($i = 0; $i < count($color) && $variant_count < $max_variants; $i++) {
			$c = mysqli_real_escape_string($con, $color[$i]);
			$sc = mysqli_real_escape_string($con, $standard_color[$i]);
			if ($c != '' && !empty($nqty[$i]) && (int) $nqty[$i] > 0) {
				$v_id = 'temp_' . $itemid . '_' . $i;
				$product['variants'][] = [
					'v_id' => $v_id,
					'color' => $c,
					'standard_color' => $sc,
					'size' => '',
					'stock' => (int) $nqty[$i],
					'purrate' => 0,
					'edsellrate' => $edsellrate,
					'webstock' => 0
				];
				$variant_count++;
			}
		}
	}

	if (!empty($product['variants'])) {
		if (!isset($_SESSION['temp_products'])) {
			$_SESSION['temp_products'] = [];
		}
		// Limit total products
		if (count($_SESSION['temp_products']) < 50) 
		{
			$_SESSION['temp_products'][$itemid] = $product;
			$_SESSION['tax_value'][$itemid] = $_POST['tax'];
			// Log session size for debugging
			file_put_contents('session.log', "Memory used after adding product: " . memory_get_usage() . " bytes\n", FILE_APPEND);
			// Redirect to avoid memory issues
			header("Location: addproduct.php?msg=Product added to session");
			exit;
		} else {
			$msg1 = 'Maximum product limit reached. Please save or clear existing products.';
		}
	} else {
		$msg1 = 'No valid variants provided';
	}
	end:
}

// Handle Save
if (isset($_REQUEST['s1'])) 
{
	foreach ($_REQUEST as $key => $value) {
		if (is_string($value)) {
			$_REQUEST[$key] = mysqli_real_escape_string($con, $value);
		}
	}
	$web = intval($_REQUEST['website'] ?? 0);

	// Insert new material types
	if (isset($_SESSION['new_materials']) && !empty($_SESSION['new_materials'])) {
		$stmt = mysqli_prepare($con, "INSERT INTO material_type (type, status) VALUES (?, 1)");
		foreach ($_SESSION['new_materials'] as $itemid => $material) {
			mysqli_stmt_bind_param($stmt, 's', $material);
			mysqli_stmt_execute($stmt);
			$_SESSION['new_materials_id'][$itemid] = mysqli_insert_id($con);
		}
		mysqli_stmt_close($stmt);
	}

	// Insert new collections
	if (isset($_SESSION['new_collections']) && !empty($_SESSION['new_collections'])) {
		$stmt = mysqli_prepare($con, "INSERT INTO collection (name, status) VALUES (?, 1)");
		foreach ($_SESSION['new_collections'] as $itemid => $collection) {
			mysqli_stmt_bind_param($stmt, 's', $collection);
			mysqli_stmt_execute($stmt);
			$_SESSION['new_collections_id'][$itemid] = mysqli_insert_id($con);
		}
		mysqli_stmt_close($stmt);
	}

	// Insert new subcategories
	if (isset($_SESSION['new_subcategories']) && !empty($_SESSION['new_subcategories'])) {
		$stmt = mysqli_prepare($con, "INSERT INTO pro_subcategory (s_id, pt_id, sname) VALUES (?, ?, ?)");
		foreach ($_SESSION['new_subcategories'] as $itemid => $subcat) {

			$chk1 = mysqli_query($con, "SELECT MAX(s_id) FROM pro_subcategory");
			$chk = mysqli_fetch_row($chk1);
			$s_id = ($chk[0] ?? 0) + 1;
			mysqli_stmt_bind_param($stmt, 'iis', $s_id, $subcat['pt_id'], $subcat['sname']);
			mysqli_stmt_execute($stmt);

			mysqli_query($con, "UNLOCK TABLES");

			$_SESSION['new_subcategories_id'][$itemid] = $s_id;
		}
		mysqli_stmt_close($stmt);
	}

	if (isset($_SESSION['temp_products']) && !empty($_SESSION['temp_products']))
	{
		$stmt_product = mysqli_prepare($con, "INSERT INTO item_details (item_id, pcode, ptype, hsn, saledesp, unit, tax, website, status, s_id, material_type, collection,product_desp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, '1', ?, ?, ?,?)");
		$stmt_variant = mysqli_prepare($con, "INSERT INTO variant (item_id, size, color, stock, webstock, purrate, edsellrate,standard_color) VALUES (?, ?, ?, ?, ?, ?, ?,?)");
		$stmt_tax = mysqli_prepare($con, "UPDATE item_details SET tax = ? WHERE item_id = ?");

		foreach ($_SESSION['temp_products'] as $itemid => $product) {
			$pid1 = mysqli_query($con, "SELECT MAX(item_id) FROM item_details");
			$pid = mysqli_fetch_row($pid1);
			$actual_item_id = ($pid[0] ?? 0) + 1;
			$pcode = "ED";
			if ($actual_item_id < 10) {
				$pcode .= "0" . $actual_item_id;
			} else {
				$pcode .= $actual_item_id;
			}

			// Resolve temporary IDs
			$material_id = $product['material_id'];
			if (strpos($material_id, 'temp_') === 0) {
				$material_id = $_SESSION['new_materials_id'][$itemid] ?? $material_id;
			}
			$collection_id = $product['collection_id'];
			if (strpos($collection_id, 'temp_') === 0) {
				$collection_id = $_SESSION['new_collections_id'][$itemid] ?? $collection_id;
			}
			$s_id = $product['s_id'];
			if (strpos($s_id, 'temp_') === 0) {
				$s_id = $_SESSION['new_subcategories_id'][$itemid] ?? $s_id;
			}

			// Insert product
			mysqli_stmt_bind_param(
				$stmt_product,
				'isssssisisss',
				$actual_item_id,
				$pcode,
				$product['ptype'],

				$product['hsn'],
				$product['saledesp'],
				$product['unit'],
				$product['tax'],
				$web,
				$s_id,
				$material_id,
				$collection_id,
				$product['product_desp']
			);
			mysqli_stmt_execute($stmt_product);

			// Insert variants
			$purerate = 0;
			$variant_index = 0;
			foreach ($product['variants'] as $variant) {
				$stock = floatval($_REQUEST['qty'][$itemid][$variant_index] ?? $variant['stock']);
				$webstock = floatval($_REQUEST['webqty'][$itemid][$variant_index] ?? $variant['webstock']);
				$edsellrate = floatval($_REQUEST['edsellrate'][$itemid][$variant_index] ?? $variant['edsellrate']);
				$tax = floatval($_REQUEST['taxper'][$itemid][$variant_index] ?? $product['tax_percent']);
				if ($stock > 0) {
					mysqli_stmt_bind_param(
						$stmt_variant,
						'isssiids',
						$actual_item_id,
						$variant['size'],
						$variant['color'],
						$stock,
						$webstock,
						$purerate,
						$edsellrate,
						$variant['standard_color'],
					);
					mysqli_stmt_execute($stmt_variant);
					$v_id = mysqli_insert_id($con);
					$barcode = encryptId($v_id);
					mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$v_id'");

					mysqli_stmt_bind_param($stmt_tax, 'di', $tax, $actual_item_id);
					mysqli_stmt_execute($stmt_tax);
				}
				$variant_index++;
			}
		}
		mysqli_stmt_close($stmt_product);
		mysqli_stmt_close($stmt_variant);
		mysqli_stmt_close($stmt_tax);
	}

	// Clear session
	unset($_SESSION['temp_products']);
	unset($_SESSION['tax_value']);
	unset($_SESSION['new_materials']);
	unset($_SESSION['new_materials_id']);
	unset($_SESSION['new_collections']);
	unset($_SESSION['new_collections_id']);
	unset($_SESSION['new_subcategories']);
	unset($_SESSION['new_subcategories_id']);

	header("Location: viewproduct1.php?msg1=added");
	die;
}
$color_options='<option value="">--Select--</option>';
$c1=mysqli_query($con,"select color_name from color_code order by color_name");
while($c=mysqli_fetch_assoc($c1))
{
    $color_options.='<option value="'.$c['color_name'].'">'.$c['color_name'].'</option>';
}
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
	<script src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>
	<script>
		function calc() {
			var itemIds = <?php echo json_encode(array_keys($_SESSION['temp_products'] ?? [])); ?>;
			var qtytot = 0;
			var webqtytot = 0;
			var edsellratetot = 0;

			for (var i = 0; i < itemIds.length; i++) {
				var qty = document.getElementsByName("qty[" + itemIds[i] + "][]");
				var webqty = document.getElementsByName("webqty[" + itemIds[i] + "][]");
				var edsellrate = document.getElementsByName("edsellrate[" + itemIds[i] + "][]");

				for (var j = 0; j < qty.length; j++) {
					var q = parseFloat(qty[j].value) || 0;
					var w = parseFloat(webqty[j].value) || 0;
					var e = parseFloat(edsellrate[j].value) || 0;

					qtytot += q;
					webqtytot += w;
					edsellratetot += e;
				}
			}

			document.getElementById("qtytot").value = qtytot.toFixed(2);
			document.getElementById("webqtytot").value = webqtytot.toFixed(2);
			document.getElementById("edsellratetot").value = edsellratetot.toFixed(2);
		}

		function delete_variant(itemid, v_id) {
			if (confirm('Are you sure you want to delete this variant?')) {
				try {
					// Disable the delete link to prevent multiple clicks
					var deleteLink = document.querySelector(`a[onclick="delete_variant('${itemid}', '${v_id}')"]`);
					if (deleteLink) {
						deleteLink.style.pointerEvents = 'none';
						deleteLink.style.opacity = '0.5';
					}

					// Create a hidden form
					var form = document.createElement('form');
					form.method = 'POST';
					form.action = 'addproduct.php';

					// Add hidden inputs
					var inputDelete = document.createElement('input');
					inputDelete.type = 'hidden';
					inputDelete.name = 'delete_variant';
					inputDelete.value = '1';
					form.appendChild(inputDelete);

					var inputItemId = document.createElement('input');
					inputItemId.type = 'hidden';
					inputItemId.name = 'itemid';
					inputItemId.value = itemid;
					form.appendChild(inputItemId);

					var inputVId = document.createElement('input');
					inputVId.type = 'hidden';
					inputVId.name = 'v_id';
					inputVId.value = v_id;
					form.appendChild(inputVId);

					// Append form to document and submit
					document.body.appendChild(form);
					form.submit();
				} catch (e) {
					alert('Error submitting delete request: ' + e.message);
					if (deleteLink) {
						deleteLink.style.pointerEvents = 'auto';
						deleteLink.style.opacity = '1';
					}
				}
			}
		}



		function more5() {
			var $table = $('#input_fields5');
			if ($table.find('tr').length >= 50) {
				alert('Maximum 50 variants allowed.');
				return;
			}
			var $tr = $table.find('tr').eq(1).clone();
			$tr.appendTo($table).find('input').val('');
			$tr.appendTo($table).find('select').val('');
			$("#input_fields5").append($tr);
			$tr.find('input').eq(0).focus();

		}

		function calc2() {
			var tot = 0;
			var ptype = document.getElementById("ptype").value;
			if (ptype == 'Garments') {
				var sizes = ['size1qty[]', 'size2qty[]', 'size3qty[]', 'size4qty[]', 'size5qty[]',
					'size6qty[]', 'size7qty[]', 'size8qty[]', 'size9qty[]', 'size10qty[]'
				];
				for (var i = 0; i < sizes.length; i++) {
					var qty = document.getElementsByName(sizes[i]);
					for (var j = 0; j < qty.length; j++) {
						tot += (parseFloat(qty[j].value) || 0);
					}
				}
			} else {
				var nqty = document.getElementsByName("nqty[]");
				for (var i = 0; i < nqty.length; i++) {
					tot += (parseFloat(nqty[i].value) || 0);
				}
			}
			document.getElementById("nqtytot").value = tot.toFixed(2);
		}

		function gettable(ptype) 
		{
			var colorOptions = `<?php echo $color_options; ?>`;
			var str = '';
			if (ptype == 'Garments') {
				str = `<thead>
					<tr>
						<th>Color</th>
						<th>Standard Color</th>
						<th>XS</th><th>S</th><th>M</th><th>L</th><th>XL</th>
						<th>2XL</th><th>3XL</th><th>4XL</th><th>5XL</th><th>6XL</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><div class="form-group"><input type="text" class="form-control" name="color[]" /></div></td>
						<td><div class="form-group"><select class="form-control" name="standard_color[]" required>
                                ${colorOptions}
                            </select></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size1qty[]" onkeyup="calc2();" /></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size2qty[]" onkeyup="calc2();" /></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size3qty[]" onkeyup="calc2();" /></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size4qty[]" onkeyup="calc2();" /></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size5qty[]" onkeyup="calc2();" /></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size6qty[]" onkeyup="calc2();" /></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size7qty[]" onkeyup="calc2();" /></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size8qty[]" onkeyup="calc2();" /></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size9qty[]" onkeyup="calc2();" /></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="size10qty[]" onkeyup="calc2();" /></div></td>
					</tr>
				</thead>
				<tbody>`;
			} else {
				str = `<thead>
					<tr>
						<th>Color *</th>
						<th>Standard Color *</th>
						<th>Qty *</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><div class="form-group"><input type="text" class="form-control" name="color[]" /></div></td>
						<td><div class="form-group"><select class="form-control" name="standard_color[]" required>
                                ${colorOptions}
                            </select></div></td>
						<td><div class="form-group"><input type="text" class="form-control" name="nqty[]" onkeyup="calc2();" required /></div></td>
					</tr>
				</tbody>`;
			}
			$("#input_fields5").html(str);
		}

		function getsubcategory(val) {
			$.ajax({
				url: 'insert_data.php',
				type: 'POST',
				data: {
					subcategory: val
				},
				success: function (response) {
					$('#subcategory').html(response);
				},
				error: function () {
					alert("Error fetching subcategories");
				}
			});
		}
	</script>
</head>

<body>
	<div class="page-container">
		<?php
		$menu2 = true;
		$smenu2 = "2";
		$ssmenu2 = "21";
		include_once("sidebar.php");
		?>
		<div class="page-content">
			<?php include_once("topheader.php"); ?>
			<ul class="breadcrumb">
				<li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="#">Masters</a></li>
				<li><a href="#">Product Master</a></li>
				<li class="active">Add Product</li>
			</ul>
			<div class="page-title">
				<h2><span class="fa fa-list"></span> Add Product</h2>
				<span id="form_response"></span>
			</div>
			<div class="page-content-wrap">
				<div class="content-frame-body">
					<div class="row">
						<div class="col-md-12">
							<?php
							if ($msg) {
								echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>' . htmlspecialchars($msg) . '</strong></div>';
							}
							if ($msg1) {
								echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>' . htmlspecialchars($msg1) . '</strong></div>';
							}
							?>
							<form class="form-horizontal" method="post" action="addproduct.php"
								enctype="multipart/form-data" onsubmit="return confirm('Sure?');" name="frm2">
								<div class="panel panel-default">
									<div class="panel-body">
										<div class="row">
											<div class="col-md-12">
												<div class="table-responsive">
													<table class="table table-bordered table-striped table-actions">
														<tbody>
															<tr>
																<td colspan="2">
																	<div class="form-group">
																		<button type="button"
																			class="btn btn-success btn-xl"
																			data-toggle="modal"
																			data-target="#addProductModal"
																			tabindex="1">Add Product</button>
																	</div>
																</td>
																<th width="15%">Show On Website</th>
																<td>
																	<div class="form-group">
																		<select name="website"
																			class="form-control select">
																			<option value="0">Hide</option>
																			<option value="1">Show</option>
																		</select>
																	</div>
																</td>
															</tr>
															<tr>
																<td colspan="4">
																	<div class="table-responsive">
																		<table
																			class="table table-bordered table-striped table-actions"
																			id="input_fields">
																			<thead>
																				<tr>
																					<th>Product Code</th>
																					<th>Description</th>
																					<th>Color</th>
																					<th>Standard Color</th>
																					<th>Size</th>
																					<th>Store Stock</th>
																					<th>Website Stock</th>
																					<th>Tax %</th>
																					<th>Ethic Selling Price</th>
																					<th>Action</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php
																				if (isset($_SESSION['temp_products']) && !empty($_SESSION['temp_products'])) {
																					$row_counter = 0;
																					foreach ($_SESSION['temp_products'] as $itemid => $product) {
																						foreach ($product['variants'] as $variant) {
																							$variant_value = $variant['v_id'];
																							?>
																							<tr id="<?php echo $row_counter; ?>">
																								<td><?php echo htmlspecialchars($product['pcode']); ?>
																								</td>
																								<td><?php echo htmlspecialchars($product['saledesp']); ?>
																								</td>
																								<td><?php echo htmlspecialchars($variant['color']); ?>
																								</td>
																								<td><?php echo htmlspecialchars($variant['standard_color']); ?>
																								</td>
																								<td><?php echo htmlspecialchars($variant['size'] ?: '-'); ?>
																								</td>
																								<td>
																									<div class="form-group">
																										<input type="text"
																											class="form-control"
																											name="qty[<?php echo $itemid; ?>][]"
																											onkeyup="calc();"
																											value="<?php echo $variant['stock']; ?>" />
																									</div>
																								</td>
																								<td>
																									<div class="form-group">
																										<input type="text"
																											class="form-control"
																											name="webqty[<?php echo $itemid; ?>][]"
																											onkeyup="calc();"
																											value="<?php echo $variant['webstock']; ?>" />
																									</div>
																								</td>
																								<td>
																									<div class="form-group">
																										<input type="text"
																											class="form-control"
																											name="taxper[<?php echo $itemid; ?>][]"
																											value="<?php echo $product['tax_percent']; ?>" />
																									</div>
																								</td>
																								<td>
																									<div class="form-group">
																										<input type="text"
																											class="form-control"
																											name="edsellrate[<?php echo $itemid; ?>][]"
																											onkeyup="calc();"
																											value="<?php echo $variant['edsellrate']; ?>" />
																									</div>
																								</td>
																								<td>
																									<input type="hidden"
																										name="v_id[<?php echo $itemid; ?>][]"
																										value="<?php echo $variant['v_id']; ?>" />
																									<a
																										onclick="delete_variant('<?php echo $itemid; ?>', '<?php echo $variant['v_id']; ?>');"><i
																											class="fa fa-times"></i></a>
																								</td>
																							</tr>
																							<?php
																							$row_counter++;
																						}
																					}
																				} else {
																					?>
																					<tr id="0">
																						<td colspan="10">No products added
																							yet.</td>
																					</tr>
																					<?php
																				}
																				?>
																				<tr>
																					<td align="right">Total</td>
																					<td colspan="3"></td>
																					<td>
																						<div class="form-group">
																							<input type="text"
																								class="form-control"
																								name="qtytot"
																								id="qtytot" readonly />
																						</div>
																					</td>
																					<td colspan="2"></td>
																					<td class="hidden">
																						<div class="form-group">
																							<input type="text"
																								class="form-control"
																								name="webqtytot"
																								id="webqtytot"
																								readonly />
																						</div>
																					</td>
																					<td>
																						<div class="form-group">
																							<input type="text"
																								class="form-control"
																								name="edsellratetot"
																								id="edsellratetot"
																								readonly />
																						</div>
																					</td>
																					<td colspan="2"></td>
																				</tr>
																			</tbody>
																		</table>
																	</div>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<button class="btn btn-primary" type="submit" name="s1"
											tabindex="1">Save</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Bootstrap Modal for Adding New Product -->
	<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="addProductModalLabel">Add Product</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="addProductForm" method="post" action="addproduct.php" enctype="multipart/form-data">
						<div class="table-responsive">
							<table class="table table-bordered table-striped table-actions">
								<tbody>
									<tr>
										<th width="15%">Product Type *</th>
										<td>
											<div class="form-group">
												<select class="form-control" name="ptype" id="ptype"
													onchange="gettable(this.value); getsubcategory(this.value);"
													required>
													<option value="">--Select--</option>
													<?php
													$f1 = mysqli_query($con, "SELECT * FROM producttype");
													while ($f = mysqli_fetch_row($f1)) {
														echo "<option value='$f[1]'>$f[1]</option>";
													}
													?>
												</select>
											</div>
										</td>
										<th width="10%">Tax *</th>
										<td>
											<div class="form-group">
												<select class="form-control" name="tax" id="tax" required>
													<option value="">--Select--</option>
													<?php
													$f1 = mysqli_query($con, "SELECT * FROM taxes WHERE status='1'");
													while ($f = mysqli_fetch_row($f1)) {
														$selected = ($f[2] == '5') ? 'selected' : '';
														echo "<option value='$f[0]:$f[2]' $selected>$f[1]</option>";
													}
													?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<th width="15%">Sub-category *</th>
										<td id="subcategory">
											<div class="form-group">
												<select class="form-control" name="s_id" id="s_id" required>
													<option value="">--Select--</option>
												</select>
											</div>
										</td>
										<th width="10%">Or Add New Sub-category</th>
										<td>
											<div class="form-group">
												<input type="text" class="form-control" name="new_sub" id="new_sub" />
											</div>
										</td>
									</tr>
									<tr>
										<th width="10%">Product Name *</th>
										<td>
											<div class="form-group">
												<input type="text" class="form-control" name="saledesp" id="saledesp"
													required />
											</div>
										</td>
										<th width="10%">Selling Rate *</th>
										<td>
											<div class="form-group">
												<input type="number" step="0.01" class="form-control" name="edsellrate"
													id="edsellrate" required />
											</div>
										</td>
									</tr>
									<tr>
										<th width="15%">Material type</th>
										<td>
											<div class="form-group">
												<select class="form-control" name="material_id" id="material_id">
													<option value="">--Select--</option>
													<?php
													$material = "SELECT m_id, type FROM material_type WHERE status = 1 ORDER BY type ASC";
													$material_res = mysqli_query($con, $material);
													if ($material_res && mysqli_num_rows($material_res) > 0) {
														while ($row = mysqli_fetch_assoc($material_res)) {
															echo "<option value='{$row['m_id']}'>" . htmlspecialchars($row['type']) . "</option>";
														}
													}
													?>
												</select>
											</div>
										</td>
										<th width="10%">Or Add New Material Type</th>
										<td>
											<div class="form-group">
												<input type="text" class="form-control" name="new_material_type"
													id="new_material_type" />
											</div>
										</td>
									</tr>
									<tr>
										<th width="15%">Collection type</th>
										<td>
											<div class="form-group">
												<select class="form-control" name="collection_id" id="collection_id">
													<option value="">--Select--</option>
													<?php
													$collection = "SELECT c_id, name FROM collection WHERE status = 1 ORDER BY name ASC";
													$collection_res = mysqli_query($con, $collection);
													if ($collection_res && mysqli_num_rows($collection_res) > 0) {
														while ($row = mysqli_fetch_assoc($collection_res)) {
															echo "<option value='{$row['c_id']}'>" . htmlspecialchars($row['name']) . "</option>";
														}
													}
													?>
												</select>
											</div>
										</td>
										<th width="10%">Or Add New Collection</th>
										<td>
											<div class="form-group">
												<input type="text" class="form-control" name="new_collection_name"
													id="new_collection_name" />
											</div>
										</td>
									</tr>
									<tr>
										<th width="10%">Product Description</th>
										<td colspan='3'>
											<div class="form-group">
												<textarea name="product_desp" class="form-control" id="product_desp"></textarea>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="4" style="max-width:300px; overflow-x:auto;">
											<div class="table-responsive">
												<table class="table table-bordered table-striped table-actions"
													id="input_fields5">
													<!-- Dynamic table content generated by gettable() -->
												</table>
											</div>
											<button class="btn btn-primary" type="button" onclick="more5();">Add
												More</button>
										</td>
									</tr>
									<tr>
										<th width="10%">Total Qty</th>
										<td>
											<div class="form-group">
												<input type="text" class="form-control" name="nqtytot" id="nqtytot"
													readonly />
											</div>
										</td>
										<td colspan="2"></td>
									</tr>
								</tbody>
							</table>
						</div>
						<input type="hidden" name="add_product" value="1" />
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary" form="addProductForm">Add</button>
				</div>
			</div>
		</div>
	</div>

	<?php include_once("footer.php"); ?>

	<script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/plugins/icheck/icheck.min.js"></script>
	<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
	<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>
	<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
	<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
	<script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
	<script type="text/javascript" src="js/plugins.js"></script>
	<script type="text/javascript" src="js/actions.js"></script>
	<script src="js/model.js"></script>
	<audio id="audio-alert" src="audio/alert.mp3" preload="auto"></audio>
	<audio id="audio-fail" src="audio/fail.mp3" preload="auto"></audio>


	<?php
	if (isset($_GET['msg']) || (isset($_SESSION['temp_products']) && !empty($_SESSION['temp_products']))) {
		?>
		<script>
			$(document).ready(function () {
				calc();
				$('#addProductModal').modal('hide');
			});
		</script>
		<?php
	}
	?>

</body>

</html>