<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.


class AMC_302f_2_STG1_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_302f_2_STG1 Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
        //The number of patients in the denominator who have entries of height/length and weight recorded as structured data (Effective 2013 onward for providers for whom blood pressure is out of scope of practice)
        if ( (exist_database_item($patient->id,'form_vitals','height' ,'gt','0','ge',1,'','',$endDate)) &&
             (exist_database_item($patient->id,'form_vitals','weight' ,'gt','0','ge',1,'','',$endDate)) 
           )
        {
            return true;
        }
        else {
			return false;
        }
    }
}
