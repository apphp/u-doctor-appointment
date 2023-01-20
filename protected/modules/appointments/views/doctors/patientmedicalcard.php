<?php
$this->_breadCrumbs = array(
    array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
    array('label'=>A::t('appointments', 'Patients'), 'url'=>'doctors/patients'),
    array('label'=>(!empty($patientName) ? $patientName.' - ' : '').A::t('appointments', 'Medical Card')),
);

use \Modules\Appointments\Models\Appointments;

$tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div id="patient-medical-card" class="cmsms-form-builder">
                    <?= $actionMessage; ?>

                    <table class="table patient-info">
                        <thead>
                        <tr>
                            <th colspan="2"><?= A::t('appointments', 'Patient Info'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Patient Name'); ?>: </strong></td>
                            <td class="left"><a href="doctors/patients/?id=<?= $patient->id; ?>&but_filter=Filter"><?= $patientName; ?></a></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Birth Date'); ?>: </strong></td>
                            <td class="left"><?= $patient->birth_date ? CLocale::date($dateFormat, $patient->birth_date) : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Gender'); ?>: </strong></td>
                            <td class="left"><?= $patient->gender ? ($patient->gender == 'm') ? A::t('appointments', 'Male') : A::t('appointments', 'Female') : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Weight'); ?>: </strong></td>
                            <td class="left"><?= $patient->weight ? $patient->weight : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Height'); ?>: </strong></td>
                            <td class="left"><?= $patient->height ? $patient->height : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Blood Type'); ?>: </strong></td>
                            <td class="left"><?= $patient->blood_type ? $patient->blood_type : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Allergies'); ?>: </strong></td>
                            <td class="left"><?= $patient->allergies ? $patient->allergies : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'High Blood Pressure'); ?>: </strong></td>
                            <td class="left"><?= $patient->high_blood_presure ? $patient->high_blood_presure : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Low Blood Pressure'); ?>: </strong></td>
                            <td class="left"><?= $patient->low_blood_presure ? $patient->low_blood_presure : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Cardiac Rhythm'); ?>: </strong></td>
                            <td class="left"><?= $patient->cardiac_rythm ? $patient->cardiac_rythm : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Smoking'); ?>: </strong></td>
                            <td class="left"><?= $patient->smoking == 1 ? '<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>' : '<span class="label-red label-square">'.A::t('appointments', 'No').'</span>'; ?></td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?= A::t('appointments', 'Tried Drugs'); ?>: </strong></td>
                            <td class="left"><?= $patient->tried_drugs == 1 ? '<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>' : '<span class="label-red label-square">'.A::t('appointments', 'No').'</span>'; ?></td>
                        </tr>


                        </tbody>
                    </table>
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
                        'options'	=> array(
                            'filterDiv' 	=> array('class'=>'frmFilter'),
                            'gridWrapper'   => array('tag'=>'div', 'class'=>'table-responsive'),
                            'gridTable'     => array('class'=>'table'),
                        ),
                        'filters'=>array(
                            'appointment_number' => array('title'=>A::t('appointments', 'Appointment ID'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
                            'doctor_specialty_id' 	=> array('title'=>A::t('appointments', 'Specialty'), 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'source'=>$filterSpecialty, 'emptyOption'=>true, 'emptyValue'=>'', 'htmlOptions'=>array('class'=>'chosen-select-filter')),
                            'doctor_id' 	=> array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'source'=>$filterDoctors, 'emptyOption'=>true, 'emptyValue'=>'', 'htmlOptions'=>array('class'=>'chosen-select-filter')),
                        ),
                        'fields'=>array(
                            //'appointment_number'        => array('type'=>'label', 'title'=>A::t('appointments', 'Appointment ID'), 'width'=>'125px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                            'doctor_name'               => array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'link', 'align'=>'', 'width'=>'100px', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'doctors/profile/{doctor_id}', 'linkText'=>'{doctor_name}', 'definedValues'=>array(), 'htmlOptions'=>array()),
                            'specialty_name'            => array('type'=>'label', 'title'=>A::t('appointments', 'Specialty'), 'width'=>'230px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                            'appointment_date' 	        => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'80px', 'maxLength'=>'', 'format'=>$dateFormat, 'htmlOptions'=>array()),
                            'appointment_time' 	        => array('title'=>A::t('appointments', 'Time'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'60px', 'maxLength'=>'', 'format'=>$timeFormat, 'htmlOptions'=>array(),'isSortable'=>false),
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
        </div>
    </div>
</section>