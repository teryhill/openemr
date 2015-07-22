<?php
/**
 *
 * AMC 304a 1 Class Extension
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


class AMC_304a_1 extends AbstractAmcReport
{
    public function getTitle()
    {
        return "AMC_304a_1";
    }

    public function getObjectToCount()
    {
        return "lab_radiology";
    }
 
    public function createDenominator() 
    {
        return new AMC_304a_1_Denominator();
    }
    
    public function createNumerator()
    {
        return new AMC_304a_1_Numerator();
    }
}
