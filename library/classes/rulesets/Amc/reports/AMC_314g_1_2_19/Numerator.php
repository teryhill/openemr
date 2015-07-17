<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.
//

class AMC_314g_1_2_19_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_314g_1_2_19 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
		// Convert the date/time to UTC as all the date information is stored in db as UTC
		$beginDate = date_to_utc("Y-m-d H:i:s",$beginDate);
		$endDate = date_to_utc("Y-m-d H:i:s",$endDate);
		
		//Secure electronic message received by EP using secure electronic messaging function of CEHRT
		$smQry = "SELECT  IF(sm.from_type = 2, sm.from_id, (SELECT pgd.pid from patient_guardian_details pgd where pgd.id = sm.from_id)) as pat_id FROM secure_messages sm ".
				 "INNER JOIN secure_message_details smd ON sm.message_id = smd.message_id AND sm.from_type IN(2,3) AND smd.to_type = 1 ".
				 "WHERE sm.message_time BETWEEN ? AND ? ".
				 "HAVING pat_id = ? ";
		$check = sqlQuery( $smQry, array($beginDate, $endDate, $patient->id) );   
		if (!(empty($check))){
			return true;
		}else{
			return false;
		}
    }
}
?>
