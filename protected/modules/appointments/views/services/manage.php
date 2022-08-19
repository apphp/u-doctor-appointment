<?php
    $this->_activeMenu = 'services/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Services Management')),
    );

?>

<h1><?= A::t('appointments', 'Services Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('services', 'add')){
            echo '<a href="services/add" class="add-new">'.A::t('appointments', 'Add Services').'</a>';
        }
        
        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('services', 'edit')){
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'services/changeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status')));
        }else{
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::te('appointments', 'Active')));
        }

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\Services',
            'actionPath'=>'services/manage',
            'condition'=>'',
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'name' => array('title'=>A::t('appointments', 'Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'200px', 'maxLength'=>'32'),
            ),
            'fields'=>array(
                'image_file'  => array('title'=>A::t('appointments', 'Image'), 'type'=>'image', 'align'=>'', 'width'=>'50px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'imagePath'=>'assets/modules/appointments/images/services/', 'defaultImage'=>'no_image.png', 'imageWidth'=>'35px', 'imageHeight'=>'25px', 'alt'=>'', 'showImageInfo'=>true),
                'name'        => array('title'=>A::t('appointments', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'250px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'description' => array('title'=>A::t('appointments', 'Descriptions'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'maxLength'=>'120'),
                'sort_order'  => array('title'=>A::t('appointments', 'Sort Order'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
                'is_active'   => $isActive,
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('services', 'edit'),
                    'link'=>'services/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('services', 'delete'),
                    'link'=>'services/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'messagesSource' => 'core',
            'alerts'         => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Service')),
            'return'         => true,
        ));
        
    ?>
    </div>
</div>
