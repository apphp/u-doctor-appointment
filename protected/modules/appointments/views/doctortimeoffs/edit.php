<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors Management'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Timeoffs'), 'url'=>'doctorTimeoffs/manage/doctorId/'.$doctorId),
        array('label'=>A::t('appointments', 'Edit Timeoff')),
    );

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('assets/vendors/timepicker/jquery.timepicker.min.js', 2);
    A::app()->getClientScript()->registerCssFile('assets/vendors/timepicker/jquery.timepicker.min.css');

    $formName = 'frmDoctorTimeoffEdit';
?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab previous" href="doctorTimeoffs/manage/doctorId/<?= $doctorId; ?>"><?= A::t('appointments', 'Timeoffs').' | '.$doctorName; ?></a> »
        <?= A::t('appointments', 'Edit Timeoff'); ?>
    </div>
    <div class="content">
        <?php

        echo CWidget::create('CDataForm', array(
            'model'=>'Modules\Appointments\Models\DoctorTimeoffs',
            'primaryKey'=>$id,
            'operationType'=>'edit',
            'action'=>'doctorTimeoffs/edit/doctorId/'.$doctorId.'/id/'.$id,
            'successUrl'=>'doctorTimeoffs/manage/doctorId/'.$doctorId,
            'cancelUrl'=>'doctorTimeoffs/manage/doctorId/'.$doctorId,
            'method'=>'post',
            'htmlOptions'=>array(
                'id'=>$formName,
                'name'=>$formName,
                'autoGenerateId'=>true
            ),
            'requiredFieldsAlert'=>true,
            'fields'=>array(
                'description' => array('type'=>'textbox', 'title'=>A::t('appointments', 'Description'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxlength'=>'255'), 'htmlOptions'=>array('maxlength'=>'255')),
                'date_from'   => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid From Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>'10'), 'htmlOptions'=>array('maxLength'=>'10', 'class'=>'medium'), 'viewType'=>'date', 'dateFormat'=>'yy-mm-dd', 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'minDate'=>'', 'maxDate'=>''),
                'time_from'   => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid From Time'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>'8'), 'htmlOptions'=>array('maxLength'=>'5', 'class'=>'medium'), 'viewType'=>'time', 'timeFormat'=>'HH:mm', 'definedValues'=>array(), 'format'=>''),
                'date_to'     => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid To Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'minValue'=>$minDate, 'maxLength'=>'10'), 'htmlOptions'=>array('maxLength'=>'10', 'class'=>'medium'), 'viewType'=>'date', 'dateFormat'=>'yy-mm-dd', 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'maxDate'=>'', 'minDate'=>''),
                'time_to'     => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid To Time'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>'8'), 'htmlOptions'=>array('maxLength'=>'5', 'class'=>'medium'), 'viewType'=>'time', 'timeFormat'=>'HH:mm', 'definedValues'=>array(), 'format'=>''),
            ),
            'buttons'=>array(
                'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'=>array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'buttonsPosition'       => 'bottom',
            'messagesSource'        => 'core',
            'showAllErrors'         => false,
            'alerts'                => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Doctor Timeoff')),
            'return'                => true,
        ));
    ?>
    </div>
</div>
<?php
A::app()->getClientScript()->registerScript(
    'timepickerExample',
    'jQuery(document).ready(function(){
        $(".timepicker").timepicker({
            timeFormat: "'.$timeFormat.'",
            lang: {decimal: ".", mins: "'.A::t('appointments', 'min.').'", hr: "'.A::t('appointments', 'hr').'", hrs: "'.A::t('appointments', 'hrs').'"},
            maxTime: "23:59:59"
        });
    });',
    1
);
?>
