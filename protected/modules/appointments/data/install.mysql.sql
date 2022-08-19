
INSERT INTO `<DB_PREFIX>modules` (`id`, `code`, `class_code`, `name`, `description`, `version`, `icon`, `show_on_dashboard`, `show_in_menu`, `is_installed`, `is_system`, `is_active`, `installed_at`, `updated_at`, `has_test_data`, `sort_order`) VALUES
(NULL, 'appointments', 'Appointments', 'Doctor Appointments', 'Clinic management, doctor and therapist online medical appointment scheduling system', '0.0.2', 'icon.png', 1, 1, 1, 1, 1, '<CURRENT_DATETIME>', NULL, 1, (SELECT COUNT(m.id) + 1 FROM `<DB_PREFIX>modules` m WHERE m.is_system = 1));


INSERT INTO `<DB_PREFIX>module_settings` (`id`, `module_code`, `property_group`, `property_key`, `property_value`, `name`, `description`, `property_type`, `property_source`, `property_length`, `append_text`, `trigger_condition`, `is_required`) VALUES
(NULL, 'appointments', '', 'moduleblock', 'drawAppointmentsBlock', 'Appointments Block', 'Draws Appointments side block', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Login', 'doctor_allow_login', '1', 'Allow Doctors to Login', 'Specifies whether to allow existing doctors to login', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Login', 'doctor_removal_type', 'logical', 'Remove Account Type', 'Specifies the type of doctor account removal: logical, physical', 'enum', 'logical,physical','', '', '', 0),
(NULL, 'appointments', 'Doctor Login', 'doctor_allow_remember_me', '1', 'Allow Remember Me', 'Specifies whether to allow Remember Me feature by doctors', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Login', 'modulelink', 'doctors/login', 'Doctor Login Link', 'This link leads to the page where doctor can login to the site', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Registration', 'doctor_allow_registration', '1', 'Allow Doctors to Register', 'Specifies whether to allow doctors to register', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Registration', 'doctor_new_registration_alert', '1', 'New Registration Admin Alert', 'Specifies whether to alert admin on new doctors registration', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Registration', 'modulelink', 'doctors/registration', 'Doctor Registration Link', 'This link leads to the page where doctor can register to the site', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Registration', 'modulelink', 'doctors/termsConditions', 'Doctor Terms & Conditions Link', 'This link leads to the page where doctor can see terms & conditions', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Restore Password', 'doctor_allow_restore_password', '1', 'Allow Restore Password', 'Specifies whether to allow doctors to restore their passwords', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'doctors_watermark', '0', 'Add Watermark to Images', 'Specifies whether to add watermark to doctors images or not', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'doctors_watermark_text', 'Watermark Text', 'Watermark text', 'Text that will be added to images', 'string', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Restore Password', 'modulelink', 'doctors/restorePassword', 'Restore Password Link', 'This link leads to the page where doctor may restore forgotten password', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'doctor_approval_type', 'automatic', 'Confirmation Type (by Doctor)', 'Defines whether confirmation (which type of) is required for registration', 'enum', 'by_admin,by_email,automatic', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'shortcode', '{module:doctors}', 'Doctors Key', 'The shortcode that will be replaced with the list of doctors (copy and paste it into the page)', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'change_doctor_password', '1', 'Admin Changes Doctor Password', 'Specifies whether to allow changing doctor password by Admin', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'doctors_verification_allow', '1', 'Verification captcha', 'Specifies whether to allow verification captcha on doctor registration page', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'doctors_allow_search_by_name', '1', 'Allow Search By Name', 'Specifies whether to allow patients to search by doctors by name', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'doctors_allow_search_by_location', '1', 'Allow Search By Location', 'Specifies whether to allow patients to search by doctors by location', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'show_rating', '1', 'Show/Hide Rating And Reviews', 'Specifies to show the rating and reviews of the doctor', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'show_rating_form', '1', 'Show/Hide Rating Form', 'Specifies to whether to show the rating form', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'review_moderation', '1', 'Review Moderation', 'Specifies moderating the review after publication', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'reviews_per_page', '3', 'Reviews Per Page', 'Defines how many reviews will be shown per page', 'range', '1-100', '', '', '', 1),
(NULL, 'appointments', 'Doctor Settings', 'doctors_per_page', '10', 'Doctors Per Page', 'Defines how many doctor profiles show on search result page', 'range', '1-100', '', '', '', 1),
(NULL, 'appointments', 'Doctor Settings', 'profile_link_format', 'doctors/profile/ID/Name', 'Doctor Profile Link Format', 'Defines a SEO format for profile links that will be used on the site', 'enum', 'doctors/profile/id/ID,doctors/profile/id/ID/Name,doctors/profile/ID,doctors/profile/ID/Name,doctors/ID,doctors/ID/Name', '', '', '', 0),
(NULL, 'appointments', 'Doctor Settings', 'show_fields_for_unregistered_users', '0', 'Show fields for unregistered users', 'Specifies whether to show for unregistered users the fields: phone, email, address', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Settings', 'patient_approval_type', 'automatic', 'Confirmation Type (by Patient)', 'Defines whether confirmation (which type of) is required for registration', 'enum', 'by_admin,by_email,automatic', '', '', '', 0),
(NULL, 'appointments', 'Patient Settings', 'change_patient_password', '1', 'Admin Changes Patient Password', 'Specifies whether to allow changing patient password by Admin', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Settings', 'patients_verification_allow', '1', 'Verification captcha', 'Specifies whether to allow verification captcha on doctor registration page', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Settings', 'max_allowed_appointment_per_patient', '5', 'Maximum Appointments', 'Specifies the maximum allowed number of scheduled appointments per patient', 'range', '1-50', '', '', '', 1),
(NULL, 'appointments', 'Patient Settings', 'max_allowed_appointment_to_specialist', '1', 'Maximum Active Appointments', 'Specifies the maximum allowed number of scheduled appointments to a specialist', 'range', '1-50', '', '', '', 1),
(NULL, 'appointments', 'Patient Login', 'patient_allow_login', '1', 'Allow Patients to Login', 'Specifies whether to allow existing patients to login', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Login', 'patient_removal_type', 'logical', 'Remove Account Type', 'Specifies the type of patient account removal: logical, physical', 'enum', 'logical,physical','', '', '', 0),
(NULL, 'appointments', 'Patient Login', 'patient_allow_remember_me', '1', 'Allow Remember Me', 'Specifies whether to allow Remember Me feature by patients', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Login', 'modulelink', 'patients/login', 'Patient Login Link', 'This link leads to the page where patient can login to the site', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Registration', 'patient_allow_registration', '1', 'Allow Patients to Register', 'Specifies whether to allow patients to register', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Registration', 'patient_new_registration_alert', '1', 'New Registration Admin Alert', 'Specifies whether to alert admin on new patients registration', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Registration', 'modulelink', 'patients/registration', 'Patient Registration Link', 'This link leads to the page where patient can register to the site', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Registration', 'modulelink', 'patients/termsConditions', 'Patient Terms & Conditions Link', 'This link leads to the page where patient can see terms & conditions', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Restore Password', 'patient_allow_restore_password', '1', 'Allow Restore Password', 'Specifies whether to allow patients to restore their passwords', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Patient Restore Password', 'modulelink', 'patients/restorePassword', 'Restore Password Link', 'This link leads to the page where patient may restore forgotten password', 'label', '', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'approval_required', 'automatic', 'Appointment Approval Required', 'Defines whether an approval (which type of) is required for appointments', 'enum', 'automatic,by_admin_or_doctor', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'send_email_doctor_appointment_reserved', '1', 'Doctor Email on New Appointment', 'Specifies whether to send an email to doctor when new appointment was created', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'send_email_doctor_appointment_verified', '1', 'Doctor Verify Appointment Email', 'Specifies whether to send an email to doctor when new appointment was created', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'send_email_doctor_appointment_canceled', '1', 'Doctor Cancel Appointment Email', 'Specifies whether to send email to doctor, if appointment was verified by patient', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'send_email_doctor_appointment_changed', '1', 'Doctor Cnange Appointment Email', 'Specifies whether to send email to doctor, if appointment was canceled by patient', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'send_email_patient_appointment_reserved', '1', 'Patient New Appointment Email', 'Specifies whether to send email to doctor, if appointment was changed by patient', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'send_email_patient_appointment_verified', '1', 'Patient Verified Appointment Email', 'Specifies whether to send an email to patient when new appointment was created', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'send_email_patient_appointment_canceled', '1', 'Patient Cancel Appointment Email', 'Specifies whether to send email to patient, if appointment was verified by doctor', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'send_email_patient_appointment_changed', '1', 'Patient Change Appointment Email', 'Specifies whether to send email to patient, if appointment was canceled by doctor', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Appointment Settings', 'time_format_appointment_time', 'H:i', 'Appointment Time Format', 'Specifies which time format to show in appointments', 'enum', 'H:i,h:i a,h:i A,g:i a,g:i A', '', '', '', 0),
(NULL, 'appointments', 'Reminder Settings', 'reminder_expired_membership', '10', 'Membership Renewal Reminder', 'Specifies a period of days for email reminder of membership plan renewal', 'range', '0-50', '', '', '', 0),
(NULL, 'appointments', 'Reminder Settings', 'reminder_type', 'email', 'Reminder Type', 'Specifies a type of the Reminder', 'enum', 'email', '', '', '', 0),
(NULL, 'appointments', 'Reminder Settings', 'reminder_patient_arrival_reminder', '24', 'Patient Arrival Reminder', 'Specifies a time interval in hours, before arrival reminder will be sent to patient', 'range', '0-50', '', '', '', 0),
-- (NULL, 'appointments', 'Reminder Settings', '#reminder_doctor_confirm_reminder', '24', 'Doctor Confirm Reminder', 'Specifies a time interval in hours, before confirm reminder will be sent to doctor', 'enum', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,30,36,42,48,72', '', '', '', 0),
-- (NULL, 'appointments', 'Reminder Settings', '#reminder_patient_confirm_reminder', '36', 'Patient Confirm Reminder', 'Specifies a time interval in hours, before confirm reminder will be sent to patient', 'enum', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,30,36,42,48,72', '', '', '', 0),
(NULL, 'appointments', 'Images', 'image_max_size', '2Mb', 'Maximum Image Size', 'Defines a maximum size for doctor image in megabytes (Mb)', 'enum', '500Kb,1Mb,1.5Mb,2Mb,2.5Mb,3Mb,4Mb', '', '', '', 0),
(NULL, 'appointments', 'Images', 'allow_multi_image_upload', '1', 'Multi Image Upload', 'Specifies whether to allow multiple images upload for doctors', 'bool', '', '', '', '', 0),
(NULL, 'appointments', 'Images', 'doctor_maximum_images_upload', '20', 'Maximum Images to Upload', 'Defines a maximum number of files for doctor multi-images uploading', 'range', '1-50', '', '', '', 0);


INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'appointment', 'add', 'Add Appointment', 'Add appointment on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'appointment', 'edit', 'Edit Appointment', 'Edit appointment on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'appointment', 'delete', 'Delete Appointment', 'Delete appointment from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'clinic', 'add', 'Add Clinic', 'Add clinic on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'clinic', 'edit', 'Edit Clinic', 'Edit clinic on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'clinic', 'delete', 'Delete Clinic', 'Delete clinic from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'doctor', 'add', 'Add Doctor', 'Add doctor on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'doctor', 'edit', 'Edit Doctor', 'Edit doctor on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'doctor', 'delete', 'Delete Doctor', 'Delete doctor from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'patient', 'add', 'Add Patient', 'Add patient on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'patient', 'edit', 'Edit Patient', 'Edit patient on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'patient', 'delete', 'Delete Patient', 'Delete patient from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'masterdata', 'add', 'Add Master Data', 'Add master data on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'masterdata', 'edit', 'Edit Master Data', 'Edit master data on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'masterdata', 'delete', 'Delete Master Data', 'Delete master data from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'order', 'add', 'Add Order', 'Add order on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'order', 'edit', 'Edit Order', 'Edit order on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'order', 'delete', 'Delete Order', 'Delete order from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'membership', 'add', 'Add Membership', 'Add membership on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'membership', 'edit', 'Edit Membership', 'Edit membership on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'membership', 'delete', 'Delete Membership', 'Delete membership from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);

INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'services', 'add', 'Add Services', 'Add services on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'services', 'edit', 'Edit Services', 'Edit services on the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'appointments', 'services', 'delete', 'Delete Services', 'Delete services from the site');
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);


INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, 0, '', 'appointments', 'appointments.png', 0, 1, 8);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Doctor Appointments' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'modules/settings/code/appointments', 'appointments', '', 0, 1, 0);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Settings' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'integrationWidgets/code', 'appointments', '', 0, 1, 1);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Integration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'clinics/manage', 'appointments', '', 0, 1, 2);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Clinic Info' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'workingHours/edit', 'appointments', '', 0, 1, 3);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Working Hours' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'services/manage', 'appointments', '', 0, 1, 4);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Services' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'masterData/index', 'appointments', '', 0, 1, 5);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Master Data' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'doctors/manage', 'appointments', '', 0, 1, 6);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Doctors' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'doctorReviews/manage', 'appointments', '', 0, 1, 7);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Reviews' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'patients/manage', 'appointments', '', 0, 1, 8);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Patients' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'memberships/manage', 'appointments', '', 0, 1, 9);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Membership' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'appointments/manage', 'appointments', '', 0, 1, 10);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Appointments' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'orders/index', 'appointments', '', 0, 1, 11);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Orders' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'appointments' AND bm.parent_id = 0), 'statistics/manage', 'appointments', '', 0, 1, 12);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Statistics' FROM `<DB_PREFIX>languages`;

INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'appointments', 'services/viewAll', '', 'top', 1, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Services' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'appointments', 'doctors/ourStaff', '', 'top', 2, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Our Doctors' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'pagelink', 'appointments', 'appointments/findDoctors', '', 'top', 3, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Appointments' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>frontend_menus` (`id`, `parent_id`, `menu_type`, `module_code`, `link_url`, `link_target`, `placement`, `sort_order`, `access_level`, `is_active`) VALUES (NULL, 0, 'moduleblock', 'appointments', 'drawAppointmentsBlock', '', 'right', 0, 'public', 1);
INSERT INTO `<DB_PREFIX>frontend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>frontend_menus`), code, 'Appointments' FROM `<DB_PREFIX>languages`;


INSERT INTO `<DB_PREFIX>email_templates` (`id`, `code`, `module_code`, `is_system`) VALUES
(NULL, 'doctors_new_account_created_by_admin', 'appointments', 1),
(NULL, 'doctors_password_changed_by_admin', 'appointments', 1),
(NULL, 'doctors_account_approved_by_admin', 'appointments', 1),
(NULL, 'doctors_account_created_notify_admin', 'appointments', 1),
(NULL, 'doctors_account_created_admin_approval', 'appointments', 1),
(NULL, 'doctors_account_created_email_confirmation', 'appointments', 1),
(NULL, 'doctors_account_created_auto_approval', 'appointments', 1),
(NULL, 'doctors_account_removed_by_doctor', 'appointments', 1),
(NULL, 'doctors_password_forgotten', 'appointments', 1),
(NULL, 'patients_new_account_created_by_admin', 'appointments', 1),
(NULL, 'patients_password_changed_by_admin', 'appointments', 1),
(NULL, 'patients_account_approved_by_admin', 'appointments', 1),
(NULL, 'patients_account_created_notify_admin', 'appointments', 1),
(NULL, 'patients_account_created_admin_approval', 'appointments', 1),
(NULL, 'patients_account_created_email_confirmation', 'appointments', 1),
(NULL, 'patients_account_created_auto_approval', 'appointments', 1),
(NULL, 'patients_account_removed_by_patient', 'appointments', 1),
(NULL, 'patients_password_forgotten', 'appointments', 1),
(NULL, 'success_order', 'appointments', 1),
(NULL, 'success_order_for_admin', 'appointments', 1),
(NULL, 'paid_order', 'appointments', 1),
(NULL, 'appointment_reserved_by_doctor', 'appointments', 1),
(NULL, 'appointment_verified_by_doctor', 'appointments', 1),
(NULL, 'appointment_canceled_by_doctor', 'appointments', 1),
(NULL, 'appointment_changed_by_doctor', 'appointments', 1),
(NULL, 'appointment_reserved_by_patient', 'appointments', 1),
(NULL, 'appointment_verified_by_patient', 'appointments', 1),
(NULL, 'appointment_canceled_by_patient', 'appointments', 1),
(NULL, 'appointment_changed_by_patient', 'appointments', 1),
(NULL, 'appointment_reminder', 'appointments', 1),
(NULL, 'appointment_doctor_reminder', 'appointments', 1),
(NULL, 'reminder_expiries_membership_plan', 'appointments', 1);


-- Doctors --
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'doctors_new_account_created_by_admin', code, 'New doctor account created (by admin)', 'Your account has been created by administrator', 'Dear <b>{FIRST_NAME} {LAST_NAME}!</b>\r\n\r\nThe {WEB_SITE} Admin has invited you to contribute to our site.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nPlease follow the link below to log into your account: <a href={SITE_URL}doctors/login>Login</a>.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'doctors_password_changed_by_admin', code, 'Password for the doctor account changed (by admin)', 'Your password has been changed by admin', 'Hello <b>{FIRST_NAME} {LAST_NAME}!</b>\r\n\r\nYour password has been changed by administrator of the site:\r\n{WEB_SITE}\r\n\r\nBelow your new login info:\r\n-\r\nUsername: {USERNAME} \r\nPassword: {PASSWORD}\r\n\r\n-\r\nBest Regards,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'doctors_account_approved_by_admin', code, 'New doctor account approved (by admin)', 'Your account has been approved', 'Dear <b>{FIRST_NAME} {LAST_NAME}!</b>\r\n\r\nCongratulations! This e-mail is to confirm that your registration at {WEB_SITE} has been approved.\r\n\r\nYou may now <a href={SITE_URL}doctors/login>log into</a> your account.\r\n\r\nThank you for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'doctors_account_created_notify_admin', code, 'New doctor account created (notify admin)', 'New doctor account has been created', "Hello Admin!\r\n\r\nA new doctor has been registered on your site.\r\n\r\nThis email contains a doctor account details:\r\n\r\nName: {FIRST_NAME} {LAST_NAME}\r\nEmail: {CUSTOMER_EMAIL}\r\nUsername: {USERNAME}\r\n\r\nP.S. Please check if it doesn't require your approval for activation." FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'doctors_account_created_admin_approval', code, 'New doctor account created (admin approval)', 'Your account has been created (admin approval required)', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nCongratulations on creating your new account.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nAfter your registration is approved by administrator, you could log into your account with a following link:\r\n<a href={SITE_URL}doctors/login>Login Here</a>\r\n\r\nP.S. Remember, we will never sell or pass to someone else your personal information or email address.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nSupport service' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'doctors_account_created_email_confirmation', code, 'New doctor account created (email confirmation)', 'Your account has been created (email confirmation required)', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nCongratulations on creating your new account.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nIn order to become authorized member, you will need to confirm your registration. You may follow the link below to access the confirmation page:\r\n<a href="{SITE_URL}doctors/confirmRegistration/code/{REGISTRATION_CODE}">Confirm Registration</a>\r\n\r\nP.S. Remember, we will never sell or pass to someone else your personal information or email address.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nSupport service' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'doctors_account_created_auto_approval', code, 'New doctor account created (auto approval)', 'Your account has been created and activated', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nCongratulations on creating your new account.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nYou may follow the link below to log into your account:\r\n<a href={SITE_URL}doctors/login>Login Here</a>\r\n\r\nP.S. Remember, we will never sell or pass to someone else your personal information or email address.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nSupport service' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'doctors_account_removed_by_doctor', code, 'Account removed (by doctor)', 'Your account has been removed', 'Dear {USERNAME}!\r\n\r\nYour account has been successfully removed according to your request.\r\n\r\n-\r\nBest Regards,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'doctors_password_forgotten', code, 'Restore forgotten password (by doctor)', 'Forgotten Password', 'Hello!\r\n\r\nYou or someone else asked to restore your login info on our site:\r\n<a href={SITE_URL}doctors/login>{WEB_SITE}</a>\r\n\r\nYour new login:\r\n---------------\r\nUsername: {USERNAME}\r\nPassword: {PASSWORD}\r\n\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'success_order', code, 'Success Order', 'Your order has been placed in our system!', 'Dear {FIRST_NAME} {LAST_NAME}!\r\n\r\nThank you for reservation request!\r\n\r\nYour order <b>{ORDER_NUMBER}</b> has been placed in our system and will be processed shortly.\r\nStatus: {STATUS}\r\n\r\nDate Created: {DATE_CREATED}\r\nPayment Date: {DATE_PAYMENT}\r\nPayment Type: {PAYMENT_TYPE}\r\nCurrency: {CURRENCY}\r\nPrice: {PRICE}\r\n\r\nThanks for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'paid_order', code, 'Membership Plan Paid', 'Payment of the membership plan has been successfully confirmed.', 'Dear {FIRST_NAME} {LAST_NAME}!\r\n\r\nThank you for your paynent!!\r\n\r\nYour order <b>{ORDER_NUMBER}</b> has been paid and <b>{MEMBERSHIP_PLAN}</b> membership plan was approved.\r\n\r\nOrder Status: {STATUS}\r\nDate Created: {DATE_CREATED}\r\nDate Status Changed: {STATUS_CHANGED}\r\n\r\nThanks for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_reserved_by_doctor', code, 'Appointment reserved (doctor copy)', 'A new patient appointment has been successfully reserved to you.', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nThis email is to confirm that a new patient appointment has been successfully reserved to you.\r\n\r\n{APPOINTMENT_DETAILS}\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_verified_by_doctor', code, 'Appointment verified (doctor copy)', 'The patient appointment reserved to you was confirmed.', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nThis email is to confirm that the patient appointment reserved to you was confirmed.\r\n\r\n{APPOINTMENT_DETAILS}\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_canceled_by_doctor', code, 'Appointment canceled (doctor copy)', 'Appointment has been canceled by patient/administration.', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nThis email is to confirm that your patient appointment has been canceled patient/administration.\r\n\r\n{APPOINTMENT_DETAILS}\r\n\r\nFor more information, please contact us.\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_changed_by_doctor', code, 'Appointment changed (doctor copy)', 'Patient appointment has been successfully changed.', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nThis email is to confirm that your patient appointment has been successfully changed.\r\n\r\n{APPOINTMENT_DETAILS}\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'reminder_expiries_membership_plan', code, 'Reminder of membership plan expiration', 'Your membership plan will expire soon.', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nWe remind you that accordint to the terms of your membership plan it expires {EXPIRIES_DATE}\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_doctor_reminder', code, 'Appointment Doctor Reminder', 'Reminder of appointments for the day', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nWe remind you about your appointments on {APPOINTMENTS_DATE} \r\n\r\n{APPOINTMENTS}\r\n\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
-- Patients --
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'patients_new_account_created_by_admin', code, 'New patient account created (by admin)', 'Your account has been created by administrator', 'Dear <b>{FIRST_NAME} {LAST_NAME}!</b>\r\n\r\nThe {WEB_SITE} Admin has invited you to contribute to our site.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nPlease follow the link below to log into your account: <a href={SITE_URL}patients/login>Login</a>.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'patients_password_changed_by_admin', code, 'Password for the patient account changed (by admin)', 'Your password has been changed by admin', 'Hello <b>{FIRST_NAME} {LAST_NAME}!</b>\r\n\r\nYour password has been changed by administrator of the site:\r\n{WEB_SITE}\r\n\r\nBelow your new login info:\r\n-\r\nUsername: {USERNAME} \r\nPassword: {PASSWORD}\r\n\r\n-\r\nBest Regards,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'patients_account_approved_by_admin', code, 'New patient account approved (by admin)', 'Your account has been approved', 'Dear <b>{FIRST_NAME} {LAST_NAME}!</b>\r\n\r\nCongratulations! This e-mail is to confirm that your registration at {WEB_SITE} has been approved.\r\n\r\nYou may now <a href={SITE_URL}patients/login>log into</a> your account.\r\n\r\nThank you for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'patients_account_created_notify_admin', code, 'New patient account created (notify admin)', 'New patient account has been created', "Hello Admin!\r\n\r\nA new patient has been registered on your site.\r\n\r\nThis email contains a patient account details:\r\n\r\nName: {FIRST_NAME} {LAST_NAME}\r\nEmail: {CUSTOMER_EMAIL}\r\nUsername: {USERNAME}\r\n\r\nP.S. Please check if it doesn't require your approval for activation." FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'patients_account_created_admin_approval', code, 'New patient account created (admin approval)', 'Your account has been created (admin approval required)', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nCongratulations on creating your new account.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nAfter your registration is approved by administrator, you could log into your account with a following link:\r\n<a href={SITE_URL}patients/login>Login Here</a>\r\n\r\nP.S. Remember, we will never sell or pass to someone else your personal information or email address.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nSupport service' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'patients_account_created_email_confirmation', code, 'New patient account created (email confirmation)', 'Your account has been created (email confirmation required)', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nCongratulations on creating your new account.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nIn order to become authorized member, you will need to confirm your registration. You may follow the link below to access the confirmation page:\r\n<a href="{SITE_URL}patients/confirmRegistration/code/{REGISTRATION_CODE}">Confirm Registration</a>\r\n\r\nP.S. Remember, we will never sell or pass to someone else your personal information or email address.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nSupport service' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'patients_account_created_auto_approval', code, 'New patient account created (auto approval)', 'Your account has been created and activated', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nCongratulations on creating your new account.\r\n\r\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\r\n\r\nYour login: {USERNAME}\r\nYour password: {PASSWORD}\r\n\r\nYou may follow the link below to log into your account:\r\n<a href={SITE_URL}patients/login>Login Here</a>\r\n\r\nP.S. Remember, we will never sell or pass to someone else your personal information or email address.\r\n\r\nEnjoy!\r\n-\r\nSincerely,\r\nSupport service' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'patients_account_removed_by_patient', code, 'Account removed (by patient)', 'Your account has been removed', 'Dear {USERNAME}!\r\n\r\nYour account has been successfully removed according to your request.\r\n\r\n-\r\nBest Regards,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'patients_password_forgotten', code, 'Restore forgotten password (by patient)', 'Forgotten Password', 'Hello!\r\n\r\nYou or someone else asked to restore your login info on our site:\r\n<a href={SITE_URL}patients/login>{WEB_SITE}</a>\r\n\r\nYour new login:\r\n---------------\r\nUsername: {USERNAME}\r\nPassword: {PASSWORD}\r\n\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_reserved_by_patient', code, 'Appointment reserved (patient copy)', 'You have reserved a new appointment.', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nThank you for order appointment!\r\n\r\nThis email is to confirm that you have reserved a new appointment.\r\n\r\n{APPOINTMENT_DETAILS}\r\n\r\n-\r\nThanks for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_verified_by_patient', code, 'Appointment verified (patient copy)', 'The appointment you have reserved was confirmed.', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nThank you for order appointment!\r\n\r\nThis email is to confirm that the appointment you have reserved was confirmed.\r\n\r\n{APPOINTMENT_DETAILS}\r\n\r\n-\r\nThanks for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_canceled_by_patient', code, 'Appointment canceled (patient copy)', 'Your appointment has been canceled by doctor/administration.', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nThis email is to confirm that your appointment has been canceled doctor/administration.\r\n\r\n{APPOINTMENT_DETAILS}\r\n\r\nFor more information, please contact us.\r\n\r\n-\r\nThanks for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_changed_by_patient', code, 'Appointment changed (patient copy)', 'Your appointment has been successfully changed.', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nThank you for order appointment!\r\n\r\nThis email is to confirm that your appointment has been successfully changed.\r\n\r\n{APPOINTMENT_DETAILS}\r\n\r\n-\r\nThanks for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'appointment_reminder', code, 'Appointment Reminder', 'Reminder of Appointment', 'Dear <b>{FULL_NAME}!</b>\r\n\r\nWe remind you that you have ordered an appointment.\r\n\r\n{APPOINTMENT_DETAILS}\r\n\r\n-\r\nThanks for choosing {WEB_SITE}.\r\n-\r\nSincerely,\r\nAdministration\r\n' FROM `<DB_PREFIX>languages`;

-- Administrator --
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'success_order_for_admin', code, 'Success Order (admin copy)', 'The order has been placed in system!', 'User <b>{FIRST_NAME} {LAST_NAME} ({USERNAME})</b>!\r\n\r\nThe order <b>{ORDER_NUMBER}</b> has been placed in system.\r\nStatus: {STATUS}\r\n\r\nDate Created: {DATE_CREATED}\r\nPayment Date: {DATE_PAYMENT}\r\nPayment Type: {PAYMENT_TYPE}\r\nCurrency: {CURRENCY}\r\nPrice: {PRICE}\r\n\r\n' FROM `<DB_PREFIX>languages`;

UPDATE `<DB_PREFIX>site_info` SET `header`='Doctor Appointments', `slogan`='Welcome to Doctor Appointments!', `footer`='&copy; 2018 Powered by <a class="footer_link" target="_blank" rel="noopener noreferrer" href="https://www.apphp.com">ApPHP</a>', `meta_title`='Doctor Appointments', `meta_description`='Doctor Appointments', `meta_keywords`='doctor appointments, medical appointments, medical framework, medical content management framework, medical cms, medical appointments cms', `site_address`='Medicure Ltd.<br>51, North Ave<br>New York, NY';


--create here module tables
DROP TABLE IF EXISTS `<DB_PREFIX>appt_appointments`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_appointments` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `appointment_number` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `appointment_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `doctor_id` int(10) unsigned NOT NULL DEFAULT '0',
  `doctor_specialty_id` int(10) unsigned NOT NULL DEFAULT '0',
  `doctor_address_id` int(10) unsigned NOT NULL DEFAULT '0',
  `patient_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NULL DEFAULT NULL,
  `appointment_date` date NULL DEFAULT NULL,
  `appointment_time` time NOT NULL DEFAULT '00:00:00',
  `visit_duration` tinyint(3) unsigned NOT NULL DEFAULT '15',
  `visit_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `doctor_internal_notes` text COLLATE utf8_unicode_ci NOT NULL,
  `doctor_external_notes` text COLLATE utf8_unicode_ci NOT NULL,
  `patient_internal_notes` text COLLATE utf8_unicode_ci NOT NULL,
  `patient_external_notes` text COLLATE utf8_unicode_ci NOT NULL,
  `for_whom` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1 - me, 2 - someone else',
  `for_whom_someone_else` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `first_visit` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 - New patient, 2 - Existing patient of this practice',
  `insurance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `visit_reason_id` int(10) unsigned NOT NULL DEFAULT '0',
  `other_reasons` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - reserved, 1 - verifyed, 2 - canceled',
  `status_review` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not exist review, 1 - exist review',
  `status_changed` datetime NULL DEFAULT NULL,
  `created_by` enum('owner','admin','doctor','patient') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'patient',
  `p_arrival_reminder_sent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `doctor_id` (`doctor_id`),
  KEY `patient_id` (`patient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `<DB_PREFIX>appt_appointments` (`id`, `appointment_number`, `appointment_description`, `doctor_id`, `doctor_specialty_id`, `doctor_address_id`, `patient_id`, `date_created`, `appointment_date`, `appointment_time`, `visit_duration`, `visit_price`, `doctor_internal_notes`, `doctor_external_notes`, `patient_internal_notes`, `patient_external_notes`, `for_whom`, `first_visit`, `insurance_id`, `visit_reason_id`, `status`, `status_changed`, `status_review`, `created_by`, `p_arrival_reminder_sent`) VALUES
(1, '12345-TEST', 'Appointment with a doctor', 1, 1, 1, 1, '2018-06-05 11:15:30', '2018-06-05', '12:00:00', 30, '98.00', '', '', '', '', 0, 1, 5, 0, 1, NULL, 0, 'patient', 0);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_clinics`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_clinics` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(32) CHARACTER SET latin1 NOT NULL,
  `fax` varchar(32) CHARACTER SET latin1 NOT NULL,
  `time_zone` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `longitude` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `latitude` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_default` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `<DB_PREFIX>appt_clinics` (`id`, `phone`, `fax`, `time_zone`, `longitude`, `latitude`, `is_default`, `is_active`, `sort_order`) VALUES
(1, '1-800-123-4567', '1-800-123-5689', 'Africa/Casablanca', '-73.9750139', '40.7390642', 1, 1, 0);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_clinic_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_clinic_translations` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `clinic_id` smallint(6) unsigned NOT NULL DEFAULT '1',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(2048) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

INSERT INTO `<DB_PREFIX>appt_clinic_translations` (`id`, `clinic_id`, `language_code`, `name`, `address`, `description`) SELECT NULL, 1, code, 'Default Clinic', '462 1st Avenue, New York, NY 10016', '<p>The <strong>Clinic Name</strong> is a full-service medical clinic with five Board Certified Family physicians, a Board Certified General/Gynecological Surgeon and four Board Certified mid-level providers (Physician&rsquo;s Assistant &amp; Nurse Practitioners).</p>' FROM <DB_PREFIX>languages;


DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctors`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL DEFAULT 0,
  `doctor_first_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `doctor_middle_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `doctor_last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gender` enum('f','m') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm',
  `birth_date` date NULL DEFAULT NULL,
  `title_id` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `work_phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `work_mobile_phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fax` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address_2` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zip_code` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `state` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `medical_degree_id` smallint(6) unsigned NOT NULL DEFAULT 0,
  `license_number` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `additional_degree` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `education` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `experience_years` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `residency_training` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hospital_affiliations` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `board_certifications` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `awards_and_publications` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `languages_spoken` varchar(125) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `insurances_accepted` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `default_visit_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `default_visit_duration` tinyint(3) unsigned NOT NULL DEFAULT '15',
  `membership_plan_id` int(10) unsigned NOT NULL DEFAULT '0',
  `membership_images_count` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `membership_clinics_count` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `membership_schedules_count` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `membership_specialties_count` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `membership_show_in_search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `membership_enable_reviews` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `last_membership_reminder_date` date NULL DEFAULT NULL,
  `last_reminded_date` date NULL DEFAULT NULL,
  `membership_expires` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `membership_plan_id` (`membership_plan_id`),
  KEY `medical_degree_id` (`medical_degree_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;


INSERT INTO `<DB_PREFIX>accounts` (`id`, `role`, `username`, `password`, `salt`, `token_expires_at`, `email`, `language_code`, `avatar`, `created_at`, `created_ip`, `last_visited_at`, `last_visited_ip`, `password_changed_at`, `notifications`, `notifications_changed_at`, `is_active`, `is_removed`, `comments`, `registration_code`) VALUES (NULL, 'doctor', 'doctor1', '1921a0fb5aad4577086262cb6fcb4fc1461e4a4cf2f12499593154c0e4f3a9b8', 'aSt/VJyNz1rTQHIMWrSseRHUAbv6cqRj', '', 'doctor1@exampe.com', 'en', 'doctor1.jpg', NULL, '000.000.000.000', NULL, '000.000.000.000', NULL, 0, NULL, 1, 0, '', '');
INSERT INTO `<DB_PREFIX>appt_doctors` (`id`, `account_id`, `doctor_first_name`, `doctor_middle_name`, `doctor_last_name`, `gender`, `birth_date`, `title_id`, `work_phone`, `work_mobile_phone`, `phone`, `fax`, `address`, `address_2`, `city`, `zip_code`, `country_code`, `state`, `medical_degree_id`, `license_number`, `additional_degree`, `education`, `experience_years`, `residency_training`, `hospital_affiliations`, `board_certifications`, `awards_and_publications`, `languages_spoken`, `insurances_accepted`, `default_visit_price`, `default_visit_duration`, `membership_plan_id`, `membership_images_count`, `membership_schedules_count`, `membership_specialties_count`, `membership_clinics_count`, `membership_show_in_search`, `membership_enable_reviews`, `last_membership_reminder_date`, `last_reminded_date`, `membership_expires`) VALUES (1, (SELECT MAX(id) FROM `<DB_PREFIX>accounts`), 'Jon', '', 'Carter', 'm', '1978-01-09', 1, '', '', '', '', '', '', '', '', 'US', '', 1, '', '', 'Medical School - New York Medical College, Saint Vincent''s Hospital, Internship in Internal Medicine', 6, '', '', 'American Board of Internal Medicine', 'Undergraduate Research Award: UTY-Chapel, 2003, Bachelor of Science in Biology with Honors: UTY-Chapel, 2003', 'ar;de;en', '1199SEIZ,AtnaZ,Blue Cross Shield', '75', '15',  3, 3, 2, 3, 3, 1, 1, NULL, NULL, NOW() + INTERVAL 1 YEAR);
INSERT INTO `<DB_PREFIX>accounts` (`id`, `role`, `username`, `password`, `salt`, `token_expires_at`, `email`, `language_code`, `avatar`, `created_at`, `created_ip`, `last_visited_at`, `last_visited_ip`, `password_changed_at`, `notifications`, `notifications_changed_at`, `is_active`, `is_removed`, `comments`, `registration_code`) VALUES (NULL, 'doctor', 'doctor2', '943d0a294b302095cec71c55a6a06167c850fa9728c1c8bbf5945e804be3027e', 'PaYf7dFoHIlHq8k/zOGlD+Pehbjv+U+P', '', 'doctor2@exampe.com', 'en', 'doctor2.jpg', NULL, '000.000.000.000', NULL, '000.000.000.000', NULL, 0, NULL, 1, 0, '', '');
INSERT INTO `<DB_PREFIX>appt_doctors` (`id`, `account_id`, `doctor_first_name`, `doctor_middle_name`, `doctor_last_name`, `gender`, `birth_date`, `title_id`, `work_phone`, `work_mobile_phone`, `phone`, `fax`, `address`, `address_2`, `city`, `zip_code`, `country_code`, `state`, `medical_degree_id`, `license_number`, `additional_degree`, `education`, `experience_years`, `residency_training`, `hospital_affiliations`, `board_certifications`, `awards_and_publications`, `languages_spoken`, `insurances_accepted`, `default_visit_price`, `default_visit_duration`, `membership_plan_id`, `membership_images_count`, `membership_schedules_count`, `membership_specialties_count`, `membership_clinics_count`, `membership_show_in_search`, `membership_enable_reviews`, `last_membership_reminder_date`, `last_reminded_date`, `membership_expires`) VALUES (2, (SELECT MAX(id) FROM `<DB_PREFIX>accounts`), 'Rosy', '', 'Gracey', 'f', '1965-01-07', 4, '050-123-4444', '050-455-2222', '', '', '', '', '', '', 'US', '', 3, '', '', 'Medical School - University of Pennsylvania, School of Medicine,Goverment Medical Centerrn', 4, '', 'Goverment Medical Center - Petrie Division, New York University Elaine A. and Kenneth G., Langone Medical Center', 'American Board of Internal Medicine', 'Dr. Smith was named in Castle Connolly’s "Top Doctors-New York Metro Area" 2006, 2007, 2008 2009, 2010.', 'en', '', '98', '20', 4, 5, 3, 5, 5, 1, 1, NULL, NULL, NOW() + INTERVAL 6 MONTH);
INSERT INTO `<DB_PREFIX>accounts` (`id`, `role`, `username`, `password`, `salt`, `token_expires_at`, `email`, `language_code`, `avatar`, `created_at`, `created_ip`, `last_visited_at`, `last_visited_ip`, `password_changed_at`, `notifications`, `notifications_changed_at`, `is_active`, `is_removed`, `comments`, `registration_code`) VALUES (NULL, 'doctor', 'doctor3', '', '', '', 'doctor3@exampe.com', 'en', 'doctor3.jpg', NULL, '000.000.000.000', NULL, '000.000.000.000', NULL, 0, NULL, 1, 0, '', '');
INSERT INTO `<DB_PREFIX>appt_doctors` (`id`, `account_id`, `doctor_first_name`, `doctor_middle_name`, `doctor_last_name`, `gender`, `birth_date`, `title_id`, `work_phone`, `work_mobile_phone`, `phone`, `fax`, `address`, `address_2`, `city`, `zip_code`, `country_code`, `state`, `medical_degree_id`, `license_number`, `additional_degree`, `education`, `experience_years`, `residency_training`, `hospital_affiliations`, `board_certifications`, `awards_and_publications`, `languages_spoken`, `insurances_accepted`, `default_visit_price`, `default_visit_duration`, `membership_plan_id`, `membership_images_count`, `membership_schedules_count`, `membership_specialties_count`, `membership_clinics_count`, `membership_show_in_search`, `membership_enable_reviews`, `last_membership_reminder_date`, `last_reminded_date`, `membership_expires`) VALUES (3, (SELECT MAX(id) FROM `<DB_PREFIX>accounts`), 'John', '', 'Smith', 'm', '1965-01-07', 1, '050-123-5555', '050-455-3333', '', '', '', '', '', '', 'US', '', 3, '', '', 'Medical School - University of Pennsylvania, School of Medicine,Goverment Medical Centerrn', 4, '', 'Goverment Medical Center - Petrie Division, New York University Elaine A. and Kenneth G., Langone Medical Center', 'American Board of Internal Medicine', 'Dr. Smith was named in Castle Connolly’s "Top Doctors-New York Metro Area" 2006, 2007, 2008 2009.', 'ar;de', '', '98', '20', 2, 2, 1, 2, 2, 1, 1, NULL, NULL, NOW() + INTERVAL 2 YEAR);
INSERT INTO `<DB_PREFIX>accounts` (`id`, `role`, `username`, `password`, `salt`, `token_expires_at`, `email`, `language_code`, `avatar`, `created_at`, `created_ip`, `last_visited_at`, `last_visited_ip`, `password_changed_at`, `notifications`, `notifications_changed_at`, `is_active`, `is_removed`, `comments`, `registration_code`) VALUES (NULL, 'doctor', 'doctor4', '', '', '', 'doctor4@exampe.com', 'en', 'doctor4.jpg', NULL, '000.000.000.000', NULL, '000.000.000.000', NULL, 0, NULL, 1, 0, '', '');
INSERT INTO `<DB_PREFIX>appt_doctors` (`id`, `account_id`, `doctor_first_name`, `doctor_middle_name`, `doctor_last_name`, `gender`, `birth_date`, `title_id`, `work_phone`, `work_mobile_phone`, `phone`, `fax`, `address`, `address_2`, `city`, `zip_code`, `country_code`, `state`, `medical_degree_id`, `license_number`, `additional_degree`, `education`, `experience_years`, `residency_training`, `hospital_affiliations`, `board_certifications`, `awards_and_publications`, `languages_spoken`, `insurances_accepted`, `default_visit_price`, `default_visit_duration`, `membership_plan_id`, `membership_images_count`, `membership_schedules_count`, `membership_specialties_count`, `membership_clinics_count`, `membership_show_in_search`, `membership_enable_reviews`, `last_membership_reminder_date`, `last_reminded_date`, `membership_expires`) VALUES (4, (SELECT MAX(id) FROM `<DB_PREFIX>accounts`), 'Robet', '', 'Dova', 'm', '1968-03-02', 1, '050-123-6666', '050-455-4444', '', '', '', '', '', '', 'US', '', 3, '', '', 'Medical Institute - Pennsylvania, School of Medicine,Goverment Medical Centerrn', 4, '', 'Goverment Medical Center - Petrie Division, New York University Elaine A. and Kenneth G., Langone Medical Center', 'American Board of Internal Medicine', 'Dr. Smith was named in Castle Connolly’s "Top Doctors-New York Metro Area" 2006, 2007, 2008 2009.', 'de;en', '', '98', '20', 1, 0, 1, 1, 1, 1, 0, NULL, NULL, NOW() + INTERVAL 1 YEAR);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_images`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_doctor_images` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_file_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(6) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

INSERT INTO `<DB_PREFIX>appt_doctor_images` (`id`, `doctor_id`, `title`, `image_file`, `image_file_thumb`, `sort_order`, `is_active`) VALUES (1,	1,	'1 photo',	'd1_1.jpg',	'd1_1_thumb.jpg',	0,	1);
INSERT INTO `<DB_PREFIX>appt_doctor_images` (`id`, `doctor_id`, `title`, `image_file`, `image_file_thumb`, `sort_order`, `is_active`) VALUES (2,	1,	'2 photo',	'd1_2.jpg',	'd1_2_thumb.jpg',	1,	1);
INSERT INTO `<DB_PREFIX>appt_doctor_images` (`id`, `doctor_id`, `title`, `image_file`, `image_file_thumb`, `sort_order`, `is_active`) VALUES (3,	1,	'3 photo',	'd1_3.jpg',	'd1_3_thumb.jpg',	2,	1);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_specialties`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_doctor_specialties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(10) unsigned NOT NULL DEFAULT '0',
  `specialty_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `specialty_id` (`specialty_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7;

INSERT INTO `<DB_PREFIX>appt_doctor_specialties` (`id`, `doctor_id`, `specialty_id`, `sort_order`, `is_default`) VALUES
(1, 1, 12, 1, 0),
(2, 1, 1, 0, 1),
(3, 1, 29, 1, 0),
(4, 2, 5, 0, 0),
(5, 2, 9, 1, 1),
(6, 3, 1, 0, 1),
(7, 4, 7, 0, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_schedules`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_doctor_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `doctor_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_from` date NULL DEFAULT NULL,
  `date_to` date NULL DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6;

INSERT INTO `<DB_PREFIX>appt_doctor_schedules` (`id`, `name`, `doctor_id`, `date_from`, `date_to`, `is_active`) VALUES
(1, '2018 - Summer', 1, '2018-06-01', '2018-08-31', 1),
(2, '2018 - Autumn', 1, '2018-09-01', '2018-11-30', 1),
(3, '2018 - Spring', 1, '2018-03-01', '2018-05-31', 1),
(4, '2018 Half-Year #1', 2, '2018-01-01', '2018-05-31', 1),
(5, '2018 Half-Year #2', 2, '2018-06-01', '2018-12-31', 1);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_schedule_timeblocks`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_doctor_schedule_timeblocks` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(10) unsigned NOT NULL DEFAULT '0',
  `schedule_id` int(11) NOT NULL DEFAULT '0',
  `address_id` int(11) NOT NULL DEFAULT '0',
  `week_day` enum('1','2','3','4','5','6','7') CHARACTER SET latin1 NOT NULL DEFAULT '1',
  `time_from` time NOT NULL DEFAULT '00:00:00',
  `time_to` time NOT NULL DEFAULT '00:00:00',
  `time_slots` varchar(3) CHARACTER SET latin1 NOT NULL DEFAULT '15',
  PRIMARY KEY (`id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=42 ;

INSERT INTO `<DB_PREFIX>appt_doctor_schedule_timeblocks` (`id`, `doctor_id`, `schedule_id`, `address_id`, `week_day`, `time_from`, `time_to`, `time_slots`) VALUES
(1, 1, 1, 1, '2', '08:00:00', '14:00:00', '15'),
(2, 1, 1, 1, '2', '15:00:00', '20:00:00', '20'),
(3, 1, 1, 1, '3', '08:30:00', '15:00:00', '15'),
(4, 1, 1, 1, '5', '08:00:00', '14:00:00', '15'),
(5, 1, 1, 1, '4', '10:00:00', '12:00:00', '20'),
(6, 1, 1, 1, '6', '16:00:00', '21:00:00', '15'),
(7, 1, 2, 1, '2', '09:00:00', '15:00:00', '15'),
(8, 1, 2, 1, '3', '09:30:00', '17:00:00', '20'),
(9, 1, 2, 1, '5', '08:00:00', '13:00:00', '30'),
(10, 1, 2, 1, '6', '09:00:00', '15:00:00', '15'),
(11, 1, 3, 1, '2', '07:30:00', '14:00:00', '15'),
(12, 1, 3, 1, '2', '15:00:00', '18:00:00', '20'),
(13, 1, 3, 1, '3', '08:00:00', '14:00:00', '15'),
(14, 1, 3, 1, '4', '15:00:00', '20:00:00', '15'),
(15, 1, 3, 1, '5', '08:00:00', '14:00:00', '15'),
(16, 1, 3, 1, '6', '08:00:00', '15:00:00', '15'),
(17, 2, 4, 1, '2', '09:00:00', '14:00:00', '15'),
(18, 2, 4, 1, '2', '16:00:00', '20:00:00', '20'),
(19, 2, 4, 1, '3', '10:00:00', '17:00:00', '15'),
(20, 2, 4, 1, '4', '09:00:00', '15:00:00', '15'),
(21, 2, 4, 1, '5', '10:00:00', '17:00:00', '15'),
(22, 2, 4, 1, '6', '08:00:00', '12:00:00', '15'),
(23, 2, 5, 1, '2', '08:00:00', '16:00:00', '15'),
(24, 2, 5, 1, '4', '09:00:00', '14:00:00', '15'),
(25, 2, 5, 1, '4', '16:00:00', '19:00:00', '15'),
(26, 2, 5, 1, '5', '12:00:00', '20:00:00', '15'),
(27, 2, 5, 1, '6', '09:00:00', '17:00:00', '15');


DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_timeoffs`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_doctor_timeoffs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_from` date NULL DEFAULT NULL,
  `time_from` time NOT NULL DEFAULT '00:00:00',
  `date_to` date NULL DEFAULT NULL,
  `time_to` time NOT NULL DEFAULT '00:00:00',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `<DB_PREFIX>appt_doctor_timeoffs` (`id`, `doctor_id`, `date_from`, `time_from`, `date_to`, `time_to`, `description`) VALUES
(1, 1, '2018-05-01', '00:50:00', '2018-06-23', '09:00:00', 'Vacations');

DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_reviews`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_doctor_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(10) unsigned NOT NULL DEFAULT '0',
  `patient_id` int(10) unsigned NOT NULL DEFAULT '0',
  `appointment_id` int(10) unsigned NOT NULL DEFAULT '0',
  `patient_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `patient_name` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `rating_price` tinyint(1) NOT NULL DEFAULT '0',
  `rating_wait_time` tinyint(1) NOT NULL DEFAULT '0',
  `rating_bedside_manner` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `patient_id` (`patient_id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5;

INSERT INTO `<DB_PREFIX>appt_doctor_reviews` (`id`, `doctor_id`, `patient_id`, `appointment_id`, `patient_email`, `patient_name`, `message`, `rating_price`, `rating_wait_time`, `rating_bedside_manner`, `created_at`, `status`) VALUES
(1, 1, 1, 1, 'patient1@exampe.com', 'patient1', 'Doctor is very professional, he listen and give good advice to how to resolve my  problem in a good way.', 5, 4, 4, NOW() - INTERVAL 1 MONTH, 1),
(2, 1, 1, 1, 'patient1@exampe.com', 'patient1', 'Doctor is very professional, he listen and give good advice to how to resolve my  problem in a good way.', 5, 5, 4, NOW() - INTERVAL 15 DAY, 1),
(3, 1, 1, 1, 'patient1@exampe.com', 'patient1', 'Doctor is very professional, he listen and give good advice to how to resolve my  problem in a good way.', 5, 4, 5, NOW() - INTERVAL 10 DAY, 1),
(4, 1, 1, 1, 'patient1@exampe.com', 'patient1', 'Doctor is very professional, he listen and give good advice to how to resolve my  problem in a good way.', 5, 4, 5, NOW() - INTERVAL 5 DAY, 1);

DROP TABLE IF EXISTS `<DB_PREFIX>appt_doctor_clinics`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_doctor_clinics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) unsigned NOT NULL DEFAULT '0',
  `clinic_id` int(11) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `clinic_id` (`clinic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5;

INSERT INTO `<DB_PREFIX>appt_doctor_clinics` (`id`, `doctor_id`, `clinic_id`, `sort_order`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 0),
(3, 3, 1, 0),
(4, 4, 1, 0);

DROP TABLE IF EXISTS `<DB_PREFIX>appt_patients`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL DEFAULT 0,
  `patient_first_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `patient_last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gender` enum('f','m') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm',
  `birth_date` date NULL DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fax` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address_2` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zip_code` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `state` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;

INSERT INTO `<DB_PREFIX>accounts` (`id`, `role`, `username`, `password`, `salt`, `token_expires_at`, `email`, `language_code`, `avatar`, `created_at`, `created_ip`, `last_visited_at`, `last_visited_ip`, `notifications`, `notifications_changed_at`, `is_active`, `is_removed`, `comments`, `registration_code`) VALUES
(NULL, 'patient', 'patient1', '1921a0fb5aad4577086262cb6fcb4fc1461e4a4cf2f12499593154c0e4f3a9b8', 'aSt/VJyNz1rTQHIMWrSseRHUAbv6cqRj', '', 'patient1@exampe.com', 'en', '', NULL, '', NULL, '000.000.000.000', 0, NULL, 1, 0, '', '');
INSERT INTO `<DB_PREFIX>appt_patients` (`id`, `account_id`, `patient_first_name`, `patient_last_name`, `gender`, `birth_date`, `phone`, `fax`, `address`, `address_2`, `city`, `zip_code`, `country_code`, `state`) VALUES
(1, (SELECT MAX(id) FROM `<DB_PREFIX>accounts`), 'Donald', 'Johnson', 'm', '1981-12-17', '125-121-55', '', 'Green street 41', '', 'New York', '876-54321', 'US', 'FL');
INSERT INTO `<DB_PREFIX>accounts` (`id`, `role`, `username`, `password`, `salt`, `token_expires_at`, `email`, `language_code`, `avatar`, `created_at`, `created_ip`, `last_visited_at`, `last_visited_ip`, `notifications`, `notifications_changed_at`, `is_active`, `is_removed`, `comments`, `registration_code`) VALUES
(NULL, 'patient', 'patient2', '943d0a294b302095cec71c55a6a06167c850fa9728c1c8bbf5945e804be3027e', 'PaYf7dFoHIlHq8k/zOGlD+Pehbjv+U+P', '', 'patient2@exampe.com', 'en', '', NULL, '', NULL, '000.000.000.000', 0, NULL, 1, 0, '', '');
INSERT INTO `<DB_PREFIX>appt_patients` (`id`, `account_id`, `patient_first_name`, `patient_last_name`, `gender`, `birth_date`, `phone`, `fax`, `address`, `address_2`, `city`, `zip_code`, `country_code`, `state`) VALUES
(2, (SELECT MAX(id) FROM `<DB_PREFIX>accounts`), 'Ronald', 'Orgunson', 'm', '1989-01-11', '114-232-11', '', 'Green street 41', '', 'New York', '12345-678', 'US', 'FL');


DROP TABLE IF EXISTS `<DB_PREFIX>appt_specialties`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_specialties` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=35;

INSERT INTO `<DB_PREFIX>appt_specialties` (`id`, `is_active`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_specialty_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_specialty_translations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `specialty_id` int(10) NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 1, code, 'Allergist (Immunologist)', 'An allergist/immunologist is a medical doctor with specialty training in the diagnosis and treatment of allergic diseases, asthma and diseases of the immune system.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 2, code, 'Cardiologist (Heart Doctor)', 'Cardiologists are doctors who specialize in diagnosing and treating diseases or conditions of the heart and blood vessels—the cardiovascular system.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 3, code, 'Dentist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 4, code, 'Dermatologist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 5, code, 'Dietitian', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 6, code, 'Ear, Nose & Throat Doctor (ENT)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 7, code, 'Endocrinologist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 8, code, 'Eye Doctor', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 9, code, 'Gastroenterologist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 10, code, 'Hematologist (Blood Specialist)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 11, code, 'Infectious Disease Specialist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 12, code, 'Nephrologist (Kidney Specialist)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 13, code, 'Neurologist (incl Headache Specialists)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 14, code, 'OB-GYN (Obstetrician-Gynecologist)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 15, code, 'Ophthalmologist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 16, code, 'Optometrist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 17, code, 'Orthodontist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 18, code, 'Orthopedic Surgeon (Orthopedist)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 19, code, 'Pain Management Specialist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 20, code, 'Pediatric Dentist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 21, code, 'Pediatrician', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 22, code, 'Physical Therapist (Physical Medicine)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 23, code, 'Plastic Surgeon', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 24, code, 'Podiatrist (Foot Specialist)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 25, code, 'Primary Care Doctor (General Practitioner)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 26, code, 'Prosthodontist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 27, code, 'Psychiatrist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 28, code, 'Psychologist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 29, code, 'Pulmonologist (Lung Doctor)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 30, code, 'Radiologist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 31, code, 'Rheumatologist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 32, code, 'Sleep Medicine Specialist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 33, code, 'Sports Medicine Specialist', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_specialty_translations` (`id`, `specialty_id`, `language_code`, `name`, `description`) SELECT NULL, 34, code, 'Urologist', '' FROM <DB_PREFIX>languages;


DROP TABLE IF EXISTS `<DB_PREFIX>appt_insurance`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_insurance` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10;

INSERT INTO `<DB_PREFIX>appt_insurance` (`id`, `is_active`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_insurance_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_insurance_translations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `insurance_id` int(10) NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `<DB_PREFIX>appt_insurance_translations` (`id`, `insurance_id`, `language_code`, `name`, `description`) SELECT NULL, 1, code, 'Aetna', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_insurance_translations` (`id`, `insurance_id`, `language_code`, `name`, `description`) SELECT NULL, 2, code, 'Anthem Blue Cross Blue Shield', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_insurance_translations` (`id`, `insurance_id`, `language_code`, `name`, `description`) SELECT NULL, 3, code, 'Blue Cross Blue Shield', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_insurance_translations` (`id`, `insurance_id`, `language_code`, `name`, `description`) SELECT NULL, 4, code, 'Cigna', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_insurance_translations` (`id`, `insurance_id`, `language_code`, `name`, `description`) SELECT NULL, 5, code, 'Empire Blue Cross Blue Shield', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_insurance_translations` (`id`, `insurance_id`, `language_code`, `name`, `description`) SELECT NULL, 6, code, 'GHI', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_insurance_translations` (`id`, `insurance_id`, `language_code`, `name`, `description`) SELECT NULL, 7, code, 'HIP', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_insurance_translations` (`id`, `insurance_id`, `language_code`, `name`, `description`) SELECT NULL, 8, code, 'UnitedHealthcare', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_insurance_translations` (`id`, `insurance_id`, `language_code`, `name`, `description`) SELECT NULL, 9, code, 'UnitedHealthcare Oxford', '' FROM <DB_PREFIX>languages;


DROP TABLE IF EXISTS `<DB_PREFIX>appt_visit_reasons`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_visit_reasons` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12;

INSERT INTO `<DB_PREFIX>appt_visit_reasons` (`id`, `sort_order`, `is_active`) VALUES
(1, 10, 1),
(2, 9, 1),
(3, 8, 1),
(4, 7, 1),
(5, 6, 1),
(6, 5, 1),
(7, 4, 1),
(8, 3, 1),
(9, 2, 1),
(10, 1, 1),
(11, 11, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_visit_reason_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_visit_reason_translations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `reason_id` int(10) NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 1, code, 'Illness', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 2, code, 'General Consultation', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 3, code, 'General Follow Up', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 4, code, 'Annual Physical', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 5, code, 'Cardiovascular Screening Visit', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 6, code, 'Flu Shot', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 7, code, 'Pre-Surgery Checkup', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 8, code, 'Pre-Travel Checkup', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 9, code, 'Prescription / Refill', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 10, code, 'STD (Sexually Transmitted Disease)', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_visit_reason_translations` (`id`, `reason_id`, `language_code`, `name`, `description`) SELECT NULL, 11, code, 'Other', '' FROM <DB_PREFIX>languages;


DROP TABLE IF EXISTS `<DB_PREFIX>appt_titles`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_titles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12;

INSERT INTO `<DB_PREFIX>appt_titles` (`id`, `sort_order`, `is_active`) VALUES
(1, 0, 1),
(2, 1, 1),
(3, 2, 1),
(4, 3, 1),
(5, 4, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_title_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_title_translations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title_id` int(10) NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `<DB_PREFIX>appt_title_translations` (`id`, `title_id`, `language_code`, `title`) SELECT NULL, 1, code, 'Mr.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_title_translations` (`id`, `title_id`, `language_code`, `title`) SELECT NULL, 2, code, 'Sir' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_title_translations` (`id`, `title_id`, `language_code`, `title`) SELECT NULL, 3, code, 'Ms.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_title_translations` (`id`, `title_id`, `language_code`, `title`) SELECT NULL, 4, code, 'Mrs.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_title_translations` (`id`, `title_id`, `language_code`, `title`) SELECT NULL, 5, code, 'Miss' FROM <DB_PREFIX>languages;


DROP TABLE IF EXISTS `<DB_PREFIX>appt_degrees`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_degrees` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12;

INSERT INTO `<DB_PREFIX>appt_degrees` (`id`, `title`, `sort_order`, `is_active`) VALUES
(1, 'MBBS', 20, 1),
(2, 'BDS', 19, 1),
(3, 'MBChB', 18, 1),
(4, 'MB BCh', 17, 1),
(5, 'BMed', 16, 1),
(6, 'MD', 15, 1),
(7, 'MDCM', 14, 1),
(8, 'Dr.MuD', 13, 1),
(9, 'Dr.Med', 12, 1),
(10, 'Cand.med', 11, 1),
(11, 'DO', 1, 0),
(12, 'MCM', 2, 0),
(13, 'MMSc', 3, 0),
(14, 'MM', 4, 0),
(15, 'MS', 5, 0),
(16, 'MSc', 6, 0),
(17, 'DCM', 7, 0),
(18, 'DClinSurg', 8, 0),
(19, 'NP', 9, 0),
(20, 'CNM', 10, 0);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_degree_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_degree_translations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `degree_id` int(10) NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 1, code, 'Bachelor of Medicine, Bachelor of Surgery', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 2, code, 'Bachelor of Dental Surgery', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 3, code, 'Bachelor of Medicine and Bachelor of Surgery', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 4, code, 'Bachelor of Medicine, Bachelor of Surgery', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 5, code, 'Bachelor of Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 6, code, 'Doctor of Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 7, code, 'Doctor of Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 8, code, 'Doctor of Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 9, code, 'Doctor of Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 10, code, 'Candidate of Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 11, code, 'Doctor of Osteopathic Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 12, code, 'Master of Clinical Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 13, code, 'Master of Medical Science', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 14, code, 'Master of Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 15, code, 'Master of Surgery', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 16, code, 'Master of Science', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 17, code, 'Doctor of Clinical Medicine', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 18, code, 'Doctor of Clinical Surgery', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 19, code, 'Nurse practitioner', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_degree_translations` (`id`, `degree_id`, `language_code`, `name`, `description`) SELECT NULL, 20, code, 'Certified nurse midwife', '' FROM <DB_PREFIX>languages;


DROP TABLE IF EXISTS `<DB_PREFIX>appt_membership_plans`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_membership_plans` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `images_count` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `clinics_count` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `schedules_count` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `specialties_count` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `duration` smallint(6) NOT NULL DEFAULT '1' COMMENT 'in days',
  `show_in_search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `enable_reviews` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `update_doctor_features` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

INSERT INTO `<DB_PREFIX>appt_membership_plans` (`id`, `images_count`, `clinics_count`, `schedules_count`, `specialties_count`,  `price`, `duration`, `show_in_search`, `enable_reviews`, `is_default`, `is_active`, `update_doctor_features`) VALUES
(1, 1, 1, 1, 1, '0.00', 365, 1, 0, 1, 1, 0),
(2, 2, 1, 2, 2, '50.00', 730, 1, 1, 0, 1, 0),
(3, 3, 2, 3, 3, '100.00', 1460, 1, 1, 0, 1, 0),
(4, 5, 3, 5, 5, '200.00', 1825, 1, 1, 0, 1, 0);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_membership_plans_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_membership_plans_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `membership_plan_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `membership_plan_id` (`membership_plan_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5;

INSERT INTO `<DB_PREFIX>appt_membership_plans_translations` (`id`, `membership_plan_id`, `language_code`, `name`, `description`) SELECT NULL, 1, code, 'Free', 'Free membership plan. This plan offers a minimal features, but free.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_membership_plans_translations` (`id`, `membership_plan_id`, `language_code`, `name`, `description`) SELECT NULL, 2, code, 'Bronze', 'A step up from free membership plan. More features available.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_membership_plans_translations` (`id`, `membership_plan_id`, `language_code`, `name`, `description`) SELECT NULL, 3, code, 'Silver', 'More features and details are allowed and are listed higher in results.' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_membership_plans_translations` (`id`, `membership_plan_id`, `language_code`, `name`, `description`) SELECT NULL, 4, code, 'Gold', 'This membership plan provides maximum features and benefits.' FROM <DB_PREFIX>languages;


DROP TABLE IF EXISTS `<DB_PREFIX>appt_orders`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `order_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `vat_percent` decimal(4,2) NOT NULL DEFAULT '0.00',
  `vat_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'USD',
  `membership_plan_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `doctor_id` int(11) NOT NULL DEFAULT '0',
  `patient_id` int(11) NOT NULL DEFAULT '0',
  `appointment_id` int(11) NOT NULL DEFAULT '0',
  `transaction_number` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `payment_date` datetime NULL DEFAULT NULL,
  `payment_id` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `payment_method` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - Payment Company Account, 1 - Credit Card, 2 - E-Check',
  `coupon_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount_campaign_id` int(10) DEFAULT '0',
  `additional_info` text COLLATE utf8_unicode_ci NOT NULL,
  `cc_type` varchar(20) CHARACTER SET latin1 NOT NULL,
  `cc_holder_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cc_number` varchar(50) CHARACTER SET latin1 NOT NULL,
  `cc_expires_month` varchar(2) CHARACTER SET latin1 NOT NULL,
  `cc_expires_year` varchar(4) CHARACTER SET latin1 NOT NULL,
  `cc_cvv_code` varchar(4) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - preparing, 1 - pending, 2 - paid, 3 - refunded 4 - canceled',
  `status_changed` datetime NULL DEFAULT NULL,
  `email_sent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `payer` enum('doctor','patient') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'doctor',
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `status` (`status`),
  KEY `membership_plan_id` (`membership_plan_id`),
  KEY `payer` (`payer`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT = 4;

INSERT INTO `<DB_PREFIX>appt_orders` (`id`, `order_number`, `order_description`, `order_price`, `vat_percent`, `vat_fee`, `total_price`, `currency`, `membership_plan_id`, `doctor_id`, `patient_id`, `appointment_id`, `transaction_number`, `created_date`, `payment_date`, `payment_id`, `payment_method`, `coupon_number`, `discount_campaign_id`, `additional_info`, `cc_type`, `cc_holder_name`, `cc_number`, `cc_expires_month`, `cc_expires_year`, `cc_cvv_code`, `status`, `status_changed`, `email_sent`, `payer`) VALUES
(1, 'TEST-UK9A', '', '100.00', '0.00', '0.00', '100.00', 'USD', '3', '1', '0', '0', 'kajll78plk32', '2018-08-02 20:11:43', '2018-08-02 20:21:15', '1', '1', '', '0', '', '', '', '', '', '', '', '2', '2017-08-02 20:23:33', '0', 'doctor'),
(2, 'TEST-K29N', '', '200.00', '0.00', '0.00', '200.00', 'USD', '4', '2', '1', '1', 'd9akni9nk12f', '2018-08-03 07:26:11', '2018-08-03 20:27:15', '1', '1', '', '0', '', '', '', '', '', '', '', '2', '2017-08-03 07:30:33', '0', 'doctor'),
(3, 'TEST-Z34T', '', '50.00', '0.00', '0.00', '50.00', 'USD', '2', '3', '1', '1', 'sdf3wer5335r', '2018-08-04 06:34:44', '2018-08-04 06:34:44', '1', '1', '', '0', '', '', '', '', '', '', '', '2', '2017-08-03 07:30:33', '0', 'doctor');


DROP TABLE IF EXISTS `<DB_PREFIX>appt_working_hours`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_working_hours` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `clinic_id` smallint(6) unsigned NOT NULL DEFAULT '1',
  `week_day` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `start_time` varchar(5) NOT NULL DEFAULT '00:00',
  `end_time` varchar(5) NOT NULL DEFAULT '00:00',
  `is_day_off` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

INSERT INTO `<DB_PREFIX>appt_working_hours` (`id`, `clinic_id`, `week_day`, `start_time`, `end_time`, `is_day_off`) VALUES
(1, 1, 1, '09:30', '15:00', 0),
(2, 1, 2, '08:00', '17:00', 0),
(3, 1, 3, '08:00', '17:00', 0),
(4, 1, 4, '08:00', '17:00', 0),
(5, 1, 5, '08:00', '17:00', 0),
(6, 1, 6, '08:00', '17:00', 0),
(7, 1, 7, '09:30', '17:30', 0);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_services`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_services` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `image_file` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(6) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

INSERT INTO `<DB_PREFIX>appt_services` (`id`, `image_file`, `sort_order`, `is_active`) VALUES
(1, 'service1.jpg', 0, 1),
(2, 'service2.jpg', 1, 1),
(3, 'service3.jpg', 2, 1),
(4, 'service4.jpg', 3, 1),
(5, 'service5.jpg', 4, 1),
(6, 'service6.jpg', 5, 1),
(7, 'service7.jpg', 6, 1),
(8, 'service8.jpg', 7, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>appt_services_translations`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>appt_services_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `language_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

INSERT INTO `<DB_PREFIX>appt_services_translations` (`id`, `service_id`, `language_code`, `name`, `description`, `tags`) SELECT NULL, 1, code, 'Pediatric Clinic', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in aliquam risus. Suspendisse ac ultrices ante. Phasellus malesuada aliquam odio, in laoreet lectus sagittis ut. Duis gravida id sapien sit amet luctus.', 'Pediatric,Therapy' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_services_translations` (`id`, `service_id`, `language_code`, `name`, `description`, `tags`) SELECT NULL, 2, code, 'Gynecological Clinic', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in aliquam risus. Suspendisse ac ultrices ante. Phasellus malesuada aliquam odio, in laoreet lectus sagittis ut. Duis gravida id sapien sit amet luctus.', 'Therapy' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_services_translations` (`id`, `service_id`, `language_code`, `name`, `description`, `tags`) SELECT NULL, 3, code, 'Laboratory Analysis', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in aliquam risus. Suspendisse ac ultrices ante. Phasellus malesuada aliquam odio, in laoreet lectus sagittis ut. Duis gravida id sapien sit amet luctus.', 'Diagnosis,Pharmacy' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_services_translations` (`id`, `service_id`, `language_code`, `name`, `description`, `tags`) SELECT NULL, 4, code, 'Cardiac Clinic', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in aliquam risus. Suspendisse ac ultrices ante. Phasellus malesuada aliquam odio, in laoreet lectus sagittis ut. Duis gravida id sapien sit amet luctus.', 'Diagnosis,Therapy' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_services_translations` (`id`, `service_id`, `language_code`, `name`, `description`, `tags`) SELECT NULL, 5, code, 'Diagnosis Clinic', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in aliquam risus. Suspendisse ac ultrices ante. Phasellus malesuada aliquam odio, in laoreet lectus sagittis ut. Duis gravida id sapien sit amet luctus.', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_services_translations` (`id`, `service_id`, `language_code`, `name`, `description`, `tags`) SELECT NULL, 6, code, 'Rehabilitation Therapy', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in aliquam risus. Suspendisse ac ultrices ante. Phasellus malesuada aliquam odio, in laoreet lectus sagittis ut. Duis gravida id sapien sit amet luctus.', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_services_translations` (`id`, `service_id`, `language_code`, `name`, `description`, `tags`) SELECT NULL, 7, code, 'Medical Counseling', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in aliquam risus. Suspendisse ac ultrices ante. Phasellus malesuada aliquam odio, in laoreet lectus sagittis ut. Duis gravida id sapien sit amet luctus.', '' FROM <DB_PREFIX>languages;
INSERT INTO `<DB_PREFIX>appt_services_translations` (`id`, `service_id`, `language_code`, `name`, `description`, `tags`) SELECT NULL, 8, code, 'Psychological Counseling', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in aliquam risus. Suspendisse ac ultrices ante. Phasellus malesuada aliquam odio, in laoreet lectus sagittis ut. Duis gravida id sapien sit amet luctus.', '' FROM <DB_PREFIX>languages;


INSERT INTO `<DB_PREFIX>search_categories` (`id`, `module_code`, `category_code`, `category_name`, `callback_class`, `callback_method`, `items_count`, `sort_order`, `is_active`) VALUES (NULL, 'appointments', 'services', 'Services', 'Modules\\Appointments\\Models\\Services', 'search', '20', (SELECT COUNT(sc.id) + 1 FROM `<DB_PREFIX>search_categories` sc), 1);
INSERT INTO `<DB_PREFIX>search_categories` (`id`, `module_code`, `category_code`, `category_name`, `callback_class`, `callback_method`, `items_count`, `sort_order`, `is_active`) VALUES (NULL, 'appointments', 'doctors', 'Doctors', 'Modules\\Appointments\\Models\\Doctors', 'search', '20', (SELECT COUNT(sc.id) + 1 FROM `<DB_PREFIX>search_categories` sc), 1);