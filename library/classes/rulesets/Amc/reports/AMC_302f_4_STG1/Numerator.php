<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.


class AMC_302f_4_STG1_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_302f_4_STG1 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
        //If height/length, weight, and blood pressure (all) within scope of practice (Optional 2013; Required effective 2014):  
		//Patients 3 years of age or older in the denominator for whom Height/length, weight, and blood pressure are recorded 
		//Patients younger than 3 years of age in the denominator for whom height/length and weight are recorded
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
