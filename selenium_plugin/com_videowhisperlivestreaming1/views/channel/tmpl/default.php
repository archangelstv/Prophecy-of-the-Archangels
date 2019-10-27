<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

	$stream = JRequest::getVar( 'n');
	$stream = preg_replace("/[^0-9a-zA-Z]/","-",$stream);

    if (!$stream) 
    {
	  echo "No stream name provided!"; 
	  return;
    } 

$agent = $_SERVER['HTTP_USER_AGENT'];
if( strstr($agent,'iPhone') || strstr($agent,'iPod') || strstr($agent,'iPad'))
{
  	$params = &JComponentHelper::getParams( 'com_videowhisperlivestreaming' );
	
$httpstreamer = $params->get( 'httpstreamer');

//transcoding?
          $cmd = "ps aux | grep '/i_$stream -i rtmp'";
                exec($cmd, $output, $returnvalue);
                //var_dump($output);

                $transcoding = 0;

                foreach ($output as $line) if (strstr($line, "ffmpeg"))
                    {
                        $columns = preg_split('/\s+/',$line);
                        $transcoding = 1;
                        break;
                    }

if ($transcoding) $streamURL = "${httpstreamer}i_$stream/playlist.m3u8";
else $streamURL = "${httpstreamer}$stream/playlist.m3u8";

$thumbFilename = JURI::base() . "components/com_videowhisperlivestreaming/snapshots/" . $stream . ".jpg";

$hlsEmbed = <<<HTMLCODE
<video id="videowhisper_hls_$stream" width="480px" height="360px" autobuffer autoplay controls poster="$thumbFilename">
 <source src="$streamURL" type='video/mp4'>
    <div class="fallback">
	<p>You must have an HTML5 capable browser with HLS support (Ex. Safari) to open this live stream: $streamURL</p>
	</div>
</video>
<br>For full mode functionality and low latency, use a device/browser with Flash plugin support.
HTMLCODE;

echo $hlsEmbed;

}
else
{
$swfurl  = JURI::base() . "components/com_videowhisperlivestreaming/live_watch.swf?n=".urlencode($stream);
$swfurl .= "&prefix=" . urlencode(JURI::base() .  "index.php?option=com_videowhisperlivestreaming&view=flash&format=raw&videowhisper=1&task=");
$swfurl .= "&extension=_none_";
$swfurl .= "&ws_res=" . urlencode(JURI::base() . "components/com_videowhisperlivestreaming/");

$bgcolor="#333333";
$baseurl = JURI::base() . "components/com_videowhisperlivestreaming/";
$wmode="transparent";
?>
 
 <script type="text/javascript" src="components/com_videowhisper_consultation/flash_detect_min.js"> </script>
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
	
<div style="width=100%; height:350px; background-color: <?=$bgcolor?>; border: 6px solid #CCC" >

<object width="100%" height="100%" type="application/x-shockwave-flash" data="<?=$swfurl?>">
<param name="movie" value="<?=$swfurl?>" /><param name="bgcolor" value="<?=$bgcolor?>" /><param name="salign" value="lt" /><param name="scale" value="noscale" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /> <param name="base" value="<?=$baseurl?>" /> <param name="wmode" value="<?=$wmode?>" /> 
</object>

 </div>
<noscript>
<p align=center>Powered by <a href="http://www.videowhisper.com/"><strong>VideoWhisper</strong></a> <a href="http://www.videowhisper.com/?p=Joomla+Live+Streaming">Joomla Webcam Live Video Streaming</a>.
</p>
<p align="center"><strong>This content requires the Adobe Flash Player:
  	<a href="http://get.adobe.com/flashplayer/">Get Flash</a></strong>!</p>
</noscript>
<?php
}
?>
