<?php
// Copyright (C) 2011 Brady Miller <brady@sparmy.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//


class AMC_304a_2_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304a_2 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
        // MEASURE STAGE2: Procedure Order(s) Created CPOE
		if ( $patient->object['cpoe_stat'] == 'Yes')  {
          return true;
        }else {
          return false;
        }
    }
}
