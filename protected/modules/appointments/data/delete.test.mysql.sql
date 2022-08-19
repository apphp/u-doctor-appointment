UPDATE `<DB_PREFIX>site_info` SET `header` = 'PHP Directy CMF', `slogan` = 'Welcome to PHP Directy CMF!', `footer` = 'PHP Directy CMF © <a class="footer_link" target="_blank" rel="noopener noreferrer" href="https://www.apphp.com/php-directy-cmf/index.php">ApPHP</a>', `meta_title` = 'PHP Directy CMF', `meta_description` = 'Directy CMF', `meta_keywords` = 'php cmf, php framework, php content management framework, php cms', `site_address`='';

DELETE FROM `<DB_PREFIX>appt_appointments` WHERE `appointment_number` = '12345-TEST';

UPDATE `<DB_PREFIX>appt_clinics` SET `phone` = '', `fax` = '' WHERE `id` = 1;
UPDATE `<DB_PREFIX>appt_clinic_translations` SET `name` = '', `description` = '' WHERE `clinic_id` = 1;

DELETE FROM `<DB_PREFIX>appt_doctors` WHERE EXISTS (SELECT * FROM `<DB_PREFIX>accounts` WHERE `<DB_PREFIX>accounts`.`id` = `<DB_PREFIX>appt_doctors`.`account_id` AND `<DB_PREFIX>accounts`.`username` = 'doctor1');
DELETE FROM `<DB_PREFIX>accounts` WHERE `username` = 'doctor1';

DELETE FROM `<DB_PREFIX>appt_doctors` WHERE EXISTS (SELECT * FROM `<DB_PREFIX>accounts` WHERE `<DB_PREFIX>accounts`.`id` = `<DB_PREFIX>appt_doctors`.`account_id` AND `<DB_PREFIX>accounts`.`username` = 'doctor2');
DELETE FROM `<DB_PREFIX>accounts` WHERE `username` = 'doctor2';

DELETE FROM `<DB_PREFIX>appt_orders` WHERE `order_number` IN ('TEST-UK9A', 'TEST-K29N');

DELETE FROM `<DB_PREFIX>appt_doctor_specialties` WHERE `doctor_id` IN(1, 2);

DELETE FROM `<DB_PREFIX>appt_doctor_schedule_timeblocks` WHERE EXISTS (SELECT * FROM `<DB_PREFIX>appt_doctor_schedules` WHERE `doctor_id` IN(1, 2));

DELETE FROM `<DB_PREFIX>appt_doctor_schedules` WHERE `doctor_id` IN(1, 2);

DELETE FROM `<DB_PREFIX>appt_doctor_timeoffs` WHERE `doctor_id` IN(1, 2);

DELETE FROM `<DB_PREFIX>appt_patients` WHERE EXISTS (SELECT * FROM `<DB_PREFIX>accounts` WHERE `<DB_PREFIX>accounts`.`id` = `<DB_PREFIX>appt_patients`.`account_id` AND `<DB_PREFIX>accounts`.`username` = 'patient1');
DELETE FROM `<DB_PREFIX>accounts` WHERE `username` = 'patient1';

DELETE FROM `<DB_PREFIX>appt_patients` WHERE EXISTS (SELECT * FROM `<DB_PREFIX>accounts` WHERE `<DB_PREFIX>accounts`.`id` = `<DB_PREFIX>appt_patients`.`account_id` AND `<DB_PREFIX>accounts`.`username` = 'patient2');
DELETE FROM `<DB_PREFIX>accounts` WHERE `username` = 'patient2';

DELETE FROM `<DB_PREFIX>appt_services` WHERE `id` <= 8;
DELETE FROM `<DB_PREFIX>appt_services_translations` WHERE `id` <= 8;

UPDATE `<DB_PREFIX>modules` SET `has_test_data` = 0 WHERE `code` = 'appointments';