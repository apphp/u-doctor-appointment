<?php
    $this->_pageTitle = A::t('appointments', 'Add Image');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Images'), 'url'=>'doctorImages/myImages'),
        array('label'=>A::t('appointments', 'Add Image')),
    );

    $formName = 'frmDoctorImageAdd';
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php

						echo CWidget::create('CDataForm', array(
							'model'=>'Modules\Appointments\Models\DoctorImages',
							'operationType'=>'add',
							'action'=>'doctorImages/addMyImage',
							'successUrl'=>'doctorImages/myImages',
							'cancelUrl'=>'doctorImages/myImages',
							'method'=>'post',
							'htmlOptions'=>array(
								'id'=>$formName,
								'class'=>'doctor-form',
								'name'=>$formName,
								'autoGenerateId'=>true
							),
							'requiredFieldsAlert'=>false,
							'fields'=>array(
								'image_file'    => array(
									'type'              => 'imageUpload',
									'title'             => A::t('appointments', 'Image'),
									'validation'        => array('required'=>true, 'type'=>'image', 'maxSize'=>$imageMaxSize, 'targetPath'=>'assets/modules/appointments/images/doctorimages/', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'d'.$doctorId.'_'.CHash::getRandomString(10)),
									'imageOptions'      => array('showImage'=>false),
									'thumbnailOptions'  => array('create'=>true, 'directory'=>'thumbs/', 'field'=>'image_file_thumb', 'postfix'=>'_thumb', 'width'=>'170', 'height'=>'114'),
									'deleteOptions'     => array('showLink'=>false),
									'watermarkOptions'	=> array('enable'=>$doctorsWatermark, 'text'=>$watermarkText),
									'fileOptions'       => array('showAlways'=>false, 'class'=>'file', 'size'=>'25')
								),
								'title'         => array('type'=>'textbox', 'title'=>A::t('appointments', 'Image Title'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxlength'=>125, 'style'=>'width:73%;')),
								'sort_order'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'tooltip'=>'', 'default'=>'0', 'validation'=>array('required'=>true, 'type'=>'numeric', 'maxlength'=>3), 'htmlOptions'=>array('maxLength'=>3, 'style'=>'width:73%;')),
								'is_active'     => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'tooltip'=>'', 'default'=>'1', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array()),
								'doctor_id'    => array('type'=>'data', 'default'=>$doctorId),
							),
							'buttons'=>array(
								'submit' => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'', 'class'=>'button')),
								'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
							),
							'buttonsPosition'       => 'bottom',
							'messagesSource'        => 'core',
							'showAllErrors'         => false,
							'alerts'                => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Image')),
							'return'                => true,
						));
					?>
                    </div>
                </div>
            </div>
        </div>
    </section>
