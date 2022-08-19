<?php
    $this->_pageTitle = A::t('appointments', 'Add Schedule');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Schedules'), 'url'=>'doctorSchedules/mySchedules'),
        array('label'=>A::t('appointments', 'Add Schedule')),
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
                        'model'=>'Modules\Appointments\Models\DoctorSchedules',
                        'operationType'=>'add',
                        'action'=>'doctorSchedules/addMySchedule',
                        'successUrl'=>'doctorSchedules/mySchedules',
                        'cancelUrl'=>'doctorSchedules/mySchedules',
                        'method'=>'post',
                        'htmlOptions'=>array(
                            'id'=>$formName,
                            'class'=>'doctor-form',
                            'name'=>$formName,
                            'autoGenerateId'=>true
                        ),
                        'requiredFieldsAlert'=>false,
                        'fields'=>array(
                            'name'      => array('type'=>'textbox', 'title'=>A::t('appointments', 'Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxlength'=>'255'), 'htmlOptions'=>array('maxlength'=>'255')),
                            'date_from' => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid From Date'), 'defaultEditMode'=>null, 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>10), 'htmlOptions'=>array('maxlength'=>'10', 'style'=>'width:100px'), 'definedValues'=>array(null=>'')),
                            'date_to'   => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid To Date'), 'defaultEditMode'=>null, 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>10, 'minValue'=>$minDate), 'htmlOptions'=>array('maxlength'=>'10', 'style'=>'width:100px'), 'definedValues'=>array(null=>'')),
                            'is_active' => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'default'=>1, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array()),
                            'doctor_id' => array('type'=>'data', 'default'=>$doctorId),
                        ),
                        'buttons'=>array(
                            'submit' => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'', 'class'=>'button')),
                            'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                        ),
                        'buttonsPosition'=>'bottom',
                        'alerts'=>array('type'=>'flash'),
                        'messagesSource'=>'core',
                        'return'=>true,
                    ));
                ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
