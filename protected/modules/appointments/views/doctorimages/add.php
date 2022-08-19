<?php
    Website::setMetaTags(array('title'=>A::t('appointments', 'Add Image')));

    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Doctor Images'), 'url'=>'doctorImages/manage/doctorId/'.$doctorId),
        array('label'=>A::t('appointments', 'Add Image')),
    );

    // Register script with general functions
    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/general.js');
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="sub-title">
        <a class="sub-tab active" href="doctorImages/manage/doctorId/<?= $doctorId; ?>"><?= A::t('appointments', 'Images').' | '.$doctorName; ?></a>
        <?= A::t('appointments', 'Add Image'); ?>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        echo CWidget::create('CDataForm', array(
            'model'                 => 'Modules\Appointments\Models\DoctorImages',
            'operationType'         => 'add',
            'action'                => 'doctorImages/add/doctorId/'.$doctorId,
            'successUrl'            => 'doctorImages/manage/doctorId/'.$doctorId,
            'cancelUrl'             => 'doctorImages/manage/doctorId/'.$doctorId,
            'method'                => 'post',
            'htmlOptions'           => array(
                'id'                    => 'frmDoctorImageAdd',
                'name'                  => 'frmDoctorImageAdd',
                'enctype'               => 'multipart/form-data',
                'autoGenerateId'        => true
            ),
            'requiredFieldsAlert'   => true,
            'fields'                => array(
                'doctor_id'    => array('type'=>'data', 'default'=>$doctorId),
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
                'title'         => array('type'=>'textbox', 'title'=>A::t('appointments', 'Image Title'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxlength'=>125, 'class'=>'middle')),
                'sort_order'    => array('type'=>'textbox', 'title'=>A::t('appointments', 'Sort Order'), 'tooltip'=>'', 'default'=>'0', 'validation'=>array('required'=>true, 'type'=>'numeric', 'maxlength'=>3), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
                'is_active'     => array('type'=>'checkbox', 'title'=>A::t('appointments', 'Active'), 'tooltip'=>'', 'default'=>'1', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
            ),
            'buttons'   => array(
                'submit'            => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'')),
                'cancel'            => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
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
