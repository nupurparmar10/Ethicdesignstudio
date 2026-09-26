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
  
  .page {
         margin: 5mm;
  }
  @media print {
    .no-print {
      display: none !important;
    }
    .page {
      overflow: visible !important;
      -webkit-box-decoration-break: clone;
      box-decoration-break: clone;
    }
    .sheet {
      display: block !important;
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
    font-size: 11pt;
    color: #262322;
    line-height: 1.3;
  }
  .page {
    position: relative;
    width: 198mm;
    min-height: 285mm;
    height: max-content;
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
    z-index: 10;
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
    font-size: 10.5pt;
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
  .brand img { height: 16mm; display: block; }
  .invoice-tag {
    text-align: right;
  }
  .invoice-tag .tax-invoice {
    display: inline-block;
    background: #e0672c;
    color: #fff;
    font-weight: 700;
    font-size: 13pt;
    letter-spacing: 1.2px;
    padding: 1.5mm 4mm;
    border-radius: 2px;
    margin-bottom: 1mm;
  }
  .invoice-tag .meta-row {
    font-size: 11pt;
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
    font-size: 11.5pt;
    font-weight: 700;
    color: #e0672c;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 1.5mm;
    border-bottom: 0.5pt dotted #cbb7ab;
    padding-bottom: 1.2mm;
  }
  .party-box.billed .party-title { color: #6d6355; }
  .company-name { font-weight: 700; font-size: 12pt; margin-bottom: 1.2mm; }
  .line { margin-bottom: 0.5mm; }
  .icon-line { display: flex; gap: 1.3mm; margin-bottom: 0.5mm; }
  .icon-line .ic {
    flex: 0 0 auto;
    width: 4mm; height: 4mm;
    border-radius: 50%;
    background: #e0672c;
    color: #fff;
    font-size: 9pt;
    text-align: center;
    line-height: 4mm;
  }
  .party-box.billed .icon-line .ic { background: #e0672c; }
  .icon-line .txt { flex: 1; word-break: break-word; }
  .gstin-tag {
    margin-top: 1.5mm;
    display: inline-block;
    font-weight: 700;
    font-size: 10.5pt;
    background: #f1e3da;
    color: #a2481a;
    padding: 1mm 2mm;
    border-radius: 2px;
  }
  .party-box.billed .gstin-tag { background: #efece4; color: #5b5344; }

  .pay-strip {
    display: flex;
    justify-content: space-between;
    font-size: 10.5pt;
    margin-top: 2mm;
    padding-top: 1.5mm;
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
    font-size: 8pt;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 1.5mm 1.5mm;
    border: 0.5pt solid #c85a22;
    text-align: center;
  }
  table.items tbody td {
    padding: 1.5mm 1.5mm;
    border: 0.5pt solid #e6dcd3;
    text-align: center;
    font-size: 9pt;
  }
  table.items tbody tr:nth-child(even) { background: #fdf8f5; }
  table.items td.desc { text-align: left; }
  table.items td.num { text-align: right; padding-right: 2.5mm; }
  table.items tbody tr.blank-row td { border-bottom: none; border-top: none; height: 7mm; }
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
    font-size: 9.5pt;
    padding: 1.5mm;
    border: 0.5pt solid #e6dcd3;
  }
  .gst-table td {
    font-size: 10pt;
    padding: 1.5mm;
    border: 0.5pt solid #e6dcd3;
    text-align: right;
    padding-right: 2.5mm;
  }
  .gst-table td.pct { text-align: center; padding-right: 1mm; }

  .charges-table {
    flex: 1;
    border-collapse: collapse;
  }
  .charges-table td {
    font-size: 10.5pt;
    padding: 1.2mm 2mm;
    border: 0.5pt solid #e6dcd3;
  }
  .charges-table td.label { color: #555; }
  .charges-table td.val { text-align: right; font-weight: 600; }
  .charges-table tr.grand td {
    background: #e0672c;
    color: #fff;
    font-weight: 700;
    font-size: 11.5pt;
    border-color: #c85a22;
  }

  .amount-words {
    background: #fdf8f5;
    border: 0.6pt solid #ddc9bd;
    border-radius: 2px;
    padding: 1.5mm 3mm;
    font-size: 11pt;
    margin-bottom: 2mm;
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
    font-size: 12px;
    color: #444;
  }
  .notes-col .h { font-weight: 700; color: #e0672c; font-size: 10.5pt; margin-bottom: 1mm; text-transform: uppercase; letter-spacing: 0.4px; }
  .notes-col ol { margin: 0; padding-left: 4mm; }
  .notes-col li { margin-bottom: 0.5mm; }

  .qr-col {
    flex: 0 0 auto;
    text-align: center;
    font-size: 9pt;
    color: #555;
  }
  .qr-col img { width: 32mm; height: 32mm; display: block; margin: 0 auto 1mm; border: 0.5pt solid #ddc9bd; padding: 1mm; }

  .bank-col {
    flex: 1;
    font-size: 12px;
    border: 0.6pt solid #ddc9bd;
    border-radius: 2px;
    padding: 1.5mm 2.5mm;
    background: #fdf8f5;
  }
  .bank-col .h { font-weight: 700; color: #a2481a; font-size: 10.5pt; margin-bottom: 1mm; }
  .bank-col div { margin-bottom: 0.5mm; }

  /* ---------- SIGNATURE ---------- */
  .signatures {
    display: flex;
    justify-content: space-between;
    margin-top: 8mm;
    padding-top: 1.5mm;
    border-bottom: 0.6pt solid #e0672c;
    font-size: 10.5pt;
    font-weight: 600;
  }
  .for-company {
    text-align: right;
    font-size: 10.5pt;
    font-weight: 700;
    color: #333;
    margin-bottom: 4mm;
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
	$amt1=0; $tot=0; $dis=0; $igst=0; $j=1; $tax=0;
	$taxableamt28=0; $taxableamt18=0; $taxableamt12=0; $taxableamt5=0; $exempted=0;
    $sgst28=0; $cgst28=0; $sgst18=0; $cgst18=0; $sgst12=0; $cgst12=0; $sgst5=0; $cgst5=0;

	$k1=mysqli_query($con,"select name from ledger_accounts where ledger_id=$d[2]");
	$k=mysqli_fetch_row($k1);
	$party1=mysqli_query($con,"select * from ledger_details where ledger_id=$d[2]");	
    if($p1=mysqli_fetch_row($party1)){} else { for($i=0;$i<=5;$i++) $p1[$i]=""; }
	$emp1=mysqli_query($con,"select empname from empdet where ledger_id='$d[17]'");
	if($emp=mysqli_fetch_row($emp1)) {} else $emp[0]="";

    // 1. Fetch all items and estimate line-height footprint
    $all_items = [];
    $pro1=mysqli_query($con,"select * from bill_items where sale_id='".$_REQUEST['sale_id']."'");
    while($pro=mysqli_fetch_row($pro1)) {
        $v=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$pro[1]'"));
        if(!$v) $v = array_fill(0, 10, "");
        $list1=mysqli_query($con,"select * from item_details where item_id='$v[1]'");
        $l=mysqli_fetch_row($list1);
        if(!$l) $l = array_fill(0, 10, "");
        
        $desc = "$l[1]-$l[5] $v[2] $v[3]";
        // Estimate lines (approx 35 chars fit per line on average for standard sizes)
        $lines = ceil(strlen($desc) / 35);
        if ($lines < 1) $lines = 1;
        
        $all_items[] = [ 'pro' => $pro, 'desc' => $desc, 'l' => $l, 'v' => $v, 'lines' => $lines ];
    }

?>
<div class="page" >
  <div class="watermark">
    <div class="watermark-inner">
      <img src="assets/admin.png" alt="">
    </div>
  </div>

<div class="sheet">

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
     

      <span class="gstin-tag">GSTIN: 24AAJFE0234H1ZV</span>
    </div>

    <div class="party-box billed">
      <div class="party-title">Billed To</div>
      <div class="company-name">M/s <?php echo (!empty($k[0])) ? $k[0] : ''; ?></div>
      <?php if (!empty($p1[2])) { ?>
      <div class="icon-line" style="margin-bottom: 1mm;"><span class="ic">&#127970;</span><span class="txt"><?php echo $p1[2]; ?></span></div>
      <?php } ?>
      <div class="icon-line"><span class="ic">&#128241;</span><span class="txt"><?php echo (!empty($p1[4])) ? $p1[4] : ((!empty($d[8])) ? $d[8] : '-'); ?></span></div>
      <div class="pay-strip" style="align-items: center;">
        <span class="gstin-tag" style="margin-top: 0;">GSTIN: <?php echo (!empty($p1[3])) ? $p1[3] : '-'; ?></span>
        <span><b>Paid By:</b> <?php 
        if($d[7]=="3") {
            echo "Cash"; 
        } else if($d[7]=="Credit") {
            echo "Credit"; 
        } else {
            $paid_led = mysqli_fetch_row(mysqli_query($con, "select name from ledger_accounts where ledger_id='$d[7]'"));
            echo ($paid_led[0] != "") ? $paid_led[0] : "";
        } 
        ?></span>
      </div>
    </div>
  </div>

  <table class="items" style="min-height: 105mm;">
    <thead>
      <tr >
        <th style="width:5%;">S.No.</th>
        <th style="width:27%;">Description of Goods<br><span style="font-weight:400;font-size:6.2pt;text-transform:none;">(Code - Name - Variant)</span></th>
        <th style="width:8%;">HSN</th>
        <th style="width:6%;">Qty</th>
        <th style="width:8%;">Unit</th>
        <th style="width:9%;">MRP</th>
        <th style="width:9%;">Disc</th>
        <th style="width:9%;">Rate</th>
        <th style="width:7%;">Tax (%)</th>
        <th style="width:7%;">Tax Amt.</th>
        <th style="width:10%;">Amount</th>
      </tr>
    </thead>
    <tbody>
    <?php
      foreach ($all_items as $p_item) {
          $pro = $p_item['pro'];
          $l = $p_item['l'];
          $v = $p_item['v'];
          $desc = $p_item['desc'];
          
          if($pro[7]=="P")		
              $d1_discount=$pro[2]*$pro[6]*$pro[4]/100; // Qty * MRP * Disc%
          else
              $d1_discount=$pro[4]*$pro[2]; // Disc * Qty
          
          $dis+=$d1_discount;
          // Recalculate base amount dynamically to match addsales.php JS precision
          $amt = ($pro[2] * $pro[6]) - $d1_discount;
          
          // Calculate total tax, round it once, then split (matching JS exactly)
          $v1 = round($amt * $pro[5] / 100, 2);
          $item_sgst = $v1 / 2;
          $item_cgst = $v1 / 2;
          
          if($pro[5]==28) { $taxableamt28+=$amt; $sgst28+=$item_sgst; $cgst28+=$item_cgst; }
          else if($pro[5]==18) { $taxableamt18+=$amt; $sgst18+=$item_sgst; $cgst18+=$item_cgst; }
          else if($pro[5]==12) { $taxableamt12+=$amt; $sgst12+=$item_sgst; $cgst12+=$item_cgst; }
          else if($pro[5]==5) { $taxableamt5+=$amt; $sgst5+=$item_sgst; $cgst5+=$item_cgst; }
          
          // Round final amount matching JS
          $amt = round($amt + $v1, 2);
          
          $igst+=$v1;						
                                  
          $amt = round($amt, 2);
          $tot+=$amt;
          $tax+=$v1;
    ?>
      <tr>
        <td class="text-center"><?php echo $j; ?></td>
        <td class="desc"><?php echo $desc; ?></td>
        <td class="text-center"><?php echo $l[4]; ?></td>
        <td class="text-center"><?php echo $pro[2]; ?></td>
        <td class="text-center"><?php echo $l[6]; ?></td>
        <td class="text-center"><?php echo $pro[6]; ?></td>
        <td class="num"><?php echo number_format($d1_discount,2); ?></td>
        <td class="num"><?php echo number_format($pro[3],2); ?></td>
        <td class="text-center"><?php echo $pro[5]; ?></td>
        <td class="num"><?php echo number_format($v1,2); ?></td>
        <td class="num"><?php echo number_format($amt,2); ?></td>
      </tr>
      <?php
          $j++;
         
      }
    ?>
      <!-- Filler row to stretch the table height -->
      <tr class="blank-row" style="height: 100%;">
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
        <td style="border-bottom:none; border-top:none;"></td>
      </tr>
    </tbody>
    <tr class="item-totals">
      <td colspan="8"></td>
      <td class="desc"><b>Total</b></td>
      <td class="num"><b><?php echo number_format($tax,2); ?></b></td>
      <td class="num"><b><?php echo number_format($tot,2); ?></b></td>
      </tr>
  </table>
  
  <?php 
        $dis1=$dis;
        $vat1=number_format($igst,2);
        $vattot=($igst);
  ?>
  <div class="invoice-footer" style="margin-top: auto;">
  <div class="totals-wrap">
    <table class="gst-table">
      <thead>
        <tr><th style="width:22%;">GST %</th><th class="num">Taxable Amount</th><th class="num">SGST</th><th class="num">CGST</th></tr>
      </thead>
      <tbody>
        <?php if ($taxableamt5 > 0) { ?>
        <tr><td class="pct">5%</td><td class="num"><?php echo number_format($taxableamt5,2); ?></td><td class="num"><?php echo number_format($sgst5,2); ?></td><td class="num"><?php echo number_format($cgst5,2); ?></td></tr>
        <?php } ?>
        <?php if ($taxableamt12 > 0) { ?>
        <tr><td class="pct">12%</td><td class="num"><?php echo number_format($taxableamt12,2); ?></td><td class="num"><?php echo number_format($sgst12,2); ?></td><td class="num"><?php echo number_format($cgst12,2); ?></td></tr>
        <?php } ?>
        <?php if ($taxableamt18 > 0) { ?>
        <tr><td class="pct">18%</td><td class="num"><?php echo number_format($taxableamt18,2); ?></td><td class="num"><?php echo number_format($sgst18,2); ?></td><td class="num"><?php echo number_format($cgst18,2); ?></td></tr>
        <?php } ?>
        <?php if ($taxableamt28 > 0) { ?>
        <tr><td class="pct">28%</td><td class="num"><?php echo number_format($taxableamt28,2); ?></td><td class="num"><?php echo number_format($sgst28,2); ?></td><td class="num"><?php echo number_format($cgst28,2); ?></td></tr>
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
		$grand=$tot;
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
      <?php
        $upi_id = "9724200065-1@okbizaxis";
        $payee_name = "ETHIC DESIGN'S LLP";
        $upi_url = "upi://pay?pa={$upi_id}&pn=" . urlencode($payee_name) . "&cu=INR";
        if (isset($grand) && $grand > 0) {
            $upi_url .= "&am=" . urlencode($grand);
        }
        $qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($upi_url);
      ?>
      <img src="<?php echo $qr_image_url; ?>" alt="QR Code" width="150" height="150">
      For Payment Scan Here
      <div style="display: flex; gap: 3mm; justify-content: center; flex-wrap: wrap; text-align:center;">
        <a href="mailto:ethicdesignstudio@gmail.com" style="text-decoration: none; color: black;">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 512 512" fill="currentColor" style="vertical-align: middle;"><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
        </a>
        <a href="https://www.ethicdesignstudio.com" target="_blank" style="text-decoration: none; color: black;">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 512 512" fill="currentColor" style="vertical-align: middle;"><path d="M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64h185.4c2.2 20.4 3.3 41.8 3.3 64zm28.8-64h123.1c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0H18.6C48.6 88.5 112.3 31.7 190.6 11c-25.5 34.2-45.3 87.7-55.3 151.6zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM190.6 501c-25.5-34.2-45.3-87.7-55.3-151.6H18.6c30 74.1 93.6 130.9 172 151.6zm130.1 0c78.3-20.7 142-77.5 171.9-151.6H376.7c-10 63.9-29.8 117.4-55.3 151.6zM256 512c-7.3 0-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5c-11.6-26-20.9-58.2-27-94.7h176.6c-6.1 36.4-15.5 68.6-27 94.7c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512z"/></svg>
        </a>
        <a href="https://www.instagram.com/ethicdesignstudio" target="_blank" style="text-decoration: none; color: black;">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" style="vertical-align: middle;">
            <defs>
              <linearGradient id="ig-grad" x1="0%" y1="100%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#f09433" />
                <stop offset="25%" stop-color="#e6683c" />
                <stop offset="50%" stop-color="#dc2743" />
                <stop offset="75%" stop-color="#cc2366" />
                <stop offset="100%" stop-color="#bc1888" />
              </linearGradient>
            </defs>
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="url(#ig-grad)"/>
            <circle cx="12" cy="12" r="4.5" fill="none" stroke="#fff" stroke-width="2"/>
            <circle cx="17.5" cy="6.5" r="1" fill="#fff"/>
          </svg>
        </a>
      </div>
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

  <div class='signatures'><span>Customer&rsquo;s Signature</span><span class='sig-right'>Authorised Signatory</span></div>

  <div style="text-align: center; font-size: 7pt; color: #666; margin-top: 1mm;">
    This is a computer generated invoice.
  </div>
  
  </div> <!-- end invoice-footer -->
</div>
</div>
</div>
</body>
</html>