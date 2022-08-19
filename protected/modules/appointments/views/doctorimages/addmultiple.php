<?php
    Website::setMetaTags(array('title'=>A::t('appointments', 'Add Images')));

    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Doctor Images'), 'url'=>'doctorImages/manage/doctorId/'.$doctorId),
        array('label'=>A::t('appointments', 'Add Images')),
    );
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="sub-title">
        <a class="sub-tab active" href="doctorImages/manage/doctorId/<?= $doctorId; ?>"><?= A::t('appointments', 'Images').' | '.$doctorName; ?></a>
        <?= A::t('appointments', 'Add Images'); ?>
    </div>

    <div class="content">
        <?= $actionMessage; ?>

        <?php
            echo CWidget::create('CFormView', array(
                'action'        =>  'doctorImages/addMultiple/doctorId/'.$doctorId,
                'cancelUrl'     =>  'doctorImages/manage/doctorId/'.$doctorId,
                'method'        =>  'post',
                'htmlOptions'   =>  array(
                    'name'          =>  'form-contact',
                    'enctype'       =>  'multipart/form-data',
                    'autoGenerateId'=>  false
                ),
                'requiredFieldsAlert'=>true,
                'fields'=>array(
                    'act'           =>  array('type'=>'hidden', 'value'=>'send', 'htmlOptions'=>array()),
                    'doctor_image[]'=> array('type'=>'file', 'title'=>'', 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>'', 'htmlOptions'=>array('multiple'=>'multiple', 'id'=>'doctor_image')),
                ),
                'buttons'=>array(
                   'submit'=>array('type'=>'submit', 'value'=>A::te('appointments', 'Start Upload'), 'htmlOptions'=>array('name'=>'')),
                   'reset' =>array('type'=>'reset', 'value'=>A::te('appointments', 'Reset'), 'htmlOptions'=>array('class'=>'button white')),
                   'cancel'=>array('type'=>'button', 'value'=>A::te('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white', 'onclick'=>"$(location).attr('href','doctorImages/manage/doctorId/".$doctorId."')")),
                ),
                'buttonsPosition'=>'bottom',
                'return'=>true
            ));
        ?>
        <br>
    </div>
</div>

<?php
A::app()->getClientScript()->registerScript(
    'autoportalMultiUpload',
    '$(document).ready(function(){
        $("input:submit").click(function(){
            if(parseInt($("#doctor_image").get(0).files.length) > '.(int)$maxImages.'){
                alert("'.A::te('appointments', 'You can only upload a maximum of {count} files!', array('{count}'=>(int)$maxImages)).'");
                return false;
            }
            $(this).val("'.A::te("appointments", "Uploading...").'");
            $(this).closest("form").submit();
            $(this).attr("disabled", true);
        });
    });
    ',
    4
);
