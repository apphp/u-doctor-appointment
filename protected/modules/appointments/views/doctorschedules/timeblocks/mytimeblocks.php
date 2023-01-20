<?php
    $this->_pageTitle = A::t('appointments', 'Schedules');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Schedules').' ('.$scheduleName.')', 'url'=>'doctorSchedules/mySchedules'),
        array('label'=>A::t('appointments', 'Time Blocks')),
    );
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php
                        echo $actionMessage;
                        if($checkAccessAccountUsingMembershipPlan):
                            echo '<div class="margin-bottom-20">';
                            echo '<a href="doctorSchedules/addMyTimeBlock/scheduleId/'.$scheduleId.'" class="add-new button">'.A::t('appointments', 'Add Time Slot').'</a>';
                            echo '</div>';
                        endif;
                        $fields = array();
                        $condition = '';

                        CWidget::create('CGridView', array(
                            'model'=>'Modules\Appointments\Models\DoctorScheduleTimeBlocks',
                            'actionPath'=>'doctorSchedules/myTimeBlocks/scheduleId/'.$scheduleId,
                            'condition'=>'schedule_id = '.(int)$scheduleId.' AND doctor_id = '.(int)$doctorId,
                            'passParameters'=>true,
                            'pagination'=>array('enable'=>true, 'pageSize'=>20),
                            'defaultOrder'=>array('week_day'=>'ASC', 'time_from'=>'ASC'),
                            'options'=>array(
                                'gridTable'=>array('class'=>'table'),
                            ),
                            'sorting'=>true,
                            'fields'=>array(
                                'week_day'          => array('title'=>A::t('appointments', 'Week Day'), 'type'=>'enum', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrWeekDays),
                                'address'           => array('title'=>A::t('appointments', 'Address'), 'type'=>'label', 'align'=>'left', 'width'=>'', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown'))),
			                    'clinic_name'       => array('type'=>'label', 'title'=>A::t('appointments', 'Clinic Name'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown'))),
                                'time_from'         => array('title'=>A::t('appointments', 'From Time'), 'type'=>'label', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat),
                                'time_to'           => array('title'=>A::t('appointments', 'To Time'), 'type'=>'label', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat),
                                'time_slots'        => array('title'=>A::t('appointments', 'Time Slots'), 'type'=>'enum', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'source'=>$arrTimeSlots, 'definedValues'=>array(), 'format'=>''),
                                'time_slot_type_id' => array('title'=>A::t('appointments', 'Time Slots Type'), 'type'=>'enum', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrTimeSlotsType, 'definedValues'=>array(), 'format'=>''),
                            ),
                            'actions'=>array(
                                'edit'    => array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorSchedules/editMyTimeBlock/scheduleId/'.$scheduleId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                                ),
                                'delete'=>array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorSchedules/deleteMyTimeBlock/scheduleId/'.$scheduleId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                                )
                            ),
                            'return'=>false,
                        ));
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
