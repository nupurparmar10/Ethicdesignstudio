<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
if (isset($_GET['toggle_id'])) {
    $id = mysqli_real_escape_string($con, $_GET['toggle_id']);

    $result = mysqli_query($con, "SELECT status FROM marque_tag WHERE m_id='$id'");
    if ($row = mysqli_fetch_assoc($result)) {
        $new_status = $row['status'] == 1 ? 0 : 1;

        mysqli_query($con, "UPDATE marque_tag SET status='$new_status' WHERE m_id='$id'");

        $msg = "Status Updated Successfully";
    } else {
        $msg1 = "Error: Invalid ID";
    }
}

if (isset($_GET['update_id']) && isset($_GET['value'])) {
    $id = intval($_GET['update_id']);
    $value = mysqli_real_escape_string($con, $_GET['value']);

    $sql = "UPDATE marque_tag SET value = '$value' WHERE m_id = $id";
    if (mysqli_query($con, $sql)) {

        $msg = "Value Updated Successfully";
    } else {
        echo "Error updating value.";
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($con, $_GET['delete_id']);
    mysqli_query($con, "DELETE FROM marque_tag WHERE m_id='$id'");
    $msg = "Tag Deleted Successfully";
}

// Handle add
if (isset($_POST['submit'])) {
    $value = mysqli_real_escape_string($con, $_POST['additional'] ?? '');
    if (!empty($value)) {
        mysqli_query($con, "INSERT INTO marque_tag (value, status) VALUES ('$value', 1)");
        $msg = "Tag Added Successfully";
    } else {
        $msg1 = "Please provide a value to add";
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
            var path = "upmarquee.php?toggle_id=" + row + "&status=" + new_status;
            window.location.href = path;
        }

        function delete_row(row) {
            var box = $("#mb-remove-row");
            box.addClass("open");
            box.find(".mb-control-yes").off("click").on("click", function() {
                box.removeClass("open");
                window.location.href = "upmarquee.php?delete_id=" + row;
            });
        }

        function updateValue(id) {
            console.log("ID received:", id);
            const element = document.getElementById('value' + id);
            if (!element) {
                alert("Element with id 'value" + id + "' not found!");
                return;
            }
            const value = element.value;
            const encodedValue = encodeURIComponent(value);
            console.log(value);
            window.location.href = `upmarquee.php?update_id=${id}&value=${encodedValue}`;
        }
    </script>
</head>

<body>
    <div class="page-container">
        <?php $menu13 = true;
        $smenu13 = "4";
        $ssmenu13 = "3";
        include_once("sidebar.php"); ?>
        <div class="page-content">
            <?php include_once("topheader.php"); ?>
            <ul class="breadcrumb">
                <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#">Master</a></li>
                <li class="active">Add Tags</li>
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
                        $result = mysqli_query($con, "SELECT * FROM marque_tag ORDER BY m_id ASC");
                        $data = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['m_id'];
                            $data[$id] = [
                                'value' => $row['value'] ?? '',
                                'status' => $row['status'] ?? '',
                            ];
                        }
                        ?>

                        <form class="form-horizontal" method="post" action="upmarquee.php">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Add</strong> Tags</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 70%;">Tag Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $id => $tag) { ?>
                                                    <tr>
                                                        <td>
                                                            <input type="text" id="value<?php echo $id; ?>" class="form-control" name="value<?php echo $id; ?>" value="<?php echo htmlspecialchars($tag['value']); ?>" required />
                                                        </td>
                                                        <td>
                                                            <button type="button" onclick="updateValue('<?php echo $id; ?>')" class="btn btn-primary btn-block">
                                                                <i class="fa fa-save"></i>
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button type="button" onclick="delete_row('<?php echo $id; ?>')" class="btn btn-danger btn-block">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button type="button" onclick="toggle_row('<?php echo $id; ?>', '<?php echo $tag['status']; ?>')" class="btn btn-<?php echo $tag['status'] == 1 ? 'danger' : 'success'; ?> btn-block">
                                                                <?php echo $tag['status'] == 1 ? 'Hide' : 'Show'; ?>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <!-- Add New Row -->
                                                <tr>
                                                    <td colspan="3">
                                                        <input type="text" class="form-control" name="additional" placeholder="Additional Value" />
                                                    </td>
                                                    <td>
                                                        <button type="submit" name="submit" class="btn btn-primary btn-block">Add</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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
                    <p>Are you sure you want to toggle the status of this tag?</p>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <script type="text/javascript" src="js/plugins.js"></script>
    <script type="text/javascript" src="js/actions.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#mytextarea0'))
            .catch(error => {
                console.error(error);
            });
    </script>
</body>

</html>