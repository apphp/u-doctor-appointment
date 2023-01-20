<?php
    $this->_activeMenu = 'masterData/index';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Master Data'), 'url'=>'masterData/index'),
        array('label'=>A::t('appointments', 'Specialties'), 'url'=>'masterData/specialtiesManage'),
        array('label'=>A::t('appointments', 'Edit Specialty')),
    );
?>

<h1><?= A::t('appointments', 'Specialties Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
    <?php

        echo CWidget::create('CDataForm', array(
            'model'             => 'Modules\Appointments\Models\Specialties',
            'primaryKey'        => $id,
            'operationType'     => 'edit',
            'action'            => 'masterData/specialtyEdit/id/'.$id,
            'successUrl'        => 'masterData/specialtiesManage',
            'cancelUrl'         => 'masterData/specialtiesManage',
            'passParameters'    => true,
            'method'            => 'post',
            'htmlOptions'       => array(
                'id'                => 'frmSpecialtyEdit',
                'name'              => 'frmSpecialtyEdit',
                //'enctype'         => 'multipart/form-data',
                'autoGenerateId'    => true
            ),
            'requiredFieldsAlert' => true,
            'fields'            => array(
                'is_active'         => array('type'=>'checkbox', 'title'=>A::t('app', 'Active'), 'default'=>true, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
            ),
            'translationInfo'   => array('relation'=>array('id', 'specialty_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
            'translationFields' => array(
                'name'=>array('type'=>'textbox', 'title'=>A::t('appointments', 'Name'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'70'), 'htmlOptions'=>array('maxLength'=>'70', 'class'=>'middle')),
                'description'=>array('type'=>'textarea', 'title'=>A::t('appointments', 'Descriptions'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255), 'htmlOptions'=>array('maxLength'=>'255')),
            ),
            'buttons'           => array(
                'submitUpdateClose' => array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'      => array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'            => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'messagesSource' 	=> 'core',
            'showAllErrors'     => false,
            'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Specialty')),
            'return'            => true,
        ));
    ?>
    </div>
</div>
