<?php
ob_start();
session_start();
if (!isset($_SESSION['u_id'])) {
    header("Location: index");
    exit;
}
include_once("connect.php");

$u_id = (int)$_SESSION['u_id'];
$msg = "";
$msg_type = "";

// Handle profile update
// Handle profile update
if (isset($_POST['update_profile'])) 
{
    $name     = trim(mysqli_real_escape_string($con, $_POST['name'] ?? ''));
    $lastname = trim(mysqli_real_escape_string($con, $_POST['lastname'] ?? ''));
    $email    = trim(mysqli_real_escape_string($con, $_POST['email'] ?? ''));
    $mobile   = trim(mysqli_real_escape_string($con, $_POST['mobile'] ?? ''));
    $address  = trim(mysqli_real_escape_string($con, $_POST['address'] ?? ''));
    $pincode  = trim(mysqli_real_escape_string($con, $_POST['pincode'] ?? ''));
    $country = 101;

    $state_id = 0;
    $state = mysqli_prepare($con, "SELECT id FROM states WHERE name=? LIMIT 1");
    mysqli_stmt_bind_param($state, "s", $_POST['state']);
    mysqli_stmt_execute($state);
    $state_res = mysqli_stmt_get_result($state);
    if ($state_row = mysqli_fetch_assoc($state_res)) {
        $state_id = (int)$state_row['id'];
    }
    mysqli_stmt_close($state);

    // Find city ID
    $city_id = 0;
    $city = mysqli_prepare($con, "SELECT id FROM cities WHERE name=? LIMIT 1");
    mysqli_stmt_bind_param($city, "s", $_POST['city']);
    mysqli_stmt_execute($city);
    $city_res = mysqli_stmt_get_result($city);
    if ($city_row = mysqli_fetch_assoc($city_res)) {
        $city_id = (int)$city_row['id'];
    }
    mysqli_stmt_close($city);

    $state    = trim(mysqli_real_escape_string($con, $_POST['state'] ?? ''));
    $city     = trim(mysqli_real_escape_string($con, $_POST['city'] ?? ''));

    if (empty($name) || empty($email)) {

        $msg = "Name and Email are required.";
        $msg_type = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $msg = "Please enter a valid email address.";
        $msg_type = "danger";

    } else {

        // Check duplicate email
        $email_check = mysqli_query(
            $con,
            "SELECT u_id FROM users 
             WHERE email='$email' 
             AND u_id != '$u_id' 
             LIMIT 1"
        );

        if (mysqli_num_rows($email_check) > 0) {

            $msg = "This email address is already in use by another account.";
            $msg_type = "danger";

        } else {

            // Update users
            $upd_users = mysqli_query(
                $con,
                "UPDATE users SET
                    name='$name',
                    lastname='$lastname',
                    email='$email',
                    mobile='$mobile',
                    address='$address',
                    state='$state_id',
                    city='$city_id',
                    pincode='$pincode',
                    country='$country'
                 WHERE u_id='$u_id'"
            );

            // Update login details
            $upd_login = mysqli_query(
                $con,
                "UPDATE user_login_det SET
                    uname='$name',
                    email='$email'
                 WHERE u_id='$u_id'"
            );

            if ($upd_users) {

                $msg = "Profile updated successfully!";
                $msg_type = "success";

            } else {

                $msg = "Failed to update profile. Please try again.";
                $msg_type = "danger";
            }
        }
    }
}

// Fetch fresh user data after possible update
$u = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE u_id='$u_id'"));
$saved_state_name = '';
$saved_city_name  = '';
if (!empty($u['state']) && is_numeric($u['state'])) 
{
    $st_r = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM states WHERE id='" . (int)$u['state'] . "' LIMIT 1"));
    if ($st_r) $saved_state_name = $st_r['name'];
} elseif (!empty($u['state'])) {
    // Already a name string (saved by myprofile form)
    $saved_state_name = $u['state'];
}
if (!empty($u['city']) && is_numeric($u['city'])) {
    $ct_r = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM cities WHERE id='" . (int)$u['city'] . "' LIMIT 1"));
    if ($ct_r) $saved_city_name = $ct_r['name'];
} elseif (!empty($u['city'])) {
    // Already a name string
    $saved_city_name = $u['city'];
}
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>
<head>
    <meta charset="utf-8" />
    <title>My Profile | Ethic Design Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="View and update your profile on Ethic Design Studio">
    <link rel="shortcut icon" href="assets/images/k_favicon_32x.png">
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/flickity/flickity.min.css">
    <link rel="stylesheet" href="assets/libs/jarallax/jarallax.min.css">
    <link href="https://fonts.googleapis.com/css?family=Libre+Baskerville:300,300i,400,400i,500,500i&amp;display=swap" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/icons/font-icon.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    
    <style>
        .text-teal  { color: #0d9488 !important; }
        .bg-teal    { background-color: #e86e35 !important; }
        .btn-teal   { background-color: #e86e35; color: #fff !important; }
        .btn-teal:hover { background-color: #e86e35; color: #fff !important; }

        .profile-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #e86e35;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #fff;
            font-weight: 700;
            letter-spacing: 1px;
            flex-shrink: 0;
        }
        .profile-card {
            border: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            border-radius: 12px;
        }
        .profile-card .card-header {
            background: #fff;
            border-bottom: 2px solid #f0f0f0;
            border-radius: 12px 12px 0 0 !important;
            padding: 1.5rem;
        }
        .section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #aaa;
            margin-bottom: 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 0.2rem rgba(13,148,136,.15);
        }
        .info-item {
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .info-item:last-child { border-bottom: 0; }
        .info-label {
            font-size: 12px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 15px;
            color: #222;
            font-weight: 500;
        }
        .nav-pills .nav-link.active {
            background-color: #0d9488;
            color: #fff;
        }
        .nav-pills .nav-link {
            color: #0d9488;
            font-weight: 500;
            border-radius: 50px;
        }
    </style>
</head>

<body class="" x-data="{ showMenuScroll : false }">
<!--head banner-->
<?php include_once("header.php"); ?>

<div class="backdrop-shadow d-none"></div>
<div>
    <!-- Page Hero -->
     <?php
        $query = mysqli_query($con, "SELECT * FROM banner WHERE b_id = 12");
        if ($row = mysqli_fetch_assoc($query)) {
            $banner = $row['pic'];
        }
    ?>
    <div style="background-image: url('<?php echo $banner; ?>'); background-position: center; background-size: cover;" class="position-relative">
        <div class="position-absolute top-0 start-0 right-0 bottom-0 bg-dark w-100 opacity-50"></div>
        <div class="container">
            <div class="text-white text-center py-51 position-relative">
                <h1 class="fs-20 fw-medium mb-0">MY PROFILE</h1>
            </div>
        </div>
    </div>

    <section class="py-5 bg-light">
        <div class="container">

            <?php if (!empty($msg)): ?>
            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show mb-4 rounded-3" role="alert">
                <i class="pr pegk pe-7s-<?php echo $msg_type === 'success' ? 'check' : 'close-circle'; ?> me-2"></i>
                <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="row g-4">

                <!-- Left: Profile Summary Card -->
                <div class="col-lg-4">
                    <div class="card profile-card mb-4">
                        <div class="card-body text-center py-4 px-4">
                            <div class="profile-avatar mx-auto mb-3">
                                <?php echo strtoupper(mb_substr($u['name'], 0, 1)); ?>
                            </div>
                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($u['name'] . (!empty($u['lastname']) ? ' ' . $u['lastname'] : '')); ?></h5>
                            <p class="text-muted fs-14 mb-3"><?php echo htmlspecialchars($u['email']); ?></p>
                            <span class="badge bg-success rounded-pill px-3 py-2">Active Account</span>
                        </div>
                    </div>

                    <!-- Quick Links Card -->
                    <div class="card profile-card">
                        <div class="card-body p-3">
                            <p class="section-label mb-3">Quick Links</p>
                            <div class="d-grid gap-2">
                                <a href="order_history" class="btn btn-outline-secondary rounded-pill text-start ps-3 fs-14">
                                    <i class="pr pegk pe-7s-cart me-2"></i> My Orders
                                </a>
                                <a href="change_password" class="btn btn-outline-secondary rounded-pill text-start ps-3 fs-14">
                                    <i class="pr pegk pe-7s-lock me-2"></i> Change Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Edit Profile Form -->
                <div class="col-lg-8">
                    <div class="card profile-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-0">Profile Information</h5>
                                <small class="text-muted">Update your personal and address details</small>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <form action="myprofile" method="POST" id="profile-form" novalidate>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label class="fw-medium mb-1 fs-14" for="profile_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control rounded-pill"
                                               id="profile_name"
                                               name="name"
                                               value="<?php echo htmlspecialchars($u['name']); ?>"
                                               required
                                               placeholder="First name">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="fw-medium mb-1 fs-14" for="profile_lastname">Last Name</label>
                                        <input type="text"
                                               class="form-control rounded-pill"
                                               id="profile_lastname"
                                               name="lastname"
                                               value="<?php echo htmlspecialchars($u['lastname'] ?? ''); ?>"
                                               placeholder="Last name">
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label class="fw-medium mb-1 fs-14" for="profile_email">Email Address <span class="text-danger">*</span></label>
                                        <input type="email"
                                               class="form-control rounded-pill"
                                               id="profile_email"
                                               name="email"
                                               value="<?php echo htmlspecialchars($u['email']); ?>"
                                               required
                                               placeholder="Enter your email address">
                                        <div id="email-feedback" class="form-text text-danger d-none fs-13 mt-1 ps-2"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="fw-medium mb-1 fs-14" for="profile_mobile">Mobile Number</label>
                                        <input type="tel"
                                               class="form-control rounded-pill"
                                               id="profile_mobile"
                                               name="mobile"
                                               value="<?php echo htmlspecialchars($u['mobile'] ?? ''); ?>"
                                               placeholder="Enter your mobile number"
                                               maxlength="15">
                                    </div>
                                </div>

                                <hr class="my-2">
                                <p class="section-label mt-3">Delivery Address</p>

                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label class="fw-medium mb-1 fs-14" for="profile_address">Street Address</label>
                                        <textarea class="form-control"
                                                  id="profile_address"
                                                  name="address"
                                                  rows="2"
                                                  placeholder="House number and street name"
                                                  style="border-radius:16px;"><?php echo htmlspecialchars($u['address'] ?? ''); ?></textarea>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label class="fw-medium mb-1 fs-14" for="profile_state">State</label>
                                        <select class="form-select rounded-pill" id="profile_state" name="state">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="fw-medium mb-1 fs-14" for="profile_city">City / District</label>
                                        <select class="form-select rounded-pill" id="profile_city" name="city">
                                            <option value="">Select State First</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label class="fw-medium mb-1 fs-14" for="profile_pincode">Pincode</label>
                                        <input type="text"
                                               class="form-control rounded-pill"
                                               id="profile_pincode"
                                               name="pincode"
                                               value="<?php echo htmlspecialchars($u['pincode'] ?? ''); ?>"
                                               placeholder="6-digit PIN code"
                                               maxlength="6">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="fw-medium mb-1 fs-14" for="profile_country">Country</label>
                                        <select class="form-select rounded-pill" id="profile_country" name="country">
                                            <option value="101" selected>India</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex gap-3 mt-4">
                                    <button type="submit" name="update_profile" id="save-btn" class="btn btn-teal px-5 py-2 rounded-pill fw-medium">
                                        SAVE CHANGES
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div><!-- /row -->
        </div>
    </section>

    <?php include_once("footer.php"); ?>

    <a href="#" x-on:click.prevent="
          window.scrollTo({ top: 0, behavior: 'smooth' });
       " class="position-fixed bg-white border rounded d-flex align-items-center justify-content-center shadow" id="nt_backtop">
        <i class="pr pegk pe-7s-angle-up"></i>
    </a>

    <div class="backdrop-shadow d-none"></div>
</div>

<!-- custom header -->
<?php include_once("custom_header.php"); ?>

<!-- JAVASCRIPT -->
<script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/js/store.js"></script>
<script src="assets/libs/jarallax/jarallax.min.js"></script>
<script src="assets/libs/swiper/swiper-bundle.min.js"></script>
<script src="assets/libs/alpinejs/cdn.min.js"></script>
<script src="assets/libs/jquery-countdown/jquery.countdown.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/product-slider.init.js"></script>
<script src="assets/js/popup.js"></script>
<script src="assets/libs/flickity/flickity.pkgd.min.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/app.js"></script>

<script>
$(document).ready(function() {

    // ── State/City data ──────────────────────────────────────────────
    const stateCities = {"Andhra Pradesh":["Anantapur","Alluri Sitharama Raju","Anakapalli","Annamayya","Bapatla","Chittoor","East Godavari","Eluru","Guntur","Kakinada","Konaseema","Krishna","Kurnool","Markapuram","NTR","Nandyal","Nellore (SPSR Nellore)","Palnadu","Parvathipuram Manyam","Polavaram","Prakasam","Sri Sathya Sai","Srikakulam","Tirupati","Visakhapatnam","Vizianagaram","West Godavari","YSR Kadapa"],"Arunachal Pradesh":["Anjaw","Changlang","Dibang Valley","East Kameng","East Siang","Kamle","Kra Daadi","Kurung Kumey","Lepa Rada","Lohit","Longding","Lower Dibang Valley","Lower Siang","Lower Subansiri","Namsai","Pakke-Kessang","Papum Pare","Shi Yomi","Siang","Tawang","Tirap","Upper Siang","Upper Subansiri","West Kameng","West Siang"],"Assam":["Baksa","Barpeta","Biswanath","Bongaigaon","Cachar","Charaideo","Chirang","Darrang","Dhemaji","Dhubri","Dibrugarh","Dima Hasao","Goalpara","Golaghat","Hailakandi","Hojai","Jorhat","Kamrup","Kamrup Metropolitan","Karbi Anglong","Karimganj","Kokrajhar","Lakhimpur","Majuli","Morigaon","Nagaon","Nalbari","Sivasagar","Sonitpur","South Salmara-Mankachar","Tinsukia","Udalguri","West Karbi Anglong"],"Bihar":["Araria","Arwal","Aurangabad","Banka","Begusarai","Bhagalpur","Bhojpur","Buxar","Darbhanga","East Champaran","Gaya","Gopalganj","Jamui","Jehanabad","Kaimur","Katihar","Khagaria","Kishanganj","Lakhisarai","Madhepura","Madhubani","Munger","Muzaffarpur","Nalanda","Nawada","Patna","Purnia","Rohtas","Saharsa","Samastipur","Saran","Sheikhpura","Sheohar","Sitamarhi","Siwan","Supaul","Vaishali","West Champaran"],"Chhattisgarh":["Balod","Baloda Bazar","Balrampur","Bastar","Bemetara","Bijapur","Bilaspur","Dantewada","Dhamtari","Durg","Gariaband","Gaurela-Pendra-Marwahi","Janjgir-Champa","Jashpur","Kabirdham","Kanker","Khairagarh-Chhuikhadan-Gandai","Kondagaon","Korba","Koriya","Mahasamund","Manendragarh-Chirmiri-Bharatpur","Mohla-Manpur-Ambagarh Chowki","Mungeli","Narayanpur","Raigarh","Raipur","Rajnandgaon","Sakti","Sarangarh-Bilaigarh","Sukma","Surajpur","Surguja"],"Goa":["North Goa","South Goa"],"Gujarat":["Ahmedabad","Amreli","Anand","Aravalli","Banaskantha","Bharuch","Bhavnagar","Botad","Chhota Udaipur","Dahod","Dang","Devbhoomi Dwarka","Gandhinagar","Gir Somnath","Jamnagar","Junagadh","Kheda","Kutch","Mahisagar","Mehsana","Morbi","Narmada","Navsari","Panchmahal","Patan","Porbandar","Rajkot","Sabarkantha","Surat","Surendranagar","Tapi","Vadodara","Valsad"],"Haryana":["Ambala","Bhiwani","Charkhi Dadri","Faridabad","Fatehabad","Gurugram","Hisar","Jhajjar","Jind","Kaithal","Karnal","Kurukshetra","Mahendragarh","Nuh","Palwal","Panchkula","Panipat","Rewari","Rohtak","Sirsa","Sonipat","Yamunanagar"],"Himachal Pradesh":["Bilaspur","Chamba","Hamirpur","Kangra","Kinnaur","Kullu","Lahaul and Spiti","Mandi","Shimla","Sirmaur","Solan","Una"],"Jharkhand":["Bokaro","Chatra","Deoghar","Dhanbad","Dumka","East Singhbhum","Garhwa","Giridih","Godda","Gumla","Hazaribagh","Jamtara","Khunti","Koderma","Latehar","Lohardaga","Pakur","Palamu","Ramgarh","Ranchi","Sahebganj","Seraikela Kharsawan","Simdega","West Singhbhum"],"Karnataka":["Bagalkot","Ballari","Belagavi","Bengaluru Rural","Bengaluru Urban","Bidar","Chamarajanagar","Chikballapur","Chikkamagaluru","Chitradurga","Dakshina Kannada","Davanagere","Dharwad","Gadag","Hassan","Haveri","Kalaburagi","Kodagu","Kolar","Koppal","Mandya","Mysuru","Raichur","Ramanagara","Shivamogga","Tumakuru","Udupi","Uttara Kannada","Vijayanagara","Vijayapura","Yadgir"],"Kerala":["Alappuzha","Ernakulam","Idukki","Kannur","Kasaragod","Kollam","Kottayam","Kozhikode","Malappuram","Palakkad","Pathanamthitta","Thiruvananthapuram","Thrissur","Wayanad"],"Madhya Pradesh":["Agar Malwa","Alirajpur","Anuppur","Ashoknagar","Balaghat","Barwani","Betul","Bhind","Bhopal","Burhanpur","Chhatarpur","Chhindwara","Damoh","Datia","Dewas","Dhar","Dindori","Guna","Gwalior","Harda","Indore","Jabalpur","Jhabua","Katni","Khandwa","Khargone","Maihar","Mandla","Mandsaur","Mauganj","Morena","Narsinghpur","Neemuch","Niwari","Panna","Pandhurna","Raisen","Rajgarh","Ratlam","Rewa","Sagar","Satna","Sehore","Seoni","Shahdol","Shajapur","Sheopur","Shivpuri","Sidhi","Singrauli","Tikamgarh","Ujjain","Umaria","Vidisha"],"Maharashtra":["Ahmednagar","Akola","Amravati","Chhatrapati Sambhajinagar","Beed","Bhandara","Buldhana","Chandrapur","Dharashiv","Dhule","Gadchiroli","Gondia","Hingoli","Jalgaon","Jalna","Kolhapur","Latur","Mumbai City","Mumbai Suburban","Nagpur","Nanded","Nandurbar","Nashik","Palghar","Parbhani","Pune","Raigad","Ratnagiri","Sangli","Satara","Sindhudurg","Solapur","Thane","Wardha","Washim","Yavatmal"],"Manipur":["Bishnupur","Chandel","Churachandpur","Imphal East","Imphal West","Jiribam","Kakching","Kamjong","Kangpokpi","Noney","Pherzawl","Senapati","Tamenglong","Tengnoupal","Thoubal","Ukhrul"],"Meghalaya":["East Garo Hills","West Garo Hills","South Garo Hills","North Garo Hills","South West Garo Hills","East Khasi Hills","West Khasi Hills","South West Khasi Hills","Eastern West Khasi Hills","Ri Bhoi","East Jaintia Hills","West Jaintia Hills"],"Mizoram":["Aizawl","Champhai","Hnahthial","Khawzawl","Kolasib","Lawngtlai","Lunglei","Mamit","Saitual","Saiha","Serchhip"],"Nagaland":["Chümoukedima","Dimapur","Kiphire","Kohima","Longleng","Mokokchung","Mon","Niuland","Noklak","Peren","Phek","Shamator","Tseminyu","Tuensang","Wokha","Zunheboto"],"Odisha":["Angul","Balangir","Balasore","Bargarh","Bhadrak","Boudh","Cuttack","Deogarh","Dhenkanal","Gajapati","Ganjam","Jagatsinghpur","Jajpur","Jharsuguda","Kalahandi","Kandhamal","Kendrapara","Kendujhar","Khordha","Koraput","Malkangiri","Mayurbhanj","Nabarangpur","Nayagarh","Nuapada","Puri","Rayagada","Sambalpur","Subarnapur","Sundargarh"],"Punjab":["Amritsar","Barnala","Bathinda","Faridkot","Fatehgarh Sahib","Fazilka","Ferozepur","Gurdaspur","Hoshiarpur","Jalandhar","Kapurthala","Ludhiana","Malerkotla","Mansa","Moga","Sri Muktsar Sahib","Pathankot","Patiala","Rupnagar","S.A.S. Nagar (Mohali)","Sangrur","Shaheed Bhagat Singh Nagar","Tarn Taran"],"Rajasthan":["Ajmer","Alwar","Balotra","Banswara","Baran","Barmer","Beawar","Bharatpur","Bhilwara","Bikaner","Bundi","Chittorgarh","Churu","Dausa","Deeg","Didwana-Kuchaman","Dholpur","Dungarpur","Hanumangarh","Jaipur","Jaisalmer","Jalore","Jhalawar","Jhunjhunu","Jodhpur","Karauli","Khairthal-Tijara","Kota","Kotputli-Behror","Nagaur","Pali","Phalodi","Pratapgarh","Rajsamand","Salumbar","Sawai Madhopur","Sikar","Sirohi","Sri Ganganagar","Tonk","Udaipur"],"Sikkim":["East Sikkim (Gangtok)","West Sikkim (Gyalshing)","North Sikkim (Mangan)","South Sikkim (Namchi)","Pakyong","Soreng"],"Tamil Nadu":["Ariyalur","Chengalpattu","Chennai","Coimbatore","Cuddalore","Dharmapuri","Dindigul","Erode","Kallakurichi","Kanchipuram","Kanyakumari","Karur","Krishnagiri","Madurai","Mayiladuthurai","Nagapattinam","Namakkal","Nilgiris","Perambalur","Pudukkottai","Ramanathapuram","Ranipet","Salem","Sivaganga","Tenkasi","Thanjavur","Theni","Thoothukudi","Tiruchirappalli","Tirunelveli","Tirupathur","Tiruppur","Tiruvallur","Tiruvannamalai","Tiruvarur","Vellore","Viluppuram","Virudhunagar"],"Telangana":["Adilabad","Bhadradri Kothagudem","Hyderabad","Jagtial","Jangaon","Jayashankar Bhupalpally","Jogulamba Gadwal","Kamareddy","Karimnagar","Khammam","Komaram Bheem Asifabad","Mahabubabad","Mahabubnagar","Mancherial","Medak","Medchal-Malkajgiri","Mulugu","Nagarkurnool","Nalgonda","Narayanpet","Nirmal","Nizamabad","Peddapalli","Rajanna Sircilla","Rangareddy","Sangareddy","Siddipet","Suryapet","Vikarabad","Wanaparthy","Warangal","Hanumakonda","Yadadri Bhuvanagiri"],"Tripura":["Dhalai","Gomati","Khowai","North Tripura","Sepahijala","South Tripura","Unakoti","West Tripura"],"Uttar Pradesh":["Agra","Aligarh","Ambedkar Nagar","Amethi","Amroha","Auraiya","Ayodhya","Azamgarh","Baghpat","Bahraich","Ballia","Balrampur","Banda","Barabanki","Bareilly","Basti","Bhadohi","Bijnor","Budaun","Bulandshahr","Chandauli","Chitrakoot","Deoria","Etah","Etawah","Farrukhabad","Fatehpur","Firozabad","Gautam Buddha Nagar","Ghaziabad","Ghazipur","Gonda","Gorakhpur","Hamirpur","Hapur","Hardoi","Hathras","Jalaun","Jaunpur","Jhansi","Kannauj","Kanpur Dehat","Kanpur Nagar","Kasganj","Kaushambi","Kheri (Lakhimpur Kheri)","Kushinagar","Lalitpur","Lucknow","Maharajganj","Mahoba","Mainpuri","Mathura","Mau","Meerut","Mirzapur","Moradabad","Muzaffarnagar","Pilibhit","Pratapgarh","Prayagraj","Raebareli","Rampur","Saharanpur","Sambhal","Sant Kabir Nagar","Shahjahanpur","Shamli","Shravasti","Siddharthnagar","Sitapur","Sonbhadra","Sultanpur","Unnao","Varanasi"],"Uttarakhand":["Almora","Bageshwar","Chamoli","Champawat","Dehradun","Haridwar","Nainital","Pauri Garhwal","Pithoragarh","Rudraprayag","Tehri Garhwal","Udham Singh Nagar","Uttarkashi"],"West Bengal":["Alipurduar","Bankura","Birbhum","Cooch Behar","Dakshin Dinajpur","Darjeeling","Hooghly","Howrah","Jalpaiguri","Jhargram","Kalimpong","Kolkata","Malda","Murshidabad","Nadia","North 24 Parganas","Paschim Bardhaman","Paschim Medinipur","Purba Bardhaman","Purba Medinipur","Purulia","South 24 Parganas","Uttar Dinajpur"],"Andaman and Nicobar Islands":["Nicobar","North and Middle Andaman","South Andaman"],"Chandigarh":["Chandigarh"],"Dadra and Nagar Haveli and Daman and Diu":["Dadra and Nagar Haveli","Daman","Diu"],"Delhi":["Central Delhi","East Delhi","New Delhi","North Delhi","North East Delhi","North West Delhi","Shahdara","South Delhi","South East Delhi","South West Delhi","West Delhi"],"Jammu and Kashmir":["Anantnag","Bandipora","Baramulla","Budgam","Doda","Ganderbal","Jammu","Kathua","Kishtwar","Kulgam","Kupwara","Poonch","Pulwama","Rajouri","Ramban","Reasi","Samba","Shopian","Srinagar","Udhampur"],"Ladakh":["Leh","Kargil","Zanskar","Drass","Sham","Nubra","Changthang"],"Lakshadweep":["Lakshadweep"],"Puducherry":["Puducherry","Karaikal","Mahe","Yanam"]};

    // Saved values from DB (for pre-selection on page load)
    const savedCountry = <?php echo json_encode($u['country'] ?? '101'); ?>;
    const savedState = <?php echo json_encode($saved_state_name); ?>;
    const savedCity  = <?php echo json_encode($saved_city_name);  ?>;
    const countrySelect = document.getElementById('profile_country');
    const stateSelect = document.getElementById('profile_state');
    const citySelect  = document.getElementById('profile_city');

    // Select country
    if (countrySelect) {
            countrySelect.value = String(savedCountry);
        }

    // Populate states
    for (const state in stateCities) 
    {
        const opt = document.createElement('option');
        opt.value = state;
        opt.textContent = state;
        if (state.trim().toLowerCase() ===
            String(savedState).trim().toLowerCase()
        ) {
            opt.selected = true;
        }
        stateSelect.appendChild(opt);
    }

    // Populate cities for the currently-selected state
    function populateCities(selectedState, selectedCity) 
    {
        citySelect.innerHTML = '<option value="">Select City</option>';
        const matchedState = Object.keys(stateCities).find(function(state) {
            return state.trim().toLowerCase() ===
                String(selectedState).trim().toLowerCase();
        });
        if (!matchedState) {
            return;
        }
        stateCities[matchedState].forEach(function(city) {
            const opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            if (
                city.trim().toLowerCase() ===
                String(selectedCity).trim().toLowerCase()
            ) {
                opt.selected = true;
            }
            citySelect.appendChild(opt);
        });
    }

    if (savedState) {
        populateCities(savedState, savedCity);
    }

    stateSelect.addEventListener('change', function() {
        populateCities(this.value, '');
    });

    // On page load pre-fill cities
    if (savedState) populateCities(savedState, savedCity);

    stateSelect.addEventListener('change', function() {
        populateCities(this.value, '');
    });

});
</script>
</body>
</html>