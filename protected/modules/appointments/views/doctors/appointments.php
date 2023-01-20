<?php
$this->_activeMenu = 'appointments/manage';
$this->_breadCrumbs = array(
    array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
    array('label'=>A::t('appointments', 'Appointments')),
);

use Modules\Appointments\Models\Appointments;
$tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();
$futureLink = '';
$pastLink = '';

if ($status == 'future'):
    $futureLink = 'javascript:void();';
    $pastLink = 'doctors/appointments/status/past';
elseif ($status == 'past'):
    $futureLink = 'doctors/appointments/status/future';
    $pastLink = 'javascript:void();';
elseif ($status == 'all'):
    $futureLink = 'doctors/appointments/status/future';
    $pastLink = 'doctors/appointments/status/past';
endif;
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
					<?= $actionMessage; ?>
                    <div class="margin-bottom-20">
                        <a href="doctors/addAppointment" class="add-new button margin-right-5"><?= A::t('appointments', 'Add Appointment'); ?></a>
                    </div>
                    <div class="tab-link">
                        <ul class="tabs active">
                            <li class="<?= $status == 'future' ? 'current' : ''; ?>"><a href="<?= $futureLink; ?>"><span><?= A::t('appointments', 'Future'); ?></span></a></li>
                            <li class="<?= $status == 'past' ? 'current' : ''; ?>"><a href="<?= $pastLink; ?>"><span><?= A::t('appointments', 'Past'); ?></span></a></li>
                        </ul>
                        <div class="tab_content">
                            <div class="tabs_tab" style="display: block;">
								<?php

								echo CWidget::create('CGridView', array(
									'model'=>'Modules\Appointments\Models\Appointments',
									'actionPath'=>'doctors/appointments/status/'.$status,
									'condition'	=> $condition,
									'defaultOrder'=>array('appointment_date'=>'ASC'),
									'passParameters'=>true,
									'pagination'=>array('enable'=>true, 'pageSize'=>20),
									'sorting'=>true,
									'options'	=> array(
										'filterDiv' 	=> array('class'=>'frmFilter'),
										'gridWrapper'   => array('tag'=>'div', 'class'=>'table-responsive'),
                                        'gridTable'     => array('class'=>'table'),
									),
									'filters'=>array(
										'patient_id' => array('title'=>'', 'visible'=>false, 'table'=>$tableName, 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>'32'),
										'appointment_number' => array('title'=>A::t('appointments', 'ID'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'80px', 'maxLength'=>'32'),
                                        'appointment_date' 	 => array('title'=>A::t('appointments', 'Appointment Date'), 'type'=>'datetime', 'align'=>'center', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'left', 'isSortable'=>true, 'maxLength'=>'100', 'definedValues'=>array(null=>'--'), 'format'=>$dateTimeFormat),
										'patient_first_name,patient_last_name' => array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32'),
									),
									'fields'=>array(
										//'appointment_number'=> array('type'=>'label', 'title'=>A::t('appointments', 'Appointment ID'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                                        'patient_name'              => array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'link', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'doctors/patients/?id={patient_id}&but_filter=Filter', 'linkText'=>'{patient_name}', 'definedValues'=>array(), 'htmlOptions'=>array()),
                                        'specialty_clinic'          => array('title'=>A::t('appointments', 'Specialty'), 'type'=>'concat', 'align'=>'', 'width'=>'200px', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'concatFields'=>array('specialty_name', 'clinic_name'), 'concatSeparator'=>'<br>',),
                                        //'specialty_name'	        => array('type'=>'label', 'title'=>A::t('appointments', 'Specialty'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                                        //'clinic_name'               => array('title'=>A::t('appointments', 'Clinic Name'), 'type'=>'link', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'clinics/{doctor_address_id}/{clinic_name}', 'linkText'=>'{clinic_name}', 'definedValues'=>array(), 'htmlOptions'=>array()),
                                        'appointment_date' 	        => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'120px', 'maxLength'=>'', 'format'=>$dateFormat, 'htmlOptions'=>array()),
                                        'appointment_time' 	        => array('title'=>A::t('appointments', 'Time'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'maxLength'=>'', 'format'=>$appointmentTimeFormat, 'htmlOptions'=>array()),
                                        //'p_arrival_reminder_sent'   => array('type'=>'label', 'title'=>A::t('appointments', 'PA'), 'headerTooltip'=>A::t('appointments', 'Patient Arrival Reminder'), 'width'=>'', 'class'=>'center', 'headerClass'=>'center', 'definedValues'=>array('0'=>'<span class="label-red label-square">'.A::t('appointments', 'Not Sent').'</span>', '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Sent').'</span>'), 'isSortable'=>true),
                                        'confirm_link'              => array('title'=>'', 'type'=>'html', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'htmlOptions'=>array(), 'callback'=>array('class'=>'Modules\Appointments\Components\AppointmentsComponent', 'function'=>'getConfirmLink'), 'disabled'=>(($status == 'future' && $checkAccessAccountUsingMembershipPlan) ? false : true)),
                                        'change_link'               => array('title'=>'', 'type'=>'link', 'width'=>'75px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'doctors/changeAppointment/id/{id}', 'linkText'=>A::t('appointments', 'Change'), 'prependCode'=>'[ ', 'appendCode'=>' ]', 'htmlOptions'=>array('onclick'=>'return onChangeRecord(this)'), 'disabled'=>($status == 'future' ? false: true)),
                                        'cancel_link'               => array('title'=>'', 'type'=>'link', 'width'=>'70px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'doctors/cancelAppointment/id/{id}/status/'.$status, 'linkText'=>A::t('appointments', 'Cancel'), 'prependCode'=>'[ ', 'appendCode'=>' ]', 'htmlOptions'=>array('onclick'=>'return onCancelRecord(this)'), 'disabled'=>( ($status == 'future' && '{status}' < 2 && $checkAccessAccountUsingMembershipPlan) ? false: true)),
                                        'status'                    => array('type'=>'label', 'title'=>A::t('appointments', 'Status'), 'definedValues'=>$labelStatusAppointments, 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                                        'status_arrival'            => array('type'=>'label', 'title'=>A::t('appointments', 'Arrived'), 'definedValues'=>$labelPatientArrivalStatus, 'width'=>'', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
                                    ),
									'actions'=>array(
										'edit' => array(
                                            'disabled'=>!$checkAccessAccountUsingMembershipPlan,
											'link'=>'doctors/editAppointment/id/{id}/status/'.$status, 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
										),
									),
									'return'=>true,
								));
								?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
A::app()->getClientScript()->registerScript(
    'cancel-appointment',
    'function onCancelRecord(el){
        console.log(el);
        return confirm("'.A::te('appointments', 'Are you sure you want to cancel this appointment?').'");
    }',
    2
);
A::app()->getClientScript()->registerScript(
    'confirm-appointment',
    'function onConfirmRecord(el){
        return confirm("'.A::te('appointments', 'Are you sure you want to confirm this appointment?').'");
    }',
    2
);
A::app()->getClientScript()->registerScript(
    'change-appointment',
    'function onChangeRecord(el){
        return confirm("'.A::te('appointments', 'Are you sure you want to change this appointment?').'");
    }',
    2
);
?>