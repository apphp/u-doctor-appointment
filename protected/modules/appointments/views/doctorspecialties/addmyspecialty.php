<?php
    $this->_pageTitle = A::t('appointments', 'Add Specialty');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Specialties'), 'url'=>'doctorSchedules/mySpecialties'),
        array('label'=>A::t('appointments', 'Add Specialty')),
    );

    $formName = 'frmDoctorScheduleAdd';

    A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
    A::app()->getClientScript()->registerScriptFile('assets/vendors/jquery/jquery-ui.min.js', 1);
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php

						echo CWidget::create('CDataForm', array(
							'model'=>'Modules\Appointments\Models\DoctorSpecialties',
							'operationType'=>'add',
							'action'=>'doctorSpecialties/addMySpecialty',
							'successUrl'=>'doctorSpecialties/mySpecialties',
							'cancelUrl'=>'doctorSpecialties/mySpecialties',
							'method'=>'post',
							'htmlOptions'=>array(
								'id'=>$formName,
								'class'=>'doctor-form',
								'name'=>$formName,
								'autoGenerateId'=>true
							),
							'requiredFieldsAlert'=>false,
							'fields'=>array(
								'specialty_id' => array('type'=>'select', 'title'=>A::t('appointments', 'Specialty'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($specialties)), 'data'=>$specialties, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('options'=>$specialtyOptions)),
								'sort_order'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>6, 'type'=>'integer'), 'htmlOptions'=>array('maxLength'=>6, 'class'=>'small')),
								'is_default'    => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Default'), 'default'=>0, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array()),
								'doctor_id'     => array('type'=>'data', 'default'=>$doctorId),
							),
							'buttons'=>array(
								'submit' => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'', 'class'=>'button')),
								'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
							),
							'buttonsPosition'       => 'bottom',
							'messagesSource'        => 'core',
							'showAllErrors'         => false,
							'alerts'                => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Specialty')),
							'return'                => true,
						));
					?>
                    </div>
                </div>
            </div>
        </div>
    </section>
