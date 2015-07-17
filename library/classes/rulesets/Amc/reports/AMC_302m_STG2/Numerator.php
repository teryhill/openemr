<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_302m_STG2_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_302m_STG2 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
        // Is patient provided patient specific education during the report period.

        // Check for any patient specific education instances.
        $item = sqlQuery("SELECT * FROM `amc_misc_data` as amc, `form_encounter` as enc " .
                         "WHERE enc.pid = amc.pid AND enc.pid = ? " .
                         "AND amc.map_category = 'form_encounter' " .
                         "AND enc.encounter = amc.map_id " .
                         "AND `amc_id` = 'patient_edu_amc' " .
                         "AND enc.date >= ? " .
                         "AND enc.date <= ?", array($patient->id,$beginDate,$endDate) );

        if ( !(empty($item)) ) {
          return true;
        }
        else {
          return false;
        }
    }
}
