<?php
/* Copyright (C) 2014 Terry Hill <terry@lillysystems.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 */
// I used the program example supplied with the Avery Label Print Class to produce this program
//

include_once("../globals.php");
require_once('PDF_Label.php');

//Get the data to place on labels
//

$patdata = sqlQuery("SELECT " .
  "p.fname, p.mname, p.lname, p.pubpid, p.DOB, " .
  "p.street, p.city, p.state, p.postal_code, p.pid " .
  "FROM patient_data AS p " .
  "WHERE p.pid = '$pid' LIMIT 1");

// re-order the dates
//
  
$today = date('m/d/Y');
$dob   = substr($patdata['DOB'],5,2) ."/". Substr($patdata['DOB'],8,2) ."/". Substr($patdata['DOB'],0,4);

//get label type and number of labels on sheet
//

if ($GLOBALS['label_type'] == '1') { 
$pdf = new PDF_Label('5160');
$last = 30;
}

if ($GLOBALS['label_type'] == '2') { 
$pdf = new PDF_Label('5161');
$last = 20;
}

if ($GLOBALS['label_type'] == '3') { 
$pdf = new PDF_Label('5162');
$last = 14;
}

$pdf->AddPage();

// For loop for printing the labels 
// 

for($i=1;$i<=$last;$i++) {
	$text = sprintf("%s %s\n%s\n%s\n%s", $patdata['fname'], $patdata['lname'], $dob, $today, $patdata['pid']);
	$pdf->Add_Label($text);
}

$pdf->Output();
?>
