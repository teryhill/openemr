<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.


class AMC_302f_STG2_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_302f_STG2 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
        if( ( ($patient->calculateAgeOnDate($endDate) >= 3) && 
		      (exist_database_item($patient->id,'form_vitals','bps'    ,'gt'  ,'0' ,'ge',1,'','',$endDate)) &&
		      (exist_database_item($patient->id,'form_vitals','bpd'    ,'gt'  ,'0' ,'ge',1,'','',$endDate)) &&
			  (exist_database_item($patient->id,'form_vitals','height' ,'gt','0','ge',1,'','',$endDate)) &&
              (exist_database_item($patient->id,'form_vitals','weight' ,'gt','0','ge',1,'','',$endDate))
			) 
		   ||
		   ( ($patient->calculateAgeOnDate($endDate) < 3) &&
			 (exist_database_item($patient->id,'form_vitals','height' ,'gt','0','ge',1,'','',$endDate)) &&
             (exist_database_item($patient->id,'form_vitals','weight' ,'gt','0','ge',1,'','',$endDate)) 
		   ) ){
			return true;	   
		}else {
            return false;
        }
    }
}
