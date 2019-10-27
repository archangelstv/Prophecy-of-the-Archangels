<?php
/**
 * VideoWhisper Livestreams - Module for VideoWhisper.com Live Streaming
 *
 *
 * @author		VideoWhisper.com
 * @version		Version 1.0
 * @created		April 14 2010
 * @link		http://www.videowhisper.com
 
	<VideoWhisper Live Streams> 
    Copyright (C) <2009>  <VideoWhisper.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
    If you want more information, contact us from http://www.videowhisper.com  
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// j3.0
if(!defined('DS')){
	define('DS', '/');
}

// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );

// get a parameter from the module's configuration
$pCount = $params->get('count');
$pColumns = $params->get('columns');
 
// get the items to display from the helper
$liveStreams = modLiveHelper::getLiveStreams(0, $pCount);
require(JModuleHelper::getLayoutPath('mod_videowhisperlivestreams'));
