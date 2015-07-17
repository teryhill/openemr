<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_304i_STG2_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304i_STG2 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
		//The number of transitions of care and referrals in the denominator where a summary of care record was electronically transmitted using CEHRT to a recipient.
		$sumQry =   "SELECT count(*) as cnt FROM amc_misc_data ".
					"WHERE map_category = 'form_encounter' ".
					"AND amc_id IN( 'med_reconc_amc' ) ".
					"AND from_ccda = 1 ".
					"AND map_id = '".$patient->object['encounter']."'";
		$check = sqlQuery($sumQry); 
		if ($check['cnt'] > 0){
			return true;
		}else{
			return false;
		}
    }
}
