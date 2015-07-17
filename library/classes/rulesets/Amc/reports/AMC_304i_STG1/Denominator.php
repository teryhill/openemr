<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_304i_STG1_Denominator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304i_STG1 Denominator";
    }

    public function test( AmcPatient $patient, $beginDate, $endDate )
    {
        //  (basically needs a referral within the report dates,
        //   which are already filtered for, so all the objects are a positive)
        return true;
    }
    
}
