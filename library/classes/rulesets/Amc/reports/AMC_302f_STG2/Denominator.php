<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_302f_STG2_Denominator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_302f_STG2 Denominator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
		// If height/length, weight, and blood pressure (all) within scope of practice:
		// Number of unique patients seen by the EP during the EHR reporting period
		if ( (exist_database_item($patient->id,'form_vitals','height' ,'gt','0','ge',1,'','',$endDate)) &&
             (exist_database_item($patient->id,'form_vitals','weight' ,'gt','0','ge',1,'','',$endDate)) &&
             (exist_database_item($patient->id,'form_vitals','bps'    ,'gt'  ,'0' ,'ge',1,'','',$endDate)) &&
		     (exist_database_item($patient->id,'form_vitals','bpd'    ,'gt'  ,'0' ,'ge',1,'','',$endDate)) 
           )
        {
            $options = array( Encounter::OPTION_ENCOUNTER_COUNT => 1 );
			if (Helper::checkAnyEncounter($patient, $beginDate, $endDate, $options)) {
				return true;
			}
			else {
				return false;
			}
        }
		// If height/length and weight (only) within scope of practice:
		// Number of unique patients seen by the EP during the EHR reporting period
        else if( (exist_database_item($patient->id,'form_vitals','height' ,'gt','0','ge',1,'','',$endDate)) &&
				(exist_database_item($patient->id,'form_vitals','weight' ,'gt','0','ge',1,'','',$endDate)) )
		{
			$options = array( Encounter::OPTION_ENCOUNTER_COUNT => 1 );
			if (Helper::checkAnyEncounter($patient, $beginDate, $endDate, $options)) {
				return true;
			}
			else {
				return false;
			}
        }
		// If blood pressure (only) within scope of practice:
		// Number of unique patients 3 years of age or older seen by the EP during the EHR reporting period.
		else if( (exist_database_item($patient->id,'form_vitals','bps'    ,'gt'  ,'0' ,'ge',1,'','',$endDate)) &&
				 (exist_database_item($patient->id,'form_vitals','bpd'    ,'gt'  ,'0' ,'ge',1,'','',$endDate)) 
			    )
		{
			$options = array( Encounter::OPTION_ENCOUNTER_COUNT => 1 );
			if ( (Helper::checkAnyEncounter($patient, $beginDate, $endDate, $options)) &&
				 ($patient->calculateAgeOnDate($endDate) >= 3) ) {
				return true;
			}
			else {
				return false;
			}
		}
    }
}
