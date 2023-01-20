<?php
$this->_activeMenu = 'appointments/manage';
$this->_breadCrumbs = array(
	array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
	array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
	array('label'=>A::t('appointments', 'Appointments'), 'url'=>'doctors/appointments'),
	array('label'=>A::t('appointments', 'Edit Appointment')),
);
?>

<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
				<?php
				echo CWidget::create('CDataForm', array(
					'model'             => 'Modules\Appointments\Models\Appointments',
					'primaryKey'        => $id,
					'operationType'     => 'edit',
					'action'            => 'doctors/editAppointment/id/'.$id.'/status/'.$status,
					'successUrl'        => 'doctors/appointments/status/'.$status,
					'cancelUrl'         => 'doctors/appointments/status/'.$status,
					'passParameters'    => false,
					'method'            => 'post',
					'htmlOptions'       => array(
						'name'           => 'frmAppointments',
						'class'          => 'frmAppointments',
						'autoGenerateId' => true
					),
					'requiredFieldsAlert' => true,
                    'fields' => array(
                        'separatorPatientInfo' =>array(
                            'separatorInfo'=>array('legend'=>A::t('appointments', 'Patient Info'), 'disabled'=>false),
                            'patient_name'  =>array('type'=>'label', 'title'=>A::t('appointments', 'Patient Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                            'patient_phone' =>array('type'=>'label', 'title'=>A::t('appointments', 'Phone'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                            //'patient_email' =>array('type'=>'label', 'title'=>A::t('appointments', 'Email'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                        ),
                        'separatorDoctorInfo' =>array(
                            'separatorInfo'=>array('legend'=>A::t('appointments', 'Doctor Info'), 'disabled'=>false),
                            'doctor_name'               =>array('type'=>'label', 'title'=>A::t('appointments', 'Doctor Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                            'specialty_name'            =>array('type'=>'label', 'title'=>A::t('appointments', 'Specialty'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array('class'=>"large"), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                        ),
                        'separatorAppointmentInfo' =>array(
                            'separatorInfo'=>array('legend'=>A::t('appointments', 'Appointment Info'), 'disabled'=>false),
                            'appointment_number'      =>array('type'=>'label', 'title'=>A::t('appointments', 'Appointment ID'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                            'appointment_date'        =>array('type'=>'label', 'title'=>A::t('appointments', 'Appointment Date'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>$dateFormat, 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                            'appointment_time'        =>array('type'=>'label', 'title'=>A::t('appointments', 'Appointment Time'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>$appointmentTimeFormat, 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                            'clinic_name'             =>array('type'=>'label', 'title'=>A::t('appointments', 'Clinic Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                            'visit_duration'          =>array('type'=>'label', 'title'=>A::t('appointments', 'Visit Duration'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array('class'=>"width-15"), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>''), 'prependCode'=>'', 'appendCode'=>'<label> '.A::t('appointments', 'min.').'</label>'),
                            'visit_price'             =>array('type'=>'textbox',  'title'=>A::t('appointments', 'Visit Price'),          'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0.00', 'maxValue'=>'', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>11, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
                            'visit_reason'            =>array('type'=>'label', 'title'=>A::t('appointments', 'Visit Reason'), 'tooltip'=>'', 'default'=>$visitReason, 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                            'for_is_whom'             =>array('type'=>'label', 'title'=>A::t('appointments', 'Who is this appointment for?'), 'tooltip'=>'', 'default'=>$forWhom, 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                            'status_arrival'            => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Arrived'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom'),
                            'patient_external_notes'  =>array('type'=>'label', 'title'=>A::t('appointments', 'Comments for doctor'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false),
                            'doctor_internal_notes'   =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Doctor Comments (for medical card)'), 'tooltip'=>A::t('appointments', 'Visible only for you'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1000), 'htmlOptions'=>array('maxLength'=>'1000')),
                            'doctor_external_notes'   =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Comments for patient'), 'tooltip'=>A::t('appointments', 'Visible for patient'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1000), 'htmlOptions'=>array('maxLength'=>'1000')),                        ),
                    ),
					'buttons' => array(
						'submitUpdateClose' =>array('type'=>'submit', 'value'=>'Update & Close', 'htmlOptions'=>array('name'=>'btnUpdateClose')),
						'submitUpdate'      =>array('type'=>'submit', 'value'=>'Update', 'htmlOptions'=>array('name'=>'btnUpdate')),
						'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
					),
					'messagesSource'    => 'core',
					'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Appointment')),
					'return'            => true,
				));
				?>
            </div>
        </div>
    </div>
</section>
