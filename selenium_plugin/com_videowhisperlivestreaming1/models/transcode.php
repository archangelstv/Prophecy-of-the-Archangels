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

class VwModelTranscode extends JModelLegacy
{


function getOutput($task)
	{
  	$params = &JComponentHelper::getParams( 'com_videowhisperlivestreaming' );
	
	$rtmp_server = $params->get( 'rtmp' );
	$rtmpTranscode = $params->get( 'rtmpTranscode' );

	$alwaysRTMP =  $params->get( 'alwaysRTMP');
	
	$ffmpegPath = $params->get( 'ffmpegPath');
	$httpstreamer = $params->get( 'httpstreamer');
	
	$stream = JRequest::getVar( 'stream' );
	$stream = preg_replace("/[^0-9a-zA-Z]/","-",$stream);
	
    if (!$stream) return "No stream name provided!";


	$htmlCode = '';
	
	switch ($task)
	{
           case 'hls':
           
		   		$user =& JFactory::getUser();
                if (!$user->id)  return "Not authorised: Login first!";


				if (!$alwaysRTMP) $htmlCode .= 'Warning: Because alwaysRTMP is disabled stream may not be available!<br>';

                $cmd = "ps aux | grep '/i_$stream -i rtmp'";
                exec($cmd, $output, $returnvalue);
                //var_dump($output);

                $transcoding = 0;

                foreach ($output as $line) if (strstr($line, "ffmpeg"))
                    {
                        $columns = preg_split('/\s+/',$line);
                        $htmlCode .= "Transcoder Already Active (".$columns[1]." CPU: ".$columns[2]." Mem: ".$columns[3].")";
                        $transcoding = 1;
                    }

                if (!$transcoding)
                {

                        $rtmpAddress = $rtmpTranscode;
                        $rtmpAddressView = $rtmp_server;
                        
                        $htmlCode .= "Starting transcoder for '$stream'... <BR>";
                        
                    $upath="components/com_videowhisperlivestreaming/uploads/";
                    
                    $log_file =  $upath . "$stream-transcode.txt";
                    
                    //-rtmp_pageurl \"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . "\" -rtmp_swfurl \"http://".$_SERVER['HTTP_HOST']."\"
                    
                    $cmd = $ffmpegPath . " -f flv \"" .
                        $rtmpAddress . "/i_". $stream . "\" -i \"" . $rtmpAddressView ."/". $stream . "\" >&$log_file & ";

                    //echo $cmd;
                    exec($cmd, $output, $returnvalue);
                    exec("echo '$cmd' >> $log_file.cmd", $output, $returnvalue);

                    $cmd = "ps aux | grep '/i_$stream -i rtmp'";
                    exec($cmd, $output, $returnvalue);
                    //var_dump($output);

                    foreach ($output as $line) if (strstr($line, "ffmpeg"))
                        {
                            $columns = preg_split('/\s+/',$line);
                            $htmlCode .= "Transcoder Started (".$columns[1].")<BR>";
                        }

                }

                $transcodeUrl = JURI::base() . "index.php?option=com_videowhisperlivestreaming&view=transcode&format=raw&videowhisper=1&task=";


                $htmlCode .= "<BR><a target='_blank' href='".$transcodeUrl . "html5&stream=$stream'>Preview</a> (open in Safari)";
                
                return $htmlCode;
                
                break;

          case 'close':
          
		   		$user =& JFactory::getUser();
                if (!$user->id)  return "Not authorised: Login first!";

                $cmd = "ps aux | grep '/i_$stream -i rtmp'";
                exec($cmd, $output, $returnvalue);
                //var_dump($output);

                $transcoding = 0;
                foreach ($output as $line) if (strstr($line, "ffmpeg"))
                    {
                        $columns = preg_split('/\s+/',$line);
                        $cmd = "kill -9 " . $columns[1];
                        exec($cmd, $output, $returnvalue);
                        $htmlCode .= "<BR>Closing ".$columns[1]." CPU: ".$columns[2]." Mem: ".$columns[3];
                        $transcoding = 1;
                    }

                if (!$transcoding)
                {
                    $htmlCode .= "Transcoder not found for '$stream'!";
                }
				
				return $htmlCode;
				
                break;

          case "html5";
          
            $streamURL = "${httpstreamer}i_$stream/playlist.m3u8";

            $thumbFilename = JURI::base() . "components/com_videowhisperlivestreaming/snapshots/" . $stream . ".jpg";
            


$htmlCode .= <<<HTMLCODE
<p>iOS live stream link (open with Safari or test with VLC): <a href="$streamURL">$stream Video</a></p>

<p>HTML5 live video embed below should be accessible <u>only in <B>Safari</B> browser</u> (PC or iOS):</p>
HTMLCODE;

$hlsEmbed = <<<HTMLCODE
<video id="videowhisper_hls_$stream" width="480px" height="360px" autobuffer autoplay controls poster="$thumbFilename">
 <source src="$streamURL" type='video/mp4'>
    <div class="fallback">
	    <p>You must have an HTML5 capable browser with HLS support (Ex. Safari) to open this live stream: $streamURL</p>
	</div>
</video>
HTMLCODE;

$htmlCode .= $hlsEmbed;

$htmlCode .= <<<HTMLCODE
<p> Due to HTTP based live streaming technology limitations, video can have 15s or more latency. Use a browser with flash support for faster interactions based on RTMP. </p>
<p>Most devices other than iOS, support regular flash playback for live streams.</p>

<style type="text/css">
<!--
BODY
{
	margin:0px;
	background: #333;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #EEE;
	padding: 20px;
}

a {
	color: #F77;
	text-decoration: none;
}
-->
</style>

<p>HTML5 Embed Code:</p>
HTMLCODE;

$htmlCode .= htmlspecialchars($hlsEmbed);
            return $htmlCode;
            
?>





<?php

                break;


	default:
	return "Transcode Error: ($task) Not Supported!";
	break;
	}

    }
}