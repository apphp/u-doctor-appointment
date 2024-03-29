<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors Management'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Specialties'), 'url'=>'doctorSpecialties/manage/doctorId/'.$doctorId),
        array('label'=>A::t('appointments', 'Edit Specialty')),
    );

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);

    $formName = 'frmDoctorEdit';
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab previous" href="doctorSpecialties/manage/doctorId/<?= $doctorId; ?>"><?= A::t('appointments', 'Specialties').' | '.$doctorName; ?></a> »
        <?= A::t('appointments', 'Edit Specialty'); ?>
    </div>
    <div class="content">
        <?php

        echo CWidget::create('CDataForm', array(
            'model'=>'Modules\Appointments\Models\DoctorSpecialties',
            'primaryKey'=>$specialty->id,
            'operationType'=>'edit',
            'action'=>'doctorSpecialties/edit/doctorId/'.$doctorId.'/id/'.$specialty->id,
            'successUrl'=>'doctorSpecialties/manage/doctorId/'.$doctorId,
            'cancelUrl'=>'doctorSpecialties/manage/doctorId/'.$doctorId,
			'passParameters'=> true,
            'method'=>'post',
            'htmlOptions'=>array(
                'id'=>$formName,
                'name'=>$formName,
                'autoGenerateId'=>true
            ),
            'requiredFieldsAlert'=>true,
            'fields'=>array(
                'specialty_id' => array('type'=>'select', 'title'=>A::t('appointments', 'Specialty'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($specialties)), 'data'=>$specialties, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('options'=>$specialtyOptions)),
                'sort_order'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>6, 'type'=>'integer'), 'htmlOptions'=>array('maxLength'=>6, 'class'=>'small')),
				'is_default' 	=> array('type'=>'checkbox', 'title'=>A::t('app', 'Default'), 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>($specialty->is_default ? array('disabled'=>'disabled', 'uncheckValue'=>$specialty->is_default) : array()), 'viewType'=>'custom'),
            ),
            'buttons'=>array(
                'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'=>array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'buttonsPosition'       => 'bottom',
            'messagesSource'        => 'core',
            'showAllErrors'         => false,
            'alerts'                => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Doctor Specialty')),
            'return'                => true,
        ));
    ?>
    </div>
</div>
