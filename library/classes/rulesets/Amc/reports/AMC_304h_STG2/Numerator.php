<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.
//

class AMC_304h_STG2_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304h_STG2 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
		//The number of office visits in the denominator where the patient or a patient authorized Representative is provided a clinical summary of their visit within 1 Business day.
		$amcElement = amcCollect('provide_sum_pat_amc',$patient->id,'form_encounter',$patient->object['encounter']);
		if (!(empty($amcElement))) {
		  $daysDifference = businessDaysDifference( date("Y-m-d",strtotime($patient->object['date'])) , date("Y-m-d",strtotime($amcElement['date_completed'])) );
		  error_log("DEBUG: ".$daysDifference,0);
		  if ($daysDifference < 2) {
			return true;
		  }
		}

		return false;
    }
}
