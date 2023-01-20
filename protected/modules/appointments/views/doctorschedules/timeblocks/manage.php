<?php
	$this->_activeMenu = 'doctors/manage';
	$this->_breadCrumbs = array(
		array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
		array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
		array('label'=>A::t('appointments', 'Doctors'), 'url'=>'doctors/manage'),
		array('label'=>A::t('appointments', 'Schedules'), 'url'=>'doctorSchedules/manage/doctorId/'.$doctorId),
		array('label'=>A::t('appointments', 'Time Slots')),
	);
?>

<h1><?= A::t('appointments', 'Doctor Schedules Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>
	<div class="sub-title">
		<a class="sub-tab previous" href="doctorSchedules/manage/doctorId/<?= $doctorId; ?>"><?= A::t('appointments', 'Schedules').' | '.$doctorName; ?></a> »
		<a class="sub-tab active"><?= A::t('appointments', 'Time Slots').' | '.$scheduleName; ?></a>
	</div>

	<div class="content">
	<?php
		echo $actionMessage;
		echo $messageWorkingHours;

		if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('doctor', 'add')){
			echo '<a href="doctorSchedules/addTimeBlock/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId.'" class="add-new">'.A::t('appointments', 'Add Time Slot').'</a>';
		}

		$fields = array();
		$filterFields = array();
		$configModule = \CLoader::config('appointments', 'main');
		$multiClinics = $configModule['multiClinics'];
	    $timeFormat = 'H:i';

        $filterFields['address_id'] = array('title'=>A::t('appointments', 'Clinic'), 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'source'=>$doctorClinics, 'emptyOption'=>true, 'emptyValue'=>'', 'htmlOptions'=>array('class'=>'chosen-select-filter'));

		if($multiClinics):
			$fields['week_day']          = array('title'=>A::t('appointments', 'Week Day'), 'type'=>'enum', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrWeekDays);
            $fields['time_from']         = array('title'=>A::t('appointments', 'From Time'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat);
            $fields['time_to']           = array('title'=>A::t('appointments', 'To Time'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat);
            $fields['time_slots']        = array('title'=>A::t('appointments', 'Time Slots'), 'type'=>'enum', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'source'=>$arrTimeSlots, 'definedValues'=>array(), 'format'=>'');
            $fields['time_slot_type_id'] = array('title'=>A::t('appointments', 'Time Slots Type'), 'type'=>'enum', 'align'=>'', 'width'=>'140px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrTimeSlotsType, 'definedValues'=>array(), 'format'=>'');
            $fields['clinic_link']       = array('title'=>A::t('appointments', 'Clinic'), 'type'=>'link', 'align'=>'', 'width'=>'', 'linkUrl'=>'clinics/manage/?id={address_id}&but_filter=Filter', 'linkText'=>'{clinic_name}',  'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'stripTags'=>true);
            $fields['address']           = array('title'=>A::t('appointments', 'Address'), 'type'=>'label', 'align'=>'left', 'width'=>'', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')));
		else:
			$fields['week_day']          = array('title'=>A::t('appointments', 'Week Day'), 'type'=>'enum', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrWeekDays);
			$fields['time_from']         = array('title'=>A::t('appointments', 'From Time'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat);
			$fields['time_to']           = array('title'=>A::t('appointments', 'To Time'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat);
			$fields['time_slots']        = array('title'=>A::t('appointments', 'Time Slots'), 'type'=>'enum', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'source'=>$arrTimeSlots, 'definedValues'=>array(), 'format'=>'');
            $fields['time_slot_type_id'] = array('title'=>A::t('appointments', 'Time Slots Type'), 'type'=>'enum', 'align'=>'', 'width'=>'140px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrTimeSlotsType, 'definedValues'=>array(), 'format'=>'');
		endif;

		$condition = '';
		CWidget::create('CGridView', array(
			'model'=>'Modules\Appointments\Models\DoctorScheduleTimeBlocks',
			'actionPath'=>'doctorSchedules/manageTimeBlocks/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId,
			'condition'=>'schedule_id = '.(int)$scheduleId.' AND doctor_id = '.(int)$doctorId,
			'passParameters'=>true,
			'pagination'=>array('enable'=>true, 'pageSize'=>20),
			'defaultOrder'=>array('week_day'=>'ASC', 'time_from'=>'ASC'),
			'sorting'=>true,
			'filters'=>$filterFields,
			'fields'=> $fields,
			'actions'=>array(
				'edit'    => array(
					'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'edit'),
					'link'=>'doctorSchedules/editTimeBlock/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
				),
				'delete'=>array(
					'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'delete'),
					'link'=>'doctorSchedules/deleteTimeBlock/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
				)
			),
			'return'=>false,
		));

	?>
	</div>
</div>
