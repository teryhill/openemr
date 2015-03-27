<?php
/**
 *
 * Postal Codes Look up Ajax File.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author     Sam Likins <sam.likins@wsi-services.com>
 * @author  Terry Hill <terry@lillysystems.com>
 * @link    http://www.open-emr.org
 */

$sanitize_all_escapes = true;
$fake_register_globals = false;

require_once("../../interface/globals.php");

$postalCode = trim($_POST['potal']);

if($postalCode) {
$postcode = sqlQuery('SELECT `notes` AS `city`, `mapping` AS `state`, `title` AS `postal`
		FROM `list_options`
		WHERE `list_id` = \'postal_codes\'
			AND `title` = ?;',
		array($postalCode)
	);

	echo json_encode($postcode);
} else {
	echo json_encode(array('fail' => xl('Postal Code not provided.')));
}