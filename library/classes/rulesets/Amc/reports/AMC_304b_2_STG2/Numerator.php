<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_304b_2_STG2_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304b_2_STG2 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
       //The number of prescriptions in the denominator generated, queried for a drug formulary and transmitted electronically.
       if ( ($patient->object['eTransmit'] == 1) && ($patient->object['formulary'] == 'yes') )  {
		   return true;
	    }else{
		   return false;
	    }
    }
}
