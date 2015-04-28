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
  <link rel="stylesheet" href="../library/css/bootstrap-3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="../library/css/bootstrap-3.3.4/css/bootstrap-theme.min.css">
  <script src="../library/js/jquery-1.11.2.min.js"></script>
  <script src="../library/css/bootstrap-3.3.4/js/bootstrap.min.js"></script>

  <script type="text/javascript">
	$(document).ready(function(){
		$("#myModal").modal('show');
	});
</script>	
<?php
 
    $record_id = $_GET['record_id'];

    $trow = sqlQuery("select apptdate, appttime ,lastroom , laststatus, pid " .
      "from patient_tracker where id =? LIMIT 1",array($_GET['record_id']));
  
    $patient_id = $trow['pid'];
    $oldroom = $trow['lastroom'];
    $oldstatus = $trow['laststatus'];
    $appttime = $trow['appttime'];
    $apptdate = $trow['apptdate'];

  if (! $patient_id) die(xlt("Patient must have a current status of Arrived or Arrived Late entered from the calendar."));

  if ($_POST['statustype'] !='') { 
    $tkpid = $patient_id;
    $status = $_POST['statustype'];
    $track_date = date("Y-m-d H:i:s");
    $today   = date("Y-m-d");
    $theroom = $_POST['roomnum'];
    $endtime = "00:00:00";
    $tmprow = sqlQuery("SELECT username, facility, facility_id FROM users WHERE id = ?", array($_SESSION["authUserID"]) );
    $username = $tmprow['username'];
    $srow = sqlQuery("select pc_apptstatus as status, pc_startTime as start " .
    "from openemr_postcalendar_events where pc_pid =? AND pc_eventdate =? AND pc_startTime =? limit 1" , array($patient_id,$apptdate,$appttime));

     if (strlen($status) != 0)
     {
		 if (strlen($theroom) == 0) {
			$theroom = $oldroom; 
		 }	 
	
             $tmptrk = sqlQuery("SELECT lastseq FROM patient_tracker WHERE id = ? ", array($record_id) );
             $nextseq = 1 + $tmptrk['lastseq'];
			 
         if (strpos($GLOBALS['discharge_code'],$status) !=0) {
            $endtime = substr($track_date,11);	 
         }	 
            sqlStatement("UPDATE patient_tracker SET " .
               "lastroom =? ," .
               "lastseq =? ," .
               "endtime =? ," .		   
               "laststatus =? " .
               "WHERE id =? AND apptdate =?", array($theroom,$nextseq,$endtime,$status,$record_id,$apptdate));

		    sqlInsert("INSERT INTO patient_tracker_element SET " .
			   "pt_traker_id = ?, " .
			   "start_datetime = ?, " .
			   "status = ?, " .
			   "room =? ," .
			   "seq =? ," .
			   "user = ? ",
    			array($record_id,$track_date,$status,$theroom,$nextseq,$username)
             );
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
 </head>
  <body class="body_top">
    <center>
    <form id="form_note" method="post" action="patient_tracker_status.php?record_id=<?php echo attr($record_id) ?>" enctype="multipart/form-data" >
    <table>
    <h2><?php echo xlt('Change Status for'). " " . text($row['fname']) . " " . text($row['lname']) . " " . text($srow['start']); ?></h2>

    <span class=text><?php  echo xlt('Status Type'); ?>: </span><br> 
<?php
    $res = getListItemTitle("apptstat",$appointment['pc_apptstatus']);
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
      <a href='javascript:;' class='css_button_small' style='color:gray' onclick="window.close().submit();" ><span><?php  echo xla('Cancel'); ?></span></a>
     </td>
    </tr>
    </table>
    </td>
    </form>
    </center>
  </body>
</html>
