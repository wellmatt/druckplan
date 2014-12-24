<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* SETTINGS START */

$videoPluginType = '1';
$vidWidth = '220';
$vidHeight = '165';
$applicationid = '';
$appAuthSecret = '';
$maxP = '10';
$quality = '90';
$winWidth = '650';
$winHeight = '365';
$connectUrl = 'dev.mein-druckplan.de';
$camWidth = '440';
$camHeight = '330';
$fps = '30';
$soundQuality = '7';
$zoomapplicationid = '';
$zoomappAuthSecret = '';
$email = 'email';


/* SETTINGS END */

/* videoPluginType Codes
0. Stratus
1. RED5/FMS (RTMP)
2. FMS (RTMFP)
3. CometChat Servers
4. AddLive
5. Zoom
*/

if ($videoPluginType == '0') {
	$camWidth = '435';
	$camHeight = '327';
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////