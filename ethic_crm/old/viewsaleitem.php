<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1=$msg2="";
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
        <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->                
	
		<script src="js\jquery.min.js"></script>       

 <link rel="stylesheet" href="css_js/jquery-ui.css">
  <script src="js/jquery-1.10.2.js"></script>
  <script src="js/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
  <style>
  .custom-combobox {
    position: relative;
    display: inline-block;
  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
  }
  .custom-combobox-input {
    margin: 0;
    padding: 5px 5px;
	width:100%;
  }
  </style>
  <script>
  (function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
		  .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
		
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "select", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
			var selected = this.element.children( ":selected" ),
          value = selected.val();
		  
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
   
   $(function() {
			 $("#combobox0").combobox();	
	  });
  </script>		
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php  $menu10=true; $smenu10="6"; include_once("sidebar.php"); ?>
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
                    <li><a href="#">Sales Master</a></li>
                    <li class="active">Sale Details (Item Wise)</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2> View Sale Details (Item Wise)</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                    
                    
                    <div class="row">
                        <div class="col-md-12">
							<?php
								if($msg)
								{
							?>
							<div class="alert alert-info" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg; ?></strong>
							</div>
							<?php 
								}
							?>
							<?php
								if($msg1)
								{
							?>
							<div class="alert alert-danger" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg1; ?></strong>
							</div>
							<?php 
								}
							?>
							<?php
								if($msg2)
								{
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
                                   <form class="form-horizontal" method="post" action="viewsaleitem.php" name='frm2' enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<label class="col-md-2 col-xs-2">Party Name</label>
												<label class="col-md-2 col-xs-2">Date From</label>
												<label class="col-md-2 col-xs-2">Date To</label>
												<label class="col-md-2 col-xs-2">Invoice No.</label>
												<label class="col-md-2 col-xs-2">Product Name</label>
												<label class="col-md-2 col-xs-2">GST Type</label>
											</div>
											<div class="row">
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="party">
														<option value=''>--Select--</option>
														<?php
															$l1=mysqli_query($con,"select * from ledger_accounts where status=1 and group_id in (26,27) order by name");
															while($l=mysqli_fetch_row($l1))
															{
																echo "<option value='$l[0]'>$l[1]</option>";
															}
														?>
													</select></div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dfrom"/></div>
												<div class="col-md-2 col-xs-2"><input type="date" class="form-control" name="dto"/></div>
												<div class="col-md-2 col-xs-2"><input type="text" class="form-control" name="invno"/></div>
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="item_id" id="combobox0" >
														<option value="">--Select--</option>
														<?php
                              $f1=mysqli_query($con,"select * from variant order by v_id");																		
                              while($f=mysqli_fetch_row($f1))
                              {
                                $c=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
                                echo "<option value='$f[0]'>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
                              }
														?>
													</select></div>
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="taxtype">
														<option value=''>--Select--</option>
														<option>GST</option>
														<option>IGST</option>
													</select></div>
											</div>
											<div class="row">
												<label class="col-md-2 col-xs-2">Sales Person</label>											
											</div>
											<div class="row">
												<div class="col-md-2 col-xs-12">
													<select class="form-control" name="emp_id">
														<option value=''>--Select--</option>
														<?php
															$l1=mysqli_query($con,"select * from empdet where status=1 order by empname");
															while($l=mysqli_fetch_row($l1))
															{
																echo "<option value='$l[0]'>$l[1]</option>";
															}
														?>
													</select></div>
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
	if($_REQUEST['dfrom']!="") $dfrom=" and invdate>='$_REQUEST[dfrom]'"; else $dfrom="";
	if($_REQUEST['dto']!="") $dto=" and invdate<='$_REQUEST[dto]'"; else $dto="";
	if($_REQUEST['party']!="") $party=" and party='$_REQUEST[party]'"; else $party="";
	if($_REQUEST['item_id']!="") $item_id=" and v_id='$_REQUEST[item_id]'"; else $item_id="";
	if($_REQUEST['taxtype']!="") $taxtype=" and taxtype='$_REQUEST[taxtype]'"; else $taxtype="";
	if($_REQUEST['emp_id']!="") $emp_id=" and emp_id='$_REQUEST[emp_id]'"; else $emp_id="";
	
	$sql="select * from bill_items where sale_id in (select sale_id from billbook where invno like '%$_REQUEST[invno]%' ".$dfrom." ".$dto." ".$party." ".$taxtype.") ".$item_id." ".$emp_id." order by sale_id desc";
	
	$result = mysqli_query($con,$sql);

	$table ="";
	if(mysqli_num_rows($result)==0 ) 
	{
		echo "There is no Sale Bill Available!!!";
	}
	else
	{
		$table.= "<table align='center' border='1' cellpadding='3' width='100%' style='border-collapse:collapse; font-size:12px;'>
			<tr>
				<th>S.<br>No.</th>
				<th>Date</th>
				<th>Party Name</th>
				<th>Invoice No.</th>
				<th>Product</th>
				<th>Qty</th>
				<th>MRP</th>
				<th>Discount</th>
				<th>Rate</th>
				<th>GST</th>
				<th>Amount</th>
			</tr>";
?>
		<table class="table table-bordered table-actions">
			<thead>
				<tr>
					<th style="width:20px;">S.<br>No.</th>
					<th width='80'>Date</th>
					<th>Party Name</th>
					<th>Invoice<br>No.</th>
					<th>Product</th>
					<th>Qty</th>
					<th>MRP</th>
					<th>Discount</th>
					<th>Rate</th>
					<th>GST</th>
					<th>Amount</th>
					<th width='20'>Actions</th>
				</tr>
			</thead>
			<tbody>
 <?php	
		if($d = mysqli_fetch_row($result))
		{		
			$j=1;
			$tot=$tot1=$tot2=$tot3=0;
			do
			{
				$p=mysqli_fetch_row(mysqli_query($con,"select * from billbook where sale_id='$d[0]'"));
				$table .= "<tr>";
			?>
				 <tr id="<?php echo $p[0]; ?>">
					<td><?php echo $j;?></td>
					<?php
						$table .= "<td>$j</td>";
						if($p[1]!="0000-00-00")
						{
							$date= DateTime::createFromFormat('Y-m-d', $p[1]);
							echo "<td>".$date->format('d-m-Y')."</td>";
							$table .="<td>".$date->format('d-m-Y')."</td>";
						}
						else
						{
							echo "<td>&nbsp;</td>";
							$table .= "<td>&nbsp;</td>";
						}
						$pa=mysqli_fetch_row(mysqli_query($con,"select name from ledger_accounts where ledger_id='$p[2]'"));
            $v=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$d[1]'"));
						$a=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$v[1]'"));
					?>
					<td><?php echo $pa[0]; ?></td>
					<td><?php echo $p[3]; ?></td>
					<td><?php echo htmlspecialchars("$a[1]-$a[5] $v[2] $v[3]"); ?></td>
					<td align='right'><?php echo $d[2]; ?></td>
					<td align='right'><?php echo $d[6]; ?></td>
					
					<?php
						$table .= "<td>$pa[0]</td>
								<td>$p[3]</td>
								<td>".htmlspecialchars("$a[1]-$a[5] $v[2] $v[3]")."</td>
								<td align='right'>$d[2]</td>
								<td align='right'>$d[6]</td>";
						$amt=$d[2]*$d[6];
						$tot3+=$d[2];
						if($d[7]=="P")
						$dis=round($amt*$d[4]/100,2);
						else $dis=$d[2]*$d[4];
						$amt=$d[2]*$d[3];
						$tot1+=$dis;
						echo "<td align='right'>$dis</td>";
						$table .= "<td align='right'>$dis</td>
									<td align='right'>$d[3]</td>";
					?>
					<td align='right'><?php echo $d[3]; ?></td>
					<?php
						$tax=round($amt*$d[5]/100,2);
						$amt=$amt+$tax;
						$tot2+=$tax;
						echo "<td align='right'>$tax</td>";
						$table .= "<td align='right'>$tax</td>";
						$amt=round($amt,2);
					?>
					<td align='right'><?php echo number_format($amt,2); ?></td>
					<?php
						$table .= "<td align='right'>".number_format($amt,2)."</td>";
						$tot+=$amt;
					?>
					<td>
						<button class="btn btn-warning btn-rounded btn-condensed btn-sm" onClick="window.open('saledet.php?sale_id=<?php echo $d[0]; ?>','_self');"><span class="fa fa-list"></span></button>
						<?php
							if($p[16]=="GST")
							{
						?>
						<button class="btn btn-success btn-rounded btn-condensed btn-sm" onClick="window.open('printinvoice.php?sale_id=<?php echo $d[0]; ?>','_blank');"><span class="fa fa-print"></span></button>
						<?php
							}
							else
							{
						?>
						<button class="btn btn-success btn-rounded btn-condensed btn-sm" onClick="window.open('printinvoice1.php?sale_id=<?php echo $d[0]; ?>','_blank');"><span class="fa fa-print"></span></button>
						<?php
							}
						?>
					</td>
				</tr>
			<?php
					$table .= "</tr>";
				$j++;
			}while($d = mysqli_fetch_array($result));
		}
		$table .="<tr>
			<td colspan='5'>Total</td>
			<td align='right'>$tot3</td>
			<td></td>
			<td align='right'>".number_format($tot1,2)."</td>
			<td></td>
			<td align='right'>".number_format($tot2,2)."</td>
			<td align='right'>".number_format($tot,2)."</td>			
		</tr></table>";
	?>			
		<tr>
			<td colspan='5'>Total</td>
			<td align='right'><?php echo $tot3; ?></td>
			<td></td>
			<td align='right'><?php echo number_format($tot1,2); ?></td>
			<td></td>
			<td align='right'><?php echo number_format($tot2,2); ?></td>
			<td align='right'><?php echo number_format($tot,2); ?></td>			
			<td></td>
		</tr>
		</tbody>
      </table> 
	
		<div class="col-md-1 col-xs-1">
			  <form action="printlist.php" method="post" target="_blank">
				<input type="hidden" value="<?php echo $table; ?>" name="query"/>
				<button class="btn btn-primary" type="submit" name="s10">Print</button>		
				</form>
		</div>
		<div class="col-md-3 col-xs-3">
			<form action="excel.php" method="post">
				 <input type="hidden" name="query" value="<?php echo $table; ?>"/>
				 <input type="hidden" name="fn" value="Sale Bill Details (Item Wise)"/>
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
       
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>        
        <!-- END PLUGINS -->
        
        <!-- START THIS PAGE PLUGINS-->        
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
	
    
        <!-- END THIS PAGE PLUGINS-->  
        
        <!-- START TEMPLATE -->
        
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->  
    </body>
</html>