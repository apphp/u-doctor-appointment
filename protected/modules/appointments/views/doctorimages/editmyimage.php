<?php
    $this->_pageTitle = A::t('appointments', 'Edit Image');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Images'), 'url'=>'doctorImages/myImages'),
        array('label'=>A::t('appointments', 'Edit Image')),
    );

    $formName = 'frmDoctorImageEdit';
?>

    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php

                    echo CWidget::create('CDataForm', array(
                        'model'=>'Modules\Appointments\Models\DoctorImages',
                        'primaryKey'=>$id,
                        'operationType'=>'edit',
                        'action'=>'doctorImages/editMyImage/id/'.$id,
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
                                'type'              => 'imageupload',
                                'title'             => A::t('appointments', 'Image'),
                                'validation'        => array('required'=>true, 'type'=>'image', 'targetPath'=>'assets/modules/appointments/images/doctorimages/', 'maxSize'=>$imageMaxSize, 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'d'.$doctorId.'_'.CHash::getRandomString(10)),
                                'imageOptions'      => array('showImage'=>true, 'showImageName'=>true, 'showImageSize'=>true, 'showImageDimensions'=>true, 'imageClass'=>'image-edit-mode icon-xbig'),
                                'thumbnailOptions'  => array('create'=>true, 'directory'=>'thumbs/', 'field'=>'image_file_thumb', 'postfix'=>'_thumb', 'width'=>'170', 'height'=>'114'),
                                'deleteOptions'     => array('showLink'=>true, 'linkUrl'=>'doctorImages/editMyImage/id/'.$id.'/delete/image', 'linkText'=>A::t('appointments', 'Delete')),
                                'fileOptions'       => array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'assets/modules/appointments/images/doctorimages/')
                            ),
                            'title'         => array('type'=>'textbox', 'title'=>A::t('appointments', 'Image Title'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxlength'=>125, 'class'=>'middle')),
                            'sort_order'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'tooltip'=>'', 'default'=>'0', 'validation'=>array('required'=>true, 'type'=>'numeric', 'maxLength'=>3), 'htmlOptions'=>array('maxlength'=>3, 'class'=>'small')),
                            'is_active'     => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'tooltip'=>'', 'default'=>'1', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
                        ),
                        'buttons'=>array(
                            'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                            'submitUpdate'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                            'cancel'=>array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                        ),
                        'buttonsPosition'=>'bottom',
                        'alerts'=>array('type'=>'flash'),
                        'messagesSource'=>'core',
                        'return'=>true,
                    ));
                ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
