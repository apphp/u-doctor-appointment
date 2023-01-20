UPDATE `<DB_PREFIX>modules` SET `version` = '0.0.3', `updated_at` = '<CURRENT_DATETIME>' WHERE `code` = 'appointments';

ALTER TABLE  `<DB_PREFIX>appt_doctors` ADD `social_network_facebook` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_doctors` ADD `social_network_twitter` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_doctors` ADD `social_network_youtube` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_doctors` ADD `social_network_instagram` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE  `<DB_PREFIX>appt_patients` ADD `weight` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_patients` ADD `height` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_patients` ADD `blood_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_patients` ADD `allergies` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_patients` ADD `high_blood_presure` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_patients` ADD `low_blood_presure` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_patients` ADD `cardiac_rythm` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE  `<DB_PREFIX>appt_patients` ADD `smoking` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE  `<DB_PREFIX>appt_patients` ADD `tried_drugs` tinyint(1) unsigned NOT NULL DEFAULT '0';

ALTER TABLE  `<DB_PREFIX>appt_appointments` ADD `status_arrival` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not arrived, 1 - arrived';