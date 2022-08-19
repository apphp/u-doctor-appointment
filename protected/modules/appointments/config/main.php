<?php
// Determine the number of clinics with which the module operates.
$multiClinics = true;

return array(
    // Module classes
    // Full class name with namespace => Class name
    'classes' => array(
        'Modules\Appointments\Components\AppointmentsComponent',
		'Modules\Appointments\Components\DoctorsComponent',
		'Modules\Appointments\Controllers\Appointments',
        'Modules\Appointments\Controllers\Clinics',
		'Modules\Appointments\Controllers\DateTimeSchedules',
		'Modules\Appointments\Controllers\DoctorClinics',
		'Modules\Appointments\Controllers\DoctorImages',
		'Modules\Appointments\Controllers\Doctors',
		'Modules\Appointments\Controllers\DoctorSchedules',
		'Modules\Appointments\Controllers\DoctorSpecialties',
		'Modules\Appointments\Controllers\DoctorTimeoffs',
		'Modules\Appointments\Controllers\DoctorReviews',
		'Modules\Appointments\Controllers\Home',
		'Modules\Appointments\Controllers\IntegrationWidgets',
		'Modules\Appointments\Controllers\MasterData',
		'Modules\Appointments\Controllers\Memberships',
		'Modules\Appointments\Controllers\Mobile',
		'Modules\Appointments\Controllers\Orders',
		'Modules\Appointments\Controllers\Patients',
		'Modules\Appointments\Controllers\Services',
		'Modules\Appointments\Controllers\Statistics',
		'Modules\Appointments\Controllers\WorkingHours',
    ),

    // Management links
    'managementLinks' => array(
        A::t('appointments', 'Integration') => 'integrationWidgets/code',
        A::t('appointments', $multiClinics == true ? 'Clinics' : 'Clinic Info') => 'clinics/manage',
		A::t('appointments', 'Working Hours') => 'workingHours/index',
		A::t('appointments', 'Services') => 'services/index',
        A::t('appointments', 'Master Data') => 'masterData/index',
        A::t('appointments', 'Doctors') => 'doctors/manage',
        A::t('appointments', 'Reviews') => 'doctorReviews/manage',
        A::t('appointments', 'Patients') => 'patients/manage',
        A::t('appointments', 'Appointments') => 'appointments/manage',
        A::t('appointments', 'Membership Plans') => 'memberships/manage',
        A::t('appointments', 'Orders') => 'orders/index',
        A::t('appointments', 'Statistics') => 'statistics/manage',
    ),
    'multiClinics' => $multiClinics,
);
