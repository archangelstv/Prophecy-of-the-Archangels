<?php

	$username=JRequest::getVar( 'u' );
	$session=JRequest::getVar( 's' );
	$room=JRequest::getVar( 'r' );
	$private=JRequest::getVar( 'private' );
	$message=JRequest::getVar( 'msg' );
	$time=JRequest::getVar( 'msgtime' );
	
//do not allow uploads to other folders
include_once("incsan.php");
sanV($room);
sanV($private);
sanV($session);
if (!$room) exit;

//generate same private room folder for both users
if ($private) 
{
	if ($private>$session) $proom=$session ."_". $private; else $proom=$private ."_". $session;
}

$dir=$path . "uploads";
if (!file_exists($dir)) mkdir($dir);
@chmod($dir, 0777);
$dir.="/$room";
if (!file_exists($dir)) mkdir($dir);
@chmod($dir, 0777);
if ($proom) $dir.="/$proom";
if (!file_exists($dir)) mkdir($dir);
@chmod($dir, 0777);

$day=date("y-M-j",time());

$dfile = fopen($dir."/Log$day.html","a");
fputs($dfile,$message."<BR>");
fclose($dfile);
?>loadstatus=1