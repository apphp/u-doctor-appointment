<?php
    Website::setMetaTags(array('title'=>A::t('banners', 'Edit Banner')));
	
	$this->_activeMenu = 'modules/settings/code/banners';
    $this->_breadCrumbs = array(
        array('label'=>A::t('banners', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('banners', 'Banners'), 'url'=>'modules/settings/code/banners'),
        array('label'=>A::t('banners', 'Banners Management'), 'url'=>'banners/manage'),
        array('label'=>A::t('banners', 'Edit Banners')),
    );
?>

<h1><?= A::t('banners', 'Banners Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>
		
	<div class="sub-title"><?= A::t('banners', 'Edit Banners'); ?></div>
    <div class="content">
    <?php
		echo CWidget::create('CDataForm', array(
            'model'=>'Banners',
			'primaryKey'=>$banners->id,
            'operationType'=>'edit',
            'action'=>'banners/edit/id/'.$banners->id,     
			'successUrl'=>'banners/manage/msg/updated',
            'cancelUrl'=>'banners/manage/',
            'passParameters'=>false,
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmBannerEdit',
                'enctype'=>'multipart/form-data',
                'autoGenerateId'=>true
            ),
            'requiredFieldsAlert'=>true,
            'fields'=>array(
				'image_file' => array(
					'type'          => 'imageupload',
					'title'         => A::t('banners', 'Image'),
					'validation'    => array('required'=>false, 'type'=>'image', 'maxSize'=>'990k', 'targetPath'=>'assets/modules/banners/images/items/', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'a'.CHash::getRandomString(10)),
					'imageOptions'  => array('showImage'=>true, 'showImageName'=>true, 'showImageSize'=>true, 'showImageDimensions'=>true, 'imageClass'=>'icon-big'),
                    'thumbnailOptions' => array('create'=>true, 'field'=>'image_file_thumb', 'width'=>'120', 'height'=>'90'),
					'deleteOptions' => array('showLink'=>true, 'linkUrl'=>'banners/edit/id/'.$banners->id.'/image/delete', 'linkText'=>A::t('banners', 'Delete')),
					'fileOptions'   => array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'assets/modules/banners/images/items/')
				),
				'link_url'   => array('type'=>'textbox', 'title'=>A::t('banners', 'Link'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'link'), 'htmlOptions'=>array('maxlength'=>'255', 'class'=>'large')),
                'sort_order' => array('type'=>'textbox', 'title'=>A::t('banners', 'Sort Order'), 'tooltip'=>'', 'default'=>'0', 'validation'=>array('required'=>true, 'type'=>'numeric'), 'htmlOptions'=>array('maxlength'=>'4', 'class'=>'small')),
                'is_active'  => array('type'=>'checkbox', 'title'=>A::t('banners', 'Active'), 'tooltip'=>'', 'default'=>'1', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
            ),
			'translationInfo' => array('relation'=>array('id', 'banner_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
			'translationFields' => array(
				'banner_title' 	=> array('type'=>'textbox', 'title'=>A::t('banners', 'Title'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>255), 'htmlOptions'=>array('maxLength'=>'255', 'class'=>'large')),
				'banner_text'  	=> array('type'=>'textarea', 'title'=>A::t('banners', 'Description'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1024), 'htmlOptions'=>array('maxLength'=>'1024')),
				'banner_button' => array('type'=>'textbox', 'title'=>A::t('banners', 'Button Text'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>50), 'htmlOptions'=>array('maxLength'=>'50', 'class'=>'middle')),
			),
            'buttons'=>array(
                'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('banners', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
				'submitUpdate' => array('type'=>'submit', 'value'=>A::t('banners', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
				'cancel' => array('type'=>'button', 'value'=>A::t('banners', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
			'messagesSource' 	=> 'core',
			'showAllErrors'     => false,
			'alerts'            => array('type'=>'flash', 'itemName'=>A::t('banners', 'Banner')),
			'return'            => true,
        ));   			
	?>  
    </div>
</div>

