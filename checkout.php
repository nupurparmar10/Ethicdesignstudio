<?php
ob_start();
session_start();
if (!isset($_SESSION['u_id'])) 
{
    header("Location: index");
    exit;
}
include_once("connect.php");
$apiKey = 'rzp_test_SuMLK9R3IZJYBP';
$apiSecret = 'QKXiEauOgHvuqcG56oIPRd0k';
if(!isset($_SESSION['shiprocket_token']))
{
    $token = get_valid_shiprocket_token();
    if($token) {
        $_SESSION['shiprocket_token'] = $token;
    }
}
$u=mysqli_fetch_assoc(mysqli_query($con,"select * from users where u_id='$_SESSION[u_id]'"));

// Resolve stored numeric state/city IDs to name strings for JS preselection
$saved_state_name = '';
$saved_city_name  = '';
if (!empty($u['state']) && is_numeric($u['state'])) {
    $st_r = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM states WHERE id='" . (int)$u['state'] . "' LIMIT 1"));
    if ($st_r) $saved_state_name = $st_r['name'];
} elseif (!empty($u['state'])) {
    $saved_state_name = $u['state'];
}
if (!empty($u['city']) && is_numeric($u['city'])) {
    $ct_r = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM cities WHERE id='" . (int)$u['city'] . "' LIMIT 1"));
    if ($ct_r) $saved_city_name = $ct_r['name'];
} elseif (!empty($u['city'])) {
    $saved_city_name = $u['city'];
}
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>
<head>

    <meta charset="utf-8" />
    <title>Ethic Design Studio </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="Ethic Design Studio" name="description" />
    <meta content="Ethic Design Studio" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/k_favicon_32x.png">
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/flickity/flickity.min.css">
    <link rel="stylesheet" href="assets/libs/jarallax/jarallax.min.css">
    <link href="https://fonts.googleapis.com/css?family=Libre+Baskerville:300,300i,400,400i,500,500i&amp;display=swap" rel="stylesheet">
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/icons/font-icon.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <script>
    function formatCurrency(amount)
    {
        return '₹ ' + (parseFloat(amount) || 0).toFixed(2);
    }

    function getWeight(done)
    {
        var shipping_cost = parseFloat(document.getElementById('shipping_cost').value) || 0;
        updateCheckoutTotals(shipping_cost);
        var callback = (typeof done === 'function') ? done : function(){};
        callback();
    };

    function updateCheckoutTotals(shipping_cost)
    {
        var rawSubElem = document.getElementById('raw_subtotal');
        var subtotal = rawSubElem ? (parseFloat(rawSubElem.value) || 0) : (parseFloat(document.getElementById('grand_total').value) || 0);
        var conveniencePercentInput = document.getElementById('convenience_charge');
        var conveniencePercent = conveniencePercentInput ? (parseFloat(conveniencePercentInput.value) || 0) : 0;
        shipping_cost = parseFloat(shipping_cost) || 0;
        var convenienceBase = subtotal + shipping_cost;
        var convenienceCharge = (convenienceBase * conveniencePercent) / 100;
        var final_total = convenienceBase + convenienceCharge;

        document.getElementById('subtotal').innerHTML = formatCurrency(subtotal);
        document.getElementById('sc').innerHTML = formatCurrency(shipping_cost);
        document.getElementById('shipping_cost').value = shipping_cost.toFixed(2);
        if(document.getElementById('cc'))
        {
            document.getElementById('cc').innerHTML = formatCurrency(convenienceCharge);
        }
        if(document.getElementById('convenience_charge_amount'))
        {
            document.getElementById('convenience_charge_amount').value = convenienceCharge.toFixed(2);
        }
        if(document.getElementById('grand_total'))
        {
            document.getElementById('grand_total').value = Math.round(final_total);
        }
        document.getElementById('gt').innerHTML = formatCurrency(Math.round(final_total));
    }
    </script>
</head>

<body class="" x-data="{ showMenuScroll : false }">
<!--head banner-->
<?php include_once("header.php"); ?>

<div class="backdrop-shadow d-none"></div>
<div>
    <!-- main slide -->
     <?php
        $query = mysqli_query($con, "SELECT * FROM banner WHERE b_id = 15");
        if ($row = mysqli_fetch_assoc($query)) {
            $banner = $row['pic'];
        }
    ?>
    <div style="background-image: url('<?php echo $banner; ?>'); background-position: center; background-size: cover;" class="position-relative">
        <div class="position-absolute top-0 start-0 right-0 bottom-0 bg-dark w-100 opacity-50"></div>
        <div class=" container">
            <div class="text-white text-center py-51 position-relative">
                <h4 class="fs-20 fw-medium">
                    CHECKOUT</h4>
            </div>
        </div>
    </div>
    <section>
        <div class="container">
            <div class="row my-5">
                <div class="col-md-6 col-lg-7">
                    <h3 class="border-bottom pb-3 mb-0">Billing details</h3>
                    <div class="filter-title mb-4 bg-teal" style="width: 134px;"></div>
                    <form id="checkout-form" action="checkout" method="post" class="form-comman">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="fw-medium mb-2" for="fname">First name *</label>
                                <input class="form-control rounded-pill" id="fname" name="fname" type="text" required value="<?php echo $u['name']; ?>">
                            </div>
                            <div class="col-lg-6">
                                <label class="fw-medium mb-2" for="lname">Last name *</label>
                                <input class="form-control rounded-pill" id="lname" name="lname" type="text" required value="<?php echo $u['lastname']; ?>">
                            </div>                           
                            <div class="col-12 mt-4">
                                <label class="fw-medium mb-2" for="address">Street address *</label>
                                <textarea id="address" name="address" placeholder="House number and street name" class="form-control rounded-pill" required><?php echo $u['address']; ?></textarea>
                            </div>
                             <div class="col-6 mt-4">
                                <label class="fw-medium mb-2">Country / Region *</label>
                                <select class="form-select rounded-pill mb-3" id="address_country_ship_2" name="country" required>
                                    <option value="101" selected>India</option>
                                </select>
                            </div>
                            <div class="col-6 mt-4">
                                <label class="fw-medium mb-2">State *</label>
                                <select class="form-select rounded-pill mb-3" id="address_province_ship" name="state" required>
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="col-6 mt-4">
                                <label class="fw-medium mb-2" for="city">Town / City *</label>
                                <select class="form-select rounded-pill mb-3" id="city" name="city" required>
                                    <option value="">Select State First</option>
                                </select>
                            </div>
                            <div class="col-6 mt-4">
                                <label class="fw-medium mb-2" for="zipcode">Postal/Zip Code *</label>
                                <input class="form-control rounded-pill" id="zipcode" name="zipcode" type="text" value="<?php echo $u['pincode']; ?>" required  maxlength="6">
                            </div>
                            <div class="col-6 mt-4">
                                <label class="fw-medium mb-2" for="phone">Phone *</label>
                                <input class="form-control rounded-pill" id="phone" name="phone" type="text" value="<?php echo $u['mobile']; ?>" required>
                            </div>
                            <div class="col-6 mt-4">
                                <label class="fw-medium mb-2" for="email">Email *</label>
                                <input class="form-control rounded-pill" id="email" name="email" type="email" required value="<?php echo $u['email']; ?>" >
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 col-lg-5 mt-5 mt-md-0">
                    <?php if (!empty($cart_items))
                    {
                     ?>
                    <div class="checkout-order">
                        <h3 class="border-bottom pb-3 mb-0 fs-22">Your order</h3>
                        <div class="filter-title mb-4 bg-teal" style="width: 134px;"></div>
                        <div class="d-flex justify-content-between fw-medium border-bottom mb-0 p-2">
                            <h6 class="mb-0 lh-lg">Product</h6>
                            <h6 class="mb-0 lh-lg">Subtotal</h6>
                        </div>
                        <?php
                        $total=$total_qty=0;
                        for($i=0;$i<count($cart_items);$i++)
                        {
                            $line_total = $cart_items[$i]['edsellrate'] * $cart_items[$i]['quantity'];
                            $total += $line_total;
                            $total_qty+=$cart_items[$i]['quantity'];
                        ?>
                        <div class="d-flex justify-content-between fw-medium border-bottom mb-0 p-2">
                            <h6 class="mb-0 lh-lg"><span class="fw-normal"><?php echo htmlspecialchars($cart_items[$i]['saledesp'] ?: $cart_items[$i]['pcode'], ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($cart_items[$i]['color'] . ($cart_items[$i]['size'] ? ' / ' . $cart_items[$i]['size'] : ''), ENT_QUOTES, 'UTF-8'); ?></span> x <?php echo $cart_items[$i]['quantity']; ?></h6>
                            <p class="mb-0 lh-lg">₹<?php echo number_format($line_total, 2); ?></p>
                        </div>
                        <?php
                        }
                        $weight=0;
                        if($total_qty>0)
                        {
                            
                            $h1=mysqli_query($con,"select * from dimension where min_quantity<=$total_qty and max_quantity>=$total_qty");
                            if($h=mysqli_fetch_assoc($h1))
                            {
                                $weight = ($h['length'] * $h['width'] * $h['height']) / 5000;
                            }
                        }
                        ?>
                        <input type="hidden" name="weight" id="weight" value="<?php echo $weight; ?>" />
                        <div class="d-flex justify-content-between fw-medium border-bottom mb-0 p-2">
                            <h6 class="mb-0 lh-lg">Sub-Total</h6>
                            <p class="mb-0 lh-lg" id="subtotal">₹ <?php echo number_format((float)$total,2); ?></p>
                            <input type="hidden" id="raw_subtotal" value="<?php echo $total; ?>" />
                        </div>
                        <?php
                        if ($total > 2500) {
                            $shipping = 0;
                        } else {
                            $ship = mysqli_fetch_assoc(mysqli_query($con, "Select * from matter where m_id=41"));
                            $shipping = (!empty($ship['desp'])) ? (float)$ship['desp'] : 0;
                        }
                        ?>
                        <div class="d-flex justify-content-between fw-medium border-bottom mb-0 p-2">
                            <h6 class="mb-0 lh-lg">Shipping</h6>
                            <p class="mb-0 lh-lg" id="sc">₹ <?php echo number_format((float)$shipping,2); ?></p>
                            <input type="hidden" name="shipping_cost" id="shipping_cost" value="<?php echo $shipping; ?>" />
                        </div>
                        <?php
                        $m=mysqli_fetch_assoc(mysqli_query($con,"Select * from matter where m_id=40"));
                        $convenience_percent = (!empty($m['desp'])) ? (float)$m['desp'] : 0;
                        if($convenience_percent!=0)
                        {
                        ?>
                        <input type="hidden" name="convenience_charge" id="convenience_charge" value="<?php echo htmlspecialchars($convenience_percent, ENT_QUOTES, 'UTF-8'); ?>" />
                        <div class="d-flex justify-content-between fw-medium border-bottom mb-0 p-2">
                            <h6 class="mb-0 lh-lg">Convenience Charges (<?php echo $convenience_percent; ?>%)</h6>
                            <p class="mb-0 lh-lg" id="cc">₹ 00.00</p>
                            <input type="hidden" name="convenience_charge_amount" id="convenience_charge_amount" />
                        </div>
                        <?php
                        }
                        ?>
                        <div class="d-flex justify-content-between fw-medium border-bottom mb-0 p-2">
                            <h6 class="mb-0 lh-lg">Total</h6>
                            <p class="mb-0 lh-lg" id="gt">₹<?php echo number_format($total, 2); ?></p>
                            <input type="hidden" name="grand_total" id="grand_total" value="<?php echo $total; ?>" />
                        </div>
                        <div>                            
                            <p class="py-2">Your personal data will be used to process your order, support your
                                experience
                                throughout this website, and for other purposes described in our privacy policy.</p>
                            <button type="submit" name="checkout_submit" class=" btn btn-teal my-3 px-5 py-3 fw-bold w-100 rounded-pill mb-3">
                                PLACE ORDER
                            </button>
                        </div>
                    </div>
                    <?php
                    }
                    else
                    {
                    ?>
                    <div class="checkout-order">
                        <h3 class="border-bottom pb-3 mb-0 fs-22"><?php echo "Your Cart is Empty!!!!"; ?></h3>
                    </div>
                    <?php
                        
                    }
                    ?>
                </div>

            </div>
        </div>
    </section>

    <?php include_once("footer.php"); ?>
    
    <a href="#" x-on:click.prevent="
          window.scrollTo({
             top: 0,
             behavior: 'smooth'
          });
       " class="position-fixed bg-white border rounded d-flex align-items-center justify-content-center shadow" id="nt_backtop">
        <i class="pr pegk pe-7s-angle-up"></i>
    </a>
    
    
    <div class="backdrop-shadow d-none"></div>
</div>


<!-- custome header -->
<?php include_once("custom_header.php"); ?> 


<!-- JAVASCRIPT -->
<script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/libs/jquery/jquery.min.js" ></script>
<script src="assets/js/store.js" ></script>
<script src="assets/libs/jarallax/jarallax.min.js" ></script>
<script src="assets/libs/swiper/swiper-bundle.min.js" ></script>
<script src="assets/libs/alpinejs/cdn.min.js" ></script>
<script src="assets/libs/jquery-countdown/jquery.countdown.min.js" ></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js" ></script>
<script src="assets/js/product-slider.init.js" ></script>
<script src="assets/js/popup.js" ></script>

<script src="assets/libs/flickity/flickity.pkgd.min.js" ></script>
<script src="assets/js/main.js" ></script>
<script src="assets/js/app.js" ></script>
<script>

    const stateCities = 
    {
        "Andhra Pradesh": ["Anantapur", "Alluri Sitharama Raju", "Anakapalli", "Annamayya", "Bapatla", "Chittoor", "East Godavari", "Eluru", "Guntur", "Kakinada", "Konaseema", "Krishna", "Kurnool", "Markapuram", "NTR", "Nandyal", "Nellore (SPSR Nellore)", "Palnadu", "Parvathipuram Manyam", "Polavaram", "Prakasam", "Sri Sathya Sai", "Srikakulam", "Tirupati", "Visakhapatnam", "Vizianagaram", "West Godavari", "YSR Kadapa"],

        "Arunachal Pradesh": ["Anjaw", "Changlang", "Dibang Valley", "East Kameng", "East Siang", "Kamle", "Kra Daadi", "Kurung Kumey", "Lepa Rada", "Lohit", "Longding", "Lower Dibang Valley", "Lower Siang", "Lower Subansiri", "Namsai", "Pakke-Kessang", "Papum Pare", "Shi Yomi", "Siang", "Tawang", "Tirap", "Upper Siang", "Upper Subansiri", "West Kameng", "West Siang"],

        "Assam": ["Baksa", "Barpeta", "Biswanath", "Bongaigaon", "Cachar", "Charaideo", "Chirang", "Darrang", "Dhemaji", "Dhubri", "Dibrugarh", "Dima Hasao", "Goalpara", "Golaghat", "Hailakandi", "Hojai", "Jorhat", "Kamrup", "Kamrup Metropolitan", "Karbi Anglong", "Karimganj", "Kokrajhar", "Lakhimpur", "Majuli", "Morigaon", "Nagaon", "Nalbari", "Sivasagar", "Sonitpur", "South Salmara-Mankachar", "Tinsukia", "Udalguri", "West Karbi Anglong"],

        "Bihar": ["Araria", "Arwal", "Aurangabad", "Banka", "Begusarai", "Bhagalpur", "Bhojpur", "Buxar", "Darbhanga", "East Champaran", "Gaya", "Gopalganj", "Jamui", "Jehanabad", "Kaimur", "Katihar", "Khagaria", "Kishanganj", "Lakhisarai", "Madhepura", "Madhubani", "Munger", "Muzaffarpur", "Nalanda", "Nawada", "Patna", "Purnia", "Rohtas", "Saharsa", "Samastipur", "Saran", "Sheikhpura", "Sheohar", "Sitamarhi", "Siwan", "Supaul", "Vaishali", "West Champaran"],

        "Chhattisgarh": ["Balod", "Baloda Bazar", "Balrampur", "Bastar", "Bemetara", "Bijapur", "Bilaspur", "Dantewada", "Dhamtari", "Durg", "Gariaband", "Gaurela-Pendra-Marwahi", "Janjgir-Champa", "Jashpur", "Kabirdham", "Kanker", "Khairagarh-Chhuikhadan-Gandai", "Kondagaon", "Korba", "Koriya", "Mahasamund", "Manendragarh-Chirmiri-Bharatpur", "Mohla-Manpur-Ambagarh Chowki", "Mungeli", "Narayanpur", "Raigarh", "Raipur", "Rajnandgaon", "Sakti", "Sarangarh-Bilaigarh", "Sukma", "Surajpur", "Surguja"],

        "Goa": ["North Goa", "South Goa"],

        "Gujarat": ["Ahmedabad", "Amreli", "Anand", "Aravalli", "Banaskantha", "Bharuch", "Bhavnagar", "Botad", "Chhota Udaipur", "Dahod", "Dang", "Devbhoomi Dwarka", "Gandhinagar", "Gir Somnath", "Jamnagar", "Junagadh", "Kheda", "Kutch", "Mahisagar", "Mehsana", "Morbi", "Narmada", "Navsari", "Panchmahal", "Patan", "Porbandar", "Rajkot", "Sabarkantha", "Surat", "Surendranagar", "Tapi", "Vadodara", "Valsad"],

        "Haryana": ["Ambala", "Bhiwani", "Charkhi Dadri", "Faridabad", "Fatehabad", "Gurugram", "Hisar", "Jhajjar", "Jind", "Kaithal", "Karnal", "Kurukshetra", "Mahendragarh", "Nuh", "Palwal", "Panchkula", "Panipat", "Rewari", "Rohtak", "Sirsa", "Sonipat", "Yamunanagar"],

        "Himachal Pradesh": ["Bilaspur", "Chamba", "Hamirpur", "Kangra", "Kinnaur", "Kullu", "Lahaul and Spiti", "Mandi", "Shimla", "Sirmaur", "Solan", "Una"],

        "Jharkhand": ["Bokaro", "Chatra", "Deoghar", "Dhanbad", "Dumka", "East Singhbhum", "Garhwa", "Giridih", "Godda", "Gumla", "Hazaribagh", "Jamtara", "Khunti", "Koderma", "Latehar", "Lohardaga", "Pakur", "Palamu", "Ramgarh", "Ranchi", "Sahebganj", "Seraikela Kharsawan", "Simdega", "West Singhbhum"],

        "Karnataka": ["Bagalkot", "Ballari", "Belagavi", "Bengaluru Rural", "Bengaluru Urban", "Bidar", "Chamarajanagar", "Chikballapur", "Chikkamagaluru", "Chitradurga", "Dakshina Kannada", "Davanagere", "Dharwad", "Gadag", "Hassan", "Haveri", "Kalaburagi", "Kodagu", "Kolar", "Koppal", "Mandya", "Mysuru", "Raichur", "Ramanagara", "Shivamogga", "Tumakuru", "Udupi", "Uttara Kannada", "Vijayanagara", "Vijayapura", "Yadgir"],

        "Kerala": ["Alappuzha", "Ernakulam", "Idukki", "Kannur", "Kasaragod", "Kollam", "Kottayam", "Kozhikode", "Malappuram", "Palakkad", "Pathanamthitta", "Thiruvananthapuram", "Thrissur", "Wayanad"],

        "Madhya Pradesh": ["Agar Malwa", "Alirajpur", "Anuppur", "Ashoknagar", "Balaghat", "Barwani", "Betul", "Bhind", "Bhopal", "Burhanpur", "Chhatarpur", "Chhindwara", "Damoh", "Datia", "Dewas", "Dhar", "Dindori", "Guna", "Gwalior", "Harda", "Indore", "Jabalpur", "Jhabua", "Katni", "Khandwa", "Khargone", "Maihar", "Mandla", "Mandsaur", "Mauganj", "Morena", "Narsinghpur", "Neemuch", "Niwari", "Panna", "Pandhurna", "Raisen", "Rajgarh", "Ratlam", "Rewa", "Sagar", "Satna", "Sehore", "Seoni", "Shahdol", "Shajapur", "Sheopur", "Shivpuri", "Sidhi", "Singrauli", "Tikamgarh", "Ujjain", "Umaria", "Vidisha"],

        "Maharashtra": ["Ahmednagar", "Akola", "Amravati", "Chhatrapati Sambhajinagar", "Beed", "Bhandara", "Buldhana", "Chandrapur", "Dharashiv", "Dhule", "Gadchiroli", "Gondia", "Hingoli", "Jalgaon", "Jalna", "Kolhapur", "Latur", "Mumbai City", "Mumbai Suburban", "Nagpur", "Nanded", "Nandurbar", "Nashik", "Palghar", "Parbhani", "Pune", "Raigad", "Ratnagiri", "Sangli", "Satara", "Sindhudurg", "Solapur", "Thane", "Wardha", "Washim", "Yavatmal"],

        "Manipur": ["Bishnupur", "Chandel", "Churachandpur", "Imphal East", "Imphal West", "Jiribam", "Kakching", "Kamjong", "Kangpokpi", "Noney", "Pherzawl", "Senapati", "Tamenglong", "Tengnoupal", "Thoubal", "Ukhrul"],

        "Meghalaya": ["East Garo Hills", "West Garo Hills", "South Garo Hills", "North Garo Hills", "South West Garo Hills", "East Khasi Hills", "West Khasi Hills", "South West Khasi Hills", "Eastern West Khasi Hills", "Ri Bhoi", "East Jaintia Hills", "West Jaintia Hills"],

        "Mizoram": ["Aizawl", "Champhai", "Hnahthial", "Khawzawl", "Kolasib", "Lawngtlai", "Lunglei", "Mamit", "Saitual", "Saiha", "Serchhip"],

        "Nagaland": ["Chümoukedima", "Dimapur", "Kiphire", "Kohima", "Longleng", "Mokokchung", "Mon", "Niuland", "Noklak", "Peren", "Phek", "Shamator", "Tseminyu", "Tuensang", "Wokha", "Zunheboto"],

        "Odisha": ["Angul", "Balangir", "Balasore", "Bargarh", "Bhadrak", "Boudh", "Cuttack", "Deogarh", "Dhenkanal", "Gajapati", "Ganjam", "Jagatsinghpur", "Jajpur", "Jharsuguda", "Kalahandi", "Kandhamal", "Kendrapara", "Kendujhar", "Khordha", "Koraput", "Malkangiri", "Mayurbhanj", "Nabarangpur", "Nayagarh", "Nuapada", "Puri", "Rayagada", "Sambalpur", "Subarnapur", "Sundargarh"],

        "Punjab": ["Amritsar", "Barnala", "Bathinda", "Faridkot", "Fatehgarh Sahib", "Fazilka", "Ferozepur", "Gurdaspur", "Hoshiarpur", "Jalandhar", "Kapurthala", "Ludhiana", "Malerkotla", "Mansa", "Moga", "Sri Muktsar Sahib", "Pathankot", "Patiala", "Rupnagar", "S.A.S. Nagar (Mohali)", "Sangrur", "Shaheed Bhagat Singh Nagar", "Tarn Taran"],

        "Rajasthan": ["Ajmer", "Alwar", "Balotra", "Banswara", "Baran", "Barmer", "Beawar", "Bharatpur", "Bhilwara", "Bikaner", "Bundi", "Chittorgarh", "Churu", "Dausa", "Deeg", "Didwana-Kuchaman", "Dholpur", "Dungarpur", "Hanumangarh", "Jaipur", "Jaisalmer", "Jalore", "Jhalawar", "Jhunjhunu", "Jodhpur", "Karauli", "Khairthal-Tijara", "Kota", "Kotputli-Behror", "Nagaur", "Pali", "Phalodi", "Pratapgarh", "Rajsamand", "Salumbar", "Sawai Madhopur", "Sikar", "Sirohi", "Sri Ganganagar", "Tonk", "Udaipur"],

        "Sikkim": ["East Sikkim (Gangtok)", "West Sikkim (Gyalshing)", "North Sikkim (Mangan)", "South Sikkim (Namchi)", "Pakyong", "Soreng"],

        "Tamil Nadu": ["Ariyalur", "Chengalpattu", "Chennai", "Coimbatore", "Cuddalore", "Dharmapuri", "Dindigul", "Erode", "Kallakurichi", "Kanchipuram", "Kanyakumari", "Karur", "Krishnagiri", "Madurai", "Mayiladuthurai", "Nagapattinam", "Namakkal", "Nilgiris", "Perambalur", "Pudukkottai", "Ramanathapuram", "Ranipet", "Salem", "Sivaganga", "Tenkasi", "Thanjavur", "Theni", "Thoothukudi", "Tiruchirappalli", "Tirunelveli", "Tirupathur", "Tiruppur", "Tiruvallur", "Tiruvannamalai", "Tiruvarur", "Vellore", "Viluppuram", "Virudhunagar"],

        "Telangana": ["Adilabad", "Bhadradri Kothagudem", "Hyderabad", "Jagtial", "Jangaon", "Jayashankar Bhupalpally", "Jogulamba Gadwal", "Kamareddy", "Karimnagar", "Khammam", "Komaram Bheem Asifabad", "Mahabubabad", "Mahabubnagar", "Mancherial", "Medak", "Medchal-Malkajgiri", "Mulugu", "Nagarkurnool", "Nalgonda", "Narayanpet", "Nirmal", "Nizamabad", "Peddapalli", "Rajanna Sircilla", "Rangareddy", "Sangareddy", "Siddipet", "Suryapet", "Vikarabad", "Wanaparthy", "Warangal", "Hanumakonda", "Yadadri Bhuvanagiri"],

        "Tripura": ["Dhalai", "Gomati", "Khowai", "North Tripura", "Sepahijala", "South Tripura", "Unakoti", "West Tripura"],

        "Uttar Pradesh": ["Agra", "Aligarh", "Ambedkar Nagar", "Amethi", "Amroha", "Auraiya", "Ayodhya", "Azamgarh", "Baghpat", "Bahraich", "Ballia", "Balrampur", "Banda", "Barabanki", "Bareilly", "Basti", "Bhadohi", "Bijnor", "Budaun", "Bulandshahr", "Chandauli", "Chitrakoot", "Deoria", "Etah", "Etawah", "Farrukhabad", "Fatehpur", "Firozabad", "Gautam Buddha Nagar", "Ghaziabad", "Ghazipur", "Gonda", "Gorakhpur", "Hamirpur", "Hapur", "Hardoi", "Hathras", "Jalaun", "Jaunpur", "Jhansi", "Kannauj", "Kanpur Dehat", "Kanpur Nagar", "Kasganj", "Kaushambi", "Kheri (Lakhimpur Kheri)", "Kushinagar", "Lalitpur", "Lucknow", "Maharajganj", "Mahoba", "Mainpuri", "Mathura", "Mau", "Meerut", "Mirzapur", "Moradabad", "Muzaffarnagar", "Pilibhit", "Pratapgarh", "Prayagraj", "Raebareli", "Rampur", "Saharanpur", "Sambhal", "Sant Kabir Nagar", "Shahjahanpur", "Shamli", "Shravasti", "Siddharthnagar", "Sitapur", "Sonbhadra", "Sultanpur", "Unnao", "Varanasi"],

        "Uttarakhand": ["Almora", "Bageshwar", "Chamoli", "Champawat", "Dehradun", "Haridwar", "Nainital", "Pauri Garhwal", "Pithoragarh", "Rudraprayag", "Tehri Garhwal", "Udham Singh Nagar", "Uttarkashi"],

        "West Bengal": ["Alipurduar", "Bankura", "Birbhum", "Cooch Behar", "Dakshin Dinajpur", "Darjeeling", "Hooghly", "Howrah", "Jalpaiguri", "Jhargram", "Kalimpong", "Kolkata", "Malda", "Murshidabad", "Nadia", "North 24 Parganas", "Paschim Bardhaman", "Paschim Medinipur", "Purba Bardhaman", "Purba Medinipur", "Purulia", "South 24 Parganas", "Uttar Dinajpur"],

        // Union Territories
        "Andaman and Nicobar Islands": ["Nicobar", "North and Middle Andaman", "South Andaman"],
        "Chandigarh": ["Chandigarh"],
        "Dadra and Nagar Haveli and Daman and Diu": ["Dadra and Nagar Haveli", "Daman", "Diu"],
        "Delhi": ["Central Delhi", "East Delhi", "New Delhi", "North Delhi", "North East Delhi", "North West Delhi", "Shahdara", "South Delhi", "South East Delhi", "South West Delhi", "West Delhi"],
        "Jammu and Kashmir": ["Anantnag", "Bandipora", "Baramulla", "Budgam", "Doda", "Ganderbal", "Jammu", "Kathua", "Kishtwar", "Kulgam", "Kupwara", "Poonch", "Pulwama", "Rajouri", "Ramban", "Reasi", "Samba", "Shopian", "Srinagar", "Udhampur"],
        "Ladakh": ["Leh", "Kargil", "Zanskar", "Drass", "Sham", "Nubra", "Changthang"],
        "Lakshadweep": ["Lakshadweep"],
        "Puducherry": ["Puducherry", "Karaikal", "Mahe", "Yanam"]
    };


    document.addEventListener("DOMContentLoaded", function() 
    {
        const form = document.getElementById("checkout-form");
        const countrySelect = document.getElementById("address_country_ship_2");
        const stateSelect = document.getElementById("address_province_ship");
        const citySelect = document.getElementById("city");

        const savedCountry = <?php echo json_encode($u['country'] ?? '101'); ?>;
        const savedState = <?php echo json_encode($saved_state_name); ?>;
        const savedCity  = <?php echo json_encode($saved_city_name);  ?>;

        if (countrySelect) {
            countrySelect.value = String(savedCountry);
        }

        if (stateSelect) {
            stateSelect.innerHTML = '<option value="">Select State</option>';
            for (const state in stateCities) {
                const opt = document.createElement('option');
                opt.value = state;
                opt.textContent = state;
                if (state.trim().toLowerCase() === String(savedState).trim().toLowerCase()) {
                    opt.selected = true;
                }
                stateSelect.appendChild(opt);
            }

            function populateCities(selectedState, selectedCity) {
                citySelect.innerHTML = '<option value="">Select City</option>';
                const matchedState = Object.keys(stateCities).find(function(state) {
                    return state.trim().toLowerCase() === String(selectedState).trim().toLowerCase();
                });
                if (!matchedState) {
                    return;
                }
                stateCities[matchedState].forEach(function(city) {
                    const opt = document.createElement('option');
                    opt.value = city;
                    opt.textContent = city;
                    if (city.trim().toLowerCase() === String(selectedCity).trim().toLowerCase()) {
                        opt.selected = true;
                    }
                    citySelect.appendChild(opt);
                });
            }

            if (savedState) {
                populateCities(savedState, savedCity);
            }

            stateSelect.addEventListener("change", function() {
                populateCities(this.value, '');
            });
        }

        const placeOrderBtn = document.querySelector("button[name='checkout_submit']");
        if (placeOrderBtn && form) {
            placeOrderBtn.addEventListener("click", function(e) {
                e.preventDefault();
                const fname = document.getElementById("fname").value.trim();
                const lname = document.getElementById("lname").value.trim();
                const address = document.getElementById("address").value.trim();
                const state = stateSelect.value;
                const city = citySelect.value;
                const zipcode = document.getElementById("zipcode").value.trim();

                if (!fname || !address || !state || !city || !zipcode) {
                    alert("Please enter all required details before proceeding.");
                    return;
                }

                const pinRegex = /^[1-9][0-9]{5}$/;
                if (!pinRegex.test(zipcode)) {
                    alert("Please enter a valid 6-digit Indian Pincode.");
                    return;
                }

                placeOrderBtn.disabled = true;

                const formData = new FormData(form);
                formData.append("ajax_create_checkout", "1");
                
                const sc = document.getElementById("shipping_cost") ? document.getElementById("shipping_cost").value : "0";
                formData.append("shipping_charge", sc || "0");
                
                const cc = document.getElementById("convenience_charge") ? document.getElementById("convenience_charge").value : "0";
                formData.append("convenience_charge", cc || "0");
                
                const cca = document.getElementById("convenience_charge_amount") ? document.getElementById("convenience_charge_amount").value : "0";
                formData.append("convenience_charge_amount", cca || "0");
                
                const gt = document.getElementById("grand_total") ? document.getElementById("grand_total").value : "0";
                formData.append("grandtotal", gt || "0");
                
                formData.append("name", fname);
                formData.append("lastname", lname);
                formData.append("mobile", document.getElementById("phone").value.trim());
                formData.append("pincode", zipcode);

                $.ajax({
                    url: 'checkout_ajax.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status != 1) {
                            alert(response.message || 'Unable to place order.');
                            placeOrderBtn.disabled = false;
                            return;
                        }

                        // Open Razorpay Flow
                        var options = {
                            "key": response.razorpay_key,
                            "amount": response.amount,
                            "currency": "INR",
                            "name": "Ethic Design Studio",
                            "description": "Website Order Check ID: " + response.check_id,
                            "order_id": response.razorpay_order_id,
                            "handler": function (paymentResponse) {
                                finalizeCheckoutPayment(response.check_id, paymentResponse);
                            },
                            "prefill": {
                                "name": response.name,
                                "email": response.email,
                                "contact": response.mobile
                            },
                            "theme": {
                                "color": "#0d9488"
                            },
                            "modal": {
                                "ondismiss": function() {
                                    alert("Payment cancelled.");
                                    placeOrderBtn.disabled = false;
                                }
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    },
                    error: function() {
                        alert("An error occurred while creating order checkout.");
                        placeOrderBtn.disabled = false;
                    }
                });
            });
        }
    });

    function showProcessingOverlay() 
    {
        const overlay = document.createElement('div');
        overlay.id = 'checkout-processing-overlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:9999;display:flex;align-items:center;justify-content:center;';
        overlay.innerHTML =
            '<div style="background:#fff;padding:30px 40px;border-radius:12px;text-align:center;max-width:320px;">' +
                '<div class="spinner-border text-teal mb-3" role="status" style="width:2.5rem;height:2.5rem;"></div>' +
                '<h5 class="mb-2">Processing your order</h5>' +
                '<p class="mb-0 text-muted">Please wait, do not close or refresh this page.</p>' +
            '</div>';
        document.body.appendChild(overlay);
    }

    function finalizeCheckoutPayment(checkId, paymentResponse) 
    {
        showProcessingOverlay();

        const formData = new FormData();
        formData.append("ajax_finalize_checkout", "1");
        formData.append("check_id", checkId);
        formData.append("razorpay_payment_id", paymentResponse.razorpay_payment_id || "");
        formData.append("razorpay_order_id", paymentResponse.razorpay_order_id || "");

        $.ajax({
            url: 'checkout_ajax',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status == 1) 
                {
                    window.open(
                        'ethic_crm/printinvoice1.php?sale_id=' + response.sale_id,
                        '_blank'
                    );
                    // window.location.href = "order_history?&razorpay_order_id=" + response.razorpay_order_id;
                    window.location.href = "order_history";
                    
                } else {
                    document.getElementById('checkout-processing-overlay')?.remove();
                    alert(response.message || "Failed to finalize payment.");
                }
            },
            error: function() {
                document.getElementById('checkout-processing-overlay')?.remove();
                alert("An error occurred while finalizing payment.");
            }
        });
    }
    $(document).ready(function() {
        getWeight();
    });
</script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script></body>
</html>