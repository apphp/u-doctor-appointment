<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors Management')),
    );
    use Modules\Appointments\Models\Doctors;
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('doctor', 'add')){
            echo '<a href="doctors/add" class="add-new">'.A::t('appointments', 'Add Doctor').'</a>';
        }


        $fields = array();
        $filterFields = array();
        $condition = '';

        $doctorTableName = CConfig::get('db.prefix').Doctors::model()->getTableName();
        $filterFields['id']        = array('title'=>'', 'visible'=>false, 'table'=>$doctorTableName, 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>'32');
        $filterFields['doctor_first_name,doctor_last_name'] = array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32');
        $filterFields['username']  = array('title'=>A::t('appointments', 'Username'), 'type'=>'textbox', 'operator'=>'like%', 'default'=>'', 'width'=>'100px', 'maxLength'=>'25');
        $filterFields['email']     = array('title'=>A::t('appointments', 'Email'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'100');
        $filterFields['is_active'] = array('title'=>A::t('appointments', 'Active'), 'type'=>'enum', 'operator'=>'=', 'width'=>'60px', 'source'=>array(''=>'', '0'=>A::t('appointments', 'No'), '1'=>A::t('appointments', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>'');

        $fields = array(
            'avatar_by_gender'  => array('title'=>A::t('appointments', 'Photo'), 'type'=>'image', 'align'=>'', 'width'=>'50px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'imagePath'=>'assets/modules/appointments/images/doctors/', 'defaultImage'=>'no_avatar.png', 'imageWidth'=>'30px', 'imageHeight'=>'27px', 'alt'=>'', 'showImageInfo'=>true),
            'doctor_first_name' => array('title'=>A::t('appointments', 'First Name'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
            'doctor_last_name'  => array('title'=>A::t('appointments', 'Last Name'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
            //'username'        => array('title'=>A::t('appointments', 'Username'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
            'email'             => array('title'=>A::t('appointments', 'Email'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
            //'phone'           => array('title'=>A::t('appointments', 'Phone'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
            'clinics_link'      => array('title'=>'', 'type'=>'link', 'width'=>'50px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'doctorClinics/manage/doctorId/{id}', 'linkText'=>A::t('appointments', 'Clinics'), 'prependCode'=>'[ ', 'appendCode'=>' ]', 'disabled'=>($multiClinics == true ? false: true)),
            'clinics_id'        => array('title'=>'', 'sourceField'=>'id', 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'30px', 'source'=>$clinicCounters, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>', 'disabled'=>($multiClinics == true ? false: true)),
            'schedule_link'     => array('title'=>'', 'type'=>'link', 'width'=>'75px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'doctorSchedules/manage/doctorId/{id}', 'linkText'=>A::t('appointments', 'Schedules'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
            'schedule_id'       => array('title'=>'', 'sourceField'=>'id', 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'30px', 'source'=>$scheduleCounters, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
            'timeoff_link'      => array('title'=>'', 'type'=>'link', 'width'=>'60px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'doctorTimeoffs/manage/doctorId/{id}', 'linkText'=>A::t('appointments', 'Timeoffs'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
            'timeoff_id'        => array('title'=>'', 'sourceField'=>'id', 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'30px', 'source'=>$arrTimeoffs, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
            'special_link'      => array('title'=>'', 'type'=>'link', 'width'=>'75px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'doctorSpecialties/manage/doctorId/{id}', 'linkText'=>A::t('appointments', 'Specialties'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
            'special_id'        => array('title'=>'', 'sourceField'=>'id', 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'30px', 'source'=>$specialtyCounters, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
            'images_link'       => array('title'=>'', 'type'=>'link', 'width'=>'55px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'doctorImages/manage/doctorId/{id}', 'linkText'=>A::t('appointments', 'Images'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
            'images_id'         => array('title'=>'', 'sourceField'=>'id', 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'30px', 'source'=>$arrImages, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
            'book_appointments' => array('title'=>'', 'type'=>'link', 'width'=>'120px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'appointments/{id}', 'linkText'=>A::t('appointments', 'Book Appointment'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
        );

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('doctor', 'edit')){
            $fields['is_active'] = array('title'=>A::t('appointments', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'doctors/activeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status')));
        }else{
            $fields['is_active'] = array('title'=>A::t('appointments', 'Active'), 'type'=>'html', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link'));
        }
        
        $fields['id'] = array('title'=>A::t('app', 'ID'), 'type'=>'label', 'width'=>'10px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>true);


        CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\Doctors',
            'actionPath'=>'doctors/manage',
            'condition'=>'',
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>$filterFields,
            'fields'=>$fields,
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'edit'),
                    'link'=>'doctors/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'=>array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'delete'),
                    'link'=>'doctors/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>false,
        ));

    ?>
    </div>
</div>
