<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

 if (!file_exists('components/com_videowhisperlivestreaming'))
 {
	 echo "This module requires VideoWhisper <a href='http://www.videowhisper.com/?p=Joomla+Live+Streaming'>Live Streaming component</a>.";
 }
 

  $cparams = &JComponentHelper::getParams( 'com_videowhisperlivestreaming' );
  $VideoWhisper = $cparams->get( 'VideoWhisper' );
  

function vwls_layout_tab_start($table_attributes="width=100% cellpadding=1 cellspacing=1")
{
return "<table $table_attributes border=0 align=center>\r<tr valign=top>";
}

function vwls_echo_tab_cell_start(&$n,$rn)
{
if ($n&&(!($n%$rn))) echo "</tr>\r<tr valign=top>";
echo "\r<td>";
}

function vwls_echo_tab_cell_end(&$n,$rn)
{
echo "\r</td>";
$n++;
}

function vwls_layout_tab_end($n,$rn)
{
$res="";
if ($n&&($n%$rn)) $res.="\r<td colspan=".($rn-$n%$rn)."></td>";
$res.="</tr></table>";
return $res;
}
?>
  <div>
	<?php
	if (!$liveStreams) echo "No live streams.";
	$imageBase="components/com_videowhisperlivestreaming/snapshots/";
	?>
    <?php  
	
	$n=0;
	if ($pColumns) $rn=$pColumns;
	else $rn = 1;
	echo vwls_layout_tab_start();
	
	foreach ($liveStreams as $liveStream) {
	  
	  vwls_echo_tab_cell_start($n,$rn);
	  
	  $userInfo =  modLiveHelper::getUserInfo($liveStream->user_id);
	  $imageURL= $imageBase . $liveStream->session."_thumb.jpg";
	  if (!file_exists($imageURL)) $imageURL = JURI::root() . $imageBase . "no_video_thumb.png";
	  else $imageURL=JURI::root() . $imageURL;
	  $channelURL= JRoute::_('index.php?option=com_videowhisperlivestreaming&view=channel&n=' . $liveStream->room );
	  ?>
        <a  href="<?php echo $channelURL; ?>"> <span> <img height="96" width="128" title="<?php echo $liveStream->room ." : " .$liveStream->message;?>" alt="<?php echo $liveStream->room ." : " .$liveStream->message;?>" src="<?php echo $imageURL; ?>"/> </span>
        </a>
		<?php echo "<BR>Channel: <B>" . $liveStream->room . "</B>";?>
		<?php echo "<BR>Broadcast time: " . ceil((time()-$liveStream->sdate)/60) . " minutes.";?>
        <?php echo $liveStream->message?"<BR>Status: " . $liveStream->message:"";?>
    <?

	   vwls_echo_tab_cell_end($n,$rn);
	}
	
	 echo vwls_layout_tab_end($n,$rn);
	 
	 	$state = 'block' ;
		if (!$VideoWhisper) $state = 'none';	
		echo '<div id="VideoWhisper" style="display: ' . $state . ';">Powered by VideoWhisper <a href="http://www.videowhisper.com/?p=Joomla+Live+Streaming">Live Video Streaming Software</a>.</div>';
	?>
  </div>
