<?php
/** 
 *  Patient Tracker 
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
require_once("$srcdir/patient.inc");
require_once "$srcdir/appointments.inc.php";
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/options.inc.php");

?>
<html>
<head>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript" src="../library/js/common.js"></script>

<script language="JavaScript">

var mypcc = '1';
  
function bpopup(pid) {
 top.restoreSession()	
 window.open('../custom/patient_tracker_status.php?record_id=' + pid ,'_blank', 'width=500,height=250,resizable=1');
 return false;
}

var reftime="<?php echo attr(($GLOBALS['pat_trkr_timer'])); ?>"

if (document.images){
var parsetime=reftime.split(":")
parsetime=parsetime[0]*60+parsetime[1]*1
}
function refreshbegin(){
if (!document.images)
return
if (parsetime==1)
window.location.reload()
else{ 
parsetime-=1
setTimeout("refreshbegin()",1050)
  }
}
window.onload=refreshbegin
</script>
<script>
// Taken from billing_report 
// Process a click to go to an encounter.
function toencounter(pid, pubpid, pname, enc, datestr, dobstr) {
 top.restoreSession();
<?php if ($GLOBALS['concurrent_layout']) { ?>
 var othername = (window.name == 'RTop') ? 'RBot' : 'RTop';
 parent.left_nav.setPatient(pname,pid,pubpid,'',dobstr);
 parent.left_nav.setEncounter(datestr, enc, othername);
 parent.left_nav.setRadio(othername, 'enc');
 parent.frames[othername].location.href =
  '../interface/patient_file/encounter/encounter_top.php?set_encounter='
  + enc + '&pid=' + pid;
<?php } else { ?>
 location.href = '../interface/patient_file/encounter/patient_encounter.php?set_encounter='
  + enc + '&pid=' + pid;
<?php } ?>
}
// Process a click to go to an patient.
function topatient(pid, pubpid, pname, enc, datestr, dobstr) {
 top.restoreSession();
<?php if ($GLOBALS['concurrent_layout']) { ?>
 var othername = (window.name == 'RTop') ? 'RBot' : 'RTop';
 parent.left_nav.setPatient(pname,pid,pubpid,'',dobstr);
 parent.frames[othername].location.href =
  '../interface/patient_file/summary/demographics_full.php?pid=' + pid;
<?php } else { ?>
 location.href = '../interface/patient_file/summary/demographics_full.php?pid=' + pid;
<?php } ?>
}
</script>
</head>

<body class="body_top" >
<center>

<form id='pattrk' method='post' action='patient_tracker.php' onsubmit='return top.restoreSession()' enctype='multipart/form-data'>

<table border='0' cellpadding='5' cellspacing='0'>
 <tr>
  <td  align='center'><br>

<?php
if ($GLOBALS['pat_trkr_timer'] =='0') {
?>	
   <a href='javascript:;' class='css_button_small' align='center' style='color:gray' onclick="document.getElementById('pattrk').submit();"><span><?php echo xlt('Refresh Screen'); ?></span></a>
   </td>
 </tr>
</table>
<?php
}
  $where = "";
?>

<table border='0' cellpadding='1' cellspacing='2' width='100%'>

 <tr bgcolor="#cccff">
   <td class="dehead" align="center">
   <?php  echo xlt('PID'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Patient'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Encounter'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Exam Room #'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Appt Time'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Arrive Time'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Status'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Current Status Time'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Visit Type'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Provider'); ?>
  </td>
 <td class="dehead" align="center">
   <?php  echo xlt('Total Time'); ?>
  </td>
 <td class="dehead" align="center">
   <?php  echo xlt('Check Out Time'); ?>
  </td>
   <td class="dehead" align="center">
   <?php  echo xlt('Updated By'); ?>
  </td>
 </tr>

<?php
  $orow = -1;

$appointments = array();
$today_one = oeFormatShortDate($date='today');
$from_date = date("Y-m-d");
$to_date = date("Y-m-d");

$appointments = fetchtrkrEvents( $from_date, $to_date , $where);

    $pid_list = array();
    $totalAppontments = count($appointments);   

	foreach ( $appointments as $appointment ) {
		$patient_id = $appointment['pid'];
		$record_id = $appointment['id'];
		$docname  = $appointment['lname'] . ', ' . $appointment['fname'] . ' ' . $appointment['mname'];
        $ptname = $appointment['fname'] . " " . $appointment['lname'];
        $raw_encounter_date = date("Y-m-d", strtotime($appointment['date']));
		if (strlen($docname)<= 3 ) continue;        
        $errmsg  = "";
		$pc_apptstatus = $appointment['pc_apptstatus'];

        $bgcolor = (getListItemMapping("apptstat",$appointment['pc_apptstatus']));

?>
        <tr bgcolor='<?php echo $bgcolor ?>'>
        <td class="detail" align="center">
        <?php echo text($appointment['pid']) ?>
         </td>
        <td class="detail" align="center">
        <a href="" onclick="return topatient('<?php echo text($appointment['pid']);?>','<?php echo text($appointment['pubpid']);?>','<?php echo text($ptname);?>','<?php echo text($appointment['encounter']);?>','<?php echo text(oeFormatShortDate($raw_encounter_date));?>','<?php echo text(oeFormatShortDate($appointment['DOB']));?>' )" >
		 <?php echo text($appointment['lname']) . ', ' . text($appointment['fname']) . ' ' . text($appointment['mname']); ?></a>
         </td>
        <td class="detail" align="center">
         <a href="" onclick="return toencounter('<?php echo text($appointment['pid']);?>','<?php echo text($appointment['pubpid']);?>','<?php echo text($ptname);?>','<?php echo text($appointment['encounter']);?>','<?php echo text(oeFormatShortDate($raw_encounter_date));?>','<?php echo text(oeFormatShortDate($appointment['DOB']));?>' )" >
		 <?php echo text($appointment['encounter']); ?></a>
         </td>
         <td class="detail" align="center">
         <?php echo text($appointment['room']) ; ?>  
         </td>
         <td class="detail" align="center">
         <?php echo text($appointment['pc_startTime']) ?>
         </td>
         <td class="detail" align="center">
        <?php echo text(substr($appointment['date'],11)); ?>
         </td>
         <td class="detail" align="center">     
         <a href="" onclick="return bpopup(
         <?php $statusverb = getListItemTitle("apptstat",$appointment['pc_apptstatus']); echo text($appointment['id']);  ?> ) " ><?php echo text(substr($statusverb,1)); ?></a>		 
		 </td>
         <td class="detail" align="center"> 
        <?php		 
		 //time in status
		 $to_time = strtotime(date("Y-m-d H:i:s"));
		 $yestime = '0';

		 if ($appointment['endtime'] != '00:00:00') {
 			$from_time = strtotime($appointment['date']);
			$to_time = strtotime($appointment['endtime']);
			$yestime = '0';
		 }
         else
        {	
			$from_time = strtotime($appointment['start_datetime']);
			$yestime = '1';
        }
        if ($yestime == '1') {        
		  echo text(round(abs($to_time - $from_time) / 60,0). ' ' . xl('minutes'));
		}
		$yestime = '0';
        ?>	
         <td class="detail" align="center">
         <?php echo text($appointment['pc_title']) ?>
         </td>
         <td class="detail" align="center">
         <?php echo text($appointment['ulname']) . ', ' . text($appointment['ufname']) . ' ' . text($appointment['umname']); ?>
         </td>
         <td class="detail" align="center"> 
         <?php		 
		 
		 // total time in practice
		 if ($appointment['endtime'] != '00:00:00') {
 			$from_time = strtotime($appointment['date']);
			$to_time = strtotime($appointment['endtime']);
			$yestime = '0';
		 }
         else
        {	
			$from_time = strtotime($appointment['date']);
 		    $to_time = strtotime(date("Y-m-d H:i:s"));
			$yestime = '1';
        }		 
		echo text(round(abs($to_time - $from_time) / 60,0). ' ' . xl('minutes'));
        ?>		 
		<?php echo text($appointment['pc_time']); ?>
         </td>
        <td class="detail" align="center">
         <?php 
		 if ($appointment['endtime'] != '00:00:00') {
		    echo text($appointment['endtime']) ;
		 }
		 ?>
         </td>
         <td class="detail" align="center">
         <?php echo text($appointment['user']) ?>
         </td>
        </tr>
        <?php
	} //end for
?>

</table>

</form>
</center>
</body>
</html>
