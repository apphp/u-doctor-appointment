<?php
    $this->_activeMenu = 'clinics/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Clinics Management')),
    );

    use Modules\Appointments\Models\Clinics;
?>

<h1><?= A::t('appointments', 'Clinics Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('clinic', 'add')){
            echo '<a href="clinics/add" class="add-new">'.A::t('appointments', 'Add Clinic').'</a>';
        }


        $clinicTableName = CConfig::get('db.prefix').Clinics::model()->getTableName();

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\Clinics',
            'actionPath'=>'clinics/manage',
            'condition'=>'',
            'defaultOrder'=>array('sort_order'=>'ASC'),
            'passParameters'=>true,
            //'customParameters'=>array('param_1'=>'integer', 'param_1'=>'string' [,...]),
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'id'        => array('title'=>'', 'visible'=>false, 'table'=>$clinicTableName, 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>'32'),
                'address'   => array('title'=>A::t('appointments','Address'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'160px', 'maxLength'=>'64'),
                'phone'     => array('title'=>A::t('appointments','Phone'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'', 'format'=>''),
            ),
            'fields'=>array(
                'clinic_name'           => array('type'=>'label', 'title'=>A::t('appointments', 'Clinic Name'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'address'               => array('type'=>'label', 'title'=>A::t('appointments', 'Address'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'phone'                 => array('type'=>'label', 'title'=>A::t('appointments', 'Phone'), 'width'=>'160px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'fax'                   => array('type'=>'label', 'title'=>A::t('appointments', 'Fax'), 'width'=>'150px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'working_hours_link'    => array('type'=>'link', 'title'=>'', 'class'=>'center', 'headerClass'=>'center', 'width'=>'95px', 'isSortable'=>false, 'linkUrl'=>'workingHours/edit/clinicId/{id}', 'linkText'=>A::t('appointments', 'Working Hours'), 'htmlOptions'=>array('class'=>'subgrid-link'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'is_default'            => array('type'=>'html', 'title'=>A::t('appointments', 'Default'), 'align'=>'', 'width'=>'65px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
                'is_active'             => array('type'=>'link', 'title'=>A::t('appointments', 'Active'), 'width'=>'65px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'clinics/changeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::te('appointments', 'Click to change status'))),
                'sort_order'            => array('type'=>'label', 'title'=>A::t('appointments', 'Sort Order'), 'width'=>'70px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('clinic', 'edit'),
                    'link'=>'clinics/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('clinic', 'delete'),
                    'link'=>'clinics/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));

    ?>
    </div>
</div>
