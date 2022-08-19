
INSERT INTO `<DB_PREFIX>modules` (`id`, `code`, `class_code`, `name`, `description`, `version`, `icon`, `show_on_dashboard`, `show_in_menu`, `is_installed`, `is_system`, `is_active`, `installed_at`, `updated_at`, `has_test_data`, `sort_order`) VALUES
(NULL, 'webforms', 'WebForms', 'Web Forms', 'Web Forms module allows creating online forms on site pages', '0.0.3', 'icon.png', 0, 0, 1, 0, 1, '<CURRENT_DATETIME>', NULL, 0, (SELECT COUNT(m.id) + 1 FROM `<DB_PREFIX>modules` m WHERE m.is_system = 0));


INSERT INTO `<DB_PREFIX>module_settings` (`id`, `module_code`, `property_group`, `property_key`, `property_value`, `name`, `description`, `property_type`, `property_source`, `property_length`, `append_text`, `trigger_condition`, `is_required`) VALUES
(NULL, 'webforms', '', 'contact_email', '', 'Contact Email', 'The email address used to receive submitted information', 'email', '', '', '', '', 1),
(NULL, 'webforms', '', 'contact_phone', '', 'Contact Phone', 'The phone used to receive submitted information', 'phone', '', '', '', '', 0),
(NULL, 'webforms', '', 'shortcode', '{module:webforms}', 'Shortcode', 'This shortcode allows you to display web forms on the site pages', 'label', '', '', '', '', 0),
(NULL, 'webforms', 'Form Fields', 'field_name', 'show-required', 'Name Field', 'Defines whether to display a Name field on the form', 'enum', 'show-required,show-optional,hide', '', '', '', 0),
(NULL, 'webforms', 'Form Fields', 'field_email', 'show-required', 'Email Field', 'Defines whether to display an Email field on the form', 'enum', 'show-required,show-optional,hide', '', '', '', 0),
(NULL, 'webforms', 'Form Fields', 'field_phone', 'show-required', 'Phone Field', 'Defines whether to display a Phone field on the form', 'enum', 'show-required,show-optional,hide', '', '', '', 0),
(NULL, 'webforms', 'Form Fields', 'field_company', 'show-required', 'Company Name Field', 'Defines whether to display a Company Name field on the form', 'enum', 'show-required,show-optional,hide', '', '', '', 0),
(NULL, 'webforms', 'Form Fields', 'field_message', 'show-required', 'Message Field', 'Defines whether to display a Message field on the form', 'enum', 'show-required,show-optional', '', '', '', 0),
(NULL, 'webforms', 'Form Fields', 'field_captcha', 'show', 'Captcha Validation', 'Defines whether to display a Captcha on the form', 'enum', 'show,hide', '', '', '', 0);

-- INSERT INTO `<DB_PREFIX>privileges` (`id`, `category`, `code`, `name`, `description`) VALUES (NULL, 'webforms', 'add', 'Add Webforms', 'Add Webforms to site pages'); 
-- INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
