<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_302m_STG2_Denominator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_302m_STG2 Denominator";
    }

    public function test( AmcPatient $patient, $beginDate, $endDate )
    {
        //Number of unique patients with office visits seen by the EP during the EHR reporting period
		 $sql = "SELECT count(*) as cnt " .
			    "FROM `form_encounter` " .
			    "WHERE `pid` = ? " .
			    "AND pc_catid = 5 ".
			    "AND `date` >= ? AND `date` <= ?";
         $check = sqlQuery($sql, array($patient->id, $beginDate, $endDate) );
		 if($check['cnt'] > 0){
			 return true;
		 }else{
			 return false;
		 }
    }
    
}
