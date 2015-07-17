<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_304d_STG2_Denominator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304d_STG2 Denominator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
       	$beginDate = date("Y-m-d", strtotime($beginDate));
		//MEASURE STAGE 2: Number of unique patients who have had two or more office visits with the EP in the 24 months prior to the beginning of the EHR reporting period
		$denomQry = "SELECT count(fe.encounter) as cnt FROM form_encounter fe ".
					"INNER JOIN enc_category_map em ON em.main_cat_id = fe.pc_catid AND em.rule_enc_id = 'enc_outpatient' ".
					"WHERE (DATE(fe.date) BETWEEN DATE_SUB(?, INTERVAL 2 YEAR) AND ?) ".
					"AND fe.pc_catid = 5 ".
					"AND fe.pid = ? ";
	
		$check = sqlQuery($denomQry, array($beginDate, $beginDate, $patient->id));
		if ( $check['cnt'] >= 2 ) {
				return true;
        }else {
            return false;
        }
    }
}
