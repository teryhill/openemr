<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.
//

class AMC_314g_1_2_19 extends AbstractAmcReport
{
    public function getTitle()
    {
        return "AMC_314g_1_2_19";
    }

    public function getObjectToCount()
    {
        return "encounters";
    }
 
    public function createDenominator() 
    {
        return new AMC_314g_1_2_19_Denominator();
    }
    
    public function createNumerator()
    {
        return new AMC_314g_1_2_19_Numerator();
    }
}
