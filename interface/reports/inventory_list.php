<?php
 // Copyright (C) 2008-2010 Rod Roark <rod@sunsetsystems.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 2015-06-20 - brought up to security standards terry@lillysystems.com
 
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

 require_once("../globals.php");
 require_once("$srcdir/acl.inc");
 require_once("$srcdir/options.inc.php");
 require_once("$include_root/drugs/drugs.inc.php");

 // Check authorization.
 $thisauth = acl_check('admin', 'drugs');
 if (!$thisauth) die(xl('Not authorized'));

function addWarning($msg) {
  global $warnings;
  if ($warnings) $warnings .= '<br />';
  $warnings .= $msg;
}

// this is "" or "submit".
$form_action = $_POST['form_action'];

if (!empty($_POST['form_days'])) {
  $form_days = $_POST['form_days'] + 0;
}
else {
  $form_days = sprintf('%d', (strtotime(date('Y-m-d')) - strtotime(date('Y-01-01'))) / (60 * 60 * 24) + 1);
}

// get drugs
$res = sqlStatement("SELECT d.*, SUM(di.on_hand) AS on_hand " .
  "FROM drugs AS d " .
  "LEFT JOIN drug_inventory AS di ON di.drug_id = d.drug_id " .
  "AND di.on_hand != 0 AND di.destroy_date IS NULL " .
  "WHERE d.active = 1 " .
  "GROUP BY d.name, d.drug_id ORDER BY d.name, d.drug_id");
?>
<html>

<head>
<?php html_header_show(); ?>

<link rel="stylesheet" href='<?php  echo $css_header ?>' type='text/css'>
<title><?php  echo xlt('Inventory List'); ?></title>

<style>
/* specifically include & exclude from printing */
@media print {
 #report_parameters {visibility: hidden; display: none;}
 #report_parameters_daterange {visibility: visible; display: inline;}
 #report_results {margin-top: 30px;}
}
/* specifically exclude some from the screen */
@media screen {
 #report_parameters_daterange {visibility: hidden; display: none;}
}

body       { font-family:sans-serif; font-size:10pt; font-weight:normal }

tr.head   { font-size:10pt; background-color:#cccccc; text-align:center; }
tr.detail { font-size:10pt; }
a, a:visited, a:hover { color:#0000cc; }
</style>

<script type="text/javascript" src="../../library/dialog.js"></script>

<script language="JavaScript">
 function mysubmit(action) {
  var f = document.forms[0];
  f.form_action.value = action;
  top.restoreSession();
  f.submit();
 }
</script>

</head>

<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0' class='body_top'>

<div style="text-align: center;">

<h2><?php echo xlt('Inventory List')?></h2>

<form method='post' action='inventory_list.php' name='theform' onsubmit='return top.restoreSession()'>

<div id="report_parameters">
<!-- form_action is set to "submit" at form submit time -->
<input type='hidden' name='form_action' value='' />
<table>
 <tr>
  <td width='50%'>
   <table class='text'>
    <tr>
     <td nowrap>
      <?php echo xlt('For the past'); ?>
      <input type="input" name="form_days" size='3' value="<?php echo attr($form_days); ?>" />
      <?php echo xlt('days'); ?>
     </td>
    </tr>
   </table>
  </td>
  <td align='left' valign='middle'>
   <table style='border-left:1px solid; width:100%; height:100%'>
    <tr>
     <td valign='middle'>
      <a href='#' class='css_button' onclick='mysubmit("submit")' onsubmit='return top.restoreSession()' style='margin-left:1em'>
       <span><?php echo xlt('Submit'); ?></span>
      </a>
<?php if ($form_action) { ?>
      <a href='#' class='css_button' onclick='window.print()' onsubmit='return top.restoreSession()' style='margin-left:1em'>
       <span><?php echo xlt('Print'); ?></span>
      </a>
<?php } ?>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</div>

<?php if ($form_action) { // if submit ?>

<div id="report_results">
<table border='0' cellpadding='1' cellspacing='2' width='98%'>
 <thead style='display:table-header-group'>
  <tr class='head'>
   <th><?php echo xlt('Name'); ?></th>
   <th><?php echo xlt('NDC'); ?></th>
   <th><?php echo xlt('Form'); ?></th>
   <th align='right'><?php echo xlt('QOH'); ?></th>
   <th align='right'><?php echo xlt('Reorder'); ?></th>
   <th align='right'><?php echo xlt('Avg Monthly'); ?></th>
   <th align='right'><?php echo xlt('Stock Months'); ?></th>
   <th><?php echo xlt('Warnings'); ?></th>
  </tr>
 </thead>
 <tbody>
<?php 
$encount = 0;
while ($row = sqlFetchArray($res)) {
  $on_hand = 0 + $row['on_hand'];
  $drug_id = 0 + $row['drug_id'];
  $warnings = '';

  $srow = sqlQuery("SELECT " .
    "SUM(quantity) AS sale_quantity " .
    "FROM drug_sales WHERE " .
    "drug_id = ? AND " .
    "sale_date > DATE_SUB(NOW(), INTERVAL $form_days DAY) " .
    "AND pid != 0", array($drug_id));

  ++$encount;
  $bgcolor = "#" . (($encount & 1) ? "ddddff" : "ffdddd");

  $sale_quantity = $srow['sale_quantity'];
  $months = $form_days / 30.5;

  $monthly = ($months && $sale_quantity) ?
    sprintf('%0.1f', $sale_quantity / $months) : '&nbsp;';

  $stock_months = '&nbsp;';
  if ($sale_quantity != 0) {
    $stock_months = sprintf('%0.1f', $on_hand * $months / $sale_quantity);
    if ($stock_months < 1.0) {
      addWarning(xlt('QOH is less than monthly usage'));
    }
  }

  // Check for reorder point reached.
  if (!empty($row['reorder_point']) && $on_hand <= $row['reorder_point']) {
    addWarning(xlt('Reorder point has been reached'));
  }

  // Compute the smallest quantity that might be taken from a lot based on the
  // past 30 days of sales.  If lot combining is allowed this is always 1.
  $min_sale = 1;
  if (!$row['allow_combining']) {
    $sminrow = sqlQuery("SELECT " .
      "MIN(quantity) AS min_sale " .
      "FROM drug_sales WHERE " .
      "drug_id = ? AND " .
      "sale_date > DATE_SUB(NOW(), INTERVAL $form_days DAY) " .
      "AND pid != 0 " .
      "AND quantity > 0", array($drug_id));
    $min_sale = 0 + $sminrow['min_sale'];
  }

  // Get all lots that we want to issue warnings about.  These are lots
  // expired, soon to expire, or with insufficient quantity for selling.
  $ires = sqlStatement("SELECT * " .
    "FROM drug_inventory WHERE " .
    "drug_id = ? AND " .
    "on_hand > 0 AND " .
    "destroy_date IS NULL AND ( " .
    "on_hand < ? OR " .
    "expiration IS NOT NULL AND expiration < DATE_ADD(NOW(), INTERVAL 30 DAY) " .
    ") ORDER BY lot_number", array($drug_id, $min_sale));

  // Generate warnings associated with individual lots.
  while ($irow = sqlFetchArray($ires)) {
    $lotno = $irow['lot_number'];
    if ($irow['on_hand'] < $min_sale) {
      addWarning(xlt('Lot') . " '$lotno' " . xlt('quantity seems unusable'));
    }
    if (!empty($irow['expiration'])) {
      $expdays = (int) ((strtotime($irow['expiration']) - time()) / (60 * 60 * 24));
      if ($expdays <= 0) {
        addWarning(xlt('Lot') . " '$lotno' " . xlt('has expired'));
      }
      else if ($expdays <= 30) {
        addWarning(xlt('Lot') . " '$lotno' " . xlt('expires in') . " $expdays " . xlt('days'));
      }
    }
  }

  echo " <tr class='detail' bgcolor='$bgcolor'>\n";
  echo "  <td>" . text($row['name']) . "</td>\n";
  echo "  <td>" . text($row['ndc_number']) . "</td>\n";
  echo "  <td>" .
       generate_display_field(array('data_type'=>'1','list_id'=>'drug_form'), $row['form']) .
       "</td>\n";
  echo "  <td align='right'>" . text($row['on_hand']) . "</td>\n";
  echo "  <td align='right'>" . text($row['reorder_point']) . "</td>\n";
  echo "  <td align='right'>" . attr($monthly) ."</td>\n";
  echo "  <td align='right'>" . attr($stock_months) ."</td>\n";
  echo "  <td style='color:red'>" . attr($warnings) . "</td>\n";
  echo " </tr>\n";
 }
?>
 </tbody>
</table>

<?php } // end if submit ?>

</form>
</div>
</body>
</html>
