<?php
    $this->_activeMenu = 'patients/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Patients Management')),
    );

    use \Modules\Appointments\Models\Patients;
?>

<h1><?= A::t('appointments', 'Patients Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('patient', 'add')){
            echo '<a href="patients/add" class="add-new">'.A::t('appointments', 'Add Patient').'</a>';
        }


        $fields = array();
        $filterFields = array();
        $condition = '';
        $tablePatientsName = CConfig::get('db.prefix').Patients::model()->getTableName();
        $filterFields['id']         = array('title'=>'', 'visible'=>false, 'table'=>$tablePatientsName, 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>'32');
        $filterFields['patient_first_name,patient_last_name'] = array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32');
        $filterFields['username']   = array('title'=>A::t('appointments', 'Username'), 'type'=>'textbox', 'operator'=>'like%', 'default'=>'', 'width'=>'100px', 'maxLength'=>'32');
        $filterFields['email']      = array('title'=>A::t('appointments', 'Email'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'100');
        $filterFields['is_active']  = array('title'=>A::t('appointments', 'Active'), 'type'=>'enum', 'operator'=>'=', 'width'=>'60px', 'source'=>array(''=>'', '0'=>A::t('appointments', 'No'), '1'=>A::t('appointments', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>'');

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('patient', 'edit')){
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'patients/activeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status')));
        }else{
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link'));
        }


        CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\Patients',
            'actionPath'=>'patients/manage',
            'condition'=>'',
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>$filterFields,
            'fields'=>array(
                'patient_last_name' => array('title'=>A::t('appointments', 'Last Name'), 'type'=>'label', 'align'=>'', 'width'=>'130px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'patient_first_name' => array('title'=>A::t('appointments', 'First Name'), 'type'=>'label', 'align'=>'', 'width'=>'130px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'phone' => array('title'=>A::t('appointments', 'Phone'), 'type'=>'label', 'align'=>'', 'width'=>'130px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'email' => array('title'=>A::t('appointments', 'Email'), 'type'=>'label', 'align'=>'', 'width'=>'210px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'username' => array('title'=>A::t('appointments', 'Username'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'appt_link'       => array('title'=>'', 'type'=>'link', 'width'=>'100px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'appointments/manage?patient_id={id}&but_filter=Filter', 'linkText'=>A::t('appointments', 'Appointments'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'orders_link'     => array('title'=>'', 'type'=>'link', 'width'=>'130px', 'class'=>'center', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'patients/medicalCard/patientId/{id}', 'linkText'=>A::t('appointments', 'Medical Card'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'is_active' => $isActive,
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('patient', 'edit'),
                    'link'=>'patients/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'=>array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('patient', 'delete'),
                    'link'=>'patients/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>false,
        ));

    ?>
    </div>
</div>
