<?php
<<<<<<< HEAD
/** 
 * interface/patient_file/label.php Displaying a PDF file of Labels for printing. 
 * 
 * Program for displaying Chart Labels 
 * via the popups on the left nav screen
 * 
 * Copyright (C) 2014 Terry Hill <terry@lillysystems.com> 
 * 
 * LICENSE: This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 3 
 * of the License, or (at your option) any later version. 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details. 
 * You should have received a copy of the GNU General Public License 
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;. 
 * 
 * @package OpenEMR 
 * @author Terry Hill <terry@lillysystems.com>
 * @link http://www.open-emr.org 
 */
// I used the program example supplied with the Avery Label Print Class to produce this program

$fake_register_globals=false;
$sanitize_all_escapes=true;

require_once("../globals.php");
require_once("$srcdir/classes/PDF_Label.php");
require_once("$srcdir/formatting.inc.php");

//Get the data to place on labels
//
=======
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

>>>>>>> Files to allow Chart labels
$patdata = sqlQuery("SELECT " .
  "p.fname, p.mname, p.lname, p.pubpid, p.DOB, " .
  "p.street, p.city, p.state, p.postal_code, p.pid " .
  "FROM patient_data AS p " .
<<<<<<< HEAD
  "WHERE p.pid = ? LIMIT 1", array($pid));
=======
  "WHERE p.pid = '$pid' LIMIT 1");
>>>>>>> Files to allow Chart labels

// re-order the dates
//
  
<<<<<<< HEAD
$today = oeFormatShortDate($date='today');
$dob = oeFormatShortDate($patdata['DOB']);
=======
$today = date('m/d/Y');
$dob   = substr($patdata['DOB'],5,2) ."/". Substr($patdata['DOB'],8,2) ."/". Substr($patdata['DOB'],0,4);
>>>>>>> Files to allow Chart labels

//get label type and number of labels on sheet
//

<<<<<<< HEAD
if ($GLOBALS['chart_label_type'] == '1') { 
=======
if ($GLOBALS['label_type'] == '1') { 
>>>>>>> Files to allow Chart labels
$pdf = new PDF_Label('5160');
$last = 30;
}

<<<<<<< HEAD
if ($GLOBALS['chart_label_type'] == '2') { 
=======
if ($GLOBALS['label_type'] == '2') { 
>>>>>>> Files to allow Chart labels
$pdf = new PDF_Label('5161');
$last = 20;
}

<<<<<<< HEAD
if ($GLOBALS['chart_label_type'] == '3') { 
=======
if ($GLOBALS['label_type'] == '3') { 
>>>>>>> Files to allow Chart labels
$pdf = new PDF_Label('5162');
$last = 14;
}

$pdf->AddPage();

<<<<<<< HEAD
// Added spaces to the sprintf for Fire Fox it was having a problem with alignment 
$text = sprintf("  %s %s\n  %s\n  %s\n  %s", $patdata['fname'], $patdata['lname'], $dob, $today, $patdata['pid']);

=======
>>>>>>> Files to allow Chart labels
// For loop for printing the labels 
// 

for($i=1;$i<=$last;$i++) {
<<<<<<< HEAD
=======
	$text = sprintf("%s %s\n%s\n%s\n%s", $patdata['fname'], $patdata['lname'], $dob, $today, $patdata['pid']);
>>>>>>> Files to allow Chart labels
	$pdf->Add_Label($text);
}

$pdf->Output();
?>
