
INSERT INTO `<DB_PREFIX>modules` (`id`, `code`, `class_code`, `name`, `description`, `version`, `icon`, `show_on_dashboard`, `show_in_menu`, `is_installed`, `is_system`, `is_active`, `installed_at`, `updated_at`, `has_test_data`, `sort_order`) VALUES
(NULL, 'cms', 'Cms', 'Content Management', 'CMS module allows management of site content', '0.0.4', 'icon.png', 1, 1, 1, 0, 1, '<CURRENT_DATETIME>', NULL, 0, (SELECT COUNT(m.id) + 1 FROM `<DB_PREFIX>modules` m WHERE m.is_system = 0));


INSERT INTO `<DB_PREFIX>module_settings` (`id`, `module_code`, `property_group`, `property_key`, `property_value`, `name`, `description`, `property_type`, `property_source`, `property_length`, `append_text`, `trigger_condition`, `is_required`) VALUES
(NULL, 'cms', '', 'page_link_format', 'pages/view/id/ID', 'Page Link Format', 'Defines a SEO format for page links that will be used on the site', 'enum', 'pages/view/id/ID,pages/view/id/ID/Name,pages/view/ID,pages/view/ID/Name,pages/ID,pages/ID/Name', '', '', '', 0);


INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'cms', 'pages', 'add', 'Add Pages', 'Add Pages on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'cms', 'pages', 'edit', 'Edit Pages', 'Edit Pages on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'cms', 'pages', 'delete', 'Delete Pages', 'Delete Pages from the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1);


INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, 0, '', 'cms', 'cms.png', 0, 1, 7);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Content Management' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'cms' AND bm.parent_id = 0), 'modules/settings/code/cms', 'cms', '', 0, 1, 0);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Settings' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'cms' AND bm.parent_id = 0), 'pages/manage', 'cms', '', 0, 1, 1);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Pages' FROM `<DB_PREFIX>languages`;


INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'cms', 'pages/view/id/2', '', 'top', 4, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'About Us' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'cms', 'pages/view/id/3', '', 'top', 5, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Contact Us' FROM `<DB_PREFIX>languages`;

INSERT INTO `<DB_PREFIX>search_categories` (`id`, `module_code`, `category_code`, `category_name`, `callback_class`, `callback_method`, `items_count`, `sort_order`, `is_active`) VALUES
(NULL, 'cms', 'pages', 'Pages', 'Pages', 'search', '20', (SELECT COUNT(sc.id) + 1 FROM `<DB_PREFIX>search_categories` sc), 1);

DROP TABLE IF EXISTS `<DB_PREFIX>cms_pages`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>cms_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comments_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NULL DEFAULT NULL,
  `modified_at` datetime NULL DEFAULT NULL,
  `finish_publishing_at` date NULL DEFAULT NULL,
  `is_homepage` tinyint(1) NOT NULL DEFAULT '0',
  `publish_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1 - removed, 0 - draft, 1 - published',
  `show_in_search` tinyint(1) NOT NULL DEFAULT '1',
  `access_level` enum('public','registered') CHARACTER SET latin1 NOT NULL DEFAULT 'public',
  `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `publish_status` (`publish_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

INSERT INTO `<DB_PREFIX>cms_pages` (`id`, `comments_allowed`, `created_at`, `modified_at`, `finish_publishing_at`, `is_homepage`, `publish_status`, `show_in_search`, `access_level`, `sort_order`) VALUES
(1, 0, '2013-01-01 00:00:01', '2013-01-01 00:00:01', NULL, 1, 1, 1, 'public', 0),
(2, 0, '2017-08-04 13:28:01', '2017-08-04 13:29:08', NULL, 0, 1, 1, 'public', 1),
(3, 0, '2017-08-04 13:36:22', '2017-08-04 13:49:36', NULL, 0, 1, 1, 'public', 2),
(4, 0, '2017-08-05 16:33:12', '2017-08-05 16:43:22', NULL, 0, 1, 1, 'public', 3),
(5, 0, '2018-08-07 14:00:00', '2018-08-07 14:00:00', NULL, 0, 1, 1, 'public', 4),
(6, 0, '2018-08-07 14:00:00', '2018-08-07 14:00:00', NULL, 0, 1, 1, 'public', 5);


DROP TABLE IF EXISTS `<DB_PREFIX>cms_page_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>cms_page_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) DEFAULT '0',
  `language_code` varchar(2) CHARACTER SET latin1 NOT NULL DEFAULT 'en',
  `tag_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tag_keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `tag_description` text COLLATE utf8_unicode_ci NOT NULL,
  `page_header` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_text` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `language_code` (`language_code`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 1, code, 'Our Site', 'php site', 'Our Site', 'WELCOME TO OUR WEBSITE!', '<h3>Hi there, Guest!</h3>\r\n<p>If you can read this message, this script has been successfully installed on your web hosting.</p>\r\n<p>This is an example of a HomePage, you could edit this to put information about yourself or your site do readers know where you are coming from. It’s a great way to get attention.</p>\r\n<p><strong>Dummy Text</strong></p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ac mattis elit. Nam convallis tristique lorem non ornare. Sed mi augue, luctus quis est sed, viverra aliquet metus. Pellentesque urna neque, elementum sit amet aliquam dapibus, tristique id metus. In pretium venenatis faucibus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec varius lectus sed neque tincidunt tempor. In aliquam leo quis dui egestas, quis feugiat leo facilisis.</p>\r\n<p>Aliquam at lacus non lacus rhoncus bibendum id eget dolor. Donec placerat velit sed dictum tincidunt. Praesent odio lectus, eleifend nec viverra eu, sollicitudin vitae metus. Fusce quis tortor convallis ipsum aliquam dignissim. Nullam dignissim facilisis consectetur. Vestibulum sagittis augue nibh, non aliquet diam interdum tempor. Phasellus rhoncus commodo lectus id suscipit. Nullam non enim eu metus tempus lacinia ut condimentum tellus. Vestibulum eu odio eu mauris feugiat vulputate ut sed leo. Vivamus mollis non neque quis scelerisque.</p>' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 2, code, 'About Us', 'php site', 'About Us', 'About Us', '<p><strong>Our Clinic</strong> is a full-service medical clinic with five Board Certified Family physicians, a Board Certified General/Gynecological Surgeon and four Board Certified mid-level providers (Physician’s Assistant &amp; Nurse Practitioners).<br><br><strong>Our Mission</strong><br>Our Clinic&nbsp;is a vital resource for this country and the surrounding area and provides state-of-the-art healthcare to improve our quality of life.</p><p>Since 1981, the mission of the&nbsp;Our Foundation has been to promote and enhance the care available at&nbsp;the our Clinic&nbsp;by providing a vehicle whereby individuals and organizations can give gifts and memorials to fund current and ongoing needs.&nbsp;If you do not see the health-related web link, our&nbsp;contact information, brochure, news item or medical information you were hoping for on these pages, contact&nbsp;us at 123.456.7890 ext. 123 and she will be happy to help you track it down for you.</p><h3>Electronic Health Records</h3><p>To better manage the health needs of our patients, all three North Basin Medical Clinics use Electronic Health Records. To find out more about how this service works and the benefits to you, the patient,&nbsp;contact our Help Desk tel.: 1-800-123-45-67</p><h3>Working Hours</h3><p>Our Clinic is located on the&nbsp;Presidents Hospital Medical Campus at 100 North 3rd Street in our sity.<br><br>Monday—Friday<br>9 am—5 pm<br><br>Saturday <br>8 am—Noon<br><br></p>' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 3, code, 'Contact Us', 'php site', 'Contact Us', 'Contact Us', '[iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.0436887281235!2d-73.97720258419747!3d40.739064179329!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c2590c212fc7a9%3A0x67a293dafcc5f7e0!2z0JPQvtGB0L_QuNGC0LDQu9GM0L3Ri9C5INGG0LXQvdGC0YAg0JHQtdC70LLRjNGO!5e0!3m2!1sru!2sby!4v1534263086646" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen][/iframe] {module:webforms}' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 4, code, '24 Hours Service', 'php site', '24 Hours Service', '24 Hours Service', 'We provide after-hours emergency service on nights, weekends and holidays. One of our doctors is "on-call" during those times to look after problems which cannot wait until regular office hours. When you call the clinic, the answering service will take your message and contact the doctor. If you have not heard from the on-call doctor within 20 minutes, please call again. Please do not go to a hospital Emergency Department until you have spoken to the on-call doctor, unless you feel that a delay in receiving medical care will be life-threatening. This may save you the trip or a long wait in emergency.' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 5, code, 'Privacy Policy', 'php site', 'Privacy Policy', 'Privacy Policy', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla quia cumque sit culpa similique eum quibusdam, veritatis consectetur optio dolore sint est, mollitia id, maxime sunt blanditiis architecto iure totam rem molestiae. Id porro, corporis dolorum reiciendis? Temporibus iure totam quos debitis eveniet nemo molestiae dolorum! Veritatis quasi quis blanditiis quod repudiandae animi quam minus, praesentium ad dolorum rerum ut nostrum rem velit. Deserunt consequatur ut est numquam quibusdam eos ipsa iusto, ipsam dolorem voluptatum omnis dolorum cupiditate, laudantium repellat quos dolores eveniet! Cumque ex dicta voluptatum delectus.' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>cms_page_translations` (`id`, `page_id`, `language_code`, `tag_title`, `tag_keywords`, `tag_description`, `page_header`, `page_text`) SELECT NULL, 6, code, 'Terms & Conditions', 'php site', 'Terms & Conditions', 'Terms & Conditions', '<p>Terms and conditions template for website usage</p><p>Welcome to our website. If you continue to browse and use this website, you are agreeing to comply with and be bound by the following terms and conditions of use, which together with our privacy policy govern [business name]''s relationship with you in relation to this website. If you disagree with any part of these terms and conditions, please do not use our website.</p><br /><p>The term ''[business name]'' or ''us'' or ''we'' refers to the owner of the website whose registered office is [address]. Our company registration number is [company registration number and place of registration]. The term ''you'' refers to the user or viewer of our website.</p><br/><p>The use of this website is subject to the following terms of use:</p><br/><p>The content of the pages of this website is for your general information and use only. It is subject to change without notice.</p><p>This website uses cookies to monitor browsing preferences.</p><p>Neither we nor any third parties provide any warranty or guarantee as to the accuracy, timeliness, performance, completeness or suitability of the information and materials found or offered on this website for any particular purpose. You acknowledge that such information and materials may contain inaccuracies or errors and we expressly exclude liability for any such inaccuracies or errors to the fullest extent permitted by law.</p><p>Your use of any information or materials on this website is entirely at your own risk, for which we shall not be liable. It shall be your own responsibility to ensure that any products, services or information available through this website meet your specific requirements.</p><p>This website contains material which is owned by or licensed to us. This material includes, but is not limited to, the design, layout, look, appearance and graphics. Reproduction is prohibited other than in accordance with the copyright notice, which forms part of these terms and conditions.</p><p>All trade marks reproduced in this website which are not the property of, or licensed to, the operator are acknowledged on the website.</p><p>Unauthorised use of this website may give rise to a claim for damages and/or be a criminal offence.</p><p>From time to time this website may also include links to other websites. These links are provided for your convenience to provide further information. They do not signify that we endorse the website(s). We have no responsibility for the content of the linked website(s).</p><p>Your use of this website and any dispute arising out of such use of the website is subject to the laws of [country].</p>' FROM `<DB_PREFIX>languages`;


DROP TABLE IF EXISTS `<DB_PREFIX>cms_page_comments`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>cms_page_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cms_page_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `user_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(80) CHARACTER SET latin1 NOT NULL,
  `comment_text` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - awaiting, 1 - approved, 2 - denied',
  `created_at` datetime NULL DEFAULT NULL,
  `changed_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cms_page_id` (`cms_page_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

