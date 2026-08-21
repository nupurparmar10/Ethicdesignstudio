<style>
/* NEW 2nd-Level Dropdown CSS START */
.dropdown-submenu{position: relative;}
.dropdown-submenu .caret{-webkit-transform: rotate(-90deg); transform: rotate(-90deg);}
.dropdown-submenu > .dropdown-menu {top:0; left:100%; margin-top:-6px; margin-left:-1px;}
.dropdown-submenu.open > a:after{border-left-color:#fff;}
.dropdown-submenu.open > .dropdown-menu, .dropdown-submenu.open > .dropdown-menu {display: block;}
.dropdown-submenu .dropdown-menu{margin-bottom: 8px;}
.navbar-default .navbar-nav .open .dropdown-menu .dropdown-submenu ul{background-color: #f6f6f6;}
.navbar-inverse .navbar-nav .open .dropdown-menu .dropdown-submenu ul{background-color:#333;}
.navbar .navbar-nav .open .dropdown-submenu .dropdown-menu  > li > a{padding-left: 30px;}
@media screen and (min-width:992px){
	.dropdown-submenu .dropdown-menu{margin-bottom: 2px;}
	.navbar .navbar-nav .open .dropdown-submenu .dropdown-menu  > li > a{padding-left: 25px;}
	.navbar-default .navbar-nav .open .dropdown-menu .dropdown-submenu ul{background-color:#fff;}
	.navbar-inverse .navbar-nav .open .dropdown-menu .dropdown-submenu ul{background-color:#fff;}
}
/* NEW 2nd-Level Dropdown CSS END */
</style>
<nav class="navbar navbar-inverse navbar-fixed-top hidden-lg">
  <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="dashboard.php"><?php echo "Super Admin"; ?></a>
    </div>
    <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
        <ul class="nav navbar-nav">
			<li <?php if($menu1) { ?> class="active" <?php } ?>>
				<a href="dashboard.php"><span class="fa fa-dashboard"></span> <span class="xn-text">Dashboard</span></a>
			</li>
			<?php
			if($_SESSION['account']=="0")
			{
		?>
		<li class="dropdown <?php if($menu2) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Masters </span><span class="caret"></span></a>
			<ul class="dropdown-menu">
			    <li class="dropdown-submenu <?php if($smenu2=="8") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Color Master <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if ($ssmenu2 == "81") { ?> class="active" <?php } ?>><a href="addcolor.php">Add Color</a></li>
						<li <?php if ($ssmenu2 == "82") { ?> class="active" <?php } ?>><a href="viewcolor.php">View Color</a></li>		
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu2=="1") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Product Type Master <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu2=="11") { ?> class="active" <?php } ?>><a href="addprotype.php">Add Product Type</a></li>
						<li <?php if($ssmenu2=="12") { ?> class="active" <?php } ?>><a href="viewprotype.php">View Product Type</a></li>		
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu2=="6") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Product Sub-category <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu2=="61") { ?> class="active" <?php } ?>><a href="addsubcategory.php">Add Product Sub-category</a></li>
						<li <?php if($ssmenu2=="62") { ?> class="active" <?php } ?>><a href="viewsubcategory.php">View Product Sub-category</a></li>
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu2=="2") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Products Master <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu2=="21") { ?> class="active" <?php } ?>><a href="addproduct.php">Add Product</a></li>
						<li <?php if($ssmenu2=="23") { ?> class="active" <?php } ?>><a href="viewproduct1.php">Inventory</a></li>
						<li <?php if($ssmenu2=="24") { ?> class="active" <?php } ?>><a href="viewproduct2.php">Product List</a></li>
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu2=="7") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Damage Master <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu2=="71") { ?> class="active" <?php } ?>><a href="adddamage.php">Add Damage Entry</a></li>
						<li <?php if($ssmenu2=="72") { ?> class="active" <?php } ?>><a href="viewdamage.php">View Damage Entry</a></li>
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu2=="5") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Tax Master <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu2=="51") { ?> class="active" <?php } ?>><a href="addtax.php">Add Tax</a></li>
						<li <?php if($ssmenu2=="52") { ?> class="active" <?php } ?>><a href="viewtax.php">View Tax</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu3) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Accounts Master </span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li class="dropdown-submenu <?php if($smenu3=="1") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Account Groups <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu3=="11") { ?> class="active" <?php } ?>><a href="addaccgroup.php">Create Group</a></li>
						<li <?php if($ssmenu3=="12") { ?> class="active" <?php } ?>><a href="viewaccgroup.php">View Groups</a></li>		
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu3=="2") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Ledger Accounts <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu3=="21") { ?> class="active" <?php } ?>><a href="addledger.php">Create Ledger</a></li>
						<li <?php if($ssmenu3=="22") { ?> class="active" <?php } ?>><a href="viewledger.php">View Ledger Accounts</a></li>		
					</ul>
				</li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu4) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Bank Master </span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li class="dropdown-submenu <?php if($smenu4=="1") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Bank Accounts <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu4=="11") { ?> class="active" <?php } ?>><a href="addbank.php">Add Account</a></li>
						<li <?php if($ssmenu4=="12") { ?> class="active" <?php } ?>><a href="viewbank.php">View Accounts</a></li>		
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu4=="2") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Deposit Entries <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu4=="21") { ?> class="active" <?php } ?>><a href="adddeposit.php">Add Entry</a></li>
						<li <?php if($ssmenu4=="22") { ?> class="active" <?php } ?>><a href="viewdeposit.php">View Details</a></li>		
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu4=="3") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Withdrawl Entries<b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu4=="31") { ?> class="active" <?php } ?>><a href="addwithdrawl.php">Add Entry</a></li>
						<li <?php if($ssmenu4=="32") { ?> class="active" <?php } ?>><a href="viewwithdrawl.php">View Details</a></li>		
					</ul>
				</li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu5) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Expense Master </span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				
				<li class="dropdown-submenu <?php if($smenu5=="2") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Expense Entry <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu5=="21") { ?> class="active" <?php } ?>><a href="addexpentry.php">Add Entry</a></li>
						<li <?php if($ssmenu5=="22") { ?> class="active" <?php } ?>><a href="viewexpentry.php">View Entries</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu6) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Income Master </span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				
				<li class="dropdown-submenu <?php if($smenu6=="2") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Income Entry <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu6=="21") { ?> class="active" <?php } ?>><a href="addincentry.php">Add Entry</a></li>
						<li <?php if($ssmenu6=="22") { ?> class="active" <?php } ?>><a href="viewincentry.php">View Entries</a></li>	
					</ul>
				</li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu7) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Contact Master </span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li <?php if($smenu7=="1") { ?> class="active" <?php } ?>><a href="addcontact.php">Add Contacts</a></li>
				<li <?php if($smenu7=="2") { ?> class="active" <?php } ?>><a href="viewcontact.php">View Contacts</a></li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu8) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Employee Master </span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li class="dropdown-submenu <?php if($smenu8=="2") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Manage Employees <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu8=="21") { ?> class="active" <?php } ?>><a href="addemp.php">Add Employee</a></li>
						<li <?php if($ssmenu8=="22") { ?> class="active" <?php } ?>><a href="viewemp.php">View Employees</a></li>
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu8=="3") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Attendance Master <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu8=="31") { ?> class="active" <?php } ?>><a href="markattendance.php">Mark Attendance</a></li>
						<li <?php if($ssmenu8=="32") { ?> class="active" <?php } ?>><a href="viewatt.php">Attendance Sheet</a></li>		
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu8=="5") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Advance Master <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu8=="51") { ?> class="active" <?php } ?>><a href="giveadvance.php">Give Advance</a></li>
						<li <?php if($ssmenu8=="52") { ?> class="active" <?php } ?>><a href="viewadvance.php">Advance Details</a></li>	
					</ul>
				</li>
				<li class="dropdown-submenu <?php if($smenu8=="4") { echo "active"; }?>"><a tabindex="-1" href="#" class="dropdown-submenu-toggle" ><span class="fa fa-sitemap"></span> Salary Master <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li <?php if($ssmenu8=="41") { ?> class="active" <?php } ?>><a href="paysalary.php">Pay Salary</a></li>
						<li <?php if($ssmenu8=="42") { ?> class="active" <?php } ?>><a href="viewsalary.php">Salary Details</a></li>		
					</ul>
				</li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu9) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Purchase Master</span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li <?php if($smenu9=="3") { ?> class="active" <?php } ?>><a href="addpurchase.php">Add Purchase Bill</a></li>
				<li <?php if($smenu9=="5") { ?> class="active" <?php } ?>><a href="viewpurchase.php">Purchase Details</a></li>
				<li <?php if($smenu9=="6") { ?> class="active" <?php } ?>><a href="viewpuritem.php">Purchase Details (Item Wise)</a></li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu15) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Job Master</span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li <?php if($smenu15=="1") { ?> class="active" <?php } ?>><a href="addmanu.php">Generate Manufacturing Job</a></li>
				<li <?php if($smenu15=="4") { ?> class="active" <?php } ?>><a href="addmanu1.php">Generate Service Job</a></li>
				<li <?php if($smenu15=="2") { ?> class="active" <?php } ?>><a href="viewmanu.php">Pending Jobs</a></li>
				<li <?php if($smenu15=="3") { ?> class="active" <?php } ?>><a href="viewmanu1.php">Completed Jobs</a></li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu10) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Sales Master</span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li <?php if($smenu10=="3") { ?> class="active" <?php } ?>><a href="addsales.php">Add Invoice</a></li>
				<li <?php if($smenu10=="5") { ?> class="active" <?php } ?>><a href="viewsales.php">Sales Details</a></li>
				<li <?php if($smenu10=="6") { ?> class="active" <?php } ?>><a href="viewsaleitem.php">Sales Details (Item Wise)</a></li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu11) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text">Pay Dues </span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li <?php if($smenu11=="1") { ?> class="active" <?php } ?>><a href="addpaydues.php">Pay Dues Entry</a></li>
				<li <?php if($smenu11=="2") { ?> class="active" <?php } ?>><a href="viewpaydues.php">View Details</a></li>
			</ul>
		</li>
		<li class="dropdown <?php if($menu12) { echo "active"; }?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fa fa-user"></span> <span class="xn-text"> Receipt </span><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li <?php if($smenu12=="1") { ?> class="active" <?php } ?>><a href="addreceipt.php">Receipt Entry</a></li>
				<li <?php if($smenu12=="2") { ?> class="active" <?php } ?>><a href="viewreceipt.php">View Details</a></li>
			</ul>
		</li>
		<?php
			}
		?>
		<li <?php if($menu1) { ?> class="active" <?php } ?>>
			<a href="admin_edit.php"><span class="fa fa-dashboard"></span> <span class="xn-text">Change Password</span></a>
		</li>
		<li>
			<a href="index.php"><span class="fa fa-dashboard"></span> <span class="xn-text">Logout</span></a>
		</li>
         </ul>
     </div><!-- /.navbar-collapse -->
  </div><!-- /container -->
</nav>
<script type="text/javascript" src="js/mob.js"></script>