<?php
    $this->_activeMenu = 'appointments/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Appointments Management'), 'url'=>'appointments/manage'),
    );

    $statusParam = ($status !== '' ? '/status/'.$status : '');

    use \Modules\Appointments\Models\Appointments;
?>

<h1><?= A::t('appointments', 'Appointments Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
	<?= $actionMessage; ?>
    <?php
        // echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('appointment', 'add')){
            echo '<a href="appointments/add" class="add-new">'.A::t('appointments', 'Add Appointment').'</a>';
        }

        $tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();
        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\Appointments',
            'actionPath'=>'appointments/manage'.$statusParam,
            'condition'	=> ($status !== '' ? $tableName.'.status = '.$statusCode : ''),
            'defaultOrder'=>array('appointment_date'=>'DESC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
				'appointment_number' => array('title'=>A::t('appointments', 'Appointment ID'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
				'patient_first_name,patient_last_name' => array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32'),
				'doctor_first_name,doctor_middle_name,doctor_last_name' => array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32'),
            ),
            'fields'=>array(
				'appointment_number'        => array('type'=>'label', 'title'=>A::t('appointments', 'Appointment ID'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
				'patient_name'              => array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'link', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'patients/manage/?id={patient_id}&but_filter=Filter', 'linkText'=>'{patient_name}', 'definedValues'=>array(), 'htmlOptions'=>array()),
				'doctor_name'               => array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'link', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'doctors/manage/?id={doctor_id}&but_filter=Filter', 'linkText'=>'{doctor_name}', 'definedValues'=>array(), 'htmlOptions'=>array()),
				'specialty_name'            => array('type'=>'label', 'title'=>A::t('appointments', 'Specialty'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
				'appointment_date' 	        => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'maxLength'=>'', 'format'=>$dateFormat, 'htmlOptions'=>array()),
				'appointment_time' 	        => array('title'=>A::t('appointments', 'Time'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'maxLength'=>'', 'format'=>$appointmentTimeFormat, 'htmlOptions'=>array()),
				'clinic_name'               => array('type'=>'label', 'title'=>A::t('appointments', 'Clinic Name'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'change_link'               => array('title'=>'', 'type'=>'html', 'align'=>'', 'width'=>'65px', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>false, 'callback'=>array('class'=>'Modules\Appointments\Components\AppointmentsComponent', 'function'=>'getAdminChangeLink'), 'definedValues'=>array(), 'htmlOptions'=>array()),
                'p_arrival_reminder_sent'   => array('type'=>'label', 'title'=>A::t('appointments', 'PA'), 'headerTooltip'=>A::t('appointments', 'Patient Arrival Reminder'), 'width'=>'55px', 'class'=>'center', 'headerClass'=>'center', 'definedValues'=>array('0'=>'<span class="label-red label-square">'.A::t('appointments', 'Not Sent').'</span>', '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Sent').'</span>'), 'isSortable'=>true),
				//'p_confirm_reminder_sent'   => array('type'=>'label', 'title'=>A::t('appointments', 'PC'), 'headerTooltip'=>A::t('appointments', 'Patient Confirm Reminder'), 'width'=>'55px', 'class'=>'center', 'headerClass'=>'center', 'definedValues'=>array('0'=>'<span class="label-red label-square">'.A::t('appointments', 'Not Sent').'</span>', '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Sent').'</span>'), 'isSortable'=>true),
				//'d_confirm_reminder_sent'   => array('type'=>'label', 'title'=>A::t('appointments', 'DC'), 'headerTooltip'=>A::t('appointments', 'Doctor Confirm Reminder'), 'width'=>'55px', 'class'=>'center', 'headerClass'=>'center', 'definedValues'=>array('0'=>'<span class="label-red label-square">'.A::t('appointments', 'Not Sent').'</span>', '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Sent').'</span>'), 'isSortable'=>true),
				'doctor_internal_notes'     => array('type'=>'html', 'title'=>A::t('appointments', 'ICD'), 'headerTooltip'=>A::t('appointments', 'Doctor Comments (for internal use)'), 'width'=>'55px', 'class'=>'center', 'headerClass'=>'center', 'definedValues'=>array(), 'isSortable'=>true, 'trigger'=>array('trigger_key'=>'doctor_internal_notes', 'trigger_operation'=>'!=', 'trigger_value'=>'', 'success_value'=>'<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>', 'wrong_value'=>'--')),
				'doctor_external_notes'     => array('type'=>'html', 'title'=>A::t('appointments', 'CD'), 'headerTooltip'=>A::t('appointments', 'Comments for patient'), 'width'=>'55px', 'class'=>'center', 'headerClass'=>'center', 'definedValues'=>array(), 'isSortable'=>true, 'trigger'=>array('trigger_key'=>'doctor_external_notes', 'trigger_operation'=>'!=', 'trigger_value'=>'', 'success_value'=>'<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>', 'wrong_value'=>'--')),
                'patient_internal_notes'    => array('type'=>'html', 'title'=>A::t('appointments', 'ICP'), 'headerTooltip'=>A::t('appointments', 'Patient Comments (for internal use)'), 'width'=>'55px', 'class'=>'center', 'headerClass'=>'center', 'definedValues'=>array(), 'isSortable'=>true, 'trigger'=>array('trigger_key'=>'patient_internal_notes', 'trigger_operation'=>'!=', 'trigger_value'=>'', 'success_value'=>'<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>', 'wrong_value'=>'--')),
				'patient_external_notes'    => array('type'=>'html', 'title'=>A::t('appointments', 'CP'), 'headerTooltip'=>A::t('appointments', 'Comments for doctor'), 'width'=>'55px', 'class'=>'center', 'headerClass'=>'center', 'definedValues'=>array(), 'isSortable'=>true, 'trigger'=>array('trigger_key'=>'patient_external_notes', 'trigger_operation'=>'!=', 'trigger_value'=>'', 'success_value'=>'<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>', 'wrong_value'=>'--')),
				'status'                    => array('type'=>'label', 'title'=>A::t('appointments', 'Status'), 'definedValues'=>$labelStatusAppointments, 'width'=>'', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('appointments', 'edit'),
                    'link'=>'appointments/edit/id/{id}'.$statusParam, 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('appointments', 'delete'),
                    'link'=>'appointments/delete/id/{id}'.$statusParam, 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));

    ?>
    </div>
</div>
