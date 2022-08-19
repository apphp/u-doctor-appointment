<?php
    Website::setMetaTags(array('title'=>A::t('appointments', 'Add Clinic')));

    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Doctor Clinics'), 'url'=>'doctorClinics/manage/doctorId/'.$doctorId),
        array('label'=>A::t('appointments', 'Add Clinic')),
    );

?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="sub-title">
        <a class="sub-tab active" href="doctorClinics/manage/doctorId/<?= $doctorId; ?>"><?= A::t('appointments', 'Clinics').' | '.$doctorName; ?></a>
        <?= A::t('appointments', 'Add Clinic'); ?>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        echo CWidget::create('CDataForm', array(
            'model'                 => 'Modules\Appointments\Models\DoctorClinics',
            'operationType'         => 'add',
            'action'                => 'doctorClinics/add/doctorId/'.$doctorId,
            'successUrl'            => 'doctorClinics/manage/doctorId/'.$doctorId,
            'cancelUrl'             => 'doctorClinics/manage/doctorId/'.$doctorId,
            'method'                => 'post',
            'htmlOptions'           => array(
                'id'                    => 'frmDoctorClinicsAdd',
                'name'                  => 'frmDoctorClinicsAdd',
                'enctype'               => 'multipart/form-data',
                'autoGenerateId'        => true
            ),
            'requiredFieldsAlert'   => true,
            'fields'                => array(
                'doctor_id'     => array('type'=>'data', 'default'=>$doctorId),
                'clinic_id'     => array('type'=>'select', 'title'=>A::t('appointments', 'Clinic'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($clinics)), 'data'=>$clinics, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('options'=>'')),
                'sort_order'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'tooltip'=>'', 'default'=>'0', 'validation'=>array('required'=>true, 'type'=>'numeric', 'maxlength'=>3), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
            ),
            'buttons'   => array(
                'submit'            => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'')),
                'cancel'            => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'buttonsPosition'       => 'bottom',
            'messagesSource'        => 'core',
            'showAllErrors'         => false,
            'alerts'                => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Doctor Clinic')),
            'return'                => true,
        ));
    ?>
    </div>
</div>
