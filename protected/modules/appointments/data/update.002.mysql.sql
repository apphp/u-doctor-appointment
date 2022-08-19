UPDATE `<DB_PREFIX>modules` SET `version` = '0.0.2', `updated_at` = '<CURRENT_DATETIME>' WHERE `code` = 'appointments';

DROP TABLE IF EXISTS `<DB_PREFIX>appt_working_hours`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_working_hours` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `week_day` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `start_time` varchar(5) NOT NULL DEFAULT '00:00',
  `end_time` varchar(5) NOT NULL DEFAULT '00:00',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

INSERT INTO `<DB_PREFIX>appt_working_hours` (`id`, `week_day`, `start_time`, `end_time`, `is_active`) VALUES
(1, 1, '09:00', '21:00', 1),
(2, 2, '09:00', '21:00', 1),
(3, 3, '09:00', '21:00', 1),
(4, 4, '09:00', '21:00', 1),
(5, 5, '09:00', '21:00', 1),
(6, 6, '09:00', '21:00', 1),
(7, 7, '00:00', '00:00', 0);
