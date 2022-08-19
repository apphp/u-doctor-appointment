DROP TABLE IF EXISTS `<DB_PREFIX>banners`;
DROP TABLE IF EXISTS `<DB_PREFIX>banners_translations`;

DELETE FROM `<DB_PREFIX>modules` WHERE `code` = 'banners';
DELETE FROM `<DB_PREFIX>module_settings` WHERE `module_code` = 'banners';

DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `category` = 'banners');
DELETE FROM `<DB_PREFIX>privileges` WHERE `category` = 'banners';