<?php
/**
 *
 * AMC 302j Numerator
 *
 * Copyright (C) 2011-2015 Brady Miller <brady@sparmy.com>
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Brady Miller <brady@sparmy.com>
 * @link    http://www.open-emr.org
 */

class AMC_302j_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_302j Numerator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
        // Need a medication reconciliation completed.
        //  (so basically the completed element of the object can't be empty
		$sql = "SELECT amc_misc_data.map_id as `encounter`, amc_misc_data.date_completed as `completed`, form_encounter.date as `date` " .
		"FROM `amc_misc_data`, `form_encounter` " .
		"WHERE amc_misc_data.map_id = form_encounter.encounter " .
		"AND amc_misc_data.map_category = 'form_encounter' " .
		"AND amc_misc_data.amc_id = 'med_reconc_amc' " .
		"AND form_encounter.encounter = ?";
        $check = sqlQuery( $sql, array($patient->object['encounter']) ); 
		if ($check['completed'] != ""){
			return true;
		}else{
			return false;
		}
    }
}
