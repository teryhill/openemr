<?php
/** 
 *  Patient Tracker (Patient Flow Board)
 *
 *  This program displays the information entered in the Calendar program , 
 *  allowing the user to change status and veiw those changed here and in the Calendar
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
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient_tracker.inc.php");

?>
<html>
<head>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="../library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<script type="text/javascript" src="../library/dialog.js"></script>
<script type="text/javascript" src="../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../library/js/common.js"></script>
<script type="text/javascript" src="../library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<script type="text/javascript" src="../library/js/jquery-ui.js"></script>
<script src="../library/js/jquery-1.9.1.min.js"></script>
<script src="../library/js/blink/jquery.modern-blink.js"></script>

<script>
jQuery(function($) {
    $('.js-blink-infinite').modernBlink();
});
</script>
<script language="JavaScript">

var mypcc = '1';
  
function bpopup(tkid) {
 top.restoreSession()	
 window.open('../custom/patient_tracker_status.php?record_id=' + tkid ,'_blank', 'width=500,height=250,resizable=1');
 return false;
}

var reftime="<?php echo attr($GLOBALS['pat_trkr_timer']); ?>"

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

$appointments = array();
$today_one = oeFormatShortDate($date='today');
$from_date = date("Y-m-d");
$to_date = date("Y-m-d");
$datetime = date("Y-m-d H:i:s");
  
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
		$newarrive = collect_checkin($appointment['id']);
        $newend = collect_checkout($appointment['id']);			
       if ((strtotime($newarrive) == '')) {
         $tracker1d = $appointment['pt_tracker_id'];
         $drugtest = 0;
		 $testdrug = mt_rand(1,10);
        if ($testdrug >5) { 
            $drugtest = 1;
         }
         manage_tracker_time($tracker1d,$drugtest);	
		 
       }
	   
        $bgcolor = (getApptStatusColor($appointment['status']));
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
		 <?php echo text($appointment['encounter']); ?></a>
         </td>
         <td class="detail" align="center">
         <?php echo text($appointment['room']) ; ?>  
         </td>
         <td class="detail" align="center">
         <?php echo text($appointment['appttime']) ?>
         </td>
         <td class="detail" align="center">
        <?php echo text(substr($newarrive,11)); ?>
         </td>
         <td class="detail" align="center">  
         <a href=""  onclick="return bpopup(
         <?php
            if (strlen($appointment['pt_traker_id']) == 0){		 
               $statusverb = getListItemTitle("apptstat",$appointment['status']); echo text($appointment['id']);  
            }
		 ?> ) " ><?php echo text($statusverb); ?></a>		 
		 </td>
        <?php		 
		 //time in status
		 $to_time = strtotime(date("Y-m-d H:i:s"));
		 $yestime = '0'; 
		 if (strtotime($newend) != '') {
 			$from_time = strtotime($newarrive);
			$to_time = strtotime($newend);
			$yestime = '0';
		 }
         else
        {	
			$from_time = strtotime($appointment['start_datetime']);
			$yestime = '1';
        }
          if (round(abs($to_time - $from_time) / 60,0) >= $GLOBALS['over_time_warning']) { 
            echo "<td align='center' class='js-blink-infinite'>	";
         }
        else
         {
			 echo "<td align='center' class='detail'> ";
         }
        if (($yestime == '1') && (strtotime($newarrive)!= '')) { 
		   echo text(round(abs($to_time - $from_time) / 60,0). ' ' . xl('minutes')); 
		}
        ?>	
		 </td>
         <td class="detail" align="center">
         <?php echo text(xl_appt_category($appointment['pc_title'])) ?>
         </td>
         <td class="detail" align="center">
         <?php echo text($appointment['ulname']) . ', ' . text($appointment['ufname']) . ' ' . text($appointment['umname']); ?>
         </td>
         <td class="detail" align="center"> 
         <?php		 
		 
		 // total time in practice
		 if (strtotime($newend) != '') {
 			$from_time = strtotime($newarrive);
			$to_time = strtotime($newend);
		 }
         else
        {	
			$from_time = strtotime($newarrive);
 		    $to_time = strtotime(date("Y-m-d H:i:s"));
        }	
       if (strtotime($newarrive) != '') {  		
		echo text(round(abs($to_time - $from_time) / 60,0). ' ' . xl('minutes'));
	   }
        ?>		 
		<?php echo text($appointment['pc_time']); ?>
         </td>
        <td class="detail" align="center">
         <?php 
		 if (strtotime($newend) != '') {
		    echo text(substr($newend,11)) ;
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
