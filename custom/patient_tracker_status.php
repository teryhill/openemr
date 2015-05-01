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
require_once("$srcdir/patient_tracker.inc.php");

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
  
    $tkpid = $trow['pid'];
    $oldroom = $trow['lastroom'];
    $oldstatus = $trow['laststatus'];
    $appttime = $trow['appttime'];
    $apptdate = $trow['apptdate'];

  if ($_POST['statustype'] !='') { 
    $status = $_POST['statustype'];
    $theroom = $_POST['roomnum'];
    $username = $_SESSION["authUser"];

     if (strlen($status) != 0)
     {
		 if (strlen($theroom) == 0) {
			$theroom = $oldroom; 
		 }	 
	
	     update_tracker_status($apptdate,$appttime,$tkpid,$username,$status,$theroom,$record_id);

     } 
     echo "<html>\n<body>\n<script language='JavaScript'>\n";	
     echo "window.opener.location.reload();\n";
     echo " window.close();\n";    
     echo "</script></body></html>\n";
     exit();
  }

     $row = sqlQuery("select fname, lname " .
     "from patient_data where pid =? limit 1" , array($tkpid));
	
     $srow = sqlQuery("select pc_apptstatus as status, pc_startTime as start " .
     "from openemr_postcalendar_events where pc_pid =? AND pc_eventdate =? limit 1" , array($tkpid,$today));	
	
?>
 </head>
  <body class="body_top">
    <center>
    <form id="form_note" method="post" action="patient_tracker_status.php?record_id=<?php echo attr($record_id) ?>" enctype="multipart/form-data" >
    <table>
    <h2><?php echo xlt('Change Status for'). " " . text($row['fname']) . " " . text($row['lname']); ?></h2>

    <span class=text><?php  echo xlt('Status Type'); ?>: </span><br> 
<?php
    $res = getListItemTitle("apptstat",$appointment['pc_apptstatus']);
	echo generate_select_list('statustype', 'apptstat',$res, xl('Status Type'));
?>
	<br><br>   
	<span class=text><?php  echo xlt('Exam Room Number'); ?>: </span><br>
    <input type=entry name="roomnum" size=1 value="<?php echo attr($obj{"roomnum"});?>" ><br><br>
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
