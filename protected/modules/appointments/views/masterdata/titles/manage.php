<?php
    $this->_activeMenu = 'masterData/index';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Master Data'), 'url'=>'masterData/index'),
        array('label'=>A::t('appointments', 'Titles Management')),
    );

?>

<h1><?= A::t('appointments', 'Titles Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('masterdata', 'add')){
            echo '<a href="masterData/titleAdd" class="add-new">'.A::t('appointments', 'Add Title').'</a>';
        }

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('masterdata', 'edit')){
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'masterData/titleChangeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status')));
        }else{
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::te('appointments', 'Active')));
        }

        echo CWidget::create('CGridView', array(
            'model'          => 'Modules\Appointments\Models\Titles',
            'actionPath'     => 'masterData/titlesManage',
            'condition'      => '',
            'defaultOrder'   => array('sort_order'=>'DESC'),
            'passParameters' => true,
            'pagination'     => array('enable'=>true, 'pageSize'=>20),
            'sorting'        => true,
            'filters'        => array(),
            'fields'         => array(
                'title'          => array('title'=>A::t('appointments', 'Title'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'sort_order'     => array('title'=>A::t('appointments', 'Sort Order'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
                'is_active'      => $isActive,
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled' => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('masterdata', 'edit'),
                    'link'     => 'masterData/titleEdit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled' => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('masterdata', 'delete'),
                    'link'     => 'masterData/titleDelete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));

    ?>
    </div>
</div>
