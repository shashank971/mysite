<?php

require "connect.php";
require "functions.php";

// We don't want web bots scewing our stats:
if(is_bot()) die();



$stringIp = $_SERVER['REMOTE_ADDR'];
$intIp = ip2long($stringIp);

// Checking wheter the visitor is already marked as being online:
$inDB = mysql_query("SELECT 1 FROM tz_who_is_online WHERE ip=".$intIp);

if(!mysql_num_rows($inDB))
{
	// This user is not in the database, so we must fetch
	// the geoip data and insert it into the online table:
	
	if($_COOKIE['geoData'])
	{
		// A "geoData" cookie has been previously set by the script, so we will use it
		
		// Always escape any user input, including cookies:
		list($city,$countryName,$countryAbbrev) = explode('|',mysql_real_escape_string(strip_tags($_COOKIE['geoData'])));
	}
	else
	{
		// Making an API call to Hostip:
		
		$xml = file_get_contents('http://api.hostip.info/?ip='.$stringIp);
		
		$city = get_tag('gml:name',$xml);
		$city = $city[1];
		
		$countryName = get_tag('countryName',$xml);
		$countryName = $countryName[0];
		
		$countryAbbrev = get_tag('countryAbbrev',$xml);
		$countryAbbrev = $countryAbbrev[0];
		
		// Setting a cookie with the data, which is set to expire in a month:
		setcookie('geoData',$city.'|'.$countryName.'|'.$countryAbbrev, time()+60*60*24*30,'/');
	}
	
	$countryName = str_replace('(Unknown Country?)','UNKNOWN',$countryName);
	
	// In case the Hostip API fails:
		
	if (!$countryName)
	{
		$countryName='UNKNOWN';
		$countryAbbrev='XX';
		$city='(Unknown City?)';
	}
	
	mysql_query("	INSERT INTO tz_who_is_online (ip,city,country,countrycode)
					VALUES(".$intIp.",'".$city."','".$countryName."','".$countryAbbrev."')");
}
else
{
	// If the visitor is already online, just update the dt value of the row:
	mysql_query("UPDATE tz_who_is_online SET dt=NOW() WHERE ip=".$intIp);
}

// Removing entries not updated in the last 10 minutes:
mysql_query("DELETE FROM tz_who_is_online WHERE dt<SUBTIME(NOW(),'0 0:10:0')");

// Counting all the online visitors:
list($totalOnline) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM tz_who_is_online"));

// Outputting the number as plain text:
echo $totalOnline;

?>