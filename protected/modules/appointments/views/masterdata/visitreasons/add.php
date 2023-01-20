<?php
    $this->_activeMenu = 'masterData/index';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Master Data'), 'url'=>'masterData/index'),
        array('label'=>A::t('appointments', 'Visit Reasons'), 'url'=>'masterData/visitReasonsManage'),
        array('label'=>A::t('appointments', 'Add Visit Reason')),
    );
?>

<h1><?= A::t('appointments', 'Visit Reasons Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
    <?php

        echo CWidget::create('CDataForm', array(
            'model'             => 'Modules\Appointments\Models\VisitReasons',
            'operationType'     => 'add',
            'successUrl'        => 'masterData/visitReasonsManage',
            'cancelUrl'         => 'masterData/visitReasonsManage',
            'passParameters'    => false,
            'method'            => 'post',
            'htmlOptions'       => array(
                'id'                => 'frmVisitReasonAdd',
                'name'              => 'frmVisitReasonAdd',
                //'enctype'         => 'multipart/form-data',
                'autoGenerateId'    => true
            ),
            'requiredFieldsAlert' => true,
            'fields'            => array(
                'sort_order' => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>6, 'type'=>'positiveInteger'), 'htmlOptions'=>array('maxLength'=>6, 'class'=>'small')),
                'is_active'  => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'default'=>true, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
            ),
            'translationInfo'   => array('relation'=>array('id', 'reason_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
            'translationFields' => array(
                'name'=>array('type'=>'textbox', 'title'=>A::t('appointments', 'Name'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'70'), 'htmlOptions'=>array('maxLength'=>'70', 'class'=>'middle')),
                'description'=>array('type'=>'textarea', 'title'=>A::t('appointments', 'Descriptions'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255), 'htmlOptions'=>array('maxLength'=>'255')),
            ),
            'buttons'           => array(
                'submit'            => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'')),
                'cancel'            => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'messagesSource' 	=> 'core',
            'showAllErrors'     => false,
            'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Visit Reason')),
            'return'            => true,
        ));
    ?>
    </div>
</div>
