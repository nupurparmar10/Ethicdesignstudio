<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	if(isset($_REQUEST['s1']))
	{
		$sdate=$_REQUEST['rdate'];
		$str=explode(":",$_REQUEST['party']);
		$paidby=$_REQUEST['paidby'];
		$amt=$_REQUEST['amount'];		
		$cheque=$_REQUEST['cheque'];
		$a1=mysqli_query($con,"select name from ledger_accounts where ledger_id='$str[0]'");
		$a=mysqli_fetch_row($a1);
		$cmp1=mysqli_query($con,"select max(pay_id) from paydues");
		$cmp=mysqli_fetch_row($cmp1);
		$id=$cmp[0]+1;
		
		$rid="PD".$id;
		mysqli_query($con,"insert into paydues set pay_id=$id, rdate='$sdate', party='$str[0]', remark='$_REQUEST[remark]', paidby='$paidby', amount='$amt', cheque='$_REQUEST[cheque]', relatedwith='$rid'");
		
		$cmp1=mysqli_query($con,"select max(trans_id) from transaction");
		$cmp=mysqli_fetch_row($cmp1);
		$tid=$cmp[0]+1;
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$sdate."', ledger_id='".$paidby."', amount='".$amt."', particulars='Dues Paid to - $a[0]', type='Cr.', relatedto='$rid'");		
		$tid++;
		
		mysqli_query($con,"insert into transaction set trans_id='".$tid."', tdate='".$sdate."', ledger_id='".$str[0]."', amount='".($amt)."', particulars='Dues received', type='Dr.', relatedto='$rid'");
		$tid++;
		$msg="Pay Dues Entry Saved Successfully!!!";
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
        <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->                 
	
		<script src="js\jquery.min.js"></script>
		
		
		<script>
	var counter=0;
</script>

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
	min-width:500px;
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
		   getbal(value);
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
  
  function startc()
  {
	str="#combobox0";
	$(str).combobox();	  
  }
   
  </script> 
		<script  type="text/javascript" language="javascript">
function getbal(val)
{
	var str=val.split(":");
	document.getElementById("balance").value=str[1];
}
</script>
    </head>
    <body onload="startc();">
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu11=true; $smenu11="1";  include_once("sidebar.php"); ?>
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
                    <li><a href="#">Pay Dues Master</a></li>
                    <li class="active">Add Pay Dues</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                    <div class="row">
                        <div class="col-md-12">
                            
                            <?php
								if($msg)
								{
							?>
							<div class="alert alert-success" role="alert">
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
							<div class="alert alert-warning" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<strong><?php echo $msg1; ?></strong>
							</div>
							<?php 
								}
							?>
                            <form class="form-horizontal" method="post" action="addpaydues.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									
									 <h3 class="panel-title"><strong>Add </strong> Pay Dues</h3>
                                </div>
								<?php
									$date=date("Y-m-d");
								?>
                                <div class="panel-body">   
									
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Date</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                <input type="date" class="form-control" name="rdate" value='<?php echo $date; ?>' required />
                                            </div>                                            
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Party</label>
										<div class="ui-widget form-group col-md-6 col-xs-12">
											<select class="form-control" name="party" id="combobox0" style="min-width:500px;">
												<option value="">--Select--</option>
												<?php
													$list1=mysqli_query($con,"select * from ledger_accounts where group_id in (26,27,33) order by name");
													while($list=mysqli_fetch_row($list1))
													{
														$cr=$dr=0;
														$a=mysqli_fetch_row(mysqli_query($con,"SELECT sum(amount) FROM transaction where ledger_id='$list[0]' and type='Cr.'"));
															$cr+=$a[0];
															$a=mysqli_fetch_row(mysqli_query($con,"SELECT sum(amount) FROM transaction where ledger_id='$list[0]' and type='Dr.'"));
															$dr+=$a[0];
														
														$bal=$cr-$dr+$list[3];
                            if($bal>0)
														echo "<option value='$list[0]:$bal'>$list[1]</option>";
													}
												?>	
											</select>
										</div>
                                    </div> 
									
									<div class="form-group">
                                       <label class="col-md-3 col-xs-12 control-label">Paid By</label>
                                        <div class="col-md-6 col-xs-12">
                                            <select class="form-control" name="paidby" required>
                                               <?php
													$list1=mysqli_query($con,"select ledger_id,name from ledger_accounts where group_id=(select group_id from group_master where group_name='Bank Accounts') or name='Cash Account'");
													if($l=mysqli_fetch_row($list1))
													{
														do{
															echo "<option value='$l[0]'>$l[1]</option>";
														}while($l=mysqli_fetch_row($list1));
													}
												?>
                                            </select>
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Cheque No</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <input type="text" class="form-control" name="cheque" />                                          
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Balance</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <input type="text" class="form-control" id="balance" name="balance" disabled="disabled" style="color:black;"/>                                          
                                        </div>
                                    </div> 
									<div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Amount</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="amount"/>
                                            </div>                                            
                                        </div>
                                    </div> 
																								
                                     <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Remark</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group col-md-12">
                                                <textarea class="form-control" rows="5" name="remark"></textarea>
                                            </div>                                            
                                        </div>
                                    </div>
									
                                    
                                </div>
                                <div class="panel-footer">
									
									<button class="btn btn-primary" type="submit" name="s1">Receive</button>
                                    <button class="btn btn-default">Clear Form</button>                                    
                                </div>
                            </div>
                            </form>
                            
                        </div>
                    </div>                    
                    
                </div>
                <!-- END PAGE CONTENT WRAPPER -->                                                
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->
        
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