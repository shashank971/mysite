<?php

require "connect.php";
require "functions.php";

// We don't want web bots accessing this page:
if(is_bot()) die();

// Selecting the top 15 countries with the most visitors:
$result = mysql_query("	SELECT countryCode,country, COUNT(*) AS total
						FROM tz_who_is_online
						GROUP BY countryCode
						ORDER BY total DESC
						LIMIT 15");

while($row=mysql_fetch_assoc($result))
{
	echo '
	<div class="geoRow">
		<div class="flag"><img src="who-is-online/img/famfamfam-countryflags/'.strtolower($row['countryCode']).'.gif" width="16" height="11" /></div>
		<div class="country" title="'.htmlspecialchars($row['country']).'">'.$row['country'].'</div>
		<div class="people">'.$row['total'].'</div>
	</div>
	';
}

?>