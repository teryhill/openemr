<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.
//

class AMC_304h_STG2_Denominator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304h_STG2 Denominator";
    }

    public function test( AmcPatient $patient, $beginDate, $endDate )
    {
        //  (basically needs a encounter within the report dates,
        //   which are already filtered for, so all the objects are a positive)
		if($patient->object['pc_catid'] == 5){//Office visit Category
			return true;
		}
		return false;
    }
    
}
