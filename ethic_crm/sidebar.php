<?php
include_once("connect.php");
if (!isset($_SESSION['account']) || !isset($_SESSION['Ethic'])) {
	header("Location: index.php");
	die;
}
?>
<div class="page-sidebar">

	<!-- code for mobile menu -->
	<?php include_once("mobmenu.php"); ?>
	<!-- code by mobile menu -->

	<!-- START X-NAVIGATION -->
	<ul class="x-navigation x-navigation-open hidden-xs hidden-sm hidden-md">
		<li class="xn-logo">
			<a href="index.php">Ethic Design Studios</a>
			<a href="#" class="x-navigation-control"></a>
		</li>
		<li class="xn-profile">
			<div class="profile">
				<div class="profile-image">
					<img src="assets/images/users/admin.jpg" alt="Super Admin" />
				</div>
				<div class="profile-data">
					<div class="profile-data-name" style="text-transform:capitalize;">
						<?php
						echo "Super Admin";
						?>
					</div>
				</div>
				<div class="profile-controls">
					<a href="admin_edit.php" title="Edit Profile" class="profile-control-left"><span class="fa fa-wrench"></span></a>
				</div>
			</div>
		</li>
		<li class="xn-title">Navigation</li>
		<?php
		if ($_SESSION['account'] == "0") {
		?>
			<li <?php if ($menu1) { ?> class="active" <?php } ?>>
				<a href="dashboard.php"><span class="fa fa-dashboard"></span> <span class="xn-text">Dashboard</span></a>
			</li>
			<li class="xn-openable <?php if ($menu2) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-cogs"></span> <span class="xn-text">Masters</span></a>
				<ul>
				    <li class="xn-openable <?php if ($smenu2 == "8") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-cube"></span> Color Master</a>
						<ul>
							<li <?php if ($ssmenu2 == "81") { ?> class="active" <?php } ?>><a href="addcolor.php">Add Color</a></li>
							<li <?php if ($ssmenu2 == "82") { ?> class="active" <?php } ?>><a href="viewcolor.php">View Color</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu2 == "1") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-cube"></span> Product Type Master</a>
						<ul>
							<li <?php if ($ssmenu2 == "11") { ?> class="active" <?php } ?>><a href="addprotype.php">Add Product Type</a></li>
							<li <?php if ($ssmenu2 == "12") { ?> class="active" <?php } ?>><a href="viewprotype.php">View Product Type</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu2 == "6") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-folder-open"></span> Product Sub-category</a>
						<ul>
							<li <?php if ($ssmenu2 == "61") { ?> class="active" <?php } ?>><a href="addsubcategory.php">Add Product Sub-category</a></li>
							<li <?php if ($ssmenu2 == "62") { ?> class="active" <?php } ?>><a href="viewsubcategory.php">View Product Sub-category</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu2 == "2") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-tags"></span> Products Master</a>
						<ul>
							<li <?php if ($ssmenu2 == "21") { ?> class="active" <?php } ?>><a href="addproduct.php">Add Product</a></li>
							<!-- <li <?php //if($ssmenu2=="22") { 
										?> class="active" <?php //} 
															?>><a href="viewproduct.php">Inventory (Group Wise)</a></li> -->
							<li <?php if ($ssmenu2 == "23") { ?> class="active" <?php } ?>><a href="viewproduct1.php">Inventory</a></li>
							<li <?php if ($ssmenu2 == "24") { ?> class="active" <?php } ?>><a href="viewproduct2.php">Product List</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu2 == "7") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-exclamation-triangle"></span> Damage Master</a>
						<ul>
							<li <?php if ($ssmenu2 == "71") { ?> class="active" <?php } ?>><a href="adddamage.php">Add Damage Entry</a></li>
							<li <?php if ($ssmenu2 == "72") { ?> class="active" <?php } ?>><a href="viewdamage.php">View Damage Entry</a></li>
						</ul>
					</li>

					<li class="xn-openable <?php if ($smenu2 == "5") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-balance-scale"></span> Tax Master</a>
						<ul>
							<li <?php if ($ssmenu2 == "51") { ?> class="active" <?php } ?>><a href="addtax.php">Add Tax</a></li>
							<li <?php if ($ssmenu2 == "52") { ?> class="active" <?php } ?>><a href="viewtax.php">View Tax</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu3) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-book"></span> <span class="xn-text">Accounts Master</span></a>
				<ul>
					<li class="xn-openable <?php if ($smenu3 == "1") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-th-large"></span> Account Groups</a>
						<ul>
							<li <?php if ($ssmenu3 == "11") { ?> class="active" <?php } ?>><a href="addaccgroup.php">Create Group</a></li>
							<li <?php if ($ssmenu3 == "12") { ?> class="active" <?php } ?>><a href="viewaccgroup.php">View Groups</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu3 == "2") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-file"></span> Ledger Accounts</a>
						<ul>
							<li <?php if ($ssmenu3 == "21") { ?> class="active" <?php } ?>><a href="addledger.php">Create Ledger</a></li>
							<li <?php if ($ssmenu3 == "22") { ?> class="active" <?php } ?>><a href="viewledger.php">View Ledger Accounts</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu4) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-university"></span> <span class="xn-text">Bank Master</span></a>
				<ul>
					<li class="xn-openable <?php if ($smenu4 == "1") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-credit-card"></span> Bank Accounts</a>
						<ul>
							<li <?php if ($ssmenu4 == "11") { ?> class="active" <?php } ?>><a href="addbank.php">Add Account</a></li>
							<li <?php if ($ssmenu4 == "12") { ?> class="active" <?php } ?>><a href="viewbank.php">View Accounts</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu4 == "2") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-plus-circle"></span> Deposit Entries</a>
						<ul>
							<li <?php if ($ssmenu4 == "21") { ?> class="active" <?php } ?>><a href="adddeposit.php">Add Entry</a></li>
							<li <?php if ($ssmenu4 == "22") { ?> class="active" <?php } ?>><a href="viewdeposit.php">View Details</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu4 == "3") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-minus-circle"></span> Withdrawl Entries</a>
						<ul>
							<li <?php if ($ssmenu4 == "31") { ?> class="active" <?php } ?>><a href="addwithdrawl.php">Add Entry</a></li>
							<li <?php if ($ssmenu4 == "32") { ?> class="active" <?php } ?>><a href="viewwithdrawl.php">View Details</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu5) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-calculator"></span> <span class="xn-text">Expense Master</span></a>
				<ul>
					<li class="xn-openable <?php if ($smenu5 == "2") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-pencil"></span> Expense Entry</a>
						<ul>
							<li <?php if ($ssmenu5 == "21") { ?> class="active" <?php } ?>><a href="addexpentry.php">Add Entry</a></li>
							<li <?php if ($ssmenu5 == "22") { ?> class="active" <?php } ?>><a href="viewexpentry.php">View Entries</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu6) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-money"></span> <span class="xn-text">Income Master</span></a>
				<ul>
					<li class="xn-openable <?php if ($smenu6 == "2") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-pencil"></span> Income Entry</a>
						<ul>
							<li <?php if ($ssmenu6 == "21") { ?> class="active" <?php } ?>><a href="addincentry.php">Add Entry</a></li>
							<li <?php if ($ssmenu6 == "22") { ?> class="active" <?php } ?>><a href="viewincentry.php">View Entries</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu7) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-link"></span> <span class="xn-text">Contact Master</span></a>
				<ul>
					<li <?php if ($smenu7 == "1") { ?> class="active" <?php } ?>><a href="addcontact.php">Add Contacts</a></li>
					<li <?php if ($smenu7 == "2") { ?> class="active" <?php } ?>><a href="viewcontact.php">View Contacts</a></li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu8) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-users"></span> <span class="xn-text">Employee Master</span></a>
				<ul>
					<li class="xn-openable <?php if ($smenu8 == "2") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-users"></span> Manage Employees</a>
						<ul>
							<li <?php if ($ssmenu8 == "21") { ?> class="active" <?php } ?>><a href="addemp.php">Add Employee</a></li>
							<li <?php if ($ssmenu8 == "22") { ?> class="active" <?php } ?>><a href="viewemp.php">View Employees</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu8 == "3") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-clock"></span>Attendance Master</a>
						<ul>
							<li <?php if ($ssmenu8 == "31") { ?> class="active" <?php } ?>><a href="markattendance.php">Mark Attendance</a></li>
							<li <?php if ($ssmenu8 == "32") { ?> class="active" <?php } ?>><a href="viewatt.php">Attendance Sheet</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu8 == "5") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-hand-holding-usd"></span>Advance Master</a>
						<ul>
							<li <?php if ($ssmenu8 == "51") { ?> class="active" <?php } ?>><a href="giveadvance.php">Give Advance</a></li>
							<li <?php if ($ssmenu8 == "52") { ?> class="active" <?php } ?>><a href="viewadvance.php">Advance Details</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu8 == "4") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-wallet"></span>Salary Master</a>
						<ul>
							<li <?php if ($ssmenu8 == "41") { ?> class="active" <?php } ?>><a href="paysalary.php">Pay Salary</a></li>
							<li <?php if ($ssmenu8 == "42") { ?> class="active" <?php } ?>><a href="viewsalary.php">Salary Details</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu9) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-cart-plus"></span> <span class="xn-text">Purchase Master</span></a>
				<ul>
					<li <?php if ($smenu9 == "3") { ?> class="active" <?php } ?>><a href="addpurchase.php">Add Purchase Bill</a></li>
					<li <?php if ($smenu9 == "5") { ?> class="active" <?php } ?>><a href="viewpurchase.php">Purchase Details</a></li>
					<li <?php if ($smenu9 == "6") { ?> class="active" <?php } ?>><a href="viewpuritem.php">Purchase Details (Item Wise)</a></li>
					<li <?php if ($smenu9 == "7") { ?> class="active" <?php } ?>><a href="purreturn.php">Purchase Return</a></li>
					<li <?php if ($smenu9 == "8") { ?> class="active" <?php } ?>><a href="viewpurreturn.php">Purchase Return Details</a></li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu16) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-tags"></span> <span class="xn-text">Users & Orders</span></a>
				<ul>
					<li <?php if ($smenu16 == "1") { ?> class="active" <?php } ?>><a href="viewuser.php">Users</a></li>
					<li <?php if ($smenu16 == "2") { ?> class="active" <?php } ?>><a href="vieworder.php">Orders</a></li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu15) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-briefcase"></span> <span class="xn-text">Job Master</span></a>
				<ul>
					<li <?php if ($smenu15 == "1") { ?> class="active" <?php } ?>><a href="addmanu.php">Generate Manufacturing Job</a></li>
					<li <?php if ($smenu15 == "4") { ?> class="active" <?php } ?>><a href="addmanu1.php">Generate Service Job</a></li>
					<li <?php if ($smenu15 == "2") { ?> class="active" <?php } ?>><a href="viewmanu.php">Pending Jobs</a></li>
					<li <?php if ($smenu15 == "3") { ?> class="active" <?php } ?>><a href="viewmanu1.php">Completed Jobs</a></li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu10) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-tags"></span> <span class="xn-text">Sales Master</span></a>
				<ul>
					<li <?php if ($smenu10 == "3") { ?> class="active" <?php } ?>><a href="addsales.php">Add Invoice</a></li>
					<li <?php if ($smenu10 == "5") { ?> class="active" <?php } ?>><a href="viewsales.php">Sales Details</a></li>
					<li <?php if ($smenu10 == "6") { ?> class="active" <?php } ?>><a href="viewsaleitem.php">Sales Details (Item Wise)</a></li>
					<li <?php if ($smenu10 == "7") { ?> class="active" <?php } ?>><a href="salereturn.php">Sales Return</a></li>
					<li <?php if ($smenu10 == "8") { ?> class="active" <?php } ?>><a href="viewsalereturn.php">Sales Return Details</a></li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu11) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-credit-card"></span> <span class="xn-text">Pay Dues</span></a>
				<ul>
					<li <?php if ($smenu11 == "1") { ?> class="active" <?php } ?>><a href="addpaydues.php">Pay Dues Entry</a></li>
					<li <?php if ($smenu11 == "2") { ?> class="active" <?php } ?>><a href="viewpaydues.php">View Details</a></li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu12) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-file"></span> <span class="xn-text">Receipt</span></a>
				<ul>
					<li <?php if ($smenu12 == "1") { ?> class="active" <?php } ?>><a href="addreceipt.php">Receipt Entry</a></li>
					<li <?php if ($smenu12 == "2") { ?> class="active" <?php } ?>><a href="viewreceipt.php">View Details</a></li>
				</ul>
			</li>
			<li class="xn-openable <?php if ($menu13) {
										echo "active";
									} ?>">
				<a href="#"><span class="fa fa-globe"></span> <span class="xn-text">Website Master</span></a>
				<ul>
					<li class="xn-openable <?php if ($smenu13 == "1") {
												echo "active";
											} ?>"><a href="#"><span class="	fa fa-home"></span>Home</a>
						<ul>
							<li <?php if ($ssmenu13 == "1") { ?> class="active" <?php } ?>><a href="upslider.php">Slider</a></li>
							<li <?php if ($ssmenu13 == "2") { ?> class="active" <?php } ?>><a href="uphomebanner.php">Banners</a></li>
							<li class="xn-openable <?php if ($ssmenu13 == "3") echo "active"; ?>">
								<a href="#"><span class="fa fa-bell"></span> FAQ's</a>
								<ul>
									<li <?php if ($sssmenu13 == "31") echo 'class="active"'; ?>><a href="addfaq.php">Add FAQ</a></li>
									<li <?php if ($sssmenu13 == "32") echo 'class="active"'; ?>><a href="delfaq.php">View FAQ</a></li>
								</ul>
							</li>
							<li <?php if ($ssmenu13 == "4") { ?> class="active" <?php } ?>><a href="upbreadcrumbs.php">Update Page Banner</a></li>
							<li <?php if ($ssmenu13 == "5") { ?> class="active" <?php } ?>><a href="upkeypoints.php">Update Key Points</a></li>
							<li <?php if ($ssmenu13 == "6") { ?> class="active" <?php } ?>><a href="upcharges.php">Update charges</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu13 == "2") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-tags"></span>Product Related</a>
						<ul>
							<li <?php if ($ssmenu13 == "2") { ?> class="active" <?php } ?>><a href="upcollection.php">Collection</a></li>
							<li <?php if ($ssmenu13 == "3") { ?> class="active" <?php } ?>><a href="upmaterialtype.php">Material</a></li>
							<li <?php if ($ssmenu13 == "4") { ?> class="active" <?php } ?>><a href="updimension.php">Dimension</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu13 == "3") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-file"></span>Policies</a>
						<ul>
							<li <?php if ($ssmenu13 == "1") { ?> class="active" <?php } ?>><a href="uppolicy_terms.php">Terms & Conditions</a></li>
							<li <?php if ($ssmenu13 == "2") { ?> class="active" <?php } ?>><a href="uppolicyreturn_refund.php">Return & Refund</a></li>
							<li <?php if ($ssmenu13 == "3") { ?> class="active" <?php } ?>><a href="uppolicy_privacy.php">Privacy</a></li>
							<li <?php if ($ssmenu13 == "4") { ?> class="active" <?php } ?>><a href="uppolicy_cancellation.php">Cancellation</a></li>
							<li <?php if ($ssmenu13 == "5") { ?> class="active" <?php } ?>><a href="uppolicy_payment.php">Payment</a></li>
							<li <?php if ($ssmenu13 == "6") { ?> class="active" <?php } ?>><a href="uppolicy_shipping.php">Shipping</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu13 == "4") {
												echo "active";
											} ?>"><a href="#"><span class="	fa fa-cog"></span>General</a>
						<ul>
							<li <?php if ($ssmenu13 == "9") { ?> class="active" <?php } ?>><a href="upabout.php">About</a></li>
							<li <?php if ($ssmenu13 == "8") { ?> class="active" <?php } ?>><a href="upcontact_info.php">Company Details</a></li>
							<li <?php if ($ssmenu13 == "1") { ?> class="active" <?php } ?>><a href="uppopup.php">Popup</a></li>
							<li <?php if ($ssmenu13 == "5") { ?> class="active" <?php } ?>><a href="uptestimonial.php">Testimonial</a></li>
							<li <?php if ($ssmenu13 == "6") { ?> class="active" <?php } ?>><a href="upreview.php">Review</a></li>
							<li <?php if ($ssmenu13 == "7") { ?> class="active" <?php } ?>><a href="viewemail.php">Email</a></li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu13 == "5") echo "active"; ?>">
						<a href="#"><span class="fa fa-sitemap"></span> Stores & Events</a>
						<ul>
							<!-- Store -->
							<li class="xn-openable <?php if ($ssmenu13 == "1") echo "active"; ?>">
								<a href="#"><span class="fa fa-map-marker"></span> Store</a>
								<ul>
									<li <?php if ($sssmenu13 == "1") echo 'class="active"'; ?>><a href="addstore.php">Add Store</a></li>
									<li <?php if ($sssmenu13 == "2") echo 'class="active"'; ?>><a href="viewstore.php">View Store</a></li>
								</ul>
							</li>

							<!-- Event -->
							<li class="xn-openable <?php if ($ssmenu13 == "2") echo "active"; ?>">
								<a href="#"><span class="fa fa-bell"></span> Event</a>
								<ul>
									<li <?php if ($sssmenu13 == "1") echo 'class="active"'; ?>><a href="addevent.php">Add Event</a></li>
									<li <?php if ($sssmenu13 == "2") echo 'class="active"'; ?>><a href="viewevent.php">View Event</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li class="xn-openable <?php if ($smenu13 == "6") {
												echo "active";
											} ?>"><a href="#"><span class="fa fa-tags"></span>Order Details</a>
						<ul>
							<li <?php if ($ssmenu13 == "1") { ?> class="active" <?php } ?>><a href="vieworderdetails.php">Order Details</a></li>
						</ul>
					</li>

				</ul>
			</li>
		<?php
		}
		?>
	</ul>
	<!-- END X-NAVIGATION -->
</div>