<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.
//

class AMC_314g_1_2_14_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_314g_1_2_14 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
		//The number of patients in the denominator who have timely (within 4 business days after the information is available to the EP) on-line access to their health information. 
		//Patient Portal has access to done V/D/T
		
		// Convert the date/time to UTC as all the date information is stored in db as UTC
		$beginDate = date_to_utc("Y-m-d H:i:s",$beginDate);
		$endDate = date_to_utc("Y-m-d H:i:s",$endDate);
		$portalQry = "SELECT count(*) as cnt FROM patient_data pd ".
					 "INNER JOIN ccda_log cl ON pd.pid = cl.patient_id AND cl.user_type = 2 AND cl.event IN ('patient-record-view', 'patient-record-download', 'patient-record-transmit') ".
					 "WHERE  pd.pid = ? AND cl.date BETWEEN ? AND ?";
		$check = sqlQuery( $portalQry, array($patient->id, $beginDate, $endDate) );  
		if ($check['cnt'] > 0){
			return true;
		}else{
			return false;
		}
    }
}
?>