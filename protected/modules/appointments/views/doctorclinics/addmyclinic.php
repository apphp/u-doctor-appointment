<?php
    $this->_pageTitle = A::t('appointments', 'Add Clinic');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Clinics'), 'url'=>'doctorClinics/myClinics'),
        array('label'=>A::t('appointments', 'Add Clinic')),
    );

    $formName = 'frmDoctorClinicAdd';
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php

						echo CWidget::create('CDataForm', array(
							'model'=>'Modules\Appointments\Models\DoctorClinics',
							'operationType'=>'add',
							'action'=>'doctorClinics/addMyClinic',
							'successUrl'=>'doctorClinics/myClinics',
							'cancelUrl'=>'doctorClinics/myClinics',
							'method'=>'post',
							'htmlOptions'=>array(
								'id'=>$formName,
								'class'=>'doctor-form',
								'name'=>$formName,
								'autoGenerateId'=>true
							),
							'requiredFieldsAlert'=>false,
							'fields'=>array(
                                'doctor_id'     => array('type'=>'data', 'default'=>$doctorId),
                                'clinic_id'     => array('type'=>'select', 'title'=>A::t('appointments', 'Clinic'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($clinics)), 'data'=>$clinics, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('options'=>'')),
                                'sort_order'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'tooltip'=>'', 'default'=>'0', 'validation'=>array('required'=>true, 'type'=>'numeric', 'maxlength'=>3), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
							),
							'buttons'=>array(
								'submit' => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'', 'class'=>'button')),
								'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
							),
							'buttonsPosition'       => 'bottom',
							'messagesSource'        => 'core',
							'showAllErrors'         => false,
							'alerts'                => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Clinic')),
							'return'                => true,
						));
					?>
                    </div>
                </div>
            </div>
        </div>
    </section>
