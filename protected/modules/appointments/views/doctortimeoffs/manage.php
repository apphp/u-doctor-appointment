<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Timeoffs')),
    );
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab active"><?= A::t('appointments', 'Timeoffs').' | '.$doctorName; ?></a>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('doctor', 'add')){
            echo '<a href="doctorTimeoffs/add/doctorId/'.$doctorId.'" class="add-new">'.A::t('appointments', 'Add Timeoff').'</a>';
        }

        $fields = array();
        $condition = '';
	    $timeFormat = 'H:i';

        CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\DoctorTimeoffs',
            'actionPath'=>'doctors/manageTimeoffs/doctorId/'.$doctorId,
            'condition'=>'doctor_id = '.(int)$doctorId,
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'defaultOrder'=>array('id'=>'DESC'),
            'sorting'=>true,
            'fields'=>array(
                'description'   => array('title'=>A::t('appointments', 'Description'), 'type'=>'label', 'maxLength'=>'50', 'align'=>'', 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'date_from'     => array('title'=>A::t('appointments', 'Valid From Date'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$dateFormat),
                'time_from'     => array('title'=>A::t('appointments', 'Valid From Time'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(''=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat),
                'date_to'       => array('title'=>A::t('appointments', 'Valid To Date'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$dateFormat),
                'time_to'       => array('title'=>A::t('appointments', 'Valid To Time'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(''=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'edit'),
                    'link'=>'doctorTimeoffs/edit/doctorId/'.$doctorId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'=>array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'delete'),
                    'link'=>'doctorTimeoffs/delete/doctorId/'.$doctorId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>false,
        ));

    ?>
    </div>
</div>
