<?php
$this->_activeMenu = 'appointments/manage';
$this->_breadCrumbs = array(
	array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
	array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'patients/dashboard'),
	array('label'=>A::t('appointments', 'My Appointments'), 'url'=>'patients/myAppointments'),
	array('label'=>A::t('appointments', 'Edit My Appointment')),
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
                    'action'            => 'patients/editMyAppointment/id/'.$id.'/status/'.$status,
                    'successUrl'        => 'patients/myAppointments/status/'.$status,
                    'cancelUrl'         => 'patients/myAppointments/status/'.$status,
                    'passParameters'    => false,
                    'method'            => 'post',
                    'htmlOptions'       => array(
                        'name'              => 'frmAppointments',
                        'class'             => 'frmAppointments',
                        'autoGenerateId'    => true
                    ),
                    'requiredFieldsAlert' => true,
                    'fields' => array(
                        'appointment_number'        =>array('type'=>'label', 'title'=>A::t('appointments', 'Appointment ID'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                        'patient_name'              =>array('type'=>'label', 'title'=>A::t('appointments', 'Patient Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                        'doctor_name'               =>array('type'=>'label', 'title'=>A::t('appointments', 'Doctor Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                        'specialty_name'            =>array('type'=>'label', 'title'=>A::t('appointments', 'Specialty'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                        'appointment_date'          =>array('type'=>'label', 'title'=>A::t('appointments', 'Appointment Date'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>$dateFormat, 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                        'appointment_time'          =>array('type'=>'label', 'title'=>A::t('appointments', 'Appointment Time'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>$appointmentTimeFormat, 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                        'clinic_name'               =>array('type'=>'label', 'title'=>A::t('appointments', 'Clinic Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
						'visit_duration'            =>array('type'=>'label', 'title'=>A::t('appointments', 'Visit Duration'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array('class'=>"width-15"), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>''), 'prependCode'=>'', 'appendCode'=>'<label> '.A::t('appointments', 'min.').'</label>'),
						'visit_price'               =>array('type'=>'label', 'title'=>A::t('appointments', 'Visit Price'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>$numberFormat, 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>''), 'prependCode'=>'<label class="width-15">'.$currencySymbol.' </label>', 'appendCode'=>''),
						'visit_reason'              =>array('type'=>'label', 'title'=>A::t('appointments', 'Visit Reason'), 'tooltip'=>'', 'default'=>$visitReason, 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
						'for_is_whom'               =>array('type'=>'label', 'title'=>A::t('appointments', 'Who is this appointment for?'), 'tooltip'=>'', 'default'=>$forWhom, 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                        'doctor_external_notes'     =>array('type'=>'label', 'title'=>A::t('appointments', 'Comments for patient'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(''=>'--'), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false),
                        'patient_internal_notes'    =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Patient Comments (for internal use)'), 'tooltip'=>A::t('appointments', 'Visible only for you'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1000), 'htmlOptions'=>array('maxLength'=>'1000')),
                        'patient_external_notes'    =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Comments for doctor'), 'tooltip'=>A::t('appointments', 'Visible for doctor'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1000), 'htmlOptions'=>array('maxLength'=>'1000')),
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
