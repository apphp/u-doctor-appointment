<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors Management'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Specialties')),
    );
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab active"><?= A::t('appointments', 'Specialties').' | '.$doctorName; ?></a>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        if($checkUploadSpecialtiesCountAccess){
            if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('doctor', 'add')){
                echo '<a href="doctorSpecialties/add/doctorId/'.$doctorId.'" class="add-new">'.A::t('appointments', 'Add Specialty').'</a>';
            }
        }else{
            echo CWidget::create('CMessage', array('warning', A::t('appointments', 'This doctor has the maximum allowed number of {param} for this membership plan!!!', array('{param}'=>A::t('appointments', 'Specialties'))), array('button'=>true)));
        }


        $fields = array();
        $condition = '';

        $fields = array(
        );


        CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\DoctorSpecialties',
            'actionPath'=>'doctorSpecialties/manage',
            'condition'=>'doctor_id = '.(int)$doctorId,
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'defaultOrder'=>array('sort_order'=>'ASC'),
            'sorting'=>true,
            'fields'=>array(
                'specialty_name'        => array('title'=>A::t('appointments', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'200px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'specialty_description' => array('title'=>A::t('appointments', 'Description'), 'type'=>'label', 'align'=>'', 'maxLength'=>130, 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'sort_order'             => array('title'=>A::t('appointments', 'Sort Order'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'is_default'             => array('title'=>A::t('appointments', 'Default'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'edit'),
                    'link'=>'doctorSpecialties/edit/doctorId/'.$doctorId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'=>array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'delete'),
                    'link'=>'doctorSpecialties/delete/doctorId/'.$doctorId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>false,
        ));

    ?>
    </div>
</div>
