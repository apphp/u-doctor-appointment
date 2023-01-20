<?php
$this->_activeMenu = 'appointments/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
    array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
    array('label'=>A::t('appointments', 'Patients'), 'url'=>'patients/manage'),
    array('label'=>(!empty($patientName) ? $patientName.' | ' : '').A::t('appointments', 'Orders'), 'url'=>''),
);

use \Modules\Appointments\Models\Appointments;
?>

<h1><?= (!empty($patientName) ? $patientName.' | ' : '').A::t('appointments', 'Medical Card'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab " href="patients/manage"><?= A::t('appointments', 'Patients'); ?></a>
        »
        <a class="sub-tab active" href="javascript:void(0);"><?= (!empty($patientName) ? $patientName.' | ' : '').A::t('appointments', 'Medical Card'); ?></a>
    </div>

    <div class="content">
        <?= $actionMessage; ?>

        <div class="invoice-box">
            <table class="pb10">
                <tr>
                    <td class="title" colspan="2"><?= A::t('appointments', 'Patient Info'); ?>:</td>
                </tr>
                <tr><td width="10%"><?= A::t('appointments', 'Patient Name'); ?>: </td><td><?= !empty($patientName) ? '<a href="patients/manage/?id='.$patient->id.'&but_filter=Filter">'.$patientName.'</a>' : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Birth Date'); ?>: </td><td><?= $patient->birth_date ? CLocale::date($dateFormat, $patient->birth_date) : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Gender'); ?>: </td><td><?= $patient->gender ? ($patient->gender == 'm') ? A::t('appointments', 'Male') : A::t('appointments', 'Female') : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Weight'); ?>: </td><td><?= $patient->weight ? $patient->weight : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Height'); ?>: </td><td><?= $patient->height ? $patient->height : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Blood Type'); ?>: </td><td><?= $patient->blood_type ? $patient->blood_type : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Allergies'); ?>: </td><td><?= $patient->allergies ? $patient->allergies : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'High Blood Pressure'); ?>: </td><td><?= $patient->high_blood_presure ? $patient->high_blood_presure : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Low Blood Pressure'); ?>: </td><td><?= $patient->low_blood_presure ? $patient->low_blood_presure : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Cardiac Rhythm'); ?>: </td><td><?= $patient->cardiac_rythm ? $patient->cardiac_rythm : '--'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Smoking'); ?>: </td><td><?= $patient->smoking == 1 ? '<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>' : '<span class="label-red label-square">'.A::t('appointments', 'No').'</span>'; ?></td></tr>
                <tr><td width="10%"><?= A::t('appointments', 'Tried Drugs'); ?>: </td><td><?= $patient->tried_drugs == 1 ? '<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>' : '<span class="label-red label-square">'.A::t('appointments', 'No').'</span>'; ?></td></tr>
            </table>
        </div>

        <?php

        $tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();
        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\Appointments',
            'actionPath'=>'patients/medicalCard/patientId/'.$patientId,
            'condition'	=> (!empty($patientId) ? $tableName.'.patient_id = '.$patientId : ''),
            'defaultOrder'=>array('appointment_date'=>'DESC', 'appointment_time'=>'DESC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'appointment_number' => array('title'=>A::t('appointments', 'Appointment ID'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
                'doctor_specialty_id' 	=> array('title'=>A::t('appointments', 'Specialty'), 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'source'=>$filterSpecialty, 'emptyOption'=>true, 'emptyValue'=>'', 'htmlOptions'=>array('class'=>'chosen-select-filter')),
                'doctor_id' 	=> array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'source'=>$filterDoctors, 'emptyOption'=>true, 'emptyValue'=>'', 'htmlOptions'=>array('class'=>'chosen-select-filter')),
            ),
            'fields'=>array(
                //'appointment_number'        => array('type'=>'label', 'title'=>A::t('appointments', 'Appointment ID'), 'width'=>'125px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'doctor_name'               => array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'link', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'doctors/manage/?id={doctor_id}&but_filter=Filter', 'linkText'=>'{doctor_name}', 'definedValues'=>array(), 'htmlOptions'=>array()),
                'specialty_name'            => array('type'=>'label', 'title'=>A::t('appointments', 'Specialty'), 'width'=>'300px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'appointment_date' 	        => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'80px', 'maxLength'=>'', 'format'=>$dateFormat, 'htmlOptions'=>array()),
                'appointment_time' 	        => array('title'=>A::t('appointments', 'Time'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'60px', 'maxLength'=>'', 'format'=>$timeFormat, 'htmlOptions'=>array(), 'isSortable'=>false),
                'doctor_internal_notes'            => array('type'=>'label', 'title'=>A::t('appointments', 'Doctor Comments'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'definedValues'=>array(''=>'--'), 'isSortable'=>false),
                //'appointment_time' 	        => array('title'=>A::t('appointments', 'Time'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'100px', 'maxLength'=>'', 'format'=>$appointmentTimeFormat, 'htmlOptions'=>array()),
                //'clinic_name'               => array('type'=>'label', 'title'=>A::t('appointments', 'Clinic Name'), 'width'=>'100px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
            ),
            'actions'=>array(),
            'return'=>true,
        ));

        ?>
    </div>
</div>
