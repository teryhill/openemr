<?php
	// Copyright (C) 2015 Ensoftek Inc
	//
	// This program is free software; you can redistribute it and/or
	// modify it under the terms of the GNU General Public License
	// as published by the Free Software Foundation; either version 2
	// of the License, or (at your option) any later version.
	// Functions for QRDA Category I (or) III 2014 XML format.
	
	//function for Stratification data getting for NQF# 0024 Rule
	function getQRDAStratumInfo($patArr, $begin_date){
		$startumArr = array();
		if(count($patArr) > 0){
			//Age Between 3 and 11
			$stratumOneQry = "SELECT FLOOR( DATEDIFF( '".$begin_date."' , DOB ) /365 ) as pt_age FROM patient_data WHERE pid IN (".implode(",", $patArr).") HAVING  (pt_age BETWEEN 1 AND 10) ";
			$stratumOneRes = sqlStatement($stratumOneQry);
			$stratumOneRows = sqlNumRows($stratumOneRes);
			
			//Age Between 12 and 17
			$stratumTwoQry = "SELECT FLOOR( DATEDIFF( '".$begin_date."' , DOB ) /365 ) as pt_age FROM patient_data WHERE pid IN (".implode(",", $patArr).") HAVING  (pt_age BETWEEN 11 AND 16) ";
			$stratumTwoRes = sqlStatement($stratumTwoQry);
			$stratumTwoRows = sqlNumRows($stratumTwoRes);
			$startumArr[1] = $stratumOneRows;
			$startumArr[2] = $stratumTwoRows;
		}else{
			$startumArr[1] = 0;
			$startumArr[2] = 0;
		}
		return $startumArr;
	}
	
	//function for getting Payer(Insurance Type) Information for Export QRDA
	function getQRDAPayerInfo($patArr){
		$payerCheckArr = array();
		$payerCheckArr['Medicare'] = 0;
		$payerCheckArr['Medicaid'] = 0;
		$payerCheckArr['Private Health Insurance'] = 0;
		$payerCheckArr['Other'] = 0;
		if(count($patArr) > 0){
			$insQry = "SELECT insd.*, ic.freeb_type FROM (SELECT pid, provider FROM insurance_data WHERE type = 'primary' ORDER BY id DESC) insd ".
					  "INNER JOIN  insurance_companies ic ON insd.provider = ic.id ".
					  "WHERE insd.pid IN (".implode(",", $patArr).")";
			$insRes = sqlStatement($insQry);
			while($insRow = sqlFetchArray($insRes)){
				if($insRow['freeb_type'] == 8){//Self Pay (Private Insurance)
					$payerCheckArr['Private Health Insurance']++;
				}else if($insRow['freeb_type'] == 2){//Medicare
					$payerCheckArr['Medicare']++;
				}else if($insRow['freeb_type'] == 3){//Self Pay (Private Insurance)
					$payerCheckArr['Medicaid']++;
				}else{//Other
					$payerCheckArr['Other']++;
				}
			}
		}
		
		return $payerCheckArr;
	}
	
	//function for getting Race, Ethnicity and Gender Information for Export QRDA
	function getQRDAPatientNeedInfo($patArr){
		//Defining Array elements
		//Gender
		$genderArr = array();
		$genderArr['Male'] = 0;
		$genderArr['Female'] = 0;
		$genderArr['Unknown'] = 0;
		//Race
		$raceArr = array();
		$raceArr['American Indian or Alaska Native'] = 0;
		$raceArr['Asian'] = 0;
		$raceArr['Black or African American'] = 0;
		$raceArr['Native Hawaiian or Other Pacific Islander'] = 0;
		$raceArr['White'] = 0;
		$raceArr['Other'] = 0;
		//Ethnicity
		$ethincityArr = array();
		$ethincityArr['Not Hispanic or Latino'] = 0;
		$ethincityArr['Hispanic or Latino'] = 0;
		
		$mainArr = array();
		if(count($patArr) > 0){
			$patRes = sqlStatement("SELECT pid, sex, race, ethnicity FROM patient_data WHERE pid IN (".implode(",", $patArr).")");
			while($patRow = sqlFetchArray($patRes)){
				//Gender Collection
				if($patRow['sex'] == "Male"){
					$genderArr['Male']++; 
				}else if($patRow['sex'] == "Female"){
					$genderArr['Female']++; 
				}else{
					$genderArr['Unknown']++; 
				}
				
				//Race Section
				if($patRow['race'] == "amer_ind_or_alaska_native"){
					$raceArr['American Indian or Alaska Native']++;
				}else if($patRow['race'] == "Asian"){
					$raceArr['Asian']++;
				}else if($patRow['race'] == "black_or_afri_amer"){
					$raceArr['Black or African American']++;
				}else if($patRow['race'] == "native_hawai_or_pac_island"){
					$raceArr['Native Hawaiian or Other Pacific Islander']++;
				}else if($patRow['race'] == "white"){
					$raceArr['White']++;
				}else if($patRow['race'] == "Asian_Pacific_Island"){
					$raceArr['Other']++;
				}else if($patRow['race'] == "Black_not_of_Hispan"){
					$raceArr['Other']++;
				}else if($patRow['race'] == "Hispanic"){
					$raceArr['Other']++;
				}else if($patRow['race'] == "White_not_of_Hispan"){
					$raceArr['Other']++;
				}else{
					$raceArr['Other']++;
				}
				
				//Ethnicity Section
				/*if($patRow['ethnicity'] == "eth_white_non_his"){
					$ethincityArr['Not Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_black_non_his"){
					$ethincityArr['Not Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_native_amer"){
					$ethincityArr['Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_alaskan_native"){
					$ethincityArr['Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_his_mexican"){
					$ethincityArr['Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_his_cuban"){
					$ethincityArr['Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_his_other"){
					$ethincityArr['Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_se_asian"){
					$ethincityArr['Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_asian_pac_isl"){
					$ethincityArr['Not Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_his_pr"){
					$ethincityArr['Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "eth_other" || $patRow['ethnicity'] == ""){
					//Other 
					$ethincityArr['Not Hispanic or Latino']++;
				}*/
				
				if($patRow['ethnicity'] == "hisp_or_latin"){
					$ethincityArr['Hispanic or Latino']++;
				}else if($patRow['ethnicity'] == "not_hisp_or_latin"){
					$ethincityArr['Not Hispanic or Latino']++;
				}
			}
		}
		$mainArr['gender'] = $genderArr;
		$mainArr['race'] = $raceArr;
		$mainArr['ethnicity'] = $ethincityArr;
		
		return $mainArr;
	}
	
	function payerPatient($patient_id){
		$payer = 'Other';
		$insQry = "SELECT insd.*, ic.freeb_type FROM (SELECT pid, provider FROM insurance_data WHERE type = 'primary' ORDER BY id DESC) insd ".
					  "INNER JOIN  insurance_companies ic ON insd.provider = ic.id ".
					  "WHERE insd.pid = '".$patient_id."'";
		$insRes = sqlStatement($insQry);
		while($insRow = sqlFetchArray($insRes)){
			if($insRow['freeb_type'] == 8){//Self Pay (Private Insurance)
				$payer = 'Private Health Insurance';
			}else if($insRow['freeb_type'] == 2){//Medicare
				$payer = 'Medicare';
			}else if($insRow['freeb_type'] == 3){//Self Pay (Private Insurance)
				$payer = 'Medicaid';
			}else{//Other
				$payer = 'Other';
			}
		}
		return $payer;
	}
	
	function allEncPat($patient_id, $from_date, $to_date){
		$encArr = array();
		$patQry = "SELECT encounter, date FROM form_encounter WHERE pid = '".$patient_id."' AND (DATE(date) BETWEEN '".$from_date."' AND '".$to_date."')";
		$patRes = sqlStatement($patQry);
		while( $patRow = sqlFetchArray($patRes ) ){
			$encArr[] = $patRow;
		}
		
		return $encArr;
	}
	
	function allListsPat($type, $patient_id, $from_date, $to_date){
		$diagArr = array();
		$diagQry = "SELECT * FROM lists WHERE TYPE = '".$type."' AND pid = '".$patient_id."' AND (DATE(date) BETWEEN '".$from_date."' AND '".$to_date."')";
		$diagRes = sqlStatement($diagQry);
		while( $diagRow = sqlFetchArray($diagRes ) ){
			$diagArr[] = $diagRow;
		}
		
		return $diagArr;
	}
	
	function allProcPat($proc_type = "Procedure", $patient_id, $from_date, $to_date){
		$procArr = array();
		$procQry = "SELECT poc.procedure_code, poc.procedure_name, po.date_ordered, fe.encounter FROM form_encounter fe ".
					"INNER JOIN forms f ON f.encounter = fe.encounter AND f.deleted != 1 AND f.formdir = 'procedure_order_oemr' ".
					"INNER JOIN procedure_order po ON po.procedure_order_id = f.form_id ".
					"INNER JOIN procedure_order_code poc ON poc.procedure_order_id = po.procedure_order_id ".
					"WHERE poc.procedure_order_title = '".$proc_type."' AND po.patient_id = '".$patient_id."' ".
					"AND (po.date_ordered BETWEEN '".$from_date."' AND '".$to_date."')";
		$procRes = sqlStatement($procQry);
		while( $procRow = sqlFetchArray($procRes ) ){
			$procArr[] = $procRow;
		}
		
		return $procArr;
	}
	
	function allVitalsPat($patient_id, $from_date, $to_date){
		$vitArr = array();
		$vitQry = "SELECT fe.encounter, v.bps, v.date FROM form_encounter fe ".
					"INNER JOIN forms f ON f.encounter = fe.encounter AND f.deleted != 1 AND f.formdir = 'vitals' ".
					"INNER JOIN form_vitals v ON v.id = f.form_id ".
					"WHERE v.pid = '".$patient_id."' ".
					"AND (v.date BETWEEN '".$from_date."' AND '".$to_date."')";
		$vitRes = sqlStatement($vitQry);
		while( $vitRow = sqlFetchArray($vitRes ) ){
			$vitArr[] = $vitRow;
		}
		
		return $vitArr;
	}
	
	function allImmuPat($patient_id, $from_date, $to_date){
		$immArr = array();
		$immQry =   "SELECT * FROM immunizations ".
					"WHERE patient_id = '".$patient_id."' ".
					"AND (administered_date BETWEEN '".$from_date."' AND '".$to_date."')";
		$immRes = sqlStatement($immQry);
		while( $immRow = sqlFetchArray($immRes ) ){
			$immArr[] = $immRow;
		}
		
		return $immArr;
	}
	function getPatData($patient_id){
		$patientRow = sqlQuery("SELECT * FROM patient_data WHERE pid= '".$patient_id."'");
		return $patientRow;
	}
	
	function getUsrDataCheck($provider_id){
		$userRow = array();
		if($provider_id != ""){
			$userRow = sqlQuery("SELECT facility, facility_id, federaltaxid, npi, phone,fname, lname FROM users WHERE id= '".$provider_id."'");
		}
		return $userRow;
	}
	
	function getFacilDataChk($facility_id){
		$facilResRow = sqlQuery("SELECT name, street,city,state,postal_code, country_code, phone from facility WHERE id = '".$facility_id."'");
		return $facilResRow;
	}
	
	function patientQRDAHistory($patient_id){
		$patientHistRow = sqlQuery("SELECT tobacco, date FROM history_data WHERE pid= '".$patient_id."' ORDER BY id DESC LIMIT 1");
		return $patientHistRow;
	}
	
?>