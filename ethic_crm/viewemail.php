<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = $msg2 = "";
if (isset($_REQUEST['msg'])) {
	$msg = "Email edited successfully!!!";
}

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['send_message'])) {
	$emails = $_POST['email'] ?? [];
	$message = trim($_POST['universal_message'] ?? '');

	if (empty($emails)) {
		$msg1 = "No emails selected!";
	}
	$image_url = '';
	if (empty($msg1) && isset($_FILES['email_image']) && $_FILES['email_image']['error'] == 0) {
		$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
		if (in_array($_FILES['email_image']['type'], $allowed_types)) {
			$upload_dir = '../assets/images/newsletter/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}
			$filename = time() . '_' . basename($_FILES['email_image']['name']);
			$target_file = $upload_dir . $filename;
			
			if (move_uploaded_file($_FILES['email_image']['tmp_name'], $target_file)) {
				$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
				$domain = $_SERVER['HTTP_HOST'];
				$base_path = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
				if ($base_path == '/') $base_path = '';
				
				// Construct the full path and ensure any spaces are URL encoded
				$full_path = $protocol . "://" . $domain . $base_path . "/assets/images/newsletter/" . $filename;
				$image_url = str_replace(' ', '%20', $full_path);
			} else {
				$msg1 = "Failed to upload image.";
			}
		} else {
			$msg1 = "Invalid image format. Allowed formats: JPG, PNG, GIF, WEBP.";
		}
	}

	if (empty($msg1)) {
		$errors = [];

		foreach ($emails as $email) {
			$mail = new PHPMailer(true);

			try {
				$mail->isSMTP();
				$mail->Host       = 'smtp.gmail.com';
				$mail->SMTPAuth   = true;
				$mail->Username   = 'Ethicdesignstudiotech@gmail.com';
				$mail->Password   = 'btrh kksb rbcw hdkl';
				$mail->SMTPSecure = 'tls';
				$mail->Port       = 587;

				$mail->setFrom('Ethicdesignstudiotech@gmail.com', 'Ethic Studio');
				$mail->addAddress($email);

				$mail->isHTML(true);
				$mail->Subject = 'Message from Ethic Studio';
				
				$htmlBody = nl2br(htmlspecialchars($message));
				if ($image_url != '') {
					$htmlBody .= '<br><br><img src="' . $image_url . '" style="max-width: 100%; height: auto;" alt="Newsletter Image" />';
				}
				$mail->Body = $htmlBody;

				// $mail->send();
			} catch (Exception $e) {
				$errors[] = "Failed to send to $email. Error: " . $mail->ErrorInfo;
			}
		}

		if (empty($errors)) {
			$msg = "Messages sent successfully to all selected emails.";
		} else {
			$msg1 = implode('<br>', $errors);
		}
	}
}

if (isset($_REQUEST['n_id'])) {
	mysqli_query($con, "delete from newsletter where n_id='$_REQUEST[n_id]'");
	$msg1 = "Email Deleted successfully!!!";
}
if (isset($_REQUEST['msg1'])) {
	$msg2 = "Message Send successfully!!!";
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
	<script type="text/javascript" language="javascript">
		function delete_row(row) {
			var box = $("#mb-remove-row");
			box.addClass("open");
			box.find(".mb-control-yes").on("click", function() {
				box.removeClass("open");
				delete_row1(row);
				$("#" + row).hide("slow", function() {
					$(this).remove();
				});
			});
		}

		function delete_row1(row) {
			var path = "viewemail.php?n_id=" + row;
			window.open(path, "_self");
		}

		function selectall() {
			var sall = document.getElementById("all");
			var scholar = document.getElementsByName("email[]");
			for (var i = 0; i < scholar.length; i++) {
				scholar[i].checked = sall.checked;
			}
		}
	</script>
	<script src="js\jquery.min.js"></script>
</head>

<body onload="gencontact();">
	<!-- START PAGE CONTAINER -->
	<div class="page-container">
		<!-- START PAGE SIDEBAR -->
		<?php $menu13 = true;
		$smenu13 = "4";
		$ssmenu13 = "7";
		include_once("sidebar.php"); ?>

		<!-- PAGE CONTENT -->
		<div class="page-content">

			<!-- START X-NAVIGATION VERTICAL -->
			<?php include_once("topheader.php"); ?>
			<!-- END X-NAVIGATION VERTICAL -->

			<!-- START BREADCRUMB -->
			<ul class="breadcrumb">
				<li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="#">Email Master</a></li>
				<li class="active">View Email Details</li>
			</ul>
			<!-- END BREADCRUMB -->

			<!-- PAGE TITLE -->
			<div class="page-title">
				<h2> View Email Details</h2>
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
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg; ?></strong>
							</div>
						<?php
						}
						?>
						<?php
						if ($msg1) {
						?>
							<div class="alert alert-danger" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg1; ?></strong>
							</div>
						<?php
						}
						?>
						<?php
						if ($msg2) {
						?>
							<div class="alert alert-warning" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg2; ?></strong>
							</div>
						<?php
						}
						?>

						<!-- START DATATABLE EXPORT -->
						<div class="panel panel-default">

							<div class="panel-body">


								<div class="table-responsive" id="display">
									<?php

									$sql = "SELECT * FROM newsletter";

									$result = mysqli_query($con, $sql);

									$table = "";
									if (mysqli_num_rows($result) == 0) {
										echo "There is no Email Available!!!";
									} else {
										$table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
												<caption><h1>Email Details</h1></caption>
												<tr>
													<th width='58'><span>S. No.</span></th>
													<th width='189'><span>Email</span></th>	
												</tr>";
									?>
										<form action="viewemail.php" method="post" enctype="multipart/form-data">
											<table class="table datatable table-bordered table-striped table-actions">
												<thead>
													<tr>
														<th><input type="checkbox" id="all" onchange="selectall()" /></th>
														<th>S. No.</th>
														<th>Email</th>
														<th>Actions</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$j = 1;
													while ($row = mysqli_fetch_assoc($result)) {
														echo "<tr id='{$row['n_id']}'>";
														echo "<td><input type='checkbox' name='email[]' value='{$row['email']}'></td>";
														echo "<td>$j</td>";
														echo "<td>" . htmlspecialchars($row['email']) . "</td>";
														echo "<td><button type='button' class='btn btn-danger btn-rounded btn-condensed btn-sm' onClick=\"delete_row('{$row['n_id']}');\"><span class='fa fa-times' title='Delete'></span></button></td>";
														echo "</tr>";
														$j++;
													}
													?>
												</tbody>
											</table>

											<div>
												<textarea name="universal_message" style="margin-top: 20px;" class="form-control" placeholder="Enter message here..."></textarea>
												<div style="margin-top: 10px;">
													<label>Upload Image (optional):</label>
													<input type="file" name="email_image" class="form-control" accept="image/*" />
												</div>
												<button class="btn btn-warning" style="margin-top: 10px;" type="submit" name="send_message">Send Message</button>
											</div>
										</form>

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
												<input type="hidden" name="fn" value="Contact Details" />
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