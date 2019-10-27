<?php
/* Sample local ads serving script ; Or use http://adinchat.com compatible ads server to setup http://adinchat.com/v/your-campaign-id

POST Variables:
u=Username
s=Session, usually same as username
r=Room
ct=session time (in milliseconds)
lt=last session time received (from web status script)

*/

	$username=JRequest::getVar( 'u' );
	$session=JRequest::getVar( 's' );
	$room=JRequest::getVar( 'r' );
	
	$currentTime==JRequest::getVar( 'ct' );
	$lastTime=JRequest::getVar( 'lt' );

$ztime=time();

//fill ad to show
$ad="<B>Sample Ad</B><BR>Edit ads in ads.php. Also edit vs_login.php to setup adsInterval in milliseconds (0 to disable ad calls), adsTimeout to setup time in milliseconds until first ad is shown.  Also see <a href=\"http://www.adinchat.com\" target=\"_blank\"><U><B>AD in Chat</B></U></a> compatible ad management server.";

?>x=1&ad=<?=urlencode($ad)?>&loadstatus=1