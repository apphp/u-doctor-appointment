<?php
    $this->_activeMenu = 'masterData/index';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Master Data'), 'url'=>'masterData/index'),
        array('label'=>A::t('appointments', 'Time Slots Type Management')),
    );

?>

<h1><?= A::t('appointments', 'Time Slots Type Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('masterdata', 'add')){
            echo '<a href="masterData/typeTimeSlotAdd" class="add-new">'.A::t('appointments', 'Add Time Slot Type').'</a>';
        }

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('masterdata', 'edit')){
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'masterData/timeSlotsTypeChangeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status')));
        }else{
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::te('appointments', 'Active')));
        }

        echo CWidget::create('CGridView', array(
            'model'          => 'Modules\Appointments\Models\TimeSlotsType',
            'actionPath'     => 'masterData/timeSlotsTypeManage',
            'condition'      => '',
            'defaultOrder'   => array('sort_order'=>'ASC'),
            'passParameters' => true,
            'pagination'     => array('enable'=>true, 'pageSize'=>20),
            'sorting'        => true,
            'filters'        => array(),
            'fields'         => array(
                'name'              => array('title'=>A::t('appointments', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'text_color'        => array('title'=>A::t('appointments', 'Text Color'), 'type'=>'label', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'prependCode'=>'<div style="display:inline-block;border-radius:9px;border:1px solid #ccc;width:18px;height:18px;background-color:', 'appendCode'=>'"></div>'),
                'background_color'  => array('title'=>A::t('appointments', 'Background Color'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'prependCode'=>'<div style="display:inline-block;border-radius:9px;border:1px solid #ccc;width:18px;height:18px;background-color:', 'appendCode'=>'"></div>'),
                'sort_order'        => array('title'=>A::t('appointments', 'Sort Order'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
                'is_bookable'       => array('title'=>A::t('appointments', 'Bookable'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="label-red label-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="label-green label-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::te('appointments', 'Bookable'))),
                'is_active'         => $isActive,
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled' => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('masterdata', 'edit'),
                    'link'     => 'masterData/typeTimeSlotEdit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled' => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('masterdata', 'delete'),
                    'link'     => 'masterData/typeTimeSlotDelete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));

    ?>
    </div>
</div>
