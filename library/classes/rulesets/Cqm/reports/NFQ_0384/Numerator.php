<?php
// Copyright (C) 2015 Ensoftek Inc
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

class NFQ_0384_Numerator implements CqmFilterIF
{
    public function getTitle()
    {
        return "Numerator";
    }

    public function test( CqmPatient $patient, $beginDate, $endDate )
    {
		//Patient visits in which pain intensity is quantified
		$painQry = "SELECT if(fv.pain IS NULL, 'no', fv.pain) as pain_cnt FROM form_vitals fv ".
				   "INNER JOIN form_encounter fe ON fe.pid = fv.pid ".
				   "INNER JOIN forms f ON f.pid = fe.pid and f.formdir = 'vitals' AND fe.encounter = fe.encounter AND fv.id = f.form_id ".
				   "WHERE fe.pid = ? ".
				   "AND (fe.date BETWEEN ? AND ?)";
		
		$check = sqlQuery( $painQry, array($beginDate, $endDate, $patient->id) );   
		if ( $check['pain_cnt'] != "" && $check['pain_cnt'] != 'no' ){
			return true;
		}else{
			$riskCatAssessQry = "SELECT count(*) as cnt FROM form_encounter fe ".
								"INNER JOIN openemr_postcalendar_categories opc ON fe.pc_catid = opc.pc_catid ".
								"INNER JOIN procedure_order pr ON  fe.encounter = pr.encounter_id ".
								"INNER JOIN procedure_order_code prc ON pr.procedure_order_id = prc.procedure_order_id ".
								"WHERE opc.pc_catname = 'Office Visit' ".
								"AND (fe.date BETWEEN ? AND ?) ".
								"AND fe.pid = ? ".
								"AND ( prc.procedure_code = '38208-5') ".
								"AND prc.procedure_order_title = 'Risk Category Assessment'";
		
			$check = sqlQuery( $riskCatAssessQry, array($beginDate, $endDate, $patient->id) );   
			if ($check['cnt'] > 0){
				return true;
			}else{
				return false;
			}
		}
    }
}
