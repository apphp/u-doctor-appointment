<?php
    $this->_activeMenu = 'doctorReviews/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Reviews Management'), 'url'=>'modules/settings/manage'),
		array('label'=>A::t('appointments', 'Edit Review'))
    );

    $statusParam = ($status !== '' ? '/status/'.$status : '');
?>

<h1><?= A::t('appointments', 'Reviews Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
	<div class="sub-title"><?= A::t('appointments', 'Edit Review'); ?></div>

    <div class="content">
    <?php
        echo CWidget::create('CDataForm', array(
            'model'			=> 'Modules\Appointments\Models\DoctorReviews',
            'primaryKey'    => $id,
			'operationType'	=> 'edit',
            'action'		=> 'doctorReviews/edit/id/'.$id.$statusParam,
            'successUrl'	=> 'doctorReviews/manage'.$statusParam,
            'cancelUrl'		=> 'doctorReviews/manage'.$statusParam,
            'method'		=> 'post',
            'htmlOptions'	=> array(
                'id'	         => 'frmReviewEdit',
                'name'	         => 'frmReviewEdit',
                'autoGenerateId' =>true
            ),
            'requiredFieldsAlert'=>true,
            'fields'=>array(
                'doctor_name'           =>array('type'=>'label', 'title'=>A::t('appointments', 'Doctor Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                'patient_name'          =>array('type'=>'label', 'title'=>A::t('appointments', 'Patient Name'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                'patient_email'         =>array('type'=>'label', 'title'=>A::t('appointments', 'Patient Email'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                'created_at'            =>array('type'=>'label', 'title'=>A::t('appointments', 'Created at'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat, 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                'rating_price'          =>array('type'=>'label', 'title'=>A::t('appointments', 'Rating Price'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>$ratingStars, 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                'rating_wait_time'      =>array('type'=>'label', 'title'=>A::t('appointments', 'Rating Wait Time'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>$ratingStars, 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
                'rating_bedside_manner' =>array('type'=>'label', 'title'=>A::t('appointments', 'Rating Bedside Manner'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>$ratingStars, 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>'', 'params'=>'')),
				'message'               =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Message'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>500), 'htmlOptions'=>array('maxLength'=>'500')),
				'status'                => array('type'=>'select', 'title'=>A::t('appointments', 'Status'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($editStatusReviews)), 'data'=>$editStatusReviews, 'emptyOption'=>true, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'storeType'=>'separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter')),
            ),
            'buttons'=>array(
                'submitUpdateClose' =>array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'      =>array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'            =>array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'buttonsPosition'       => 'bottom',
            'messagesSource'        => 'core',
            'showAllErrors'         => false,
            'alerts'                => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Review')),
            'return'                => true,
        ));
    ?>
	</div>
</div>