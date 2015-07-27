<?php
// Copyright (C) 2015 Ensoftek Inc
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

class NFQ_0038_2014_Denominator implements CqmFilterIF
{
    public function getTitle() {
        return "Denominator";
    }
    
    public function test( CqmPatient $patient, $beginDate, $endDate ) 
    {
        $oneEncounter = array( Encounter::OPTION_ENCOUNTER_COUNT => 1 );
        if ( Helper::check( ClinicalType::ENCOUNTER, Encounter::ENC_OUT_PCP_OBGYN, $patient, $beginDate, $endDate, $oneEncounter ) ) {
            return true;
        }
        
        return false;
    }
}
