<?php
/*
VideoWhisper Live Streaming Integration (for Joomla):

Copyright (C) 2009  VideoWhisper.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

header('Cache-Control: no-cache');
header('Pragma: no-cache');
setcookie("videowhisper_com",time(),time()+3600);

jimport( 'joomla.application.component.model' );

class VwModelFlash extends JModelLegacy
{


function getOutput($task)
	{
  	$params = &JComponentHelper::getParams( 'com_videowhisperlivestreaming' );
	
	$rtmp_server = urlencode($params->get( 'rtmp' ));
	$rtmfp_server = urlencode($params->get( 'rtmfp_server' ));
	$serverAMF = urlencode($params->get( 'serverAMF' ));

	$supportRTMP =  $params->get( 'supportRTMP');
	$alwaysRTMP =  $params->get( 'alwaysRTMP');
	$supportP2P =  $params->get( 'supportP2P');
	$disableUploadDetection =  $params->get( 'disableUploadDetection');

	$showTimerBroadcast = $params->get( 'showTimerBroadcast');
	$showTimerWatch = $params->get( 'showTimerWatch');
	$showTimerVideo = $params->get( 'showTimerVideo');
		
	$welcome_broadcasters=urlencode($params->get( 'welcome_broadcasters'));
	$bandwidth=$params->get( 'bandwidth');
	$maxbandwidth=$params->get( 'maxbandwidth');
	$noEmbeds=0; $embed_codes = $params->get( 'embed_codes' ); if (!$embed_codes) $noEmbeds=1;
	$configureSource =  $params->get( 'configureSource');

	$videoCodec = $params->get( 'videoCodec');
	$codecProfile = $params->get( 'codecProfile');
	$codecLevel = $params->get( 'codecLevel');
	$soundCodec = $params->get( 'soundCodec');
	$soundQuality = $params->get( 'soundQuality');
	$micRate = $params->get( 'micRate');
	
	$generateSnapshots =  urlencode($params->get( 'generateSnapshots' ));
	$snapshotsTime =  urlencode($params->get( 'snapshotsTime' ));
	$generateThumbs =  urlencode($params->get( 'generateThumbs' ));
	
	$visitors_watch=urlencode($params->get( 'visitors_watch'));
		
	$welcome_watchers=urlencode($params->get( 'welcome_watchers'));
	$disableChat=0; $watch_chat = urlencode($params->get( 'watch_chat')); if (!$watch_chat) $disableChat=1;
	$disableUsers=0; $watch_list = urlencode($params->get( 'watch_list')); if (!$watch_list) $disableUsers=1;
	
	$ws_ads = urlencode($params->get( 'ws_ads'));
	
	$filterRegex = urlencode($params->get( 'filter_rules'));
	$filterReplace = urlencode($params->get( 'filter_replace'));
	
//	$visitors_watch=1;
	$cpath="components/com_videowhisperlivestreaming/";
	$path="components/com_videowhisperlivestreaming/";
	
	switch ($task)
	{
	case "vc_login":
	$message="";
	$user =& JFactory::getUser();
	if (!$user->get('id')) $loggedin=0; else $loggedin=1;
	if (!$loggedin) $message=urlencode("<a href=\"/\">Please login to your member account first!</a>");
	$username=$user->get('name');
	$usertype=$user->usertype;
	$username=preg_replace("/[^0-9a-zA-Z]/","-",$username);
	
	if (!$rtmp_server || $rtmp_server=="rtmp://your_server/videowhisper" || $rtmp_server=="rtmp://youserver.com/videowhisper")
	{
		$loggedin=0;
		$message=urlencode("Setup RTMP server from component parameters in administrator area!");
		$rtmp_server="rtmp://your_server/videowhisper";
	}


	$base=JURI::root().$cpath;

	if (!$noEmbeds)
	{
	$linkcode = JURI::root() . "index.php?option=com_videowhisperlivestreaming&view=channel&n=".urlencode($username);
	$imagecode = $base."snapshots/".urlencode($username).".jpg";
	
	$swfurl = $base."live_watch.swf?n=".urlencode($username);
	$swfurl .= "&prefix=" . urlencode(JURI::root() . "index.php?option=com_videowhisperlivestreaming&view=flash&format=raw&videowhisper=1&task=");
	$swfurl .= "&extension=_none_";
	$swfurl .= "&ws_res=" . urlencode(JURI::root() . "components/com_videowhisperlivestreaming/");
	
	$swfurl2 = $base."live_video.swf?n=".urlencode($username);
	$swfurl2 .= "&prefix=" . urlencode(JURI::root() . "index.php?option=com_videowhisperlivestreaming&view=flash&format=raw&videowhisper=1&task=");
	$swfurl2 .= "&extension=_none_";
	$swfurl2 .= "&ws_res=" . urlencode(JURI::root() . "components/com_videowhisperlivestreaming/");
	
	$bgcolor="#333333";
	$baseurl="";
	$wmode="transparent";

	$embedcode =<<<EMBEDEND
<object width="100%" height="350" type="application/x-shockwave-flash" data="$swfurl">
<param name="movie" value="$swfurl" /><param name="bgcolor" value="$bgcolor" /><param name="salign" value="lt" /><param name="scale" value="noscale" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /> <param name="wmode" value="=wmode" /> 
</object>
EMBEDEND;

	$embedvcode =<<<EMBEDEND2
<object width="320" height="240" type="application/x-shockwave-flash" data="$swfurl2">
<param name="movie" value="$swfurl2" /><param name="bgcolor" value="$bgcolor" /><param name="salign" value="lt" /><param name="scale" value="noscale" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" />  <param name="wmode" value="$wmode" /> 
</object>
EMBEDEND2;
	}
		$linkcode=urlencode($linkcode);
		$embedcode=urlencode($embedcode);
		$embedvcode=urlencode($embedvcode);

		return "firstParameter=fix&server=$rtmp_server&serverAMF=$serverAMF&tokenKey=VideoWhisper&serverProxy=best&serverRTMFP=$rtmfp_server&p2pGroup=VideoWhisper&enableRTMP=$supportRTMP&disableBandwidthDetection=$disableUploadDetection&enableP2P=0&supportRTMP=$supportRTMP&supportP2P=$supportP2P&alwaysRTMP=$alwaysRTMP&alwaysP2P=0&room=$username&welcome=$welcome_broadcasters&username=$username&webserver=&msg=$message&loggedin=$loggedin&imagecode=$imagecode&linkcode=$linkcode&embedcode=$embedcode&embedvcode=$embedvcode&showTimer=$showTimerBroadcast&showCredit=$showTimerBroadcast&disconnectOnTimeout=0&camWidth=320&camHeight=240&camFPS=15&videoCodec=$videoCodec&codecProfile=$codecProfile&codecLevel=$codecLevel&soundCodec=$soundCodec&soundQuality=$soundQuality&micRate=$micRate&camBandwidth=$bandwidth&showCamSettings=1&advancedCamSettings=1&camMaxBandwidth=$maxbandwidth&bufferLive=1&bufferFull=1&configureSource=$configureSource&generateSnapshots=$generateSnapshots&snapshotsTime=$snapshotsTime&noEmbeds=$noEmbeds&loadstatus=1";
	break;
	case "vv_login":
	$showTimerWatch = $showTimerVideo;
	case "vs_login":
	$message="";
	$user =& JFactory::getUser();
	if (!$user->get('id')) $loggedin=0; else $loggedin=1;
	if (!$loggedin) $message=urlencode("<a href=\"/\">Please login to your member account first!</a>");
	if ($loggedin) $username=$user->get('name');
	$visitor=0;


  if ($username)
    {
  //look for live session with same username to prevent session name replication
  $db = &JFactory::getDBO();
  $query = "SELECT * FROM #__vw_sessions where status='1' and type='1' and username='$username'";
  $db->setQuery($query);
	$room = $db->loadObject();
	
	if ($room)   $username.="_".base_convert((time()-1224350000).rand(0,100),10,36); //has an active channel with that name
  
    }



	if ($visitors_watch)
	{
		if (!$username)
		{
			$username="VW".base_convert((time()-1224350000).rand(0,100),10,36);
			$visitor=1;
		}
		$loggedin=1;
	}

	return "firstParameter=fix&server=$rtmp_server&serverAMF=$serverAMF&tokenKey=VideoWhisper&serverProxy=best&serverRTMFP=$rtmfp_server&p2pGroup=VideoWhisper&enableRTMP=$supportRTMP&disableBandwidthDetection=$disableUploadDetection&enableP2P=0&supportRTMP=$supportRTMP&supportP2P=$supportP2P&alwaysRTMP=$alwaysRTMP&alwaysP2P=0&welcome=$welcome_watchers&username=$username&msg=$message&visitor=$visitor&loggedin=$loggedin&disableChat=$disableChat&disableUsers=$disableUsers&filterRegex=$filterRegex&filterReplace=$filterReplace&showTimer=$showTimerWatch&showCredit=$showTimerWatch&adServer=$ws_ads&fillWindow=1&loadstatus=1";
	break;
	
	case "vc_chatlog":
	include($path . "vc_chatlog.php");  
	break;
	
	case "vw_extchat":
	include($path . "vw_extchat.php");  
	break;
	
	case "ads":
	include($path . "ads.php");  
	break;
	
	case "lb_logout":
	header("Location: index.php?option=com_videowhisperlivestreaming&view=logout&room=" . urlencode(JRequest::getVar( 'room' )) . "&message=" . urlencode(JRequest::getVar( 'message' )));
	break;
	
	case "lb_status":

	$db =& JFactory::getDBO();

	$s = JRequest::getVar( 's' );
	$u = JRequest::getVar( 'u' );
	$r = JRequest::getVar( 'r' );
	$m = JRequest::getVar( 'm' );

	$currentTime = JRequest::getVar( 'ct' );
	$lastTime = JRequest::getVar( 'lt' );

	$maximumSessionTime=0; //(miliseconds, 900000ms=15 minutes) change for limiting session time

	$disconnect=""; //anything else than "" will disconnect with that message

	$ztime=time();

	$sql = "SELECT * FROM #__vw_sessions where session='$s' and status='1'";
	$db->setQuery( $sql );
	$session = &$db->loadObject();
	if (!$session)
	{
	$user =& JFactory::getUser();
	$user_id=$user->get('id');
	
	$sql="INSERT INTO `#__vw_sessions` ( `user_id`, `session`, `username`, `room`, `message`, `sdate`, `edate`, `status`, `type`) VALUES ('$user_id', '$s', '$u', '$r', '$m', $ztime, $ztime, 1, 1)";
    $db->setQuery( $sql );
  	$db->query();
	}
	else
	{
	$sql="UPDATE `#__vw_sessions` set edate=$ztime, room='$r', username='$u', message='$m' where session='$s' and status='1'";
    $db->setQuery( $sql );
  	$db->query();
	}

	$exptime=$ztime-30;
	$sql="DELETE FROM `#__vw_sessions` WHERE edate < $exptime";
    $db->setQuery( $sql );
  	$db->query();
	return "timeTotal=$maximumSessionTime&timeUsed=$currentTime&lastTime=$currentTime&disconnect=$disconnect&loadstatus=1";
	break;
	case "v_status":
	$s = JRequest::getVar( 's' );
	$u = JRequest::getVar( 'u' );
	$r = JRequest::getVar( 'r' );
	$currentTime = JRequest::getVar( 'ct' );
	$lastTime = JRequest::getVar( 'lt' );

	$maximumSessionTime=0; //(miliseconds, 900000ms=15 minutes) change for limiting session time
	$disconnect=""; //anything else than "" will disconnect with that message

	return "firstParameter=fix&timeTotal=$maximumSessionTime&timeUsed=$currentTime&lastTime=$currentTime&disconnect=$disconnect&loadstatus=1";
	break;
	case "vw_snapshots":
	$received=0;
	
	$GLOBALS["HTTP_RAW_POST_DATA"]= file_get_contents( 'php://input' );
	
	if (isset($GLOBALS["HTTP_RAW_POST_DATA"]))
{
	  $stream=JRequest::getVar('name');

	// get bytearray
	$jpg = $GLOBALS["HTTP_RAW_POST_DATA"];

  // save file
  $filepath = $cpath."snapshots/";
  @chmod($filepath, 0777);
  $fp=fopen($filepath . "$stream.jpg","w");
  if ($fp)
  {
    fwrite($fp,$jpg);
    fclose($fp);
  }
  	$received=1;
	
	if ($generateThumbs)
	{
		 //generate thumbnail
		 $source = @ImageCreateFromJPEG($filepath . $stream . ".jpg");
		 $destination = @ImageCreateTrueColor(128,96);
		 @imagecopyresized ($destination, $source, 0, 0, 0, 0, 128, 96,  @imagesx($source), @imagesy($source));
		 @imagejpeg($destination,$filepath . $stream . "_thumb.jpg",85);
	}
	
}
	return "firstParameter=fix&received=$received&loadstatus=1";
	break;
	
	case "translation":
	include($path . "translation.php");  
	break;
	
	default:
	return "error=$task-NotSupported";
	break;
	}

    }
}