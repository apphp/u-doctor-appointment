<!-- register tinymce files -->
<?php // A::app()->getClientScript()->registerScriptFile('assets/vendors/tinymce/tiny_mce.js'); ?>
<?php // A::app()->getClientScript()->registerScriptFile('assets/vendors/tinymce/config.js'); ?>
<?php // A::app()->getClientScript()->registerCssFile('assets/vendors/tinymce/general.css'); ?>

<?php
    $this->_activeMenu = 'modules/settings/code/[MODULE_CODE]';
    $this->_breadCrumbs = array(
        array('label'=>A::t('[MODULE_CODE]', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('[MODULE_CODE]', '[MODULE_NAME]'), 'url'=>'modules/settings/code/[MODULE_CODE]'),
        array('label'=>A::t('[MODULE_CODE]', '[CONTROLLER_NAME] Management'), 'url'=>'[MODULE_CODE]/manage'),
        array('label'=>A::t('[MODULE_CODE]', 'Edit [CONTROLLER_NAME]')),
    );
?>

<h1><?= A::t('[MODULE_CODE]', '[CONTROLLER_NAME] Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>
		
	<div class="sub-title"><?= A::t('[MODULE_CODE]', 'Edit [CONTROLLER_NAME]'); ?></div>
    <div class="content">
        <?php
			// echo $actionMessage;          
	    ?>
  
	<?php
		
		echo CWidget::create('CDataForm', array(
			'model'				=> 'Modules\Appointments\Models\Appointments',
			'primaryKey'		=> $id,
			'operationType'		=> 'edit',
			'action'			=> 'appointments/edit/id/'.$id,
			'successUrl'		=> 'appointments/manage',
			'cancelUrl'			=> 'appointments/manage',
			'passParameters'	=> false,
			'method'			=> 'post',
			'htmlOptions'		=> array(
				'name'				=> 'frmAppointmentsAdd',
				//'enctype'			=> 'multipart/form-data',
				'autoGenerateId'	=> true
			),
			'requiredFieldsAlert' => true,
			'fields'			=> array(
				'is_active'  		=> array('type'=>'checkbox', 'title'=>A::t('app', 'Active'), 'default'=>true, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array()),
			),
			'buttons'			=> array(
			   'submit' 			=> array('type'=>'submit', 'value'=>A::t('app', 'Create'), 'htmlOptions'=>array('name'=>'')),
			   'cancel' 			=> array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
			),
			'messagesSource'	=> 'core',
			'alerts'			=> array('type'=>'flash', 'itemName'=>A::t('app', 'Admin account')),
            'return'            => true,
		));		                
	?>    
    </div>
</div>

<?php 
//if($errorField != ''){
	// A::app()->getClientScript()->registerScript($formName, 'document.forms["'.$formName.'"].'.$errorField.'.focus();', 2);
//}
// A::app()->getClientScript()->registerScript('setTinyMceEditor', 'setEditor("news_text");', 2);
?>  
