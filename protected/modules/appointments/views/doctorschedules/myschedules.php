<?php
    $this->_pageTitle = A::t('appointments', 'Schedules');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Schedules')),
    );
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php
                        echo $actionMessage;
                        if($checkUploadSchedulesCountAccess):
                            if($checkAccessAccountUsingMembershipPlan):
                                echo '<div class="margin-bottom-20">';
                                echo '<a href="doctorSchedules/addMySchedule/doctorId/'.$doctorId.'" class="add-new button">'.A::t('appointments', 'Add Schedule').'</a>';
                                echo '</div>';
                            endif;
                        else:
                            echo CWidget::create('CMessage', array('warning', A::t('appointments', 'You have reached the maximum number of {param} allowed by your current membership plan.', array('{param}'=>A::t('appointments', 'Schedules'))), array('button'=>true)));
                        endif;
                        $fields = array();
                        $condition = '';

                        CWidget::create('CGridView', array(
                            'model'=>'Modules\Appointments\Models\DoctorSchedules',
                            'actionPath'=>'doctorSchedules/mySchedules',
                            'condition'=>'doctor_id = '.(int)$doctorId,
                            'passParameters'=>true,
                            'pagination'=>array('enable'=>true, 'pageSize'=>20),
                            'defaultOrder'=>array('id'=>'DESC'),
                            'options'=>array(
                                'gridTable'=>array('class'=>'table'),
                            ),
                            'sorting'=>true,
                            'fields'=>array(
                                'name'          => array('title'=>A::t('appointments', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                                'schedule_link' => array('title'=>A::t('appointments', ''), 'type'=>'link', 'width'=>'90px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'doctorSchedules/myTimeBlocks/scheduleId/{id}', 'linkText'=>A::t('appointments', 'Time Slots'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                                'schedule_id'   => array('title'=>'', 'sourceField'=>'id', 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'15px', 'source'=>$timeBlockCounters, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                                'date_from'     => array('title'=>A::t('appointments', 'Valid From Date'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$dateFormat),
                                'date_to'       => array('title'=>A::t('appointments', 'Valid To Date'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$dateFormat),
                                'is_active'     => array('title'=>A::t('appointments', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'doctorSchedules/activeFrontendStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status'))),
                            ),
                            'actions'=>array(
                                'edit'    => array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorSchedules/editMySchedule/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                                ),
                                'delete'=>array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorSchedules/deleteMySchedule/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
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
