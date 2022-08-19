<?php
    $this->_activeMenu = 'memberships/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Membership Plans Management')),
    );

    use \Modules\Appointments\Models\Memberships;
?>

<h1><?= A::t('appointments', 'Memberships Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('membership', 'add')){
            echo '<a href="memberships/add" class="add-new">'.A::t('appointments', 'Add Membership Plan').'</a>';
        }
        $membershipTableName = CConfig::get('db.prefix').Memberships::model()->getTableName();
        //CDebug::d($membershipTableName);

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\Memberships',
            'actionPath'=>'memberships/manage',
            'condition'=>'',
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'id'   => array('title'=>'', 'visible'=>false, 'table'=>$membershipTableName, 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>'32'),
                'name' => array('title'=>A::t('appointments', 'Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'200px', 'maxLength'=>'32'),
            ),
            'fields'=>array(
                'name'              => array('title'=>A::t('appointments', 'Name'),           'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'duration'          => array('title'=>A::t('appointments', 'Duration'),       'type'=>'label', 'align'=>'', 'width'=>'100px', 'class'=>'left',   'headerClass'=>'left',   'isSortable'=>true, 'definedValues'=>$durations),
                'images_count'      => array('title'=>A::t('appointments', 'Images Count'),   'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'clinics_count'     => array('title'=>A::t('appointments', 'Clinics Count'),  'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>', 'disabled'=>$multiClinics ? false : true),
                'schedules_count'   => array('title'=>A::t('appointments', 'Schedules Count'),   'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'specialties_count' => array('title'=>A::t('appointments', 'Specialties Count'),   'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'show_in_search'    => array('title'=>A::t('appointments', 'Show In Search'),        'type'=>'html',  'align'=>'', 'width'=>'80px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
                'enable_reviews'    => array('title'=>A::t('appointments', 'Enable Reviews'),        'type'=>'html',  'align'=>'', 'width'=>'80px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
                'price'             => array('title'=>A::t('appointments', 'Price'),          'type'=>'html',  'align'=>'', 'width'=>'60px',  'class'=>'right pr20',  'headerClass'=>'right pr20',  'isSortable'=>true, 'callback'=>array('class'=>'Modules\Appointments\Components\AppointmentsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'price'))),
                'is_default'        => array('title'=>A::t('appointments', 'Default'),        'type'=>'html',  'align'=>'', 'width'=>'80px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
                'is_active'         => array('title'=>A::t('appointments', 'Active'),         'type'=>'link',  'align'=>'', 'width'=>'70px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'memberships/changeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status'))),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('membership', 'edit'),
                    'link'=>'memberships/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('membership', 'delete'),
                    'link'=>'memberships/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));

    ?>
    </div>
</div>
