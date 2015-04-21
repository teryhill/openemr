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

//$where = '';
$firsttime = 1;

function print_r2($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}

?>
<html>
<head>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<!--<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="../library/textformat.js"></script>
<script src="../library/js/jquery-1.11.2.js"></script>
<script src="../library/js/jquery-ui-1.8.21.custom.min.js"></script>-->
<script type="text/javascript" src="../library/js/common.js"></script>

<script language="JavaScript">

var mypcc = '1';

function bpopup(pid) {
 window.open('../custom/patient_tracker_status.php?record_id=' + pid ,'_blank', 'width=500,height=250,resizable=1');
 return false;
}

function npopup(pid) {
 window.open('../custom/patient_tracker_roomnum.php?record_id=' + pid ,'_blank', 'width=500,height=250,resizable=1');
 return false;
}

function setingspopup() {
 window.open('../custom/patient_tracker_settings.php', '_blank', 'width=500,height=250,resizable=1');
 return false;
}

var reftime="<?php echo ($GLOBALS['pat_trkr_timer']); ?>"

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

</head>

<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0' >
<center>

<form id='pattrk' method='post' action='patient_tracker.php' enctype='multipart/form-data'>

<table border='0' cellpadding='5' cellspacing='0'>
 <tr>
  <td  align='center'><br>

  <!-- Reviewers this is being worked on and hopefully the demo will supply input for this if there is a need
  <a href='javascript:;' class='css_button_small' style='color:gray' onclick="return setingspopup()"><span><?php echo xlt('Settings'); ?></span></a>-->

<?php
// Reviewers I am wondering if there is a need to allow the refresh to be set in user preferences in addition to a global setting

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

<table border='0' cellpadding='1' cellspacing='2' width='98%'>

 <tr bgcolor="#cccff">
  <td class="dehead" align="center">
   &nbsp;<?php  echo xlt('Patient'); ?>
  </td>
  <td class="dehead" align="center">
   &nbsp;<?php  echo xlt('Exam Room #'); ?>
  </td>
  <td class="dehead" align="center">
   &nbsp;<?php  echo xlt('Appt Time'); ?>
  </td>
  <td class="dehead" align="center">
   &nbsp;<?php  echo xlt('Arrive Time'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Status'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Time in Current Status'); ?>
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Visit Type'); ?>&nbsp;
  </td>
  <td class="dehead" align="center">
   <?php  echo xlt('Provider'); ?>&nbsp;
  </td>
 <td class="dehead" align="center">
   <?php  echo xlt('Total Time'); ?>&nbsp;
  </td>
 <td class="dehead" align="center">
   <?php  echo xlt('Check Out Time'); ?>
  </td>
   <td class="dehead" align="left">
   <?php  echo xlt('Last Status Updated by'); ?>
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
                array_push($pid_list,$appointment['pid']);
		$patient_id = $appointment['pid'];
		$record_id = $appointment['id'];
		$docname  = $appointment['lname'] . ', ' . $appointment['fname'] . ' ' . $appointment['mname'];

		if (strlen($docname)<= 3 ) continue;        
        $errmsg  = "";
		$pc_apptstatus = $appointment['pc_apptstatus'];
	
        $bgcolor = ((++$orow & 1) ? "#ccffff" : "#ffffcc");
 /*********************************************************************
        This has been migrate to the administration->lists
 $statuses = array(
  '-' => '',
  'x' => xl('x Cancelled'), // added Apr 2008 by JRM
  '?' => xl('? No show'),
  '@' => xl('@ Arrived'),
  '~' => xl('~ Arrived late'),
  '!' => xl('! Left w/o visit'),
  '<' => xl('< In exam room'),
  '>' => xl('> Checked out'),
  '%' => xl('% Cancelled <  24h ')
 );
*********************************************************************/
?>
        <tr bgcolor='<?php echo $bgcolor ?>'>
        <td class="detail" align="center">
		 <?php echo $appointment['lname'] . ', ' . $appointment['fname'] . ' ' . $appointment['mname']; ?></a>
         </td>
         <td class="detail" align="center">
         <?php 
		 if ($appointment['pc_apptstatus']!='-' AND $appointment['pc_apptstatus']!='x' AND $appointment['pc_apptstatus'] !='%' AND $appointment['pc_apptstatus'] !='!' || $appointment['pc_apptstatus'] !='?' || $appointment['pc_apptstatus'] !='>') {
		  echo $appointment['roomnumber'] ;
		 }
		 ?>  
         </td>
         <td class="detail" align="center">
         <?php echo $appointment['pc_startTime'] ?>
         </td>
         <td class="detail" align="center">
        <?php 
		if ($appointment['pc_apptstatus']!='-') {
		echo substr($appointment['arrivedatetime'],11); 
		}
		?>
         </td>

         <td bgcolor='<?php echo $bgcolor ?>' class="detail" align="center">
		<a href="" onclick="return bpopup(
         <?php 

		 switch (true) {
           case ($appointment['pc_apptstatus']=='@'):    //  '@ Arrived'
            $statusverb = 'Arrived';
			$apptstatus = '1';
            break;
           case ($appointment['pc_apptstatus']=='~'):    //  '~ Arrived late'
            $statusverb = 'Arrived Late';
			$apptstatus = '1';
            break;	
           case ($appointment['pc_apptstatus'] =='<'):   //  '< In exam room'
            $statusverb = 'In Exam Room';
			$apptstatus = '2';
		   break;
		   case ($appointment['pc_apptstatus'] =='>'):   //  '> Checked out'
            $statusverb = 'Checked Out';
			$apptstatus = '3';
		   break;	
           case ($appointment['pc_apptstatus'] =='x'):   //  'x Cancelled'
            $statusverb = 'Cancelled';
			$apptstatus = '4';
		   break;
           case ($appointment['pc_apptstatus'] =='%'):   //  '% Cancelled <  24h '
            $statusverb = 'Cancelled <  24h';
			$apptstatus = '4';
		   break;
           case ($appointment['pc_apptstatus'] =='!'):   //  '! Left w/o visit'
            $statusverb = 'Left w/o visit';
			$apptstatus = '5';
		   break;
           case ($appointment['pc_apptstatus'] =='?'):   //  '? No show'
            $statusverb = 'No show';
			$apptstatus = '6';
		   break;
           case ($appointment['pc_apptstatus'] =='N'):   //  'N With Nurse'
            $statusverb = 'With Nurse';
			$apptstatus = '7';
		   break;
           case ($appointment['pc_apptstatus'] =='D'):   //  'D With Physician'
            $statusverb = 'With Physician';
			$apptstatus = '8';
		   break;		   
           case ($appointment['pc_apptstatus']=='-'):    //  '- None'
            $statusverb = ' ';
			$apptstatus = '9';
            break;		   
            default:
			$statusverb = ' ';
          }  
		 
		 		echo $appointment['id'];  
				?> ) "
         ><?php echo $statusverb; ?></a>
		 </td>
         <td class="detail" align="center"> 
<?php		 
		 //time in status
		 $to_time = strtotime(date("Y-m-d H:i:s"));
		 $yestime = '0';
  		if ($appointment['pc_apptstatus']!='-' AND $appointment['pc_apptstatus']!='x' AND $appointment['pc_apptstatus'] !='%' AND $appointment['pc_apptstatus'] !='!' AND $appointment['pc_apptstatus'] !='?') {
		  switch (true) {
           case (substr($appointment['checkoutdatetime'],0,4) != '0000' AND $appointment['checkoutdatetime'] != ''):
			$from_time = strtotime($appointment['checkoutdatetime']);
			$to_time = strtotime($appointment['checkoutdatetime']);
			$yestime = '0';
            break;  
           case (substr($appointment['drseendatetime'],0,4) != '0000' AND $appointment['drseendatetime'] != ''):
			$from_time = strtotime($appointment['drseendatetime']);
			$yestime = '1';
            break;
           case (substr($appointment['nurseseendatetime'],0,4) != '0000' AND $appointment['nurseseendatetime'] != ''):
			$from_time = strtotime($appointment['nurseseendatetime']);
			$yestime = '1';
            break;			
           case (substr($appointment['inroomdatetime'],0,4) != '0000' AND $appointment['inroomdatetime'] != ''):
			$from_time = strtotime($appointment['inroomdatetime']);
			$yestime = '1';
            break;
           case (substr($appointment['arrivedatetime'],0,4) != '0000' AND $appointment['arrivedatetime'] != ''):
			$from_time = strtotime($appointment['arrivedatetime']);
			$yestime = '1';
		   break;	
            default:
			$yestime = '0';
          }  
		}

        if ($yestime == '1') {        
		  echo round(abs($to_time - $from_time) / 60,0). " minutes";
		}
		$yestime = '0';
        ?>	
         <td class="detail" align="center">
         <?php echo $appointment['pc_title'] ?>&nbsp;
         </td>
         <td class="detail" align="center">
         <?php echo $appointment['ulname'] . ', ' . $appointment['ufname'] . ' ' . $appointment['umname']; ?>&nbsp;
         </td>
         <td class="detail" align="center"> 
         <?php		 
		 
		 // total time in practice
		 if (substr($appointment['checkoutdatetime'],0,4) != '0000' AND $appointment['checkoutdatetime'] != '') {
		 $to_time = strtotime($appointment['checkoutdatetime']);	
         }
         else
         {			 
		 $to_time = strtotime(date("Y-m-d H:i:s"));
		 }
         $from_time = strtotime($appointment['arrivedatetime']);

  		if ($appointment['arrivedatetime'] != '' AND $appointment['pc_apptstatus']!='-' AND $appointment['pc_apptstatus']!='x' AND $appointment['pc_apptstatus'] !='%' AND $appointment['pc_apptstatus'] !='!' AND $appointment['pc_apptstatus'] !='?') {		
		echo round(abs($to_time - $from_time) / 60,0). " minutes";
		}
        ?>		 
		<?php echo $appointment['pc_time']; ?>&nbsp;
         </td>
        <td class="detail" align="center">
         <?php echo substr($appointment['checkoutdatetime'],11) ?>
         </td>
         <td class="detail" align="center">
         <?php echo $appointment['user'] ?>
         </td>
        </tr>
        <?php
	} //end for
//} 
?>

</table>

</form>
</center>
</body>
</html>
