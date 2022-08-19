
DELETE FROM `<DB_PREFIX>modules` WHERE `code` = 'webforms';
DELETE FROM `<DB_PREFIX>module_settings` WHERE `module_code` = 'webforms';

DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `category` = 'webforms');

DELETE FROM `<DB_PREFIX>privileges` WHERE `category` = 'webforms';



