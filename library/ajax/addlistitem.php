<?php
/*
 * Copyright (C) 2009 Jason Morrill <jason@italktech.net>
 * Copyright (C) 2015 Terry Hill <terry@lillysystems.com>
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
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>.
 * 
 * This file is used to add an item to the list_options table
 *
 * 2015 I modified this file to allow the postal code feature to be implemented (TLH)
 *
 *
 * OUTPUT 
 *   on error = NULL
 *   on succcess = JSON data, array of "value":"title" for new list of options
 * @package OpenEMR
 * @author  Jason Morrill <jason@italktech.net>
 * @author  Terry Hill <terry@lillysystems.com>
 * @link    http://www.open-emr.org
*/

include_once("../../interface/globals.php");
include_once("{$GLOBALS['srcdir']}/sql.inc");

// check for required values
if ($_GET['listid'] == "" || trim($_GET['newitem']) == "" || trim($_GET['newitem_abbr']) == "") exit;

// set the values for the new list item
$is_default = 0;
$option_value = 0;
$list_id = $_GET['listid'];
if ($_GET['listid'] != 'postal_codes') {
    $title = trim($_GET['newitem']);
    $option_id = trim($_GET['newitem_abbr']);
}
else
{
    $title = trim($_GET['newitem_abbr']);
    //$option_id = trim($_GET['newitem_city']);
    $city = strtoupper(trim($_GET['newitem_city']));
    $state= strtoupper(trim($_GET['newitem']));
}
// make sure we're not adding a duplicate title or id
$exists_title = sqlQuery("SELECT * FROM list_options WHERE ".
                    " list_id='".$list_id."'".
                    " and title='".trim($title). "'" 
                    );
if ($exists_title) { 
	echo json_encode(array("error"=> xl('Record already exist') ));
	exit; 
}

if ($_GET['listid'] != 'postal_codes') {
$exists_id = sqlQuery("SELECT * FROM list_options WHERE ".
                    " list_id='".$list_id."'".
                    " and option_id='".trim($option_id)."'"
                    );
}

if ($exists_id) { 
	echo json_encode(array("error"=> xl('Record already exist') ));
	exit; 
}

if ($_GET['listid'] == 'postal_codes') {
 if (strlen(trim($title)) <5) { 
	echo json_encode(array("error"=> xl('Postal Code must be at least 5 digits in length') ));
	exit; 
 }
}

// determine the sequential order of the new item,
// it should be the maximum number for the specified list plus one
$seq = 0;
$row = sqlQuery("SELECT max(seq) as maxseq FROM list_options WHERE list_id= '".$list_id."'");
$seq = $row['maxseq']+1;

// add the new list item
if ($list_id == 'postal_codes') {
$rc = sqlInsert("INSERT INTO list_options ( " .
                "list_id, option_id, title, seq, is_default, option_value, mapping, notes" .
                ") VALUES (" .
                "'".$list_id."'".
                ",'".trim($title)."'" .
                ",'".trim($title). "'" .
                ",'".$seq."'" .
                ",'".$is_default."'" .
                ",'".$option_value."'".
				",'".$city."'".
				",'".$state."'".
                ")"
);
}
else
{
$rc = sqlInsert("INSERT INTO list_options ( " .
                "list_id, option_id, title, seq, is_default, option_value" .
                ") VALUES (" .
                "'".$list_id."'".
                ",'".trim($option_id)."'" .
                ",'".trim($title). "'" .
                ",'".$seq."'" .
                ",'".$is_default."'" .
                ",'".$option_value."'".
                ")"
);	
}

// return JSON data of list items on success
echo '{ "error":"", "options": [';
// send the 'Unassigned' empty variable
echo '{"id":"","title":"' . xl('Unassigned') . '"}';
$comma = ",";
$lres = sqlStatement("SELECT * FROM list_options WHERE list_id = '$list_id' ORDER BY seq");
while ($lrow = sqlFetchArray($lres)) {
    echo $comma;
    echo '{"id":"'.$lrow['option_id'].'",';
    
    // translate title if translate-lists flag set and not english
    if ($GLOBALS['translate_lists'] && $_SESSION['language_choice'] > 1) {
     echo '"title":"' . xl($lrow['title']) .'"}';
    }
    else {
     echo '"title":"'.$lrow['title'].'"}';	
    }
}
echo "]}";
exit;

?>
