<?php
defined( '_JEXEC' ) or die( 'Restricted access' );


  	$params = &JComponentHelper::getParams( 'com_videowhisperlivestreaming' );

	

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Video Whisper Live Broadcast</title>
</head>
<body bgcolor="#333333" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_videowhisperlivestreaming/flash_detect_min.js"> </script>
	<script type="text/javascript">
	
	var updateWarning = false;

	if(FlashDetect.installed)
	{
	
	if(!FlashDetect.versionAtLeast(10, 2))
	{
		alert("Flash was detected but is too old to run this application. Upgrade your Flash plugin to proceed!"); 
		updateWarning = true;
	}
	
	}
	else
	{
		alert("Flash was not detected in your browser: Flash plugin is required to use this application!"); 
		updateWarning = true;
	}
	
	if (updateWarning)	document.write("<B class=warning>Update to latest flash player: <a href=\"http://get.adobe.com/flashplayer/\" target=\"_blank\">http://get.adobe.com/flashplayer/</a> !</B>");
	</script>
		
<?php
$user =& JFactory::getUser();
$username=$user->get('name');
$username = preg_replace("/[^0-9a-zA-Z]/","-",$username);
$stream = urlencode($username);

$swfurl = JURI::base() . "components/com_videowhisperlivestreaming/live_broadcast.swf?room=" . $stream;
$swfurl .= "&prefix=" . urlencode(JURI::base() . "index.php?option=com_videowhisperlivestreaming&view=flash&format=raw&videowhisper=1&task=");
$swfurl .= "&extension=_none_";
$swfurl .= "&ws_res=" . urlencode(JURI::base() . "components/com_videowhisperlivestreaming/");

$bgcolor="#333333";
$baseurl = JURI::base() . "components/com_videowhisperlivestreaming/";
$wmode="transparent";


?>

<object id="videowhisper_chat" width="100%" height="100%" type="application/x-shockwave-flash" data="<?=$swfurl?>">
<param name="movie" value="<?=$swfurl?>" /><param name="bgcolor" value="<?=$bgcolor?>" /><param name="salign" value="lt" /><param name="scale" value="noscale" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /> <param name="base" value="<?=$baseurl?>" /> <param name="wmode" value="<?=$wmode?>" /> 
</object>

<noscript>
<p align=center>Powered by <a href="http://www.videowhisper.com/"><strong>VideoWhisper</strong></a> <a href="http://www.videowhisper.com/?p=Joomla+Live+Streaming">Live Video Streaming Software</a>.
</p>
<p align="center"><strong>This content requires the Adobe Flash Player:
  	<a href="http://get.adobe.com/flashplayer/">Get Flash</a></strong>!</p>
</noscript>

<?php

	function inList($groupArray, $groupList)
		{
		$groups = explode(',', $groupList);
		foreach ($groups as $group) 
		if ( in_array(trim($group), $groupArray) ) return 1;
		
		return 0;
		}

		//classify user
		$user_id  = $user->get('id');
		
		if ($user_id) //loggedin
		$groups = isset($user->groups) ? $user->groups : array($user->get('usertype')); //J1.5+
		else $groups = array();
			
		$groups[] = 'Public'; //all users have public (visitor) rights or higher
		//$groups[] = $user_id ;
		$groups[] = $user->get('username') ;	
		$groups[] = $user->get('email') ;
		
	//retrieve group names if missing (latest J) 
    $db = JFactory::getDbo();
	if (is_array($user->groups)) foreach ($user->groups as $groupId => $value) if ($groupId == $value)
	{
    $db->setQuery(
        'SELECT `title`' .
        ' FROM `#__usergroups`' .
        ' WHERE `id` = '. (int) $groupId
    );
    $groups[] = $db->loadResult();
	}

$canTranscode =  $params->get( 'canTranscode');
$transcoder = inList($groups, $canTranscode);

if ($transcoder)
{

$transcodeUrl = JURI::base() . "index.php?option=com_videowhisperlivestreaming&view=transcode&format=raw&videowhisper=1&task=";


$htmlCode .= <<<HTMLCODE
<div id="vwinfo">
iOS Transcoding (iPhone/iPad)<BR>
<a href='#' class="button" id="transcoderon">ON</a>
<a href='#' class="button" id="transcoderoff">OFF</a>
<div id="result">A stream must be broadcast for transcoder to start.</div>
<p align="right">(<a href="javascript:void(0)" onClick="vwinfo.style.display='none';">hide</a>)</p>
</div>

<style type="text/css">
<!--

#vwinfo
{
	float: right;
	width: 25%;
	position: absolute;
	bottom: 10px;
	right: 10px;
	text-align:left;
	padding: 10px;
	margin: 10px;
	background-color: #666;
	border: 1px dotted #AAA;
	z-index: 1;

	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#999', endColorstr='#666'); /* for IE */
	background: -webkit-gradient(linear, left top, left bottom, from(#999), to(#666)); /* for webkit browsers */
	background: -moz-linear-gradient(top,  #999,  #666); /* for firefox 3.6+ */

	box-shadow: 2px 2px 2px #333;


	-moz-border-radius: 9px;
	border-radius: 9px;
}

#vwinfo > a {
	color: #F77;
	text-decoration: none;
}

#vwinfo > .button {
	-moz-box-shadow:inset 0px 1px 0px 0px #f5978e;
	-webkit-box-shadow:inset 0px 1px 0px 0px #f5978e;
	box-shadow:inset 0px 1px 0px 0px #f5978e;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #db4f48), color-stop(1, #944038) );
	background:-moz-linear-gradient( center top, #db4f48 5%, #944038 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#db4f48', endColorstr='#944038');
	background-color:#db4f48;
	border:1px solid #d02718;
	display:inline-block;
	color:#ffffff;
	font-family:Verdana;
	font-size:12px;
	font-weight:normal;
	font-style:normal;
	text-decoration:none;
	text-align:center;
	text-shadow:1px 1px 0px #810e05;
	padding: 5px;
	margin: 2px;
}
#vwinfo > .button:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #944038), color-stop(1, #db4f48) );
	background:-moz-linear-gradient( center top, #944038 5%, #db4f48 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#944038', endColorstr='#db4f48');
	background-color:#944038;
}

-->
</style>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript">
	$.ajaxSetup ({
		cache: false
	});
	var ajax_load = "Loading...";

	$("#transcoderon").click(function(){
		$("#result").html(ajax_load).load("${transcodeUrl}hls&stream=$stream");
	});

	$("#transcoderoff").click(function(){
	$("#result").html(ajax_load).load("${transcodeUrl}close&stream=$stream");
	});
</script>
HTMLCODE;

echo $htmlCode;
}
?>
</body>
</html>
