<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

        require_once("verify_session.php");

        $sql = "SELECT * FROM pnotes WHERE pid = ? AND assigned_to = '-patient-' " ;

        $res = sqlStatement($sql, array($pid) );

        if(sqlNumRows($res)>0)
        {
            ?>
            <table class="class1">
                <tr class="header">
                    <th><?php echo xlt('Type'); ?></th>
                    <th><?php echo xlt('Date'); ?></th>
                    <th><?php echo xlt('Message'); ?></th>
                    <th><?php echo xlt('From'); ?></th>
                    <th><?php echo xlt('Status'); ?></th>
                </tr>
            <?php
            $even=false;
            while ($row = sqlFetchArray($res)) {
                if ($even) {
                    $class="class1_even";
                    $even=false;
                } else {
                    $class="class1_odd";
                    $even=true;
                }
                echo "<tr class='".$class."'>";
                echo "<td>". attr($row['title'])."</td>";
                echo "<td>". attr(oeFormatShortDate(substr($row['date'], 0, strpos($row['date'], " "))))."</td>";
                echo "<td>". attr(substr($row['body'], 1 + strpos($row['body'], ")")))."</td>";
                echo "<td>". attr($row['user'])."</td>";
                echo "<td>". attr($row['message_status'])."</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        }
        else
        {
            echo xlt("No Results");
        }
?>
