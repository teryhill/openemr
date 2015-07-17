<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.
// Denominator:
// 		Reporting period start and end date
// 		Prescription written for drugs requiring a prescription in order to be dispensed

// Generate and transmit permissible prescriptions electronically (Controlled substances with drug formulary).( AMC-2014:170.314(g)(1)/(2)–8 )

class AMC_304b_1_STG2_Denominator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304b_1_STG2 Denominator";
    }

    public function test( AmcPatient $patient, $beginDate, $endDate )
    {
		return true;
    }
    
}
