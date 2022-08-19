<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors Management'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Edit Schedule')),
    );

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);

    $formName = 'frmDoctorScheduleEdit';
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab previous" href="doctors/manageSchedules/doctorId/<?= $doctorId; ?>"><?= A::t('appointments', 'Schedules').' | '.$doctorName; ?></a> »
        <?= A::t('appointments', 'Edit Schedule'); ?>
    </div>
    <div class="content">
        <?php

        echo CWidget::create('CDataForm', array(
            'model'=>'Modules\Appointments\Models\DoctorSchedules',
            'primaryKey'=>$id,
            'operationType'=>'edit',
            'action'=>'doctors/editSchedule/doctorId/'.$doctorId.'/id/'.$id,
            'successUrl'=>'doctors/manageSchedules/doctorId/'.$doctorId,
            'cancelUrl'=>'doctors/manageSchedules/doctorId/'.$doctorId,
            'method'=>'post',
            'htmlOptions'=>array(
                'id'=>$formName,
                'name'=>$formName,
                'autoGenerateId'=>true
            ),
            'requiredFieldsAlert'=>true,
            'fields'=>array(
                'name'      => array('type'=>'textbox', 'title'=>A::t('appointments', 'Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxlength'=>'255'), 'htmlOptions'=>array('maxlength'=>'255')),
                'date_from' => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid From Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date'), 'htmlOptions'=>array('class'=>'medium'), 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'minDate'=>'', 'maxDate'=>''),
                'date_to'   => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid To Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'minValue'=>$minDate), 'htmlOptions'=>array('class'=>'medium'), 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'maxDate'=>'', 'minDate'=>''),
                'is_active' => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'default'=>1, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array()),
            ),
            'buttons'=>array(
                'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'=>array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'buttonsPosition'=>'bottom',
            'alerts'=>array('type'=>'flash'),
            'messagesSource'=>'core',
            'return'=>true,
        ));
    ?>
    </div>
</div>
