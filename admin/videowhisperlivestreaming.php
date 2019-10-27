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
JToolBarHelper::preferences( 'com_videowhisperlivestreaming' );


$params = &JComponentHelper::getParams( 'com_videowhisperlivestreaming' );	

$rtmp_server = urlencode($params->get( 'rtmp' ));

$sot=ini_get('short_open_tag');

if ($sot!='1') echo "<B>Warning: short_open_tag seems disabled and is required by this component for passing variables to flash. Please <a target='_blank' href='http://php.net/manual/en/ini.core.php#ini.short-open-tag'>enable from php configuration or upgrade PHP</a>.</B>";
	  
if ($rtmp_server == "rtmp://youserver.com/videowhisper") echo "<b>Warning: RTMP address is not configured. See <a href='http://www.videowhisper.com/?p=Requirements'>software requirements</a>!</b>";
?>
<p>Configure component parameters from options icon in tool bar above and also install and enable modules as needed.</p>
<p>

  <?php
	echo "<BR><IMG SRC='" .JURI::root() . "components/com_videowhisperlivestreaming/templates/live/i_webcam.png' align='absmiddle' border='0'> <a target=_vwbroadcast href='" . JURI::root() . "index.php?option=com_videowhisperlivestreaming&view=vw&format=raw'>Live Broadcast</a>";
?><BR>(frontend <a href='<?=JURI::root()?>' target=_blank>login</a> required, user name = channel name)
</p>
<p>Online Channels:
<?php
	$linkcode = JURI::root(). "index.php?option=com_videowhisperlivestreaming&view=channel&n=";
	
        $db = &JFactory::getDBO();

		$exptime=time()-30;
		$sql="DELETE FROM `#__vw_sessions` WHERE edate < $exptime";
		$db->setQuery( $sql );
		$db->query();		
		
        // get a list of all users
        $query = "SELECT * FROM #__vw_sessions where status='1' and type='1'  ORDER BY edate DESC LIMIT 0, 100";
        $db->setQuery($query);
        $items = ($items = $db->loadObjectList())?$items:array();
		echo "<ul>";
		if ($items)	foreach ($items as $item) echo "<li><a href='$linkcode".urlencode($item->room)."'><B>".$item->room."</B>".($item->message?": ".$item->message:"") ."</a></li>";
		else echo "<li>No video broadcasters online.</li>";
		echo "</ul>";
	?>
</p>

 <p><h3>Documentation</h3> For more information on available modules, installation, configuration, administration, customization, updates, feedback see:<br />
  <a href="http://www.videowhisper.com/?p=Joomla+Live+Streaming">http://www.videowhisper.com/?p=Joomla+Live+Streaming</a></p>


<p>
<h3>FFMPEG</h3>
<?php
	$ffmpegPath = $params->get( 'ffmpegPath');
	
$cmd =$ffmpegPath . ' -codecs';
exec($cmd, $output, $returnvalue); 
if ($returnvalue == 127)  echo "not detected: $cmd"; else echo "detected";

//detect codecs
if ($output) if (count($output)) 
foreach (array('h264','faac','speex', 'nellymoser') as $cod) 
{
$det=0; $outd="";
echo "<BR>$cod codec: ";
foreach ($output as $outp) if (strstr($outp,$cod)) { $det=1; $outd=$outp; };
if ($det) echo "detected ($outd)"; else echo "missing: please configure and install ffmpeg with $cod";
}
?>
</p>
<h3>Logs and Snapshots</h3>
<?
//uploads management

function deltree($path) {

	  if (is_dir($path)) {

		  if (version_compare(PHP_VERSION, '5.0.0') < 0) {

			$entries = array();

		  if ($handle = opendir($path)) {

			while (false !== ($file = readdir($handle))) $entries[] = $file;



			closedir($handle);

		  }

		  } else {

			$entries = @scandir($path);

			if ($entries === false) $entries = array(); // just in case scandir fail...

		  }



		foreach ($entries as $entry) {

		  if ($entry != '.' && $entry != '..') {

			deltree($path.'/'.$entry);

		  }

		}



		return @rmdir($path);

	  } else {

		  return @unlink($path);

	  }

	}

	

	function cleanUp($dir)

	{

	if ($dir[strlen($dir)-1]=='/') $dir=substr($dir,0, -1);
	
	echo "<BR>Cleaning up $dir ...";

	$k=0;

	$handle=opendir($dir);

		while (($file = readdir($handle))!==false) 

		{

			if (($file != ".") && ($file != "..") && ($file != 'no_video.png') && ($file != 'index.html'))

			{

				if (is_dir("$dir/" . $file)) deltree($dir."/".$file);

				else @unlink("$dir/" . $file);

				$k++;

				

				if ($k%50==0)

				{

				echo " ."; 

				flush();

				}

			}

		}

	closedir($handle); 

	echo "<BR>Finished cleaning up $k items.<br>";

	}

	

	
function getDirectorySize($path)
{
  $totalsize = 0;
  $totalcount = 0;
  $dircount = 0;
  if ($handle = opendir ($path))
  {
    while (false !== ($file = readdir($handle)))
    {
      $nextpath = $path . '/' . $file;
      if ($file != '.' && $file != '..' && !is_link ($nextpath))
      {
        if (is_dir ($nextpath))
        {
          $dircount++;
          $result = getDirectorySize($nextpath);
          $totalsize += $result['size'];
          $totalcount += $result['count'];
          $dircount += $result['dircount'];
        }
        elseif (is_file ($nextpath))
        {
          $totalsize += filesize ($nextpath);
          $totalcount++;
        }
      }
    }
  }
  closedir ($handle);
  $total['size'] = $totalsize;
  $total['count'] = $totalcount;
  $total['dircount'] = $dircount;
  return $total;
}

function sizeFormat($size)
{
	//echo $size; 
    if($size<1024)
    {
        return $size." bytes";
    }
    else if($size<(1024*1024))
    {
        $size=round($size/1024,2);
        return $size." KB";
    }
    else if($size<(1024*1024*1024))
    {
        $size=round($size/(1024*1024),2);
        return $size." MB";
    }
    else
    {
        $size=round($size/(1024*1024*1024),2);
        return $size." GB";
    }

}  

$upload_path = "../components/com_videowhisperlivestreaming/uploads/";
$snapshot_path = "../components/com_videowhisperlivestreaming/snapshots/";


if (JRequest::getVar( 'task' ) =='cleauploads') cleanUp($upload_path );
if (JRequest::getVar( 'task' ) =='cleansnapshots') cleanUp($snapshot_path );

$upload_c = getDirectorySize($upload_path); 
$upload_size = sizeFormat($upload_c['size']);
$snapshot_c = getDirectorySize($snapshot_path); 
$snapshot_size = sizeFormat($snapshot_c['size']);
?>

Logs: <?php echo "$upload_size ($upload_c[count] files, $upload_c[dircount] folders)"; ?> <a target='_blank' href="<?php echo $upload_path ?>">Locate</a> | <a href='index.php?option=com_videowhisperlivestreaming&task=cleauploads' onclick="return confirm('Really delete all room logs ?');">Delete All</a>

<br>Snapshots: <?php echo "$snapshot_size ($snapshot_c[count] files, $snapshot_c[dircount] folders)"; ?> <a target='_blank' href="<?php echo $snapshot_path ?>">Locate</a> | <a href='index.php?option=com_videowhisperlivestreaming&task=cleansnapshots' onclick="return confirm('Really delete all snapshots?');">Delete All</a>

</p>

 
