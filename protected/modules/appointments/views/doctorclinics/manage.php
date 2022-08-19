<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Clinics')),
    );
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">

    <?= $tabs; ?>

    <div class="sub-title">
        <a class="sub-tab active"><?= A::t('appointments', 'Clinics').' | '.$doctorName; ?></a>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

            if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('doctor', 'add')){
                if($checkUploadClinicsAccess){
                    if(empty($addDoctorClinics)){
                        echo '<a href="doctorClinics/add/doctorId/'.$doctorId.'" class="add-new">'.A::t('appointments', 'Add Clinic').'</a>';
                    }else{
                        echo $addDoctorClinics;
                    }
                }else{
                    echo CWidget::create('CMessage', array('warning', A::t('appointments', 'This doctor has the maximum allowed number of {param} for this membership plan!!!', array('{param}'=>A::t('appointments', 'Clinics'))), array('button'=>true)));
                }
            }

        echo CWidget::create('CGridView', array(
            'model'             => 'Modules\Appointments\Models\DoctorClinics',
            'actionPath'        => 'doctorClinics/manage/doctorId/'.$doctorId,
            'condition'         => 'doctor_id = '.$doctorId,
            'defaultOrder'      => array('sort_order'=>'ASC'),
            'passParameters'    => true,
            'pagination'        => array('enable'=>true, 'pageSize'=>20),
            'sorting'           => true,
            'filters'           => array(),
            'fields'            => array(
                'index'         => array('title'=>'', 'type'=>'index', 'align'=>'', 'width'=>'17px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false),
                'clinic_link'   => array('title'=>A::t('appointments', 'Clinic'), 'type'=>'link', 'align'=>'', 'width'=>'', 'linkUrl'=>'clinics/manage/?id={clinic_id}&but_filter=Filter', 'linkText'=>'{clinic_name}',  'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'stripTags'=>true),
                'clinic_address'   => array('title'=>A::t('appointments', 'Address'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(''=>A::t('appointments', 'Unknown')), 'stripTags'=>true),
                'sort_order'    => array('title'=>A::t('appointments', 'Sort Order'), 'type'=>'label', 'align'=>'right', 'width'=>'', 'class'=>'right', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'stripTags'=>true),
            ),
            'actions'           => array(
                'edit'    => array(
                    'disabled'      => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'edit'),
                    'link'          => 'doctorClinics/edit/doctorId/'.$doctorId.'/id/{id}/', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this Image')
                ),
                'delete'  => array(
                    'disabled'      => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'delete'),
                    'link'          => 'doctorClinics/delete/doctorId/'.$doctorId.'/id/{id}/', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this Image'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));
    ?>
    </div>
</div>
