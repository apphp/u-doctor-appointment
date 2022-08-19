
INSERT INTO `<DB_PREFIX>modules` (`id`, `code`, `class_code`, `name`, `description`, `version`, `icon`, `show_on_dashboard`, `show_in_menu`, `is_installed`, `is_system`, `is_active`, `installed_at`, `updated_at`, `has_test_data`, `sort_order`) VALUES 
(NULL, 'banners', 'Banners', 'Banners', 'This module allows you to show banners on the Frontend of the site', '0.0.3', 'icon.png', 0, 0, 1, 0, 1, '<CURRENT_DATETIME>', NULL, 0, (SELECT COUNT(m.id) + 1 FROM `<DB_PREFIX>modules` m WHERE m.is_system = 0));

INSERT INTO `<DB_PREFIX>module_settings` (`id`, `module_code`, `property_group`, `property_key`, `property_value`, `name`, `description`, `property_type`, `property_source`, `property_length`, `append_text`, `trigger_condition`, `is_required`) VALUES
(NULL, 'banners', '', 'shortcode', '{module:banners}', 'Shortcode', 'This shortcode allows you to display banners on the site pages (main page)', 'label', '', '', '', '', '0'),
(NULL, 'banners', '', 'rotation_delay', '9', 'Rotation Delay', 'Defines banners rotation delay in seconds', 'enum', '1,2,3,4,5,6,7,8,9,10,15,20,25,30,35,40,45,50,55,60', '', '', '', '0'),
(NULL, 'banners', '', 'viewer_type', 'all', 'Viewer Type', 'Defines what type of users can view this banners', 'enum', 'all,visitors only,registered only', '', '', '', '0');


INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'banners', 'banners', 'add', 'Add Banners', 'Add banners to the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'banners', 'banners', 'edit', 'Edit Banners', 'Edit banners on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'banners', 'banners', 'delete', 'Delete Banners', 'Delete banners from the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);


DROP TABLE IF EXISTS `<DB_PREFIX>banners`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>banners` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `image_file` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `image_file_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `link_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `<DB_PREFIX>banners` (`id`, `image_file`, `image_file_thumb`, `link_url`, `sort_order`, `is_active`) VALUES
(1, 'banner1.jpg', 'banner1_thumb.jpg', '', 0, 1),
(2, 'banner2.jpg', 'banner2_thumb.jpg', '', 1, 1),
(3, 'banner3.jpg', 'banner3_thumb.jpg', '', 2, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>banners_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>banners_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `banner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `banner_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `banner_text` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `banner_button` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `<DB_PREFIX>banners_translations` (`id`, `banner_id`, `language_code`, `banner_title`, `banner_text`, `banner_button`)
SELECT NULL, 1, code, 'We Care About Every Client', '<div class="tp-caption black_2 tp-fade tp-resizeme" data-x="51" data-y="bottom" data-voffset="-196" data-speed="500" data-start="2000" data-easing="Power3.easeInOut" data-endspeed="400" style="z-index: 3">We Care About</div><div class="tp-caption blue_capture tp-fade tp-resizeme" data-x="50" data-y="bottom" data-voffset="-150" data-speed="500" data-start="2700" data-easing="Power0.easeIn" data-endspeed="300" style="z-index: 4">Every Client</div><div class="tp-caption paragraph tp-fade tp-resizeme" data-x="51" data-y="481" data-speed="500" data-start="3200" data-easing="Power0.easeIn" data-endspeed="300" style="z-index: 3">A wide spectre of quality medical services and treatment for all <br> age groups. Professional staff and equipment to implement <br>newest medical technologies.</div>', '' FROM `<DB_PREFIX>languages`;

INSERT INTO `<DB_PREFIX>banners_translations` (`id`, `banner_id`, `language_code`, `banner_title`, `banner_text`, `banner_button`)
SELECT NULL, 2, code, 'Full Spectre Treatment', '<div class="tp-caption Layer2_1 tp-fade tp-resizeme" data-x="60" data-y="103" data-speed="500" data-start="700" data-easing="Power0.easeIn" data-endspeed="300" style="z-index: 2">Full Spectre</div><div class="tp-caption Layer2_2 tp-fade tp-resizeme" data-x="64" data-y="159" data-speed="600" data-start="1100" data-easing="Power0.easeIn" data-endspeed="300" style="z-index: 3">Treatment</div><div class="tp-caption Layer2_3 tp-fade tp-resizeme" data-x="97" data-y="259" data-speed="400" data-start="1800" data-easing="Power3.easeInOut" data-endspeed="300" style="z-index: 4">&bull; Medical Counseling</div><div class="tp-caption Layer2_3 tp-fade tp-resizeme" data-x="97" data-y="292" data-speed="400" data-start="2100" data-easing="Power3.easeInOut" data-endspeed="300" style="z-index: 6">&bull; Cardiac Clinyc</div><div class="tp-caption Layer2_3 tp-fade tp-resizeme" data-x="97" data-y="329" data-speed="400" data-start="2400" data-easing="Power3.easeInOut" data-endspeed="300" style="z-index: 8">&bull; Pediatric Clinic</div><div class="tp-caption Layer2_3 tp-fade tp-resizeme" data-x="97" data-y="365" data-speed="400" data-start="2700" data-easing="Power3.easeInOut" data-endspeed="300" style="z-index: 10">&bull; Gynecological Clinic</div><div class="tp-caption Layer2_3 tp-fade tp-resizeme" data-x="97" data-y="401" data-speed="400" data-start="3000" data-easing="Power3.easeInOut" data-endspeed="300" style="z-index: 12">&bull; Laboratory Analysis</div><div class="tp-caption Layer2_3 tp-fade tp-resizeme" data-x="97" data-y="437" data-speed="300" data-start="3300" data-easing="Power3.easeInOut" data-endspeed="400" style="z-index: 14">&bull; Diagnosis Clinic</div>', '' FROM `<DB_PREFIX>languages`;

INSERT INTO `<DB_PREFIX>banners_translations` (`id`, `banner_id`, `language_code`, `banner_title`, `banner_text`, `banner_button`)
SELECT NULL, 3, code, 'Playful Pediatric Care', '<div class="tp-caption tp-fade" data-x="61" data-y="321" data-speed="300" data-start="0" data-easing="Power3.easeInOut" data-endspeed="300" style="z-index: 2"></div><div class="tp-caption Layer3_2 tp-fade tp-resizeme" data-x="381" data-y="297" data-speed="600" data-start="1500" data-easing="Power0.easeIn" data-endspeed="300" style="z-index: 3">Care</div><div class="tp-caption Layer3_3 tp-fade tp-resizeme" data-x="85" data-y="209" data-speed="600" data-start="300" data-easing="Power0.easeIn" data-endspeed="300" style="z-index: 4">Pediatric</div> <div class="tp-caption Layer3_4 tp-fade tp-resizeme" data-x="97" data-y="519" data-speed="600" data-start="2500" data-easing="Power0.easeIn" data-endspeed="500" style="z-index: 5">When medical treatment is fun...</div><div class="tp-caption Layer3_5 tp-fade tp-resizeme" data-x="90" data-y="119" data-speed="600" data-start="1000" data-easing="Power0.easeIn" data-endspeed="500" style="z-index: 6">Playful</div>', '' FROM `<DB_PREFIX>languages`;

