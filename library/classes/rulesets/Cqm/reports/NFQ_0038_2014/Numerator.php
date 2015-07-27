<?php
// Copyright (C) 2015 Ensoftek Inc
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

class NFQ_0038_2014_Numerator implements CqmFilterIF 
{
    public function getTitle() {
        return "Numerator";
    }
    
    public function test( CqmPatient $patient, $beginDate, $endDate )
    {
       	if (  (Immunizations::checkDtap( $patient, $beginDate, $endDate ) ) &&
			  ( Immunizations::checkIpv( $patient, $beginDate, $endDate ) ) &&
			  ( Immunizations::checkMmr( $patient, $beginDate, $endDate ) ) &&
			  ( Immunizations::checkHib( $patient, $beginDate, $endDate ) ) &&
			  ( Immunizations::checkHepB( $patient, $beginDate, $endDate ) ) &&
			  ( Immunizations::checkVzv( $patient, $beginDate, $endDate ) )  &&
			  ( Immunizations::checkPheumococcal( $patient, $beginDate, $endDate ) ) && 
			  ( Immunizations::checkHepA( $patient, $beginDate, $endDate ) ) &&
			  ( Immunizations::checkRotavirus_2014( $patient, $beginDate, $endDate ) ) &&
			  ( Immunizations::checkInfluenza( $patient, $beginDate, $endDate ) ) 
			) 
		{
            return true;
        }
        return false;
    }
}
