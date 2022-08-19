<?php
    $this->_pageTitle = A::t('appointments', 'Edit Clinic');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Clinics'), 'url'=>'doctorClinics/myClinics'),
        array('label'=>A::t('appointments', 'Edit Clinic')),
    );

    $formName = 'frmDoctorClinicEdit';
?>

    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php

                    echo CWidget::create('CDataForm', array(
                        'model'=>'Modules\Appointments\Models\DoctorClinics',
                        'primaryKey'=>$id,
                        'operationType'=>'edit',
                        'action'=>'doctorClinics/editMyClinic/id/'.$id,
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
                            'clinic_name'     => array('type'=>'label', 'title'=>A::t('appointments', 'Clinic'), 'default'=>'', 'validation'=>array('required'=>false), 'htmlOptions'=>array('options'=>'')),
                            'sort_order'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'tooltip'=>'', 'default'=>'0', 'validation'=>array('required'=>true, 'type'=>'numeric', 'maxlength'=>3), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
                        ),
                        'buttons'=>array(
                            'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                            'submitUpdate'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                            'cancel'=>array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                        ),
                        'buttonsPosition'=>'bottom',
                        'alerts' => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Clinic')),
                        'messagesSource'=>'core',
                        'return'=>true,
                    ));
                ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
