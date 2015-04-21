<?php
/** 
 * Patient Tracker Status Editor 
 *
 * This allows entry and editing of current status for the patient from within patient tracker and updates the status on the calendar.
 * 
 * Copyright (C) 2015 Terry Hill <terry@lillysystems.com> 
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
 * @author Terry Hill <terry@lilysystems.com> 
 * @link http://www.open-emr.org 
 *   
 */ 
  
require_once("../interface/globals.php");
require_once("$srcdir/options.inc.php");

  $info_msg = "";
 
?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="../library/textformat.js"></script>
<script src="../library/js/jquery-1.11.2.js"></script>
<script src="../library/js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src="../library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/restoreSession.php"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/dialog.js"></script>
</head>
<body>


<?php
 
    $record_id = $_GET['record_id'];

    $trow = sqlQuery("select roomnumber , origappt , pid " .
      "from patient_tracker where id = '$record_id'  limit 1");	
  
    $patient_id = $trow['pid'];
    $rmnum = $trow['roomnumber'];
    $appttime = $trow['origappt'];
  
  if (! $patient_id) die(xl("You cannot access this page directly."));
echo $_POST['form_save'];
  if ($_POST['form_save']) { 
	$tkpid = $patient_id;
    $status = $_POST['statustype'];
	$track_date = date("Y-m-d H:i:s");
	$fill_dte   = "0000-00-00 00:00:00";
    $to_date = date("Y-m-d H:i:s");
	$today   = date("Y-m-d");
	$theroom = trim($_POST['form_note']);
 	
    $tmprow = sqlQuery("SELECT username, facility, facility_id FROM users WHERE id = ?", array($_SESSION["authUserID"]) );
    $username = $tmprow['username'];
	$srow = sqlQuery("select pc_apptstatus as status, pc_startTime as start " .
    "from openemr_postcalendar_events where pc_pid = '$patient_id' AND pc_eventdate = '$today' AND pc_startTime = '$appttime' limit 1");
   
    $oldstatus =  $srow['status'];
	
	 if ($_POST['statustype'] == 'x')
	 {
		     sqlStatement("INSERT INTO patient_tracker SET " .
			   "user = '$username', " .
			   "date = '$today', " .
			   "status = '$status', " .
			   "checkoutuser = '$username', " .
			   "pid = '$tkpid', " .
        	   "checkoutdatetime = '$track_date' ");
	 }

 	 if ($_POST['statustype'] == '%')
	 {
		     sqlStatement("INSERT INTO patient_tracker SET " .
			   "user = '$username', " .
			   "date = '$today', " .
			   "status = '$status', " .
			   "checkoutuser = '$username', " .
			   "pid = '$tkpid', " .
        	   "checkoutdatetime = '$track_date' ");
	 }

	 if ($_POST['statustype'] == '!')
	 {
             sqlStatement("UPDATE patient_tracker SET " .
 			   "status = '$status', " .
			   "checkoutuser = '$username', " .
        	   "checkoutdatetime = '$track_date' " . 
				"WHERE id = '$record_id' AND date = '$today' ");
	 }

 	 if ($_POST['statustype'] == '?')
	 {
		     sqlStatement("INSERT INTO patient_tracker SET " .
			   "user = '$username', " .
			   "date = '$today', " .
			   "status = '$status', " .
			   "checkoutuser = '$username', " .
			   "pid = '$tkpid', " .
        	   "checkoutdatetime = '$track_date' ");
	 }
	 
	 if ($_POST['statustype'] == '>')
	 {
		     sqlStatement("UPDATE patient_tracker SET " .
			   "user = '$username', " .
        	   "checkoutdatetime = '$track_date' , " .
			   "checkoutuser = '$username', " .
               "status	= '$status' " .		   
				"WHERE id = '$record_id' AND date = '$today'");
	 }
	 
	 if ($_POST['statustype'] == 'N' AND $$rmnum != ' ')
	 {
		 if (strlen($rmnum) != 0) {
		     sqlStatement("UPDATE patient_tracker SET " .
			   "user = '$username', " .
			   "status = '$status', " .
        	   "nurseseendatetime = '$track_date' ," .
               "nurseseenuser = '$username', " . 			   
			   "drseendatetime = '$fill_dte' " .
				"WHERE id = '$record_id' AND date = '$today'");
		 }
         else
         {
		    $status = $oldstatus;
            die(xl("Patient must have a room assigned in order to proceed."));
		 }	 
	 }
	 
	 if ($_POST['statustype'] == 'D' AND $$rmnum != ' ')
	 {
	     if (strlen($rmnum) != 0) {
		     sqlStatement("UPDATE patient_tracker SET " .
			   "user = '$username', " .
			   "status = '$status', " .
			   "drseenuser = '$username', " .   
        	   "drseendatetime = '$track_date' " . 
				"WHERE id = '$record_id' AND date = '$today'");
				 }
         else
         {
		    $status = $oldstatus;
            die(xl("Patient must have a room assigned in order to proceed."));
		 }	 
	 }	
	
		 if ($_POST['statustype'] == '<')
	 {
		    sqlStatement("UPDATE patient_tracker SET " .
                "user = '$username', " .
	            "roomnumber = '$theroom' ," .
				"status = '$status', " .
				"inroomuser = '$username', " .
	            "inroomdatetime = '$to_date' " .
                "WHERE id = '$record_id' AND date = '$today'");
	 }	
	
 if (strlen($status) != 0) {
  sqlStatement("UPDATE openemr_postcalendar_events SET " .
      "pc_apptstatus = '$status' " .
      "where pc_pid = '$patient_id' AND pc_eventdate = '$today' AND pc_startTime = '$appttime' ");
 } 
	echo "<html>\n<body>\n<script language='JavaScript'>\n";	
    if ($info_msg) echo " alert('$info_msg');\n";
	echo "window.opener.location.reload();\n";
    echo " window.close();\n";    
	echo "</script></body></html>\n";
    exit();
  }

  $row = sqlQuery("select fname, lname " .
    "from patient_data where pid = '$patient_id' limit 1");
	
  $srow = sqlQuery("select pc_apptstatus as status, pc_startTime as start " .
    "from openemr_postcalendar_events where pc_pid = '$patient_id' AND pc_eventdate = '$today' limit 1");	
	
?>
<center>
<form id="getstatus" method="post" action="patient_tracker_status.php?record_id=<?php echo $record_id ?>" enctype="multipart/form-data" >
<table>
<h2><?php echo xl('Change Status for '). $row['fname'] . " " . $row['lname'] . " " . $srow['start']; ?></h2>

	   <span class=text><?php  echo xlt('Status Type'); ?>: </span><br> 
			<?php

                $res = sqlQuery("SELECT title FROM list_options WHERE list_id = ? AND option_id = ?", array($list_id,$currvalue));
	            if ($obj{"statustype"} !=' ') {
			     $res['title'] = $obj{"statustype"};
                }
                echo generate_select_list('statustype', 'apptstat',$res['title'], 'Status Type');

				?>
			<br><br>   
	<span class=text><?php  echo xlt('Exam Room Number'); ?>: </span><br>
   <input type='text' name='form_note' size='5' maxlength='15' value=<?php echo $trow['roomnumber'] ?> ><br><br>

<tr>
  <td>
 <?php 
 // Reviewers The code below is where I am trying to get the fancier buttons to work Any help would be greatly appreciated
 ?>
<!--<a href='javascript:;' class='css_button_small' style='color:gray' onclick='document.getElementById("getstatus").submit();'><span><?php echo xlt('Save')?></span></a>
&nbsp;
<a href='javascript:;' class='css_button_small' style='color:gray' onclick="window.close().submit();"><span><?php  echo xlt('Cancel'); ?></span></a>-->

<input type='submit' name='form_save' value='<?php  echo xlt("Save")?>'>
&nbsp;
<input type='button' value='<?php  echo xlt("Cancel")?>' onclick='window.close()'>
  </td>
</tr>

</table>

</td>

</form>
</center>
</body>
</html>
