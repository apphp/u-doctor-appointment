<?php
    $this->_activeMenu = 'masterData/index';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Membership Plans'), 'url'=>'memberships/manage'),
        array('label'=>A::t('appointments', 'Add Membership')),
    );
?>

<h1><?= A::t('appointments', 'Memberships Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= A::t('appointments', 'Add Membership Plan'); ?>
    </div>

    <div class="content">
    <?php

        echo CWidget::create('CDataForm', array(
            'model'          => 'Modules\Appointments\Models\Memberships',
            'operationType'  => 'add',
            'successUrl'     => 'memberships/manage',
            'cancelUrl'      => 'memberships/manage',
            'passParameters' => false,
            'method'         => 'post',
            'htmlOptions'    => array(
                'id'             => 'frmMembershipAdd',
                'name'           => 'frmMembershipAdd',
                'autoGenerateId' => true
            ),
            'requiredFieldsAlert' => true,
            'fields'         => array(
                'price'          => array('type'=>'textbox',  'title'=>A::t('appointments', 'Price'),          'default'=>0,     'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0.00', 'maxValue'=>'', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>11, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
                'is_default'     => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Default'),        'default'=>false, 'tooltip'=>'', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom','htmlOptions'=>array()),
                'is_active'      => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'),         'default'=>true,  'tooltip'=>'', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom', 'htmlOptions'=>array()),
                'separatorPlanFeatures'  => array(
                    'separatorInfo'  => array('legend'=>A::t('appointments', 'Plan Features')),
                    'images_count'   => array('type'=>'textbox',  'title'=>A::t('appointments', 'Images Count'),   'tooltip'=>'', 'validation'=>array('required'=>true, 'maxLength'=>3, 'type'=>'integer'), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
                    'clinics_count'  => array('type'=>'textbox',  'title'=>A::t('appointments', 'Clinics Count'),  'tooltip'=>'', 'validation'=>array('required'=>true, 'maxLength'=>3, 'type'=>'integer'), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small'), 'disabled'=>$multiClinics ? false : true),
                    'schedules_count'  => array('type'=>'textbox',  'title'=>A::t('appointments', 'Schedules Count'),  'tooltip'=>'', 'validation'=>array('required'=>true, 'maxLength'=>3, 'type'=>'integer'), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
                    'specialties_count'  => array('type'=>'textbox',  'title'=>A::t('appointments', 'Specialties Count'),  'tooltip'=>'', 'validation'=>array('required'=>true, 'maxLength'=>3, 'type'=>'integer'), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
                    'show_in_search' => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Show In Search'), 'tooltip'=>'', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom','htmlOptions'=>array()),
                    'enable_reviews' => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Enable Reviews'), 'tooltip'=>'', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom','htmlOptions'=>array()),
                ),
            ),
            'translationInfo' => array('relation'=>array('id', 'membership_plan_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
            'translationFields' => array(
                'name'        => array('type'=>'textbox',  'title'=>A::t('appointments', 'Name'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'125'), 'htmlOptions'=>array('maxLength'=>'125')),
                'description' => array('type'=>'textarea', 'title'=>A::t('appointments', 'Description'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>'1024'), 'htmlOptions'=>array('maxLength'=>'1024')),
            ),
            'buttons'        => array(
                'submit'        => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'')),
                'cancel'        => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'messagesSource' => 'core',
            'alerts'         => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Membership Plan')),
            'return'         => true,
        ));
    ?>
    </div>
</div>
