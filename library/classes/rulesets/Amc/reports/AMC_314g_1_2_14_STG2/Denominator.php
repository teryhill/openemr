<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.
//

class AMC_314g_1_2_14_STG2_Denominator implements AmcFilterIF
{
	public $patArr = array();
    public function getTitle()
    {
        return "AMC_314g_1_2_14_STG2 Denominator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
		if(!in_array($patient->id, $this->patArr)){
			$this->patArr[] = $patient->id;
			return true;
		}
		else
			return false;
    }
}
