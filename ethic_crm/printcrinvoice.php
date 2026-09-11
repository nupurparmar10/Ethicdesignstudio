<?php
	ob_start();
	 session_start();
     include_once("connect.php");
	 $msg="";
	 if(!isset($_REQUEST['sale_id']))
	 {
	 	header("Location: viewsaleretun.php"); die;
	 }
     $accno="bank account no.";
     $bank="BANK NAME";
     $branch="BANK BRANCH";
     $ifsc="IFSCCODE";
     $accname="Ethic Design Studio";
	
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
$d1=mysqli_query($con,"select * from billreturn where sale_id='".$_REQUEST['sale_id']."'");
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
    size: A5;
    margin: 0;
  }
  @media print {
    .no-print {
      display: none !important;
    }
    #new {page-break-before: always;}
    .page {
      height: 210mm !important; 
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
    width: 148mm;
    min-height: 210mm;
    padding: 5mm 7mm 5mm 7mm;
    border: 1.4pt solid #e0672c;
    outline: 0.5pt solid #e0672c;
    outline-offset: -2.6mm;
    overflow: hidden;
    margin-bottom: 5mm;
  }

  /* watermark */
  .watermark {
    position: absolute;
    top: 30mm;
    left: 0;
    width: 148mm;
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
  .sheet { position: relative; z-index: 1; width: 100%; }

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
  .brand img { height: 8mm; display: block; }
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
  .party-box.billed .icon-line .ic { background: #7a715f; }
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
  table.items tbody tr.filler td { border-left: 0.5pt solid #e6dcd3; border-right: 0.5pt solid #e6dcd3; height: 8mm; }

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
  .qr-col img { width: 15mm; height: 15mm; display: block; margin: 0 auto 0.6mm; border: 0.5pt solid #ddc9bd; padding: 0.6mm; }

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
    margin-top: 2.6mm;
    padding-top: 1.2mm;
    border-top: 0.6pt solid #e0672c;
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
    var element = document.body;
    var toolbar = document.querySelector('.print-toolbar');
    toolbar.style.display = 'none';

    var opt = {
      margin:       0,
      filename:     'Return_Invoice_<?php echo $d[3]; ?>.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2, useCORS: true },
      jsPDF:        { unit: 'mm', format: 'a5', orientation: 'portrait' }
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

<?php
	$c1=mysqli_query($con,"select count(*) from sr_items where sale_id='$_REQUEST[sale_id]'");
	$c=mysqli_fetch_row($c1);
	$total_items = $c[0];
	if ($total_items == 0) $total_items = 1;
	$limit1 = 10;
	$limit2 = 16;
	if ($total_items <= $limit1) {
		$count = 1;
	} else {
		$count = 1 + ceil(($total_items - $limit1) / $limit2);
	}
	$amt1=0;
	$tot=0;
	$dis=0;
	$igst=0;
	$j=1;
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
	$emp1=mysqli_query($con,"select empname from empdet where ledger_id='$d[16]'");
	if($emp=mysqli_fetch_row($emp1)) {} else $emp[0]="";

	for($i=1;$i<=$count;$i++)
	{
        if ($i == 1) {
            $limit = $limit1;
            $start = 0;
        } else {
            $limit = $limit2;
            $start = $limit1 + ($i - 2) * $limit2;
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
      <div class="tax-invoice">SALE RETURN INVOICE</div>
      <div class="meta-row"><b>Return Invoice No.:</b> <?php echo $d[3]; ?></div>
      <div class="meta-row"><b>Date:</b> <?php if($d[1]!="0000-00-00"){ $date= DateTime::createFromFormat('Y-m-d', $d[1]); echo $date->format('M d, Y'); } ?></div>
      <div class="meta-row"><b>Salesman:</b> <?php echo ($emp[0] != "") ? $emp[0] : "&mdash;"; ?></div>
    </div>
  </div>

  <div class="parties">
    <div class="party-box">
      <div class="party-title">Ethic Designs LLP</div>
      <div class="line"><b>Main Branch:</b> 2370/71, Rani No Haziro, Manek Chowk, Ahmedabad &ndash; 380001.</div>
      <div class="line"><b>Branch(2):</b> 100, Lavanya Society, Nr. Jivraj Mehta Hospital, Vasna, Ahmedabad &ndash; 380007.</div>
      <div class="icon-line"><span class="ic">&#9742;</span><span class="txt">9824077818, 9825162255, 8980060002</span></div>
      <div class="icon-line"><span class="ic">@</span><span class="txt">ethicdesignstudio@gmail.com</span></div>
      <div class="icon-line"><span class="ic">&#127760;</span><span class="txt">www.ethicdesignstudio.com</span></div>
      <div class="icon-line">
        <span class="ic">&#128247;</span>
        <span class="txt">
          <a href="https://www.instagram.com/ethicdesignstudio" target="_blank" style="text-decoration: none; color: inherit;">
            instagram.com/ethicdesignstudio
          </a>
        </span>
      </div>

      <span class="gstin-tag">GSTIN: 24AAJFE0234H1ZV</span>
    </div>

    <div class="party-box billed">
      <div class="party-title">Billed To</div>
      <div class="company-name">M/s <?php echo (!empty($k[0])) ? $k[0] : ''; ?></div>
      <div class="icon-line"><span class="ic">&#9742;</span><span class="txt"><?php echo (!empty($p1[4])) ? $p1[4] : ((!empty($d[6])) ? $d[6] : '-'); ?></span></div>
      <div class="icon-line"><span class="ic">@</span><span class="txt"><?php echo (!empty($p1[5])) ? $p1[5] : '-'; ?></span></div>
      <div class="icon-line"><span class="ic">&#128100;</span><span class="txt"><?php echo (!empty($p1[1])) ? $p1[1] : '-'; ?></span></div>
      <span class="gstin-tag">GSTIN: <?php echo (!empty($p1[4])) ? $p1[4] : '-'; ?></span>
      <div class="pay-strip">
        <span><b>Paid By:</b> <?php if($d[5]=="3") echo "Cash"; else if($d[5]=="Credit") echo "Credit"; else echo "Cheque"; ?></span>
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
		$pro1=mysqli_query($con,"select * from sr_items where sale_id='".$_REQUEST['sale_id']."' LIMIT $start, $limit");
		while($pro=mysqli_fetch_row($pro1))
		{
            $v=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$pro[1]'"));
            if(!$v) $v = array_fill(0, 10, "");
            $list1=mysqli_query($con,"select * from item_details where item_id='$v[1]'");
            $l=mysqli_fetch_row($list1);
            if(!$l) $l = array_fill(0, 10, "");
            
            if($pro[7]=="P")		
                $d1_discount=$pro[6]*$pro[4]/100;
            else
                $d1_discount=$pro[4]*$pro[2];
            
            $dis+=$d1_discount;
            $amt=$pro[2]*$pro[3];
            
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
                                    
            $tot+=$amt;
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
      ?>
      <tr class="filler"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    </tbody>
  </table>

  <?php if($i == $count) { 
        $dis1=$dis;
        $vat1=number_format($igst,2);
        $vattot=($igst);
  ?>
  <div class="totals-wrap">
    <table class="gst-table">
      <thead>
        <tr><th style="width:22%;">GST %</th><th>Taxable Amount</th><th>IGST</th></tr>
      </thead>
      <tbody>
        <tr><td class="pct">5%</td><td><?php echo number_format($taxableamt5,2); ?></td><td><?php echo number_format(($taxableamt5*5/100),2); ?></td></tr>
        <tr><td class="pct">12%</td><td><?php echo number_format($taxableamt12,2); ?></td><td><?php echo number_format(($taxableamt12*12/100),2); ?></td></tr>
        <tr><td class="pct">18%</td><td><?php echo number_format($taxableamt18,2); ?></td><td><?php echo number_format(($taxableamt18*18/100),2); ?></td></tr>
        <tr><td class="pct">28%</td><td><?php echo number_format($taxableamt28,2); ?></td><td><?php echo number_format(($taxableamt28*28/100),2); ?></td></tr>
      </tbody>
    </table>

    <table class="charges-table">
      <tr><td class="label">Total</td><td class="val"><?php echo number_format($tot,2); ?></td></tr>
      <tr><td class="label">Spl. Discount</td><td class="val"><?php $tot=$tot-$d[7]; echo number_format($d[7],2); ?></td></tr>
      <tr><td class="label">Freight</td><td class="val"><?php $tot=$tot+$d[9]; echo number_format($d[9],2); ?></td></tr>
      <tr><td class="label">Transport</td><td class="val"><?php $tot=$tot+$d[8]; echo number_format($d[8],2); ?></td></tr>
      <tr><td class="label" style="text-transform:uppercase;"><?php echo ($d[18] != '') ? $d[18] : 'Convenience Charges'; ?></td><td class="val"><?php $tot=$tot+$d[10]; echo number_format($d[10],2); ?></td></tr>
      <tr><td class="label">Round Off</td><td class="val"><?php $tot=$tot+$d[11]; echo number_format($d[11],2); ?></td></tr>
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
  <?php } ?>

  <div class="bottom-grid">
    <div class="notes-col">
      <div class="h">Notes</div>
      <ol>
        <li>Warranty as per company rules &amp; conditions.</li>
        <li>Goods once sold can&rsquo;t be returned or exchanged.</li>
        <li>Rate should be valid for 7 days only.</li>
        <li>Packing &amp; Transportation charges extra.</li>
        <li>Payment should be 100% advance on order.</li>
        <li>All subject to Ahmedabad (Gujarat) jurisdiction.</li>
      </ol>
    </div>

    <div class="qr-col">
      <img src="img/vhspay.jpg" alt="QR Code">
      For Payment Scan Here
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

  <div class="for-company">For ETHIC DESIGNS LLP</div>

  <div class="signatures">
    <span>Customer&rsquo;s Signature</span>
    <span class="sig-right">Authorised Signatory</span>
  </div>

</div>
</div>
<?php
    if($i < $count) {
        echo '<div id="new"></div>';
    }
}
?>

</body>
</html>