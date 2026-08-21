<?php
	ob_start();
	session_start();
	include_once("connect.php");
	$msg=$msg1="";
	if(!isset($_REQUEST['contacts']))
	{
		header("Location: viewcontact.php"); die;
	}
	if(isset($_REQUEST['s1']))
	{
		$str=explode(",",$_REQUEST['contacts']);
		$text=$_REQUEST['message'];
		$text=urlencode($text);
		for($i=0;$i<count($str);$i++)
		{
			$c_id=$str[$i];
			$c=mysqli_fetch_row(mysqli_query($con,"select * from contact where c_id='$c_id'"));
			$mob="";
			if($c[2]!="") $mob .=$c[2].",";
			if($c[3]!="") $mob .=$c[3].",";
			if($c[4]!="") $mob .=$c[4].",";
			if($mob!="") $mob=substr($mob,0,strlen($mob)-1);
			if($mob!="")
			{
				/*$data="http://new.rajbusiness.com/api/mt/SendSMS?user=NDSTONE&password=N1D5STONE20&senderid=MITSMS&channel=Trans&DCS=08&flashsms=0&number=".$mob."&text=".$text."&route=01";
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
				curl_setopt($ch, CURLOPT_URL, $data);
				
				curl_exec($ch);
				curl_close($ch);*/
			}
		}
		header("Location: viewcontact.php?msg1=set"); die;
	}
	if(isset($_REQUEST['s3']))
	{
		$str=explode(",",$_REQUEST['contacts']);
		$text=$_REQUEST['message'];
		$text=urlencode($text);
		for($i=0;$i<count($str);$i++)
		{
			$c_id=$str[$i];
			$c=mysqli_fetch_row(mysqli_query($con,"select * from contact where c_id='$c_id'"));
			if($c[5]!="")
			{
				$a=explode(",",$c[5]);
				$count=count($a);
				for($i=0;$i<$count;$i++)
				{
					$str = $a[$i];
					if($str!="")
					{
						$to  = $str; // note the comma
						$subject = 'Ethic Design Studio';
						$message = '
							<html>
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
								<title>Ethic Design Studio</title>
							</head>
							<body>
							'.$_REQUEST['message'].'
							</body>
							</html>
							';
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						mail($to, $subject, $message, $headers);
					}
				}
			}
		}
		header("Location: viewcontact.php?msg1=set"); die;
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
    </head>
    <body>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
            
            <!-- START PAGE SIDEBAR -->
             <?php $menu7=true; $smenu7="2"; include_once("sidebar.php"); ?>
            <!-- END PAGE SIDEBAR -->
            
            <!-- PAGE CONTENT -->
            <div class="page-content">
                
                <!-- START X-NAVIGATION VERTICAL -->
                 <?php include_once("topheader.php"); ?>
                <!-- END X-NAVIGATION VERTICAL -->                   
                
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                   <li><a href="#">Dashboard</a></li>
                    <li><a href="#">Contact Master</a></li>
                    <li class="active">Message Selected</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                    <div class="row">
                        <div class="col-md-12">
                            
                            <form class="form-horizontal" method="post" action="messageall.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
									
									 <h3 class="panel-title"><strong>Send </strong> Message</h3>
								</div>
                                <div class="panel-body">   
									<?php
										if(isset($_REQUEST['contacts']))
										{
											$count=count($_REQUEST['contacts']);
											for($i=0,$str="";$i<$count;$i++)
											{
												$str .= $_REQUEST['contacts'][$i].",";
											}
											$len=strlen($str);
											$str=substr($str,0,$len-1);
											echo "<input type='hidden' value='$str' name='contacts'/>";
										}
									?>
                                     <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Message</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group col-md-12">
                                                <textarea class="form-control" rows="5" name="message"></textarea>
                                            </div>                                            
                                        </div>
                                    </div>
									
                                    
                                </div>
                                <div class="panel-footer">
									
									<button class="btn btn-primary" type="submit" name="s1">Send via Text Message</button>
									<button class="btn btn-primary" type="submit" name="s3">Send via Email</button>
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
        <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>                
        <!-- END PLUGINS -->
        
        <!-- THIS PAGE PLUGINS -->
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>                
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
        <script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
        <!-- END THIS PAGE PLUGINS -->       
        
        <!-- START TEMPLATE -->
        
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->
    
   
    </body>
</html>