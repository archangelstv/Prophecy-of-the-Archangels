<?php
/**
 * VideoWhisper Live Streams - Module for VideoWhisper.com Live Streaming
 *
 *
 * @author		VideoWhisper.com
 * @version		Version 1.0
 * @created		April 14 2010
 * @link		http://www.videowhisper.com
 
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

class modLiveHelper {

 public function getLiveStreams($limitStart=0, $limit=100)
    {
      $db = &JFactory::getDBO();

		$exptime=time()-30;
		$sql="DELETE FROM `#__vw_sessions` WHERE edate < $exptime";
		$db->setQuery( $sql );
		$db->query();		
		
        // get a list of all users
        $query = "SELECT * FROM #__vw_sessions where status='1' and type='1' ORDER BY edate DESC";
        $db->setQuery($query);
		
        return $db->loadObjectList();
    } 
	
	public function getUserInfo($id='')
     {
			$db 	= &JFactory::getDBO();
			$query	="SELECT id, name, username FROM #__users WHERE id=".$id;
			$db->setQuery($query);
			return $db->loadObjectList();
    } //end getUserInfo
	
}