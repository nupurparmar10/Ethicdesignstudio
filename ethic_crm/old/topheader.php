<?php
	include_once("connect.php");
	include_once("functions.php");
?>

<ul class="x-navigation x-navigation-horizontal x-navigation-panel">
	<!-- TOGGLE NAVIGATION -->
	<li class="xn-icon-button">
		<a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a>
	</li>
	<!-- END TOGGLE NAVIGATION -->
	<!-- POWER OFF -->
	<li class="xn-icon-button pull-right last">
		<a href="#"><span class="fa fa-power-off"></span></a>
		<ul class="xn-drop-left animated zoomIn">
			<li><a href="#" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span> Sign Out</a></li>
		</ul>                        
	</li> 
	<!-- END POWER OFF -->                    
	</ul>