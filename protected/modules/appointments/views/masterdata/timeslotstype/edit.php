<?php
    $this->_activeMenu = 'masterData/index';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Master Data'), 'url'=>'masterData/index'),
        array('label'=>A::t('appointments', 'Time Slots Type'), 'url'=>'masterData/timeSlotsTypeManage'),
        array('label'=>A::t('appointments', 'Edit Time Slot Type')),
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

        echo CWidget::create('CDataForm', array(
            'model'             => 'Modules\Appointments\Models\TimeSlotsType',
            'primaryKey'        => $id,
            'operationType'     => 'edit',
            'action'            => 'masterData/typeTimeSlotEdit/id/'.$id,
            'successUrl'        => 'masterData/timeSlotsTypeManage',
            'cancelUrl'         => 'masterData/timeSlotsTypeManage',
            'passParameters'    => false,
            'method'            => 'post',
            'htmlOptions'       => array(
                'id'                => 'frmTitleEdit',
                'name'              => 'frmTitleEdit',
                //'enctype'         => 'multipart/form-data',
                'autoGenerateId'    => true
            ),
            'requiredFieldsAlert' => true,
            'fields' => array(
                'text_color'       => array('type'=>'color', 'title'=>A::t('appointments', 'Text Color'), 'tooltip'=>'', 'default'=>'#ffffff', 'validation'=>array('required'=>true, 'type'=>'hexColor'), 'htmlOptions'=>array()),
                'background_color' => array('type'=>'color', 'title'=>A::t('appointments', 'Background Color'), 'tooltip'=>'', 'default'=>'#ffffff', 'validation'=>array('required'=>true, 'type'=>'hexColor'), 'htmlOptions'=>array()),
                'sort_order'       => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>6, 'type'=>'positiveInteger'), 'htmlOptions'=>array('maxLength'=>6, 'class'=>'small')),
                'is_bookable'      => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Bookable'), 'default'=>true, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
                'is_active'        => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'default'=>true, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
            ),
            'translationInfo'   => array('relation'=>array('id', 'time_slot_type_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
            'translationFields' => array(
                'name'=>array('type'=>'textbox', 'title'=>A::t('appointments', 'Name'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'50'), 'htmlOptions'=>array('maxLength'=>'50', 'class'=>'middle')),
            ),
            'buttons'           => array(
                'submitUpdateClose' => array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'      => array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'            => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'messagesSource'    => 'core',
            'showAllErrors'     => false,
            'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Time Slots Type')),
            'return'            => true,
        ));
    ?>
    </div>
</div>
