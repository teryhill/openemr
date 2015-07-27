<?php
// Copyright (C) 2015 Ensoftek Inc
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

class NFQ_0013_Exclusion implements CqmFilterIF
{
    public function getTitle() 
    {
        return "Exclusion";
    }
    
    public function test( CqmPatient $patient, $beginDate, $endDate )
    {
		//Also exclude patients with a diagnosis of pregnancy during the measurement period.
	    if ( Helper::check( ClinicalType::DIAGNOSIS, Diagnosis::PREGNANCY, $patient, $beginDate, $endDate ) ){
			return true;
		}
		
		//Dialysis procedure exists exclude the patient
		$sql = "SELECT count(*) as cnt FROM procedure_order pr ".
			   "INNER JOIN procedure_order_code prc ON pr.procedure_order_id = prc.procedure_order_id ".
			   "WHERE pr.patient_id = ? ".
			   "AND prc.procedure_code IN (' 108241001', '90937') ".
			   "AND (pr.date_ordered BETWEEN ? AND ?)"; 
		$check = sqlQuery( $sql, array($patient->id, $beginDate, $endDate) );   
		if ($check['cnt'] > 0){
			return true;
		}
		
		return false;
    }
}
