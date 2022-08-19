<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Schedules')),
    );
?>

<h1><?= A::t('appointments', 'Doctor Schedules Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab active"><?= A::t('appointments', 'Schedules').' | '.$doctorName; ?></a>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        if($checkUploadSchedulesCountAccess){
            if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('doctor', 'add')) {
                echo '<a href="doctorSchedules/add/doctorId/' . $doctorId . '" class="add-new">' . A::t('appointments', 'Add Schedule') . '</a>';
            }
        }else{
            echo CWidget::create('CMessage', array('warning', A::t('appointments', 'This doctor has the maximum allowed number of {param} for this membership plan!!!', array('{param}'=>A::t('appointments', 'Schedules'))), array('button'=>true)));
        }

        $fields = array();
        $condition = '';

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('doctor', 'edit')){
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'doctorSchedules/changeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status')));
        }else{
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link'));
        }

        CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\DoctorSchedules',
            'actionPath'=>'doctorSchedules/manage/doctorId/'.$doctorId,
            'condition'=>'doctor_id = '.(int)$doctorId,
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'defaultOrder'=>array('id'=>'DESC'),
            'sorting'=>true,
            'fields'=>array(
                'name'          => array('title'=>A::t('appointments', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'schedule_link' => array('title'=>A::t('appointments', ''), 'type'=>'link', 'width'=>'80px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'doctorSchedules/manageTimeBlocks/doctorId/{doctor_id}/scheduleId/{id}', 'linkText'=>A::t('appointments', 'Time Slots'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'schedule_id'   => array('title'=>'', 'sourceField'=>'id', 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'30px', 'source'=>$timeBlockCounters, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'date_from'     => array('title'=>A::t('appointments', 'Valid From Date'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>$dateFormat),
                'date_to'       => array('title'=>A::t('appointments', 'Valid To Date'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>$dateFormat),
                'is_active'     => $isActive,
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'edit'),
                    'link'=>'doctorSchedules/edit/doctorId/'.$doctorId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'=>array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'delete'),
                    'link'=>'doctorSchedules/delete/doctorId/'.$doctorId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>false,
        ));

    ?>
    </div>
</div>
