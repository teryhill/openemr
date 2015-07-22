<?php
/**
 *
 * AMC 314g 1 2 22 Denominator
 *
 * Copyright (C) 2015 Ensoftek, Inc
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
 * @author  Ensoftek, Inc
 * @link    http://www.open-emr.org
 *
 */

class AMC_314g_1_2_22_Denominator implements AmcFilterIF
{
	public $patArr = array();
    public function getTitle()
    {
        return "AMC_314g_1_2_22 Denominator";
    }
    
    public function test( AmcPatient $patient, $beginDate, $endDate ) 
    {
		if(!in_array($patient->id, $this->patArr)){
			$this->patArr[] = $patient->id;
			return true;
		}
		else
			return false;
    }
}
