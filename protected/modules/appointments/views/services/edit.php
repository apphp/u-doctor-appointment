<?php
    $this->_activeMenu = 'services/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Services Management'), 'url'=>'modules/settings/manage'),
		array('label'=>A::t('appointments', 'Edit Service'))
    );
?>

<?php A::app()->getClientScript()->registerCssFile('assets/vendors/xoxco/jquery.tagsinput.css'); ?>
<?php A::app()->getClientScript()->registerScriptFile('assets/vendors/xoxco/jquery.tagsinput.js',2); ?>

<h1><?= A::t('appointments', 'Services Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
	<div class="sub-title"><?= A::t('appointments', 'Edit Service'); ?></div>

    <div class="content">
    <?php
        echo CWidget::create('CDataForm', array(
            'model'			=> 'Modules\Appointments\Models\Services',
            'primaryKey'    => $id,
			'operationType'	=> 'edit',
            'action'		=> 'services/edit/id/'.$id,
            'successUrl'	=> 'services/manage',
            'cancelUrl'		=> 'services/manage',
            'method'		=> 'post',
            'htmlOptions'	=> array(
                'id'	=> 'frmServiceEdit',
                'name'	=> 'frmServiceEdit',
                'autoGenerateId'=>true
            ),
            'requiredFieldsAlert'=>true,
            'fields'=>array(
				'image_file' => array(
					'type'          	=> 'imageupload',
					'title'         	=> A::t('appointments', 'Image'),
					'validation'    	=> array('required'=>false, 'type'=>'image', 'maxSize'=>'990k', 'targetPath'=>'assets/modules/appointments/images/services/', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'a'.CHash::getRandomString(10)),
					'imageOptions'  	=> array('showImage'=>true, 'showImageName'=>true, 'showImageSize'=>true, 'showImageDimensions'=>true, 'imageClass'=>'icon-big'),
                    'thumbnailOptions' 	=> array('create'=>false, 'field'=>'image_file_thumb', 'width'=>'120', 'height'=>'90'),
					'deleteOptions' 	=> array('showLink'=>true, 'linkUrl'=>'services/edit/id/'.$service->id.'/image/delete', 'linkText'=>A::t('appointments', 'Delete')),
					'fileOptions'   	=> array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'assets/modules/appointments/images/services/')
				),
                'sort_order'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>6, 'type'=>'integer'), 'htmlOptions'=>array('maxLength'=>6, 'class'=>'small')),
                'is_active'    => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Default'), 'default'=>0, 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
            ),
            'translationInfo'   => array('relation'=>array('id', 'service_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
            'translationFields' => array(
                'name'        => array('type'=>'textbox', 'title'=>A::t('appointments', 'Name'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'70'), 'htmlOptions'=>array('maxLength'=>'70')),
                'description' => array('type'=>'textarea', 'title'=>A::t('appointments', 'Descriptions'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255), 'htmlOptions'=>array('maxLength'=>'255')),
				'tags'        => array('type'=>'textbox', 'title'=>A::t('appointments', 'Tags'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>'125'), 'htmlOptions'=>array('maxLength'=>'125', 'class'=>'tags')),
            ),
            'buttons'=>array(
                'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'=>array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'buttonsPosition'   => 'bottom',
			'messagesSource' 	=> 'core',
			'showAllErrors'     => false,
			'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Service')),
			'return'            => true,
        ));
    ?>
	</div>
</div>

<script>
</script>

<?php
A::app()->getClientScript()->registerScript(
	'services-edit',
	"jQuery(document).ready(function($) {
		jQuery('.tags').tagsInput(
			{width:'580px', height: 'auto', maxTagCount:2}
		);
	});",
	3
);
?>