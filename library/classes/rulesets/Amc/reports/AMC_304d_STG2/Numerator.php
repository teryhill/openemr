<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_304d_STG2_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304d_STG2 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
        // Were sent an appropriate reminder during the EHR reporting period
        $result_query = sqlQuery("SELECT * FROM `patient_reminders` WHERE `pid`=? AND `date_sent`>=? AND `date_sent`<=?", array($patient->id,$beginDate,$endDate) );
        if ( !(empty($result_query)) ) {
            return true;
        }
        else {
            return false;
        }
    }
}
