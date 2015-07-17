<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

// Denominator: 
// Number of prescriptions written for drugs requiring a prescription in order to be
// dispensed other than controlled substances during the EHR reporting period

class AMC_304b_STG1_Denominator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304b_STG1 Denominator";
    }

    public function test( AmcPatient $patient, $beginDate, $endDate )
    {
		return true;
    }
    
}
