
DELETE FROM `<DB_PREFIX>modules` WHERE `code` = 'appointments';
DELETE FROM `<DB_PREFIX>module_settings` WHERE `module_code` = 'appointments';

DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `module_code` = 'appointments' AND `category` = 'appointments' AND `code` = 'add');
DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `module_code` = 'appointments' AND `category` = 'appointments' AND `code` = 'edit');
DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `module_code` = 'appointments' AND `category` = 'appointments' AND `code` = 'delete');

DELETE FROM `<DB_PREFIX>privileges` WHERE `module_code` = 'appointments';
DELETE FROM `<DB_PREFIX>backend_menu_translations` WHERE `menu_id` IN (SELECT id FROM `<DB_PREFIX>backend_menus` WHERE `module_code` = 'appointments');
DELETE FROM `<DB_PREFIX>backend_menus` WHERE `module_code` = 'appointments';

DELETE FROM `<DB_PREFIX>frontend_menu_translations` WHERE `menu_id` IN (SELECT id FROM `<DB_PREFIX>frontend_menus` WHERE `module_code` = 'appointments');
DELETE FROM `<DB_PREFIX>frontend_menus` WHERE `module_code` = 'appointments';

DELETE FROM `<DB_PREFIX>email_template_translations` WHERE `template_code` IN (SELECT code FROM `<DB_PREFIX>email_templates` WHERE `module_code` = 'appointments');
DELETE FROM `<DB_PREFIX>email_templates` WHERE `module_code` = 'appointments';

DELETE FROM `<DB_PREFIX>accounts` WHERE `role` IN ('doctor', 'patient');

DELETE FROM `<DB_PREFIX>search_categories` WHERE `module_code` = 'appointments';


DROP TABLE IF EXISTS `<DB_PREFIX>appt_appointments`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_clinics`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_clinic_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctors`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_images`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_schedule_timeblocks`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_schedules`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_specialties`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_timeoffs`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_reviews`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_patients`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_specialties`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_specialty_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_insurance`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_insurance_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_visit_reasons`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_visit_reason_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_titles`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_title_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_degrees`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_degree_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_membership_plans`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_membership_plans_translations`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_orders`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_working_hours`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_services`;
DROP TABLE IF EXISTS `<DB_PREFIX>appt_services_translations`;

