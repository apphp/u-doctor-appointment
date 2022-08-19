<?php
$this->_activeMenu = 'doctorReviews/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
    array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
    array('label'=>A::t('appointments', 'Reviews Management')),
);

$statusParam = ($status !== '' ? '/status/'.$status : '');

use \Modules\Appointments\Models\DoctorReviews;
?>

<h1><?= A::t('appointments', 'Reviews Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
		<?= $subTabs; ?>
    </div>
    <div class="content">
        <?php
        echo $actionMessage;

		$filterFields = array();
		$filterFields['doctor_first_name,doctor_last_name'] = array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32');
		$filterFields['patient_name'] = array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32');
		$filterFields['created_at'] = array('title'=>A::t('appointments', 'Date Created'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32');

		$tableName = CConfig::get('db.prefix').DoctorReviews::model()->getTableName();
        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\DoctorReviews',
            'actionPath'=>'doctorReviews/manage'.$statusParam,
			'condition'	=> ($status !== '' ? $tableName.'.status = '.$statusCode : ''),
            'passParameters'=>true,
			'defaultOrder'		=> array('created_at'=>'DESC'),
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=> $filterFields,
            'fields' => array(
				'doctor_name'           => array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'link', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'doctors/manage/?id={doctor_id}&but_filter=Filter', 'linkText'=>'{doctor_name}', 'definedValues'=>array(), 'htmlOptions'=>array()),
				'patient_name'          => array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'link', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'patients/manage/?id={patient_id}&but_filter=Filter', 'linkText'=>'{patient_name}', 'definedValues'=>array(), 'htmlOptions'=>array()),
				'message' 			    => array('title'=>A::t('appointments', 'Message'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'maxLength'=>'70'),
				'created_at' 		    => array('title'=>A::t('appointments', 'Created at'), 'type'=>'datetime', 'align'=>'center', 'width'=>'130px', 'class'=>'center', 'headerClass'=>'left', 'isSortable'=>true, 'maxLength'=>'100', 'definedValues'=>array(null=>'--'), 'format'=>$dateTimeFormat),
				'rating_price'  	    => array('title'=>A::t('appointments', 'Rating Price'), 'type'=>'html', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'prependCode'=>'<label><img src="templates/default/images/small_star/smallstar-', 'appendCode'=>'.png" /></label>'),
				'rating_wait_time'      => array('title'=>A::t('appointments', 'Rating Wait Time'), 'type'=>'html', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'prependCode'=>'<label><img src="templates/default/images/small_star/smallstar-', 'appendCode'=>'.png" /></label>'),
				'rating_bedside_manner' => array('title'=>A::t('appointments', 'Rating Bedside Manner'), 'type'=>'html', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'prependCode'=>'<label><img src="templates/default/images/small_star/smallstar-', 'appendCode'=>'.png" /></label>'),
				'status'   			    => array('title'=>A::t('appointments', 'Status'), 'type'=>'label', 'width'=>'', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>$labelStatusReviews, 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::te('appointments', 'Status'))),
			),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('services', 'edit'),
                    'link'=>'doctorReviews/edit/id/{id}'.$statusParam, 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('services', 'delete'),
                    'link'=>'doctorReviews/delete/id/{id}'.$statusParam, 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'messagesSource' => 'core',
            'alerts'         => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Review')),
            'return'         => true,
        ));

        ?>
    </div>
</div>
