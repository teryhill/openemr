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

function fetchtrkrEvents( $from_date, $to_date, $where_param = null, $orderby_param = null ) 
{
    $sqlBindingArray = array();
if ($_SESSION['userauthorized'] && $GLOBALS['docs_see_entire_calendar'] !='1') {
      $getprovid = $_SESSION[authUserID];
      $where =
          "( (e.pc_endDate >=? AND e.pc_eventDate <=? AND e.pc_aid =? AND p.lname != '' AND e.pc_recurrtype = '1' ) OR " .
  		  "(e.pc_eventDate >= ? AND e.pc_eventDate <=? AND  e.pc_aid =? AND p.lname != '' ) )";
          array_push($sqlBindingArray,$from_date,$to_date,$getprovid,$from_date,$to_date,$getprovid);	 
    }
    else
    {
      $where =
      "( (e.pc_endDate >= ? AND e.pc_eventDate <=? AND p.lname != ' ' AND e.pc_recurrtype = '1') OR " .
      "(e.pc_eventDate >= ? AND e.pc_eventDate <=? AND p.lname != ' ' ) )";
      array_push($sqlBindingArray,$from_date,$to_date,$from_date,$to_date);
    }
    if ( $where_param ) $where .= $where_param;
	
    $order_by = "e.pc_eventDate, e.pc_startTime";
    if ( $orderby_param ) {
      $order_by = $orderby_param;
    }

    $query = "SELECT " .
  	"e.pc_eventDate, e.pc_startTime, e.pc_eid, e.pc_title, e.pc_apptstatus, " .
    "t.id, t.date, t.apptdate, t.appttime, t.eid, t.pid, t.original_user, t.encounter, t.lastseq, t.random_drug_test, t.drug_screen_completed, " .
    "q.pt_tracker_id, q.start_datetime, q.room, q.status, q.seq, q.user, " .
    "s.toggle_setting_1, s.toggle_setting_2, s.option_id, " .
  	"p.fname, p.mname, p.lname, p.DOB, p.pubpid, p.pid, " .
  	"u.fname AS ufname, u.mname AS umname, u.lname AS ulname, u.id AS uprovider_id " .
  	"FROM openemr_postcalendar_events AS e " .
  	"LEFT OUTER JOIN patient_tracker AS t ON t.pid = e.pc_pid AND t.apptdate = e.pc_eventDate AND t.appttime = e.pc_starttime " .
  	"LEFT OUTER JOIN patient_tracker_element AS q ON q.pt_tracker_id = t.id AND q.seq = t.lastseq " .
    "LEFT OUTER JOIN list_options AS s ON s.list_id = 'apptstat' AND s.option_id = q.status " .
	"LEFT OUTER JOIN patient_data AS p ON p.pid = e.pc_pid " .
  	"LEFT OUTER JOIN users AS u ON u.id = e.pc_aid " .
    "WHERE $where " . 
    "ORDER BY $order_by";
	
    $res = sqlStatement( $query, $sqlBindingArray );
    $events = array();
    if ( $res )
    {
        while ( $row = sqlFetchArray($res) ) 
        {
            // if it's a repeating appointment, fetch all occurances in date range
            if ( $row['pc_recurrtype'] ) {
                $reccuringEvents = getRecurringEvents( $row, $from_date, $to_date );
                $events = array_merge( $events, $reccuringEvents );
            } else {
                $events []= $row;
           }
        }
    }

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

function manage_tracker_status($apptdate,$appttime,$eid,$pid,$user,$status='',$room='',$enc_id='') {
  $datetime = date("Y-m-d H:i:s");
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
    if (($status != $tracker['laststatus']) || ($room != $tracker['lastroom'])) {
      #Status or room has changed, so need to update tracker.
      #Update laststatus and lastroom in tracker.	  
	   sqlStatement("UPDATE `patient_tracker` SET  `lastseq` = ? WHERE `id` = ?",
                   array(($tracker['lastseq']+1),$tracker['id']));
      #Add a tracker item.
      sqlInsert("INSERT INTO `patient_tracker_element` " .
                "(`pt_tracker_id`, `start_datetime`, `user`, `status`, `room`, `seq`) " .
                "VALUES (?,?,?,?,?,?)",
                array($tracker['id'],$datetime,$user,$status,$room,($tracker['lastseq']+1)));
    }
    if (!empty($enc_id)) {
      #enc_id is not blank, so update this in tracker.
      sqlStatement("UPDATE `patient_tracker` SET `encounter` = ? WHERE `id` = ?", array($enc_id,$tracker['id']));
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
}

function collectApptStatusSettings($option) {
  $row = sqlQuery("SELECT notes FROM list_options WHERE " .
    "list_id = 'apptstat' AND option_id = ?", array($option));
  if (empty($row['notes'])) return $option;
  return $row['notes'];
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

function manage_tracker_time($tracker1d,$drugtest) {
	
           sqlStatement("UPDATE patient_tracker SET " .
			   "random_drug_test = ? " .
               "WHERE id =? ", array($drugtest,$tracker1d));
}