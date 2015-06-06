<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 7:05 PM
 */
$username = 'victor';
var_dump(explode("$", shell_exec("cat /etc/shadow | grep " . $username . " | awk -F : '{print $2}'")));
?>
