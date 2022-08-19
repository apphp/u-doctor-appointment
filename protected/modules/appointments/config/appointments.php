<?php

return array(
    // Module components
    'components' => array(
        'AppointmentsComponent' => array('enable' => true, 'class' => 'AppointmentsComponent'),
		'DoctorsComponent' => array('enable' => true, 'class' => 'DoctorsComponent'),
    ),

    // Url manager (optional)
    'urlManager' => array(
        'rules' => array(
            'appointments/([0-9]+)' => 'appointments/appointments/id/{$0}',
            'appointments/appointments/([0-9]+)' => 'appointments/appointments/id/{$0}',
            'appointments/appointmentdetails/([0-9]+)' => 'appointments/appointmentdetails/id/{$0}',

			'doctors/profile/id/([0-9]+)' => 'doctors/profile/id/{$0}',
			'doctors/profile/id/([0-9]+)/(.*?)' => 'doctors/profile/id/{$0}',
			'doctors/profile/([0-9]+)' => 'doctors/profile/id/{$0}',
			'doctors/profile/([0-9]+)/(.*?)' => 'doctors/profile/id/{$0}',
			'doctors/([0-9]+)' => 'doctors/profile/id/{$0}',
			'doctors/([0-9]+)/(.*?)' => 'doctors/profile/id/{$0}',

            'clinics/viewClinic/([0-9]+)' => 'clinics/viewClinic/id/{$0}',
            'clinics/([0-9]+)' => 'clinics/viewClinic/id/{$0}',

            'orders/checkout/([0-9]+)' => 'orders/checkout/id/{$0}',
            'orders/paymentForm/([0-9]+)' => 'orders/paymentForm/id/{$0}',

			'services/view/([0-9]+)' => 'services/view/id/{$0}',

            'mobile/newsView/([0-9]+)' => 'mobile/newsView/id/{$0}',
            'mobile/clinicView/([0-9]+)' => 'mobile/clinicView/id/{$0}',
            'mobile/serviceView/([0-9]+)' => 'mobile/serviceView/id/{$0}',
            'mobile/doctorView/([0-9]+)' => 'mobile/doctorView/id/{$0}',
        ),
    ),

	// Default Backend url (optional, if defined - will be used as application default settings)
	'backendDefaultUrl' => 'clinics/manage',

    // Default settings (optional, if defined - will be used as application default settings)
	//'defaultErrorController' => 'Error',
    'defaultController' => 'home',
    'defaultAction' => 'index',
);
