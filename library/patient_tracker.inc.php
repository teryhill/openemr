  <?php
/** 
* library/patient_tracker.inc.php Functions used in the Patient Flow Board. 
* 
* Functions for use in the Patient Flow Board
* 
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
* @author Terry Hill <terry@lillysystems.com>
* @link http://www.open-emr.org 
*/
require_once "$srcdir/appointments.inc.php";

function fetchtrkrEvents( $from_date, $to_date, $where_param = null, $orderby_param = null , $tracker_board ) 
{
    $provider_id = '';
    if ($_SESSION['userauthorized'] && $GLOBALS['docs_see_entire_calendar'] !='1') {
      $provider_id = $_SESSION[authUserID];
    }
    $events = fetchAppointments( $from_date, $to_date, null, $provider_id, null, null, null, null, null, $tracker_board );
    return $events;
}

function  is_checkin($option) {
// check to see if a status code exist as a check in
  $row = sqlQuery("SELECT toggle_setting_1 FROM list_options WHERE " .
    "list_id = 'apptstat' AND option_id = ?", array($option));
  if (empty($row['toggle_setting_1'])) return(false);
  return(true);
}

function  is_checkout($option) {
// check to see if a status code exist as a check out
  $row = sqlQuery("SELECT toggle_setting_2 FROM list_options WHERE " .
    "list_id = 'apptstat' AND option_id = ?", array($option));
  if (empty($row['toggle_setting_2'])) return(false);
  return(true);
}


# This function will return false for both below scenarios:
#   1. The tracker item does not exist
#   2. If the tracker item does exist, but the encounter has not been set
function  is_tracker_encounter_exist($apptdate,$appttime,$pid,$eid) {
  #Check to see if there is an encounter in the patient_tracker table.
  $enc_yn = sqlQuery("SELECT encounter from patient_tracker WHERE `apptdate` = ? AND `appttime` = ? " .
                      "AND `eid` = ? AND `pid` = ?", array($apptdate,$appttime,$eid,$pid));
if ($enc_yn['encounter'] == '0' || $enc_yn == '0') return(false);
  return(true);
}

 # this function will return the tracker id that is managed  
function manage_tracker_status($apptdate,$appttime,$eid,$pid,$user,$status='',$room='',$enc_id='') {
  $datetime = date("Y-m-d H:i:s");
  $yearly_limit = $GLOBALS['maximum_drug_test_yearly'];
  $percentage = $GLOBALS['drug_testing_percentage'];	

  #Check to see if there is an entry in the patient_tracker table.
  $tracker = sqlQuery("SELECT id, apptdate, appttime, eid, pid, original_user, encounter, lastseq,".
                       "patient_tracker_element.room AS lastroom,patient_tracker_element.status AS laststatus ".
					   "from `patient_tracker`".
					   "LEFT JOIN patient_tracker_element " .
                       "ON patient_tracker.id = patient_tracker_element.pt_tracker_id " .
                       "AND patient_tracker.lastseq = patient_tracker_element.seq " .
					   "WHERE `apptdate` = ? AND `appttime` = ? " .
                       "AND `eid` = ? AND `pid` = ?", array($apptdate,$appttime,$eid,$pid));

  if (empty($tracker)) {
    #Add a new tracker.
    $tracker_id = sqlInsert("INSERT INTO `patient_tracker` " .
                            "(`date`, `apptdate`, `appttime`, `eid`, `pid`, `original_user`, `encounter`, `lastseq`) " .
                            "VALUES (?,?,?,?,?,?,?,'1')",
                            array($datetime,$apptdate,$appttime,$eid,$pid,$user,$enc_id));
    #If there is a status or a room, then add a tracker item.
    if (!empty($status) || !empty($room)) {
    sqlInsert("INSERT INTO `patient_tracker_element` " .
              "(`pt_tracker_id`, `start_datetime`, `user`, `status`, `room`, `seq`) " .
              "VALUES (?,?,?,?,?,'1')",
              array($tracker_id,$datetime,$user,$status,$room));
    }
  }
  else {
    #Tracker already exists.
    $tracker_id = $tracker['id'];
    if (($status != $tracker['laststatus']) || ($room != $tracker['lastroom'])) {
      #Status or room has changed, so need to update tracker.
      #Update laststatus and lastroom in tracker.	  
	   sqlStatement("UPDATE `patient_tracker` SET  `lastseq` = ? WHERE `id` = ?",
                   array(($tracker['lastseq']+1),$tracker_id));
      #Add a tracker item.
      sqlInsert("INSERT INTO `patient_tracker_element` " .
                "(`pt_tracker_id`, `start_datetime`, `user`, `status`, `room`, `seq`) " .
                "VALUES (?,?,?,?,?,?)",
                array($tracker_id,$datetime,$user,$status,$room,($tracker['lastseq']+1)));
    }
    if (!empty($enc_id)) {
      #enc_id is not blank, so update this in tracker.
      sqlStatement("UPDATE `patient_tracker` SET `encounter` = ? WHERE `id` = ?", array($enc_id,$tracker_id));
    }  
  }
  #Ensure the entry in calendar appt entry has been updated.
  $pc_appt =  sqlQuery("SELECT `pc_apptstatus`, `pc_room` FROM `openemr_postcalendar_events` WHERE `pc_eid` = ?", array($eid));
  if ($status != $pc_appt['pc_apptstatus']) {
    sqlStatement("UPDATE `openemr_postcalendar_events` SET `pc_apptstatus` = ? WHERE `pc_eid` = ?", array($status,$eid));
  }
  if ($room != $pc_appt['pc_room']) {
    sqlStatement("UPDATE `openemr_postcalendar_events` SET `pc_room` = ? WHERE `pc_eid` = ?", array($room,$eid));
  }
  if( $GLOBALS['drug_screen'] && !empty($status)  && is_checkin($status)) {
    random_drug_test($tracker_id,$percentage,$yearly_limit);
  }
  # Returning the tracker id that has been managed
return $tracker_id;
}

function collectApptStatusSettings($option) {
  $color_settings = array();
  $row = sqlQuery("SELECT notes FROM list_options WHERE " .
    "list_id = 'apptstat' AND option_id = ?", array($option));
  if (empty($row['notes'])) return $option;
  list($color_settings['color'], $color_settings['time_alert']) = explode("|", $row['notes']);
  return $color_settings;
}

function collect_checkin($trackerid) {
  $tracker = sqlQuery("SELECT patient_tracker_element.start_datetime " .
                                   "FROM patient_tracker_element " .
                                   "INNER JOIN list_options " .
                                   "ON patient_tracker_element.status = list_options.option_id " .
                                   "WHERE  list_options.list_id = 'apptstat' " .
                                   "AND list_options.toggle_setting_1 = '1' " .
                                   "AND patient_tracker_element.pt_tracker_id = ?",
                                   array($trackerid));
  if (empty($tracker['start_datetime'])) {
    return false;
  }
  else {
    return $tracker['start_datetime'];
  }
}

function collect_checkout($trackerid) {
  $tracker = sqlQuery("SELECT patient_tracker_element.start_datetime " .
                                   "FROM patient_tracker_element " .
                                   "INNER JOIN list_options " .
                                   "ON patient_tracker_element.status = list_options.option_id " .
                                   "WHERE  list_options.list_id = 'apptstat' " .
                                   "AND list_options.toggle_setting_2 = '1' " .
                                   "AND patient_tracker_element.pt_tracker_id = ?",
                                   array($trackerid));
  if (empty($tracker['start_datetime'])) {
    return false;
  }
  else {
    return $tracker['start_datetime'];
  }
}

function random_drug_test($tracker_id,$percentage,$yearly_limit) {

# Check if randomization has not yet been done (is random_drug_test NULL). If already done, then exit.
      $drug_test_done = sqlQuery("SELECT `random_drug_test`, pid from patient_tracker " .
                                     "WHERE id =? ", array($tracker_id));
      $Patient_id = $drug_test_done['pid'];

  If (is_null($drug_test_done['random_drug_test'])) {

    if ($yearly_limit >0) {
      $drug_test_count = sqlQuery("SELECT COUNT(*) from patient_tracker " .
                                 "WHERE drug_screen_completed = '1' AND pid =? ", array($Patient_id));
    }
    # check that the patient is not at the yearly limit
    if($drug_test_count['COUNT(*)'] >= $yearly_limit && ($yearly_limit >0)) {

       $drugtest = 0;
    }
    else
    {
    # Now do the randomization and set random_drug_test to the outcome.  maximum_drug_test_yearly

       $drugtest = 0;
       $testdrug = mt_rand(0,100);
       if ($testdrug <= $percentage) {
         $drugtest = 1;
       }

    }    
   sqlStatement("UPDATE patient_tracker SET " .
                 "random_drug_test = ? " .
                 "WHERE id =? ", array($drugtest,$tracker_id)); 
  }
}
?>