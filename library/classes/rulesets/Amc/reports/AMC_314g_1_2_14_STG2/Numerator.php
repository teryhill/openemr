<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.
//

class AMC_314g_1_2_14_STG2_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_314g_1_2_14_STG2 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
		$portalQry = "SELECT count(*) as cnt FROM `patient_access_onsite` WHERE pid=?";
		$check = sqlQuery( $portalQry, array($patient->id) );  
		if ($check['cnt'] > 0){
			return true;
		}else{
			return false;
		}
    }
}
?>