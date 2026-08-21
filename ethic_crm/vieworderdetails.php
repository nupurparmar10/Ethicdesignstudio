<?php
	ob_start();
	session_start();
	include_once("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>        
    <title>Ethic Design Studio</title>              
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <link rel="icon" href="logo3.png" type="image/x-icon" />
    
    <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
    <script src="js/jquery.min.js"></script>           
</head>
<body>
    <div class="page-container">
        
        <?php  $menu13=true; $smenu13="6"; $ssmenu13="1"; include_once("sidebar.php"); ?>
        
        <div class="page-content">
            
            <?php include_once("topheader.php"); ?>
            
            <ul class="breadcrumb">
                <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#">Order Details</a></li>
                <li class="active">View Order Details</li>
            </ul>
            
            <div class="page-title">                    
                <h2> View Order Details</h2>
            </div>
            
            <div class="page-content-wrap">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form class="form-horizontal" method="post" action="vieworderdetails.php">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-2 col-xs-2">Date From</label>
                                            <label class="col-md-2 col-xs-2">Date To</label>
                                            <label class="col-md-3 col-xs-3">Order ID</label>
                                            <label class="col-md-3 col-xs-3">Item Details</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 col-xs-2">
                                                <input type="date" class="form-control" name="dfrom" value=""/>
                                            </div>
                                            <div class="col-md-2 col-xs-2">
                                                <input type="date" class="form-control" name="dto" value=""/>
                                            </div>
                                            <div class="col-md-3 col-xs-3">
                                                <input type="text" class="form-control" name="order_id" value=""/>
                                            </div>
                                            <div class="col-md-3 col-xs-3">
                                                <select class="form-control" name="item_id">
                                                    <option value=''>--Select Item--</option>
                                                    <?php
                                                        $items=mysqli_query($con,"SELECT item_id, pcode, saledesp FROM item_details WHERE status=1 ORDER BY pcode");
                                                        while($item=mysqli_fetch_assoc($items))
                                                        {
                                                            echo "<option value='".$item['item_id']."'>".htmlspecialchars($item['pcode'])." - ".htmlspecialchars($item['saledesp'])."</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 col-xs-2"> 
                                                <button class="btn btn-primary" type="submit" name="open">Open</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <br>
                                <div class="table-responsive" id="display">
                                <?php
                                if(isset($_REQUEST['open']))
                                {
                                    $where = ["1=1"];
                                    
                                    if(!empty($_REQUEST['dfrom'])) {
                                        $dfrom = mysqli_real_escape_string($con, $_REQUEST['dfrom']);
                                        $where[] = "DATE(c.created_at) >= '$dfrom'";
                                    }
                                    if(!empty($_REQUEST['dto'])) {
                                        $dto = mysqli_real_escape_string($con, $_REQUEST['dto']);
                                        $where[] = "DATE(c.created_at) <= '$dto'";
                                    }
                                    if(!empty($_REQUEST['order_id'])) {
                                        $order_id = mysqli_real_escape_string($con, $_REQUEST['order_id']);
                                        $where[] = "c.order_id LIKE '%$order_id%'";
                                    }
                                    
                                    $join = "";
                                    if(!empty($_REQUEST['item_id'])) {
                                        $item_id = mysqli_real_escape_string($con, $_REQUEST['item_id']);
                                        $join = " JOIN order_item oi ON c.check_id = oi.check_id JOIN variant v ON oi.v_id = v.v_id ";
                                        $where[] = "v.item_id = '$item_id'";
                                    }
                                    
                                    $where_clause = implode(" AND ", $where);
                                    
                                    $sql = "SELECT DISTINCT c.* FROM checkout c $join WHERE $where_clause ORDER BY c.created_at DESC";
                                    $result = mysqli_query($con, $sql);
                                    
                                    $table = "";
                                    
                                    if(mysqli_num_rows($result) == 0) {
                                        echo "There is no Order Available!!!";
                                    } else {
                                        $table .= "<table align='center' border='1' cellpadding='3' width='100%' style='border-collapse:collapse; font-size:12px;'>
                                            <tr>
                                                <th>S. No.</th>
                                                <th>Date</th>
                                                <th>User Details</th>
                                                <th>Order ID</th>
                                                <th>Amount</th>
                                                <th>Payment Type</th>
                                                <th>Payment Status</th>
                                            </tr>";
                                ?>
                                    <table class="table table-bordered table-actions datatable">
                                        <thead>
                                            <tr>
                                                <th style="width:4%;">S. No.</th>
                                                <th>Date</th>
                                                <th>User Details</th>
                                                <th>Order ID</th>
                                                <th>Amount</th>
                                                <th>Payment Type</th>
                                                <th>Payment Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                <?php
                                        $j = 1;
                                        $tot_amount = 0;
                                        while($d = mysqli_fetch_assoc($result)) 
                                        {
                                            $date = date('d-m-Y h:i A', strtotime($d['created_at']));
                                            $tot_amount += $d['amount'];
                                            
                                            $table .= "<tr>
                                                <td>$j</td>
                                                <td>$date</td>
                                                <td>".htmlspecialchars($d['name']) ." ". htmlspecialchars($d['lastname'])."<br>".htmlspecialchars($d['mobile'])."<br>".htmlspecialchars($d['email'])."</td>
                                                <td>".htmlspecialchars($d['order_id'])."</td>
                                                <td>".htmlspecialchars($d['amount'])."</td>
                                                <td>".htmlspecialchars($d['payment_type'])."</td>
                                                <td>".htmlspecialchars($d['order_status'])."</td>
                                            </tr>";
                                ?>
                                            <tr>
                                                <td><?php echo $j++; ?></td>
                                                <td><?php echo $date; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($d['name']); ?> <?php echo htmlspecialchars($d['lastname']); ?></strong><br>
                                                    <small><i class="fa fa-phone"></i> <?php echo htmlspecialchars($d['mobile']); ?></small><br>
                                                    <small><i class="fa fa-envelope"></i> <?php echo htmlspecialchars($d['email']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($d['order_id']); ?></td>
                                                <td>₹<?php echo htmlspecialchars($d['amount']); ?></td>
                                                <td><?php echo htmlspecialchars($d['payment_type']); ?></td>
                                                <td>
                                                    <?php 
                                                        $ostatus = strtoupper($d['order_status']);
                                                        if ($ostatus == 'PAID') {
                                                            echo '<span class="label label-success" style="font-size:12px; padding:4px 8px;">' . htmlspecialchars($d['order_status']) . '</span>';
                                                        } elseif ($ostatus == 'NEW') {
                                                            echo '<span class="label label-danger" style="font-size:12px; padding:4px 8px;">' . htmlspecialchars($d['order_status']) . '</span>';
                                                        } else {
                                                            echo '<span class="label label-default" style="font-size:12px; padding:4px 8px;">' . htmlspecialchars($d['order_status']) . '</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td align="center">
                                                    <button class="btn btn-info btn-rounded btn-condensed btn-sm" onclick="window.open('orderdetails.php?check_id=<?php echo $d['check_id']; ?>','_self');" title="View Details"><span class="fa fa-info"></span></button>
                                                    <?php if (!empty($d['shiprocket_shipment_id'])): ?>
                                                        <br>
                                                        <button class="btn btn-success btn-rounded btn-condensed btn-sm" style="margin-top:5px;" onclick="openTrackingModal('<?php echo $d['shiprocket_shipment_id']; ?>');" title="Track Shipment">
                                                            <span class="fa fa-map-marker"></span>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                <?php
                                        }
                                        
                                            $table .= "<tr>
                                            <td colspan='4' align='right'><b>Total</b></td>
                                            <td><b>".number_format($tot_amount, 2)."</b></td>
                                            <td colspan='2'></td>
                                        </tr></table>";
                                ?>
                                        </tbody>
                                        <tfoot>
                                            <tr style='font-weight:bold;'>
                                                <td colspan="4" align="right">Total</td>
                                                <td>₹<?php echo number_format($tot_amount, 2); ?></td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    
                                    <br>
                                    <div class="row">
                                        <div class="col-md-1 col-xs-1">
                                              <form action="printlist.php" method="post" target="_blank">
                                                <input type="hidden" value="<?php echo htmlspecialchars($table); ?>" name="query"/>
                                                <button class="btn btn-primary" type="submit" name="s10">Print</button>		
                                                </form>
                                        </div>
                                        <div class="col-md-3 col-xs-3">
                                            <form action="excel.php" method="post">
                                                 <input type="hidden" name="query" value="<?php echo htmlspecialchars($table); ?>"/>
                                                 <input type="hidden" name="fn" value="Order Details"/>
                                                 <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
                                             </form>
                                        </div>
                                    </div>
                                <?php
                                    }
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>         
        </div>            
    </div>
    
    <?php include_once("footer.php"); ?>
    
    <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>                
    
    <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
    <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
    <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
    
    <script type="text/javascript" src="js/plugins.js"></script>        
    <script type="text/javascript" src="js/actions.js"></script>        

    <!-- Tracking Modal -->
    <div class="modal fade" id="trackingModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Shipment Tracking Details</h4>
                </div>
                <div class="modal-body" style="background:#f5f5f5;">
                    <div id="trackingLoading" class="text-center" style="display:none; padding: 20px;">
                        <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                        <h4 style="margin-top: 10px;" id="trackingLoadingText">Fetching tracking details...</h4>
                    </div>
                    
                    <div id="trackingMessage" class="alert" style="display:none;"></div>
                    
                    <div id="trackingContent" style="display:none;">
                        <!-- Shipment Info Grid -->
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row" style="margin-bottom:15px;">
                                    <div class="col-md-6">
                                        <h3 style="margin-top:0;"><span id="t_current_status" class="label label-primary"></span></h3>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button id="t_send_email_btn" class="btn btn-info btn-sm" style="display:none; margin-right:5px;" onclick="sendTrackingEmail()"><i class="fa fa-envelope"></i> Send Tracking Email</button>
                                        <a href="#" id="t_track_url" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-external-link"></i> Track on Courier Site</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                                        <small class="text-muted">AWB / Tracking ID</small><br>
                                        <strong id="t_awb_code"></strong>
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                                        <small class="text-muted">Courier Partner</small><br>
                                        <strong id="t_courier_name"></strong>
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                                        <small class="text-muted">Consignee Name</small><br>
                                        <strong id="t_consignee_name"></strong>
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                                        <small class="text-muted">Expected Delivery Date (EDD)</small><br>
                                        <strong id="t_edd"></strong>
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                                        <small class="text-muted">Pickup Date</small><br>
                                        <strong id="t_pickup_date"></strong>
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                                        <small class="text-muted">Delivered Date</small><br>
                                        <strong id="t_delivered_date"></strong>
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                                        <small class="text-muted">Origin</small><br>
                                        <strong id="t_origin"></strong>
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                                        <small class="text-muted">Destination</small><br>
                                        <strong id="t_destination"></strong>
                                    </div>
                                    <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                                        <small class="text-muted">Order ID / Shipment ID</small><br>
                                        <strong id="t_order_shipment"></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tracking History Timeline -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tracking History</h3>
                            </div>
                            <div class="panel-body panel-body-table">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-actions">
                                        <thead>
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Activity</th>
                                                <th>Location</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="t_activities">
                                            <!-- Dynamically populated -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    var current_shipment_id = null;

    function openTrackingModal(shipmentId) {
        current_shipment_id = shipmentId;
        $('#trackingModal').modal('show');
        $('#trackingLoadingText').text('Fetching tracking details...');
        $('#trackingLoading').show();
        $('#trackingMessage').hide().removeClass('alert-danger alert-success');
        $('#trackingContent').hide();
        $('#t_activities').empty();
        $('#t_send_email_btn').hide();
        
        $.ajax({
            url: 'track_shipment.php',
            type: 'GET',
            data: { shipment_id: shipmentId },
            dataType: 'json',
            success: function(response) {
                $('#trackingLoading').hide();
                
                if (response.status === 'error') {
                    $('#trackingMessage').addClass('alert-danger').html(response.message || 'Error fetching tracking details.').show();
                    return;
                }
                
                // Shiprocket returns 0/1 for status or just direct response sometimes. Handle gracefully.
                var trackData = response.tracking_data || {};
                var trackInfo = (trackData.shipment_track && trackData.shipment_track.length > 0) ? trackData.shipment_track[0] : null;
                
                if (!trackInfo) {
                    $('#trackingMessage').addClass('alert-danger').html('Tracking data is not yet available for this shipment.').show();
                    return;
                }
                
                // Populate Info
                function getVal(val) { return (val && val !== 'null') ? val : 'Not Available'; }
                
                $('#t_current_status').text(getVal(trackInfo.current_status));
                $('#t_awb_code').text(getVal(trackInfo.awb_code));
                $('#t_courier_name').text(getVal(trackInfo.courier_name));
                $('#t_consignee_name').text(getVal(trackInfo.consignee_name));
                $('#t_edd').text(getVal(trackInfo.expected_date) !== 'Not Available' ? getVal(trackInfo.expected_date) : getVal(trackInfo.edd));
                $('#t_pickup_date').text(getVal(trackInfo.pickup_date));
                $('#t_delivered_date').text(getVal(trackInfo.delivered_date));
                $('#t_origin').text(getVal(trackInfo.origin));
                $('#t_destination').text(getVal(trackInfo.destination));
                $('#t_order_shipment').text(getVal(trackInfo.order_id) + " / " + getVal(trackInfo.shipment_id));
                
                if (trackData.track_url) {
                    $('#t_track_url').attr('href', trackData.track_url).show();
                    $('#t_send_email_btn').show();
                } else {
                    $('#t_track_url').hide();
                    $('#t_send_email_btn').hide();
                }
                
                // Populate Activities
                var activities = trackData.shipment_track_activities || [];
                var tbody = '';
                if (activities.length > 0) {
                    $.each(activities, function(i, activity) {
                        tbody += '<tr>';
                        tbody += '<td>' + getVal(activity.date) + '</td>';
                        tbody += '<td>' + getVal(activity.activity) + '</td>';
                        tbody += '<td>' + getVal(activity.location) + '</td>';
                        tbody += '<td>' + getVal(activity.status) + '</td>';
                        tbody += '</tr>';
                    });
                } else {
                    tbody = '<tr><td colspan="4" class="text-center">No tracking history available yet.</td></tr>';
                }
                $('#t_activities').html(tbody);
                
                $('#trackingContent').show();
            },
            error: function(xhr, status, error) {
                $('#trackingLoading').hide();
                $('#trackingMessage').addClass('alert-danger').html('Failed to connect to the tracking server. Please try again later.').show();
            }
        });
    }

    function sendTrackingEmail() {
        if (!current_shipment_id) return;
        
        var $btn = $('#t_send_email_btn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
        $('#trackingMessage').hide().removeClass('alert-danger alert-success');
        
        $.ajax({
            url: 'send_tracking_email.php',
            type: 'POST',
            data: { shipment_id: current_shipment_id },
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).html('<i class="fa fa-envelope"></i> Send Tracking Email');
                if (response.status === 'success') {
                    $('#trackingMessage').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                } else {
                    $('#trackingMessage').removeClass('alert-success').addClass('alert-danger').html(response.message || 'Failed to send email.').show();
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-envelope"></i> Send Tracking Email');
                $('#trackingMessage').removeClass('alert-success').addClass('alert-danger').html('A server error occurred while sending the email.').show();
            }
        });
    }
    </script>
</body>
</html>