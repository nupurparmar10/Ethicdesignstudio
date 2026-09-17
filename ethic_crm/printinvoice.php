<?php
ob_start();
session_start();
include_once("connect.php");
$msg="";
if(!isset($_REQUEST['sale_id']))
{
    header("Location: viewsales.php"); die;
}
  $accno="02037600040";
  $bank="The Kalupur Commercial Co-Operative Bank Ltd.";
  $branch="Vasna";
  $ifsc="KCCB0VSN020";
  $accname="Ethic Designs LLP";

function no_to_words($no)
{
 $words = array('0'=> '' ,'1'=> 'One' ,'2'=> 'Two' ,'3' => 'Three','4' => 'Four','5' => 'Five','6' => 'Six','7' => 'Seven','8' => 'Eight','9' => 'Nine','10' => 'Ten','11' => 'Eleven','12' => 'Twelve','13' => 'Thirteen','14' => 'Fourteen','15' => 'Fifteen','16' => 'Sixteen','17' => 'Seventeen','18' => 'Eighteen','19' => 'Nineteen','20' => 'Twenty','30' => 'Thirty','40' => 'Fourty','50' => 'Fifty','60' => 'Sixty','70' => 'Seventy','80' => 'Eighty','90' => 'Ninty','100' => 'Hundred','1000' => 'Thousand','100000' => 'Lakh','10000000' => 'Crore');
    if($no == 0)
        return ' ';
    else {
		$no = round ($no, 0);
	$novalue='';
	$highno=$no;
	$remainno=0;
	$value=100;
	$value1=1000;       
            while($no>=100)    {
                if(($value <= $no) &&($no  < $value1))    {
                $novalue=$words["$value"];
                $highno = (int)($no/$value);
                $remainno = $no % $value;
                break;
                }
                $value= $value1;
                $value1 = $value * 100;
            }       
          if(array_key_exists("$highno",$words))
              return $words["$highno"]." ".$novalue." ".no_to_words($remainno);
          else {
             $unit=$highno%10;
             $ten =(int)($highno/10)*10;            
             return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".no_to_words($remainno);
           }
    }
}
$d1=mysqli_query($con,"select * from billbook where sale_id='".$_REQUEST['sale_id']."'");
$d=mysqli_fetch_row($d1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $d[3]; ?></title>
<link rel="icon" href="logo3.png" type="image/x-icon" />
<style>
  @page {
    size: A4;
    margin: 0;
  }
  @media print {
    .no-print {
      display: none !important;
    }
    #new {page-break-before: always;}
    .page {
      height: 297mm !important; 
      min-height: auto !important;
      margin-bottom: 0 !important;
    }
  }
  .print-toolbar {
    background-color: #f1f1f1;
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #ccc;
    margin-bottom: 15px;
  }
  .print-btn {
    padding: 8px 16px;
    font-size: 14px;
    background-color: #e0672c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
  }
  .print-btn:hover {
    background-color: #c85a22;
  }

  * { box-sizing: border-box; }

  /* ===== ADJUST THE BLANK GAP IN THE ITEMS TABLE HERE ===== */
  :root { --filler-height: 15mm; }

  html, body {
    margin: 0;
    padding: 0;
    font-family: 'Helvetica Neue', Arial, sans-serif;
    font-size: 7.2pt;
    color: #262322;
    line-height: 1.25;
  }
  .page {
    position: relative;
    width: 198mm;
    height: 285mm;
    padding: 5mm 7mm 5mm 7mm;
    border: 1.4pt solid #e0672c;
    outline: 0.5pt solid #e0672c;
    outline-offset: -2.6mm;
    overflow: hidden;
    margin: 0 auto 5mm auto;
    box-sizing: border-box;
    background: #fff;
  }

  /* watermark */
  .watermark {
    position: absolute;
    top: 30mm;
    left: 0;
    width: 198mm;
    height: 160mm;
    z-index: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    pointer-events: none;
  }
  .watermark-inner {
    /* transform: rotate(-28deg); */
    display: flex;
    flex-direction: column;
    align-items: center;
    opacity: 0.25;
  }
  .watermark-inner img {
    width: 74mm;
    margin-bottom: 3mm;
  }
  .watermark-inner .wm-text {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 17pt;
    font-weight: 700;
    color: #b5581f;
    letter-spacing: 4px;
    text-transform: uppercase;
    white-space: nowrap;
  }
  .sheet { position: relative; z-index: 1; width: 100%; height: 100%; display: flex; flex-direction: column; }

  /* ---------- HEADER ---------- */
  .top-band {
    text-align: center;
    font-size: 6.6pt;
    color: #b5651d;
    letter-spacing: 0.5px;
    margin-bottom: 0.6mm;
  }
  .header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1.6pt solid #e0672c;
    padding-bottom: 1.4mm;
    margin-bottom: 1.6mm;
  }
  .brand img { height: 12mm; display: block; }
  .invoice-tag {
    text-align: right;
  }
  .invoice-tag .tax-invoice {
    display: inline-block;
    background: #e0672c;
    color: #fff;
    font-weight: 700;
    font-size: 8.6pt;
    letter-spacing: 1.2px;
    padding: 1mm 3.6mm;
    border-radius: 2px;
    margin-bottom: 1mm;
  }
  .invoice-tag .meta-row {
    font-size: 7pt;
    color: #333;
  }
  .invoice-tag .meta-row b { color: #111; }

  /* ---------- PARTY DETAILS ---------- */
  .parties {
    display: flex;
    gap: 3mm;
    margin-bottom: 1.6mm;
  }
  .party-box {
    flex: 1;
    border: 0.6pt solid #ddc9bd;
    border-radius: 2px;
    padding: 1.6mm 2.4mm;
    background: #fdf8f5;
  }
  .party-box.billed {
    background: #fbfaf7;
    border-color: #ddd6c8;
  }
  .party-title {
    font-size: 7.6pt;
    font-weight: 700;
    color: #e0672c;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 1mm;
    border-bottom: 0.5pt dotted #cbb7ab;
    padding-bottom: 0.8mm;
  }
  .party-box.billed .party-title { color: #6d6355; }
  .company-name { font-weight: 700; font-size: 8pt; margin-bottom: 0.8mm; }
  .line { margin-bottom: 0.5mm; }
  .icon-line { display: flex; gap: 1.3mm; margin-bottom: 0.5mm; }
  .icon-line .ic {
    flex: 0 0 auto;
    width: 3mm; height: 3mm;
    border-radius: 50%;
    background: #e0672c;
    color: #fff;
    font-size: 5.6pt;
    text-align: center;
    line-height: 3mm;
  }
  .party-box.billed .icon-line .ic { background: #e0672c; }
  .icon-line .txt { flex: 1; word-break: break-word; }
  .gstin-tag {
    margin-top: 1.2mm;
    display: inline-block;
    font-weight: 700;
    font-size: 7.2pt;
    background: #f1e3da;
    color: #a2481a;
    padding: 0.6mm 1.6mm;
    border-radius: 2px;
  }
  .party-box.billed .gstin-tag { background: #efece4; color: #5b5344; }

  .pay-strip {
    display: flex;
    justify-content: space-between;
    font-size: 7.2pt;
    margin-top: 1.6mm;
    padding-top: 1.2mm;
    border-top: 0.5pt dotted #cbb7ab;
  }

  /* ---------- ITEMS TABLE ---------- */
  table.items {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.6mm;
  }
  table.items thead th {
    background: #e0672c;
    color: #fff;
    font-size: 5.4pt;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 1.1mm 1mm;
    border: 0.5pt solid #c85a22;
    text-align: center;
  }
  table.items tbody td {
    padding: 0.8mm 1mm;
    border: 0.5pt solid #e6dcd3;
    text-align: center;
    font-size: 6.2pt;
  }
  table.items tbody tr:nth-child(even) { background: #fdf8f5; }
  table.items td.desc { text-align: left; }
  table.items td.num { text-align: right; padding-right: 2mm; }
  table.items tbody tr.blank-row td { border-bottom: none; border-top: none; height: 4mm; }
  table.items tr.item-totals td { height: 1px; }

  /* ---------- TOTALS SECTION ---------- */
  .totals-wrap {
    display: flex;
    gap: 3mm;
    margin-bottom: 1.6mm;
  }
  .gst-table {
    flex: 1.15;
    border-collapse: collapse;
  }
  .gst-table th {
    background: #f1e3da;
    color: #7a3d15;
    font-size: 6.8pt;
    padding: 1mm;
    border: 0.5pt solid #e6dcd3;
  }
  .gst-table td {
    font-size: 7pt;
    padding: 1mm;
    border: 0.5pt solid #e6dcd3;
    text-align: right;
    padding-right: 2mm;
  }
  .gst-table td.pct { text-align: center; padding-right: 1mm; }

  .charges-table {
    flex: 1;
    border-collapse: collapse;
  }
  .charges-table td {
    font-size: 6.9pt;
    padding: 0.8mm 1.6mm;
    border: 0.5pt solid #e6dcd3;
  }
  .charges-table td.label { color: #555; }
  .charges-table td.val { text-align: right; font-weight: 600; }
  .charges-table tr.grand td {
    background: #e0672c;
    color: #fff;
    font-weight: 700;
    font-size: 7.6pt;
    border-color: #c85a22;
  }

  .amount-words {
    background: #fdf8f5;
    border: 0.6pt solid #ddc9bd;
    border-radius: 2px;
    padding: 1.2mm 2.4mm;
    font-size: 7pt;
    margin-bottom: 1.6mm;
  }
  .amount-words b { color: #a2481a; }

  /* ---------- NOTES & QR & BANK ---------- */
  .bottom-grid {
    display: flex;
    gap: 3mm;
    margin-bottom: 1.6mm;
  }
  .notes-col {
    flex: 1.15;
    font-size: 6.3pt;
    color: #444;
  }
  .notes-col .h { font-weight: 700; color: #e0672c; font-size: 6.8pt; margin-bottom: 0.6mm; text-transform: uppercase; letter-spacing: 0.4px; }
  .notes-col ol { margin: 0; padding-left: 3mm; }
  .notes-col li { margin-bottom: 0.3mm; }

  .qr-col {
    flex: 0 0 auto;
    text-align: center;
    font-size: 6.2pt;
    color: #555;
  }
  .qr-col img { width: 20mm; height: 20mm; display: block; margin: 0 auto 0.6mm; border: 0.5pt solid #ddc9bd; padding: 0.6mm; }

  .bank-col {
    flex: 1;
    font-size: 6.5pt;
    border: 0.6pt solid #ddc9bd;
    border-radius: 2px;
    padding: 1.2mm 2mm;
    background: #fdf8f5;
  }
  .bank-col .h { font-weight: 700; color: #a2481a; font-size: 6.8pt; margin-bottom: 0.6mm; }
  .bank-col div { margin-bottom: 0.3mm; }

  /* ---------- SIGNATURE ---------- */
  .signatures {
    display: flex;
    justify-content: space-between;
    margin-top: 10.6mm;
    padding-top: 1.2mm;
    border-bottom: 0.6pt solid #e0672c;
    font-size: 6.8pt;
    font-weight: 600;
  }
  .for-company {
    text-align: right;
    font-size: 6.8pt;
    font-weight: 700;
    color: #333;
    margin-bottom: 3mm;
  }
  .sig-right { text-align: right; }

  .jurisdiction-footer {
    text-align: center;
    font-size: 6.4pt;
    color: #999;
    margin-top: 2mm;
  }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
  function printinvoice() {
    window.print();
  }
  function saveAsPDF() {
    var element = document.getElementById('invoice-content');
    var toolbar = document.querySelector('.print-toolbar');
    toolbar.style.display = 'none';

    var opt = {
      filename:     'Invoice_<?php echo $d[3]; ?>.pdf',
      image:        { type: 'jpeg', quality: 1 },
      html2canvas:  { scale: 4, useCORS: true, windowWidth: document.documentElement.offsetWidth },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(function() {
      toolbar.style.display = 'block';
    });
  }
</script>
</head>
<body>

<div class="print-toolbar no-print">
  <button class="print-btn" onclick="printinvoice()">Print Invoice</button>
  <button class="print-btn" style="margin-left:10px; background-color: #333;" onclick="saveAsPDF()">Save as PDF</button>
</div>

<div id="invoice-content">

<?php
	$c1=mysqli_query($con,"select count(*) from bill_items where sale_id='$_REQUEST[sale_id]'");
	$c=mysqli_fetch_row($c1);
	$total_items = $c[0];
	if ($total_items == 0) $total_items = 1;
	// Pagination limits based on exact page measurements (275mm content area)
	// Page 1 fixed overhead (header+parties+bands) = ~65mm
	// Footer (totals+bank+signatures) = ~81mm
	// Available for items WITH footer on page 1 = 129mm / 4mm per row = ~30 rows
	// Available for items WITHOUT footer (items-only page) = 200mm / 4mm per row = ~42 rows
	$limit_p1_with_totals = 30;  // Max items on page 1 if footer fits
	$limit_p1_no_totals   = 38;  // Max items on page 1 if footer spills
	$limit_pn_with_totals = 38;  // Max items on page N (last) with footer
	$limit_pn_no_totals   = 42;  // Max items on page N (not last), no footer

	if ($total_items <= $limit_p1_with_totals) {
		$count = 1; // Everything fits on 1 page
	} elseif ($total_items <= $limit_p1_no_totals) {
		$count = 2; // Items overflow page 1, footer on page 2
	} else {
		$remaining = $total_items - $limit_p1_no_totals;
		$full_pages = floor($remaining / $limit_pn_no_totals);
		$remainder  = $remaining % $limit_pn_no_totals;
		if ($remainder == 0 || $remainder <= $limit_pn_with_totals) {
			$count = 1 + $full_pages + 1;
		} else {
			$count = 1 + $full_pages + 2;
		}
	}
	$amt1=0;
	$tot=0;
	$dis=0; 
	$igst=0;
	$j=1;
  $tax=0;
	$taxableamt28=0;
	$taxableamt18=0;
	$taxableamt12=0;
	$taxableamt5=0;
	$exempted=0;

	$k1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$d[2]");
	$k=mysqli_fetch_row($k1);
	$party1=mysqli_query($con,"select * from ledger_details where ledger_id=$d[2]");	
  if($p1=mysqli_fetch_row($party1)){}
  else
  {
    for($i=0;$i<=5;$i++)
      $p1[$i]="";
  }
	$emp1=mysqli_query($con,"select empname from empdet where ledger_id='$d[17]'");
	if($emp=mysqli_fetch_row($emp1)) {} else $emp[0]="";

	for($i=1;$i<=$count;$i++)
	{
        if ($i == 1) {
            $limit = ($i == $count) ? $limit_p1_with_totals : $limit_p1_no_totals;
            $start = 0;
        } else {
            $limit = ($i == $count) ? $limit_pn_with_totals : $limit_pn_no_totals;
            $start = $limit_p1_no_totals + ($i - 2) * $limit_pn_no_totals;
        }
?>
<div class="page">
  <div class="watermark">
    <div class="watermark-inner">
      <img src="assets/admin.png" alt="">
    </div>
  </div>

<div class="sheet">

  <?php if ($i == 1) { ?>
  <div class="top-band">|| श्री पार्श्वनाथाय नमः ||</div>

  <div class="header">
    <div class="brand">
      <img src="img/VHS.png" alt="Ethic Design Studio">
    </div>
    <div class="invoice-tag">
      <div class="tax-invoice">TAX INVOICE</div>
      <div class="meta-row"><b>Invoice No.:</b> <?php echo $d[3]; ?></div>
      <div class="meta-row"><b>Date:</b> <?php if($d[1]!="0000-00-00"){ $date= DateTime::createFromFormat('Y-m-d', $d[1]); echo $date->format('M d, Y'); } ?></div>
      <?php
      if($emp[0]!="")
      {
      ?>
      <div class="meta-row"><b>Salesman:</b> <?php echo ($emp[0] != "") ? $emp[0] : "&mdash;"; ?></div>
      <?php
      }
      ?>
    </div>
  </div>

  <div class="parties">
    <div class="party-box">
      <div class="party-title">ETHIC DESIGN'S LLP</div>
      <div class="line"><b>Main Branch:</b> 2370/71, Rani No Haziro, Manek Chowk, Ahmedabad 380001.</div>
      <div class="line"><b>Branch(2):</b> 100, Lavanya Society, Nr. Jivraj Mehta Hospital, Vasna, Ahmedabad 380007.</div>
      <div class="icon-line"><span class="ic">&#128241;</span><span class="txt">9824077818, 9825162255, 8980060002</span></div>
      <div style="display: flex; gap: 3mm; flex-wrap: wrap;">
        <div class="icon-line" style="margin-bottom:0;">
          <a href="mailto:ethicdesignstudio@gmail.com" style="text-decoration: none; color: inherit;">
            <span class="ic" style="background-color:transparent;color: black;">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 512 512" fill="currentColor" style="vertical-align: text-bottom;"><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
            </span>
          </a>
        </div>
        <div class="icon-line" style="margin-bottom:0;">
          <a href="https://www.ethicdesignstudio.com" target="_blank" style="text-decoration: none; color: inherit;">
            <span class="ic" style="background-color:transparent;color: black;">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 512 512" fill="currentColor" style="vertical-align: text-bottom;"><path d="M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64h185.4c2.2 20.4 3.3 41.8 3.3 64zm28.8-64h123.1c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0H18.6C48.6 88.5 112.3 31.7 190.6 11c-25.5 34.2-45.3 87.7-55.3 151.6zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM190.6 501c-25.5-34.2-45.3-87.7-55.3-151.6H18.6c30 74.1 93.6 130.9 172 151.6zm130.1 0c78.3-20.7 142-77.5 171.9-151.6H376.7c-10 63.9-29.8 117.4-55.3 151.6zM256 512c-7.3 0-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5c-11.6-26-20.9-58.2-27-94.7h176.6c-6.1 36.4-15.5 68.6-27 94.7c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512z"/></svg>
            </span>
          </a>
        </div>
        <div class="icon-line" style="margin-bottom:0;">
          <a href="https://www.instagram.com/ethicdesignstudio" target="_blank" style="text-decoration: none; color: inherit;">
            <span class="ic" style="background-color:transparent;color: black;">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 448 512" fill="currentColor" style="vertical-align: text-bottom;">
                <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/>
              </svg>
            </span>
          </a>
        </div>
      </div>

      <span class="gstin-tag">GSTIN: 24AAJFE0234H1ZV</span>
    </div>

    <div class="party-box billed">
      <div class="party-title">Billed To</div>
      <div class="company-name">M/s <?php echo (!empty($k[0])) ? $k[0] : ''; ?></div>
      <div class="icon-line"><span class="ic">&#128241;</span><span class="txt"><?php echo (!empty($p1[4])) ? $p1[4] : ((!empty($d[8])) ? $d[8] : '-'); ?></span></div>
      <span class="gstin-tag">GSTIN: <?php echo (!empty($p1[3])) ? $p1[3] : '-'; ?></span>
      <div class="pay-strip">
        <span><b>Paid By:</b> <?php 
        if($d[7]=="3") {
            echo "Cash"; 
        } else if($d[7]=="Credit") {
            echo "Credit"; 
        } else {
            $paid_led = mysqli_fetch_row(mysqli_query($con, "select name from ledger_accounts where ledger_id='$d[7]'"));
            echo ($paid_led[0] != "") ? $paid_led[0] : "Cheque";
        } 
        ?></span>
      </div>
    </div>
  </div>
  <?php } else { ?>
  <div style="height: 10mm;"></div>
  <?php } ?>

  <table class="items">
    <thead>
      <tr>
        <th style="width:5%;">S.No.</th>
        <th style="width:27%;">Description of Goods<br><span style="font-weight:400;font-size:6.2pt;text-transform:none;">(Code - Name - Variant)</span></th>
        <th style="width:8%;">HSN</th>
        <th style="width:6%;">Qty</th>
        <th style="width:8%;">Unit</th>
        <th style="width:9%;">MRP</th>
        <th style="width:9%;">Disc (₹)</th>
        <th style="width:9%;">Rate (₹)</th>
        <th style="width:7%;">Tax (%)</th>
        <th style="width:7%;">IGST</th>
        <th style="width:10%;">Amount (₹)</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $pro1=mysqli_query($con,"select * from bill_items where sale_id='".$_REQUEST['sale_id']."' LIMIT $start, $limit");
      while($pro=mysqli_fetch_row($pro1))
      {
          $v=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$pro[1]'"));
          if(!$v) $v = array_fill(0, 10, "");
          $list1=mysqli_query($con,"select * from item_details where item_id='$v[1]'");
          $l=mysqli_fetch_row($list1);
          if(!$l) $l = array_fill(0, 10, "");
          
          if($pro[7]=="P")		
              $d1_discount=$pro[2]*$pro[6]*$pro[4]/100; // Qty * MRP * Disc%
          else
              $d1_discount=$pro[4]*$pro[2]; // Disc * Qty
          
          $dis+=$d1_discount;
          $amt=$pro[2]*$pro[3]; // Qty * Rate
          
          if($pro[5]==28)
              $taxableamt28+=$amt;
          else if($pro[5]==18)
              $taxableamt18+=$amt;
          else if($pro[5]==12)
              $taxableamt12+=$amt;
          else if($pro[5]==5)
              $taxableamt5+=$amt;
          
          $v1=$amt*$pro[5]/100;
          $amt=$amt+$v1;
          
          $igst+=$v1;						
                                  
          $amt = round($amt, 2); // Round to 2 decimals to match JS toFixed(2)
          $tot+=$amt;
          $tax+=$v1;
    ?>
      <tr>
        <td><?php echo $j; ?></td>
        <td class="desc"><?php echo "$l[1]-$l[5] $v[2] $v[3]"; ?></td>
        <td><?php echo $l[4]; ?></td>
        <td><?php echo $pro[2]; ?></td>
        <td><?php echo $l[6]; ?></td>
        <td class="num"><?php echo $pro[6]; ?></td>
        <td class="num"><?php echo number_format($d1_discount,2); ?></td>
        <td class="num"><?php echo number_format($pro[3],2); ?></td>
        <td><?php echo $pro[5]; ?></td>
        <td class="num"><?php echo number_format($v1,2); ?></td>
        <td class="num"><?php echo number_format($amt,2); ?></td>
      </tr>
      <?php
          $j++;
      }
      
      $num_rows_this_page = mysqli_num_rows($pro1);
      
      // Calculate how many rows perfectly fill the available physical page space
      if ($i == 1) {
          $target_rows = ($i == $count) ? $limit_p1_with_totals : $limit_p1_no_totals;
      } else {
          $target_rows = ($i == $count) ? $limit_pn_with_totals : $limit_pn_no_totals;
      }
      
      $empty_rows = $target_rows - $num_rows_this_page;
      if ($empty_rows > 0) {
          for ($e = 0; $e < $empty_rows; $e++) {
              echo '<tr class="blank-row"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
          }
      }
    ?>
    </tbody>
    <tr class="item-totals">
      <td colspan="8"></td>
      <td class="desc"><b>Total</b></td>
      <td class="num"><b><?php echo number_format($tax,2); ?></b></td>
      <td class="num"><b><?php echo number_format($tot,2); ?></b></td>
      </tr>
  </table>
  
  <?php if($i == $count) { 
        $dis1=$dis;
        $vat1=number_format($igst,2);
        $vattot=($igst);
  ?>
  <div class="invoice-footer" style="margin-top: auto;">
  <div class="totals-wrap">
    <table class="gst-table">
      <thead>
        <tr><th style="width:22%;">GST %</th><th>Taxable Amount</th><th>IGST</th></tr>
      </thead>
      <tbody>
        <?php if ($taxableamt5 > 0) { ?>
        <tr><td class="pct">5%</td><td><?php echo number_format($taxableamt5,2); ?></td><td><?php echo number_format(($taxableamt5*5/100),2); ?></td></tr>
        <?php } ?>
        <?php if ($taxableamt12 > 0) { ?>
        <tr><td class="pct">12%</td><td><?php echo number_format($taxableamt12,2); ?></td><td><?php echo number_format(($taxableamt12*12/100),2); ?></td></tr>
        <?php } ?>
        <?php if ($taxableamt18 > 0) { ?>
        <tr><td class="pct">18%</td><td><?php echo number_format($taxableamt18,2); ?></td><td><?php echo number_format(($taxableamt18*18/100),2); ?></td></tr>
        <?php } ?>
        <?php if ($taxableamt28 > 0) { ?>
        <tr><td class="pct">28%</td><td><?php echo number_format($taxableamt28,2); ?></td><td><?php echo number_format(($taxableamt28*28/100),2); ?></td></tr>
        <?php } ?>
      </tbody>
    </table>

    <table class="charges-table">
      <!-- <tr><td class="label">Total</td><td class="val"><?php //echo number_format($tot,2); ?></td></tr> -->
      <?php if ($d[9] != 0) { ?>
      <tr><td class="label">Spl. Discount</td><td class="val"><?php $tot=$tot-$d[9]; echo number_format($d[9],2); ?></td></tr>
      <?php } ?>
      <?php if ($d[11] != 0) { ?>
      <tr><td class="label">Freight</td><td class="val"><?php $tot=$tot+$d[11]; echo number_format($d[11],2); ?></td></tr>
      <?php } ?>
      <?php if ($d[10] != 0) { ?>
      <tr><td class="label">Transport</td><td class="val"><?php $tot=$tot+$d[10]; echo number_format($d[10],2); ?></td></tr>
      <?php } ?>
      <?php if ($d[12] != 0) { ?>
      <tr><td class="label" style="text-transform:uppercase;"><?php echo ($d[6] != '') ? $d[6] : 'Convenience Charges'; ?></td><td class="val"><?php $tot=$tot+$d[12]; echo number_format($d[12],2); ?></td></tr>
      <?php } ?>
      <?php if ($d[13] != 0) { ?>
      <tr><td class="label">Round Off</td><td class="val"><?php $tot=$tot+$d[13]; echo number_format($d[13],2); ?></td></tr>
      <?php } ?>
      <?php
		$round1=round($tot,0);
		$r=$round1-$tot;
		$grand=$tot+$r;
      ?>
      <tr class="grand"><td class="label" style="color:#fff;">Grand Total</td><td class="val"><?php echo number_format($grand,2); ?></td></tr>
    </table>
  </div>

  <div class="amount-words">
    <b>Amount Payable (in words):</b> INR <?php echo no_to_words($grand); ?> Only &nbsp;&nbsp;<span style="float:right;color:#888;">E. &amp; O.E</span>
  </div>

  <div class="bottom-grid">
    <div class="notes-col">
      <div class="h">Terms & Condition</div>
      <ol>
        <li>Warranty as per company rules &amp; conditions.</li>
        <li>Goods once sold can&rsquo;t be returned or exchanged.</li>
        <li>No Gaurantee Of Color.</li>
        <li>Payment should be 100% advance on order.</li>
        <li>All subject to Ahmedabad (Gujarat) jurisdiction.</li>
      </ol>
    </div>

    <div class="qr-col">
      <?php if ($i == $count) { 
        $upi_id = "9724200065-1@okbizaxis";
        $payee_name = "ETHIC DESIGN'S LLP";
        $upi_url = "upi://pay?pa={$upi_id}&pn=" . urlencode($payee_name) . "&cu=INR";
        if (isset($grand) && $grand > 0) {
            $upi_url .= "&am=" . urlencode($grand);
        }
        $qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($upi_url);
      ?>
      <img src="<?php echo $qr_image_url; ?>" alt="QR Code" width="120" height="120">
      <br>For Payment Scan Here
      <?php } ?>
    </div>

    <div class="bank-col">
      <div class="h">Bank Details</div>
      <div><b>Bank Name:</b> <?php echo $bank; ?></div>
      <div><b>A/c Name:</b> <?php echo $accname; ?></div>
      <div><b>A/c No.:</b> <?php echo $accno; ?></div>
      <div><b>Branch:</b> <?php echo $branch; ?></div>
      <div><b>IFSC:</b> <?php echo $ifsc; ?></div>
    </div>
  </div>

  <div class="for-company">For ETHIC DESIGN'S LLP</div>

  <?php echo ($i == $count) ? "<div class='signatures'><span>Customer&rsquo;s Signature</span><span class='sig-right'>Authorised Signatory</span></div>" : ""; ?>

  <div style="text-align: center; font-size: 7pt; color: #666; margin-top: 4mm;">
    This is a computer generated invoice.
  </div>
  
  </div> <!-- end invoice-footer -->
  <?php } // end if($i == $count) for footer ?>

</div>
</div>
<?php
    if($i < $count) {
        echo '<div id="new"></div>';
    }
}
?>
</div>
</body>
</html>