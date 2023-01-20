<?php
    $this->_activeMenu = 'masterData/index';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Master Data'), 'url'=>'masterData/index'),
        array('label'=>A::t('appointments', 'Titles'), 'url'=>'masterData/titlesManage'),
        array('label'=>A::t('appointments', 'Edit Title')),
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

        echo CWidget::create('CDataForm', array(
            'model'             => 'Modules\Appointments\Models\Titles',
            'primaryKey'        => $id,
            'operationType'     => 'edit',
            'action'            => 'masterData/titleEdit/id/'.$id,
            'successUrl'        => 'masterData/titlesManage',
            'cancelUrl'         => 'masterData/titlesManage',
            'passParameters'    => false,
            'method'            => 'post',
            'htmlOptions'       => array(
                'id'                => 'frmTitleEdit',
                'name'              => 'frmTitleEdit',
                //'enctype'         => 'multipart/form-data',
                'autoGenerateId'    => true
            ),
            'requiredFieldsAlert' => true,
            'fields'            => array(
                'title'      => array('type'=>'textbox', 'title'=>A::t('appointments', 'Title'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'30'), 'htmlOptions'=>array('maxLength'=>'30')),
                'sort_order' => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>6, 'type'=>'positiveInteger'), 'htmlOptions'=>array('maxLength'=>6, 'class'=>'small')),
                'is_active'  => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'default'=>true, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
            ),
            'translationInfo'   => array('relation'=>array('id', 'title_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
            'translationFields' => array(
                'title'=>array('type'=>'textbox', 'title'=>A::t('appointments', 'Title'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'50'), 'htmlOptions'=>array('maxLength'=>'50', 'class'=>'middle')),
            ),
            'buttons'           => array(
                'submitUpdateClose' => array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'      => array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'            => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'messagesSource'    => 'core',
            'showAllErrors'     => false,
            'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Title')),
            'return'            => true,
        ));
    ?>
    </div>
</div>
