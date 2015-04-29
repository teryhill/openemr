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
 
 function  update_tracker_status($apptdate,$appttime,$pid,$user,$status,$room,$record_id)
     {

         $track_date = date("Y-m-d H:i:s");	
         $endtime = "00:00:00";
         $today   = date("Y-m-d");
		 
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
               "WHERE id =? AND apptdate =?", array($room,$nextseq,$endtime,$status,$record_id,$apptdate));

		    sqlInsert("INSERT INTO patient_tracker_element SET " .
			   "pt_tracker_id = ?, " .
			   "start_datetime = ?, " .
			   "status = ?, " .
			   "room =? ," .
			   "seq =? ," .
			   "user = ? ",
    			array($record_id,$track_date,$status,$room,$nextseq,$user)
             );	
	
         sqlStatement("UPDATE openemr_postcalendar_events SET " .
            "pc_apptstatus =? " .
            "where pc_pid =? AND pc_eventdate =? AND pc_startTime =? ", array($status,$pid,$today,$appttime)); 
    }	
	
function  add_tracker_status($apptdate,$appttime,$pid,$user,$status,$pceid,$encounter)
     {	
      $tmptrk = sqlQuery("SELECT id FROM patient_tracker WHERE pid = ? AND apptdate = ? AND appttime = ? AND eid = ?", array($pid,$apptdate,$appttime,$pceid) );
      $checkid = $tmptrk['id'];
      $track_date = date("Y-m-d H:i:s");	
      $endtime = "00:00:00";
      $today   = date("Y-m-d");
        if(strlen($checkid) == 0) {  
             sqlInsert("INSERT INTO patient_tracker SET " .
			   "date = ?, " .
			   "apptdate = ?, " .
			   "appttime = ?, " .
               "eid = ?, " .
               "pid = ?, " .
			   "user = ?, " .
			   "laststatus = ?, " .
			   "lastseq = ?, " .
			   "encounter = ? ",
    			array($track_date,$apptdate,$appttime,$pceid,$pid,$user,$status,'1',$encounter)	
              );
	
             $tmptrk = sqlQuery("SELECT id FROM patient_tracker WHERE pid = ? AND apptdate = ? AND appttime = ?", array($pid,$apptdate,$appttime) );
             $maintkid = $tmptrk['id'];
			 
		     sqlInsert("INSERT INTO patient_tracker_element SET " .
			   "pt_tracker_id = ?, " .
			   "start_datetime = ?, " .
			   "status = ?, " .
			   "seq = ?, " .
			   "user = ? ",
    			array($maintkid,$track_date,$status,'1',$user)
              );	
	    }
	 }