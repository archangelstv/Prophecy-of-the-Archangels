DROP TABLE IF EXISTS `#__vw_sessions`;
CREATE TABLE `#__vw_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` varchar(64) NOT NULL,
  `session` varchar(64) NOT NULL,
  `username` varchar(64) NOT NULL,
  `room` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `sdate` int(11) NOT NULL,
  `edate` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `session` (`session`),
  KEY `status` (`status`),
  KEY `type` (`type`),
  KEY `room` (`room`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Video Whisper: Live Sessions - 2009@videowhisper.com' AUTO_INCREMENT=1 ;