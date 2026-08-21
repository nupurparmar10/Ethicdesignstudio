<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";

if (isset($_GET['update_id']) && isset($_GET['value'])) {
    $id = intval($_GET['update_id']);
    $value = mysqli_real_escape_string($con, $_GET['value']);

    $check_query = "SELECT * FROM label WHERE name = '$value' AND l_id != $id";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $msg1 = "This value already exists.";
    } else {
        $sql = "UPDATE label SET name = '$value' WHERE l_id = $id";
        if (mysqli_query($con, $sql)) {
            $msg = "Label Updated Successfully";
        } else {
            echo "Error updating value.";
        }
    }
}


// Handle delete
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($con, $_GET['delete_id']);
    mysqli_query($con, "UPDATE label set status = 0 WHERE l_id='$id'");
    $msg = "Label Deleted Successfully";
}

// Handle add
if (isset($_POST['submit'])) {
    $value = mysqli_real_escape_string($con, $_POST['additional'] ?? '');

    if (!empty($value)) {

        $check = mysqli_query($con, "SELECT * FROM label WHERE name = '$value'");

        if (mysqli_num_rows($check) > 0) {
            $msg1 = "This value already exists.";
        } else {
            mysqli_query($con, "INSERT INTO label (name) VALUES ('$value')");
            $msg = "Label Added Successfully";
        }
    } else {
        $msg1 = "Please provide a value to add.";
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
        function delete_row(row) {
            var box = $("#mb-remove-row");
            box.addClass("open");
            box.find(".mb-control-yes").off("click").on("click", function() {
                box.removeClass("open");
                window.location.href = "uplabel.php?delete_id=" + row;
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
            window.location.href = `uplabel.php?update_id=${id}&value=${encodedValue}`;
        }
    </script>
</head>

<body>
    <div class="page-container">
        <?php $menu13 = true;
        $smenu13 = "2";
        $ssmenu13 = "1";
        include_once("sidebar.php"); ?>
        <div class="page-content">
            <?php include_once("topheader.php"); ?>
            <ul class="breadcrumb">
                <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#">Master</a></li>
                <li class="active">Add Label</li>
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
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg1; ?></strong>
                            </div>
                        <?php } ?>

                        <?php
                        $result = mysqli_query($con, "SELECT * FROM label WHERE status = 1 ORDER BY l_id ASC");
                        $data = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['l_id'];
                            $data[$id] = [
                                'name' => $row['name'] ?? ''
                            ];
                        }
                        ?>

                        <form class="form-horizontal" method="post" action="uplabel.php">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Add</strong> Label</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Label</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $id => $type) { ?>
                                                    <tr>
                                                        <td style="width: 100%;">
                                                            <input type="text" id="value<?php echo $id; ?>" class="form-control" name="value<?php echo $id; ?>" value="<?php echo htmlspecialchars($type['name']); ?>" required />
                                                        </td>
                                                        <td style="width: 10%;">
                                                            <button type="button" onclick="updateValue('<?php echo $id; ?>')" class="btn btn-primary btn-block">
                                                                <i class="fa fa-save"></i>
                                                            </button>
                                                        </td>
                                                        <td style="width: 10%;">
                                                            <button type="button" onclick="delete_row('<?php echo $id; ?>')" class="btn btn-danger btn-block">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>

                                                    </tr>
                                                <?php } ?>

                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" name="additional" placeholder="Additional Value" />
                                                    </td>
                                                    <td style="width:10%;">
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