<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

$spath = "components/com_videowhisperlivestreaming/snapshots/";
$channelurl = JURI::root() . "index.php?option=com_videowhisperlivestreaming&view=channel&n=";
$imageurl0 = JURI::root() . $spath;

function layout_tab_start($table_attributes="width=100% cellpadding=0 cellspacing=0")
{
return "<table $table_attributes border=0 align=center>\r<tr valign=top>";
}

function layout_tab_cell(&$n,$rn,$cell)
{
$res="";
if ($n&&(!($n%$rn))) $res.="</tr>\r<tr valign=top>";
$res.="\r<td>$cell</td>";
$n++;
return $res;
}

function layout_tab_end($n,$rn)
{
$res="";
if ($n&&($n%$rn)) $res.="\r<td colspan=".($rn-$n%$rn)."></td>";
$res.="</tr></table>";
return $res;
}
	

		$rowitems=3;
        $db = &JFactory::getDBO();

		$exptime=time()-30;
		$sql="DELETE FROM `#__vw_sessions` WHERE edate < $exptime";
		$db->setQuery( $sql );
		$db->query();		
		
        // get a list of all users
        $query = "SELECT * FROM #__vw_sessions where status='1' and type='1'";
        $db->setQuery($query);
		echo layout_tab_start();
		clearstatcache(); 
		
        $items = ($items = $db->loadObjectList())?$items:array();
		if (count($items)>0)
		{
		$no=0;
		foreach ($items as $item) 
		{
			$imageurl = $spath . $item->room.".jpg";
		
			if (!file_exists($imageurl)) $imageurl = $imageurl0 . "no_video.png";
			echo layout_tab_cell($no,$rowitems, "<a href='".$channelurl.urlencode($item->room)."'><IMG width='240' SRC='" .  JURI::root() . $imageurl . "' ALT='".$item->room.($item->message?": ".$item->message:"")."'>");
		}
		}else layout_tab_cell($no,$rowitems,"No video broadcasters online.");
	
		echo layout_tab_end($no,$rowitems);
		
		echo "<BR><a href='index.php?option=com_videowhisperlivestreaming&view=vw&format=raw'>Live Broadcast Yourself</a>...<BR>";
			
		$params = &JComponentHelper::getParams( 'com_videowhisperlivestreaming' );
		$VideoWhisper = $params->get( 'VideoWhisper' );
		$state = 'block' ;
		if (!$VideoWhisper) $state = 'none';	
		$code = '<div id="VideoWhisper" style="display: ' . $state . ';">Powered by VideoWhisper <a href="http://www.videowhisper.com/?p=Joomla+Live+Streaming">Live Video Streaming Software</a>.</div>';
		
		echo $code;
?>
