<?php
// Copyright (c) 2015 Ensoftek, Inc
//
// This program is protected by copyright laws; you may not redistribute it and/or
// modify it in part or whole for any purpose without prior express written permission 
// from EnSoftek, Inc.

class AMC_304i_STG1 extends AbstractAmcReport
{
    public function getTitle()
    {
        return "AMC_304i_STG1";
    }

    public function getObjectToCount()
    {
        return "transitions-out-new";
    }
 
    public function createDenominator() 
    {
        return new AMC_304i_STG1_Denominator();
    }
    
    public function createNumerator()
    {
        return new AMC_304i_STG1_Numerator();
    }
}
