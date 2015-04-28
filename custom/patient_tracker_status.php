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
 
$fake_register_globals=false;
$sanitize_all_escapes=true;
  
require_once("../interface/globals.php");
require_once("$srcdir/options.inc.php");

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
      "from patient_tracker where id =? LIMIT 1",array($_GET['record_id']));
  
    $patient_id = $trow['pid'];
    $rmnum = $trow['roomnumber'];
    $appttime = $trow['origappt'];
  
  if (! $patient_id) die(xlt("You cannot access this page directly."));

  if ($_POST['statustype'] !='') { 
    $tkpid = $patient_id;
    $status = $_POST['statustype'];
    $track_date = date("Y-m-d H:i:s");
    $fill_dte   = "0000-00-00 00:00:00";
    $today   = date("Y-m-d");
    $theroom = $_POST['roomnum'];
 	
    $tmprow = sqlQuery("SELECT username, facility, facility_id FROM users WHERE id = ?", array($_SESSION["authUserID"]) );
    $username = $tmprow['username'];
    $srow = sqlQuery("select pc_apptstatus as status, pc_startTime as start " .
    "from openemr_postcalendar_events where pc_pid =? AND pc_eventdate =? AND pc_startTime =? limit 1" , array($patient_id,$today,$appttime));
   
    $oldstatus =  $srow['status'];
	
     if ($_POST['statustype'] == 'x' || ($_POST['statustype'] == '%') || $_POST['statustype'] == '?' )
     {
        sqlStatement("INSERT INTO patient_tracker SET " .
           "user = ?, " .
           "date = ?, " .
           "status = ?, " .
           "checkoutuser = ?, " .
           "pid = ?, " .
           "checkoutdatetime = ? " ,
    			array($username,$today,$status,$username,$tkpid,$track_date)
    );
     }
	 
     if ($_POST['statustype'] == '>' || $_POST['statustype'] == '!')
     {
        sqlStatement("UPDATE patient_tracker SET " .
           "user =?, " .
           "checkoutdatetime =? , " .
           "checkoutuser =?, " .
           "status	=? " .		   
           "WHERE id =? AND date =?", array($username,$track_date,$username,$status,$record_id,$today));
     }
	 
     if ($_POST['statustype'] == 'N' AND $$rmnum != ' ')
     {
        if (strlen($rmnum) != 0) {
            sqlStatement("UPDATE patient_tracker SET " .
               "user =?, " .
               "status =?, " .
               "nurseseendatetime =? ," .
               "nurseseenuser =?, " . 			   
               "drseendatetime =? " .
               "WHERE id =? AND date =?", array($username,$status,$track_date,$username,$fill_dte,$record_id,$today));
        }
        else
        {
            $status = $oldstatus;
            die(xlt("Patient must have a room assigned in order to proceed."));
        }	 
     }
	 
     if ($_POST['statustype'] == 'D' AND $$rmnum != ' ')
     {
        if (strlen($rmnum) != 0) {
            sqlStatement("UPDATE patient_tracker SET " .
               "user =?, " .
               "status =?, " .
               "drseenuser =?, " .   
               "drseendatetime =? " . 
               "WHERE id =? AND date =?", array($username,$status,$username,$track_date,$record_id,$today));
        }
         else
         {
            $status = $oldstatus;
            die(xlt("Patient must have a room assigned in order to proceed."));
         }	 
     }	
	
     if ($_POST['statustype'] == '<')
     {
        sqlStatement("UPDATE patient_tracker SET " .
           "user =?, " .
           "roomnumber =? ," .
           "status =?, " .
           "inroomuser =?, " .
           "inroomdatetime =? " .
           "WHERE id =? AND date =?", array($username,$theroom,$status,$username,$track_date,$record_id,$today));
     }	
	
     if (strlen($status) != 0) 
     {
         sqlStatement("UPDATE openemr_postcalendar_events SET " .
            "pc_apptstatus =? " .
            "where pc_pid =? AND pc_eventdate =? AND pc_startTime =? ", array($status,$patient_id,$today,$appttime));
     } 
     echo "<html>\n<body>\n<script language='JavaScript'>\n";	
     echo "window.opener.location.reload();\n";
     echo " window.close();\n";    
     echo "</script></body></html>\n";
     exit();
  }

     $row = sqlQuery("select fname, lname " .
     "from patient_data where pid =? limit 1" , array($patient_id));
	
     $srow = sqlQuery("select pc_apptstatus as status, pc_startTime as start " .
     "from openemr_postcalendar_events where pc_pid =? AND pc_eventdate =? limit 1" , array($patient_id,$today));	
	
?>
    <center>
    <form id="form_note" method="post" action="patient_tracker_status.php?record_id=<?php echo attr($record_id) ?>" enctype="multipart/form-data" >
    <table>
    <h2><?php echo xlt('Change Status for'). " " . text($row['fname']) . " " . text($row['lname']) . " " . text($srow['start']); ?></h2>

    <span class=text><?php  echo xlt('Status Type'); ?>: </span><br> 
<?php

    $res = sqlQuery("SELECT title FROM list_options WHERE list_id = ? AND option_id = ?", array($list_id,$currvalue));
    if ($obj{"statustype"} !=' ') 
    {
       $res['title'] = $obj{"statustype"};
    }
	echo generate_select_list('statustype', 'apptstat',$res['title'], xl('Status Type'));
?>
	<br><br>   
	<span class=text><?php  echo xlt('Exam Room Number'); ?>: </span><br>
    <input type=entry name="roomnum" size=1 value="<?php echo $obj{"roomnum"};?>" ><br><br>
    <tr>
     <td>
      <a href='javascript:;' class='css_button_small' style='color:gray' onclick='document.getElementById("form_note").submit();'><span><?php echo xla('Save')?></span></a>
      &nbsp;
      <a href='javascript:;' class='css_button_small' style='color:gray' onclick="window.close().submit();"><span><?php  echo xla('Cancel'); ?></span></a>
     </td>
    </tr>

    </table>

    </td>

    </form>
    </center>
  </body>
</html>
