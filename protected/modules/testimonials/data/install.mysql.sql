
INSERT INTO `<DB_PREFIX>modules` (`id`, `code`, `class_code`, `name`, `description`, `version`, `icon`, `show_on_dashboard`, `show_in_menu`, `is_installed`, `is_system`, `is_active`, `installed_at`, `updated_at`, `has_test_data`, `sort_order`) VALUES
(NULL, 'testimonials', 'Testimonials', 'Testimonials', 'Testimonials module allows managing and displaying testimonials on the site', '0.0.3', 'icon.png', 0, 0, 1, 0, 1, '<CURRENT_DATETIME>', NULL, 0, (SELECT COUNT(m.id) + 1 FROM `<DB_PREFIX>modules` m WHERE m.is_system = 0));


INSERT INTO `<DB_PREFIX>module_settings` (`id`, `module_code`, `property_group`, `property_key`, `property_value`, `name`, `description`, `property_type`, `property_source`, `property_length`, `append_text`, `trigger_condition`, `is_required`) VALUES
(NULL, 'testimonials', '', 'testimonials_per_page', '10', 'Testimonials Per Page', 'Defines how many testimonials will be shown per page', 'range', '1-100', '', '', '', 1),
(NULL, 'testimonials', '', 'modulelink', 'testimonials/viewAll', 'All Testimonials Link', 'This link leads to the page with all testimonials', 'label', '', '', '', '', 0),
(NULL, 'testimonials', '', 'shortcode', '{module:testimonials}', 'Shortcode', 'This shortcode allows you to display a list of testimonials on the site pages', 'label', '', '', '', '', 0),
(NULL, 'testimonials', 'Side Block', 'moduleblock', 'drawTestimonialsBlock', 'Testimonials Block', 'Draws testimonials side block', 'label', '', '', '', '', 0),
(NULL, 'testimonials', 'Side Block', 'testimonials_count', '2', 'Testimonials Count', 'Defines how many testimonials will be shown in the side block', 'range', '1-10', '', '', '', 1),
(NULL, 'testimonials', 'Side Block', 'view_all_link', 'always', 'View All Link', 'Specifies whether to show View All link in the side block', 'enum', 'never,show-after_2_items,show-after_3_items,show-after_4_items,show-after_5_items,show-after_10_items,always', '', '', '', 0);


INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'testimonials', 'testimonials', 'add', 'Add Testimonials', 'Add testimonials on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'testimonials', 'testimonials', 'edit', 'Edit Testimonials', 'Edit testimonials on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'testimonials', 'testimonials', 'delete', 'Delete Testimonials', 'Delete testimonials from the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);


INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'moduleblock', 'testimonials', 'drawTestimonialsBlock', '', 'right', 0, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Testimonials' FROM `<DB_PREFIX>languages`;


DROP TABLE IF EXISTS `<DB_PREFIX>testimonials`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_country` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_city` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_company` varchar(125) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_position` varchar(125) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_image` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `testimonial_text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',  
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5;

INSERT INTO `<DB_PREFIX>testimonials` (`id`, `author_name`, `author_country`, `author_city`, `author_company`, `author_position`, `author_email`, `author_image`, `testimonial_text`, `created_at`, `is_active`, `sort_order`) VALUES
(1, 'Roberto', 'Italy', 'Rome', 'Company ltd.', 'Senior Manager', 'roberto@example.com', 'author_1.jpg', 'My family doctor offered me a very effective treatment and I really did feel significant improvement really soon. I followed all the prescriptions and now I feel great. I was impressed by pleasant atmosphere and the general assistance.', '2017-01-01 00:00:00', 1, 1),
(2, 'Hantz', 'Germany', 'Munich', '', '', 'hantz@example.com', 'author_2.jpg', 'Fantastic service and really helpful and caring personnel. I was impressed by pleasant atmosphere and the general assistance. My doctor offered me a very effective treatment. Greatly thankful to all the staff.', '2017-01-04 00:00:00', 1, 2),
(3, 'Lilian Gentz', 'Great Britan', 'London', 'Business ltd.', '', 'lilian@example.com', 'author_3.jpg', 'My doctor offered me a very effective treatment. I followed all the prescriptions and now I feel great. Greatly thankful to all the staff. Fantastic service and really helpful and caring personnel. I was impressed by pleasant atmosphere and the general assistance.', '2017-02-12 00:00:00', 1, 3),
(4, 'Frederick', 'Unuted States', '', '', '', 'frederick@example.com', '', 'If you are choosing a medical center, Medicure is the one I would recommend. Caring staff, new equipment and helpful therapy - definitely, this is a medical center of my choice!', '2017-05-16 00:00:00', 1, 4);

