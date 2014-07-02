<?php

/* Database config */

$db_host		= 'mysql5.000webhost.com';
$db_user		= 'a7442629_shank';
$db_pass		= 'Harryron16';
$db_database	= 'a7442629_online'; 

/* End config */


$link = @mysql_connect($db_host,$db_user,$db_pass) or die('Unable to establish a DB connection');

mysql_set_charset('utf8');
mysql_select_db($db_database,$link);

?>