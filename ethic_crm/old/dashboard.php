<?php
	ob_start();
	session_start();
	include_once("connect.php");
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
            <?php $menu1=true; include_once("sidebar.php"); ?>
            <!-- END PAGE SIDEBAR -->
            
            <!-- PAGE CONTENT -->
            <div class="page-content">
                
                <!-- START X-NAVIGATION VERTICAL -->
                <?php include_once("topheader.php"); ?>
                <!-- END X-NAVIGATION VERTICAL -->                     

                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">              
                    <li class="active">Dashboard</li>
                </ul>
                <!-- END BREADCRUMB -->                       
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    
                    <!-- START WIDGETS -->                    
                    <div class="row">
                        
                    <div class="col-md-3">                     
                            <a href="addproduct.php" class="tile tile-danger">
                                <span class="fa fa-list"></span>
                                <p style="font-weight:bold;">Add Product</p>  
                            </a>                        
                        </div>
						<div class="col-md-3">                     
                            <a href="viewproduct1.php" class="tile tile-info">
                                <span class="fa fa-rupee"></span>
                                <p style="font-weight:bold;">Inventory</p>  
                            </a>                        
                        </div>
						<div class="col-md-3">                     
                            <a href="viewsales.php" class="tile tile-warning">
                                <span class="fa fa-rupee"></span>
                                <p style="font-weight:bold;">View Sales</p>  
                            </a>                        
                        </div>
						<div class="col-md-3">                     
                            <a href="addsales.php" class="tile tile-success">
                                <span class="fa fa-list"></span>
                                <p style="font-weight:bold;">Create Invoice</p>  
                            </a>                        
                        </div>
                    </div>
					<div class="row">
						<div class="col-md-3">                     
                            <a href="addpurchase.php" class="tile tile-danger">
                                <span class="fa fa-rupee"></span>
                                <p style="font-weight:bold;">Add Purchase Bill</p>  
                            </a>                        
                        </div>
						<div class="col-md-3">                     
                            <a href="viewpurchase.php" class="tile tile-info">
                                <span class="fa fa-rupee"></span>
                                <p style="font-weight:bold;">View Purchase Bill</p>  
                            </a>                        
                        </div>
						<div class="col-md-3">                     
                            <a href="addpaydues.php" class="tile tile-warning">
                                <span class="fa fa-rupee"></span>
                                <p style="font-weight:bold;">Paydues</p>  
                            </a>                        
                        </div>
						<div class="col-md-3">                     
                            <a href="addreceipt.php" class="tile tile-success">
                                <span class="fa fa-rupee"></span>
                                <p style="font-weight:bold;">Create Receipt</p>  
                            </a>                        
                        </div>
						
					</div>
                    <div class="row">
						<div class="col-md-3">                     
                            <a href="addmanu.php" class="tile tile-danger">
                                <span class="fa fa-briefcase"></span>
                                <p style="font-weight:bold;">Generate Manufacturing Job</p>  
                            </a>                        
                        </div>
						<div class="col-md-3">                     
                            <a href="addmanu1.php" class="tile tile-info">
                                <span class="fa fa-briefcase"></span>
                                <p style="font-weight:bold;">Generate Service Job</p>  
                            </a>                        
                        </div>
                        <div class="col-md-3">                     
                            <a href="viewmanu.php" class="tile tile-warning">
                                <span class="fa fa-briefcase"></span>
                                <p style="font-weight:bold;">Pending Jobs</p>  
                            </a>                        
                        </div>
						<div class="col-md-3">                     
                            <a href="viewmanu1.php" class="tile tile-success">
                                <span class="fa fa-briefcase"></span>
                                <p style="font-weight:bold;">Completed Jobs</p>  
                            </a>                        
                        </div>
						
					</div>
                    <!-- END WIDGETS -->                   
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

        <!-- START THIS PAGE PLUGINS-->        
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>        
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        <script type="text/javascript" src="js/plugins/scrolltotop/scrolltopcontrol.js"></script>
        
        <script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script>
        <script type="text/javascript" src="js/plugins/morris/morris.min.js"></script>       
        <script type="text/javascript" src="js/plugins/rickshaw/d3.v3.js"></script>
        <script type="text/javascript" src="js/plugins/rickshaw/rickshaw.min.js"></script>
        <script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'></script>
        <script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'></script>                
        <script type='text/javascript' src='js/plugins/bootstrap/bootstrap-datepicker.js'></script>                
        <script type="text/javascript" src="js/plugins/owl/owl.carousel.min.js"></script>                 
        
        <script type="text/javascript" src="js/plugins/moment.min.js"></script>
        <script type="text/javascript" src="js/plugins/daterangepicker/daterangepicker.js"></script>
        <!-- END THIS PAGE PLUGINS-->        

        <!-- START TEMPLATE -->
        
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>
        
        <script type="text/javascript" src="js/demo_dashboard.js"></script>
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','../../../../www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-36783416-1', 'auto');
        ga('send', 'pageview');
    </script>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter25836617 = new Ya.Metrika({
                        id:25836617,
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true,
                        webvisor:true
                    });
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = "../../../../mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/25836617" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->    
    </body>
</html>