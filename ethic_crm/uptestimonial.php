<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";

// Handle toggle status
if (isset($_GET['toggle_id'])) {
    $id = mysqli_real_escape_string($con, $_GET['toggle_id']);
    $result = mysqli_query($con, "SELECT status FROM testimonial WHERE t_id='$id'");
    if ($row = mysqli_fetch_assoc($result)) {
        $new_status = $row['status'] == 1 ? 0 : 1;
        mysqli_query($con, "UPDATE testimonial SET status='$new_status' WHERE t_id='$id'");
        $msg = "Status Updated Successfully";
    } else {
        $msg1 = "Error: Invalid ID";
    }
}

// Handle update
if (isset($_GET['update_id']) && isset($_GET['name']) && isset($_GET['msg']) && isset($_GET['rating'])) {
    $id = intval($_GET['update_id']);
    $name = mysqli_real_escape_string($con, $_GET['name']);
    $msg_content = mysqli_real_escape_string($con, $_GET['msg']);
    $rating = intval($_GET['rating']);

    if ($rating >= 1 && $rating <= 5) { // Assuming rating is between 1 and 5
        $sql = "UPDATE testimonial SET name='$name', msg='$msg_content', rating='$rating' WHERE t_id='$id'";
        if (mysqli_query($con, $sql)) {
            $msg = "Testimonial Updated Successfully";
        } else {
            $msg1 = "Error updating testimonial.";
        }
    } else {
        $msg1 = "Invalid rating value (must be between 1 and 5).";
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($con, $_GET['delete_id']);
    mysqli_query($con, "DELETE FROM testimonial WHERE t_id='$id'");
    $msg = "Testimonial Deleted Successfully";
}

// Handle add
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
    $msg_content = mysqli_real_escape_string($con, $_POST['msg'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);

    if (!empty($name) && !empty($msg_content) && $rating >= 1 && $rating <= 5) {
        mysqli_query($con, "INSERT INTO testimonial (name, msg, rating, status) VALUES ('$name', '$msg_content', '$rating', 1)");
        $msg = "Testimonial Added Successfully";
    } else {
        $msg1 = "Please provide valid name, message, and rating (1-5)";
    }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoYmjYV0EmsbR1LpD5SuUW9Ek2v3qE88yGkERyW3og1p4h+" crossorigin="anonymous">

    <script src="js/jquery.min.js"></script>
    <script type="text/javascript">
        function toggle_row(row, current_status) {
            var box = $("#mb-toggle-row");
            box.addClass("open");
            box.find(".mb-control-yes").off("click").on("click", function() {
                box.removeClass("open");
                toggle_row1(row, current_status);
            });
        }

        function toggle_row1(row, current_status) {
            var new_status = current_status == 1 ? 0 : 1;
            window.location.href = `uptestimonial.php?toggle_id=${row}&status=${new_status}`;
        }

        function delete_row(row) {
            var box = $("#mb-remove-row");
            box.addClass("open");
            box.find(".mb-control-yes").off("click").on("click", function() {
                box.removeClass("open");
                window.location.href = `uptestimonial.php?delete_id=${row}`;
            });
        }

        function updateValue(id) {
            const name = document.getElementById('name' + id)?.value;
            const msg = document.getElementById('msg' + id)?.value;
            const rating = document.getElementById('rating' + id)?.value;

            if (!name || !msg || !rating) {
                alert("Please fill all fields before updating.");
                return;
            }
            if (rating < 1 || rating > 5) {
                alert("Rating must be between 1 and 5.");
                return;
            }

            const encodedName = encodeURIComponent(name);
            const encodedMsg = encodeURIComponent(msg);
            window.location.href = `uptestimonial.php?update_id=${id}&name=${encodedName}&msg=${encodedMsg}&rating=${rating}`;
        }
    </script>
</head>

<body>
    <div class="page-container">
        <?php $menu13 = true;
        $smenu13 = "4";
        $ssmenu13 = "5";
        include_once("sidebar.php"); ?>
        <div class="page-content">
            <?php include_once("topheader.php"); ?>
            <ul class="breadcrumb">
                <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#">Master</a></li>
                <li class="active">Update Testimonials</li>
            </ul>
            <div class="page-content-wrap">
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($msg) { ?>
                            <div class="alert alert-success" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg; ?></strong>
                            </div>
                        <?php } ?>
                        <?php if ($msg1) { ?>
                            <div class="alert alert-danger" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg1; ?></strong>
                            </div>
                        <?php } ?>

                        <?php
                        $result = mysqli_query($con, "SELECT * FROM testimonial ORDER BY t_id ASC");
                        $data = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['t_id'];
                            $data[$id] = [
                                'name' => $row['name'] ?? '',
                                'msg' => $row['msg'] ?? '',
                                'rating' => $row['rating'] ?? 0,
                                'status' => $row['status'] ?? 0,
                            ];
                        }
                        ?>

                        <form class="form-horizontal" method="post" action="uptestimonial.php">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Update</strong> Testimonials</h3>
                                </div>
                                <div class="panel-body container">
                                    <?php foreach ($data as $id => $testimonial): ?>
                                        <div class="row" style="margin-bottom: 20px;">
                                            <div class="col-md-3">
                                                <input type="text" id="name<?php echo $id; ?>" class="form-control" name="name<?php echo $id; ?>" value="<?php echo htmlspecialchars($testimonial['name']); ?>" required />
                                            </div>
                                            <div class="col-md-4">
                                                <textarea id="msg<?php echo $id; ?>" class="form-control" name="msg<?php echo $id; ?>" required><?php echo htmlspecialchars($testimonial['msg']); ?></textarea>
                                            </div>
                                            <div class="col-md-2">
                                                <select id="rating<?= $id ?>" name="rating<?= $id ?>" class="form-control" required>
                                                    <option value="" disabled>Select Rating</option>
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <option value="<?= $i ?>" <?= ($testimonial['rating'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" onclick="updateValue('<?php echo $id; ?>')" class="btn btn-primary btn-block" style="margin-bottom: 10px;">
                                                    <i class="fa fa-save"></i>
                                                </button>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" onclick="delete_row('<?php echo $id; ?>')" class="btn btn-danger btn-block" style="margin-bottom: 10px;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" onclick="toggle_row('<?php echo $id; ?>', '<?php echo $testimonial['status']; ?>')" class="btn btn-<?php echo $testimonial['status'] == 1 ? 'danger' : 'success'; ?> btn-block" style="margin-bottom: 10px;">
                                                    <?php echo $testimonial['status'] == 1 ? 'Hide' : 'Show'; ?>
                                                </button>
                                            </div>
                                        </div>


                                    <?php endforeach; ?>
                                    <div class="row">
                                        <div class="col-md-3 d-flex align-items-start">
                                            <input type="text" class="form-control" name="name" placeholder="Name" required />
                                        </div>
                                        <div class="col-md-4 d-flex align-items-start">
                                            <textarea class="form-control" name="msg" placeholder="Message" required></textarea>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-start">
                                            <select name="rating" class="form-control" required>
                                                <option value="" disabled selected>Select Rating</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>

                                        </div>
                                        <div class="col-md-3 d-flex align-items-start">
                                            <button type="submit" name="submit" class="btn btn-primary btn-block">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Confirmation Box -->
    <div class="message-box animated fadeIn" data-sound="alert" id="mb-toggle-row">
        <div class="mb-container">
            <div class="mb-middle">
                <div class="mb-title"><span class="fa fa-toggle-on"></span> Toggle <strong>Status</strong> ?</div>
                <div class="mb-content">
                    <p>Are you sure you want to toggle the status of this testimonial?</p>
                    <p>Press Yes to proceed.</p>
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

    <!-- Delete Confirmation Box -->
    <div class="message-box animated fadeIn" data-sound="alert" id="mb-remove-row">
        <div class="mb-container">
            <div class="mb-middle">
                <div class="mb-title"><span class="fa fa-times"></span> Remove <strong>Testimonial</strong> ?</div>
                <div class="mb-content">
                    <p>Are you sure you want to remove this testimonial?</p>
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

    <?php include_once("footer.php"); ?>
    <audio id="audio-alert" src="audio/alert.mp3" preload="auto"></audio>
    <audio id="audio-fail" src="audio/fail.mp3" preload="auto"></audio>
    <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>
    <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
    <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
    <script type="text/javascript" src="js/plugins.js"></script>
    <script type="text/javascript" src="js/actions.js"></script>
</body>

</html>