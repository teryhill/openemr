<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_304a_3_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304a_3 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
        // MEASURE STAGE2: Medication Order(s) Created as CPOE
		if ( $patient->object['cpoe_stat'] == 'yes')  {
          return true;
        }else {
          return false;
        }
    }
}
