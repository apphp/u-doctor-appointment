<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors Management'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Schedules'), 'url'=>'doctorSchedules/manage/doctorId/'.$doctorId),
        array('label'=>A::t('appointments', 'Time Slots'), 'url'=>'doctorSchedules/manageTimeBlocks/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId),
        array('label'=>A::t('appointments', 'Add Time Slot')),
    );

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);
    A::app()->getClientScript()->registerScriptFile('assets/vendors/timepicker/jquery.timepicker.min.js', 2);
    A::app()->getClientScript()->registerCssFile('assets/vendors/timepicker/jquery.timepicker.min.css');

    $formName = 'frmDoctorScheduleTimeBlockAdd';
?>

<h1><?= A::t('appointments', 'Doctor Schedules Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab previous" href="doctorSchedules/manage/doctorId/<?= $doctorId; ?>"><?= A::t('appointments', 'Schedules').' | '.$doctorName; ?></a> »
        <a class="sub-tab previous" href="doctorSchedules/manageTimeBlocks/doctorId/<?= $doctorId; ?>/scheduleId/<?= $scheduleId; ?>"><?= A::t('appointments', 'Time Slots').' | '.$scheduleName; ?></a> »
        <?= A::t('appointments', 'Add Time Slot'); ?>
    </div>
    <div class="content">
        <?php
            if($multiClinics && is_array($doctorClinics)):
                $address = array('type'=>'select', 'title'=>A::t('appointments', 'Address'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($doctorClinics)), 'data'=>$doctorClinics, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array());
            else:
                $address = array('type'=>'data', 'default'=>$doctorClinics);
            endif;

        echo CWidget::create('CDataForm', array(
            'model'=>'Modules\Appointments\Models\DoctorScheduleTimeBlocks',
            'operationType'=>'add',
            'action'=>'doctorSchedules/addTimeBlock/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId,
            'successUrl'=>'doctorSchedules/manageTimeBlocks/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId,
            'cancelUrl'=>'doctorSchedules/manageTimeBlocks/doctorId/'.$doctorId.'/scheduleId/'.$scheduleId,
            'method'=>'post',
            'htmlOptions'=>array(
                'id'=>$formName,
                'name'=>$formName,
                'autoGenerateId'=>true
            ),
            'requiredFieldsAlert'=>true,
            'fields'=>array(
                'address_id'  => $address,
                'time_slots'  => array('type'=>'enum', 'title'=>A::t('appointments', 'Time Slots'), 'default'=>'15', 'validation'=>array('type'=>'set', 'source'=>array_keys($arrTimeSlots)), 'data'=>$arrTimeSlots, 'htmlOptions'=>array()),
                'week_day'    => array('type'=>'enum', 'title'=>A::t('appointments', 'Week Day'), 'default'=>'', 'validation'=>array('type'=>'set', 'source'=>array_keys($arrWeekDays)), 'data'=>$arrWeekDays, 'htmlOptions'=>array()),
                'time_from'   => array('type'=>'textbox', 'title'=>A::t('appointments', 'From Time'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'text'), 'htmlOptions'=>array('maxLength'=>'5', 'class'=>'small timepicker'), 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'minDate'=>'', 'maxDate'=>''),
                'time_to'     => array('type'=>'textbox', 'title'=>A::t('appointments', 'To Time'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'text'), 'htmlOptions'=>array('maxLength'=>'5', 'class'=>'small timepicker'), 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'maxDate'=>'', 'minDate'=>''),
                'doctor_id'   => array('type'=>'data', 'default'=>$doctorId),
                'schedule_id' => array('type'=>'data', 'default'=>$scheduleId),
            ),
            'buttons'=>array(
                'submit' => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'')),
                'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'buttonsPosition'   => 'bottom',
            'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Doctor Schedule')),
            'messagesSource'    => 'core',
            'return'            => true,
        ));
    ?>
    </div>
</div>
<?php
A::app()->getClientScript()->registerScript(
    'timepickerExample',
    '$(document).ready(function(){
        var formName = "'.$formName.'";
        
        //timepicker properties
        $(".timepicker").timepicker({
            timeFormat: "H:i",
            lang: {decimal: ".", mins: "'.A::t('appointments', 'min.').'", hr: "'.A::t('appointments', 'hr').'", hrs: "'.A::t('appointments', 'hrs').'"},
            maxTime: "23:59"
        });
        $("#"+formName+"_time_to").timepicker("option","showDuration",true);
        $(".timepicker").timepicker("option","step",$("#"+formName+"_time_slots").val());
        $("#"+formName+"_time_from").change(function(){
            $("#"+formName+"_time_to").timepicker("option","minTime",$("#"+formName+"_time_from").val());
        });
        $("#"+formName+"_time_slots").change(function(){
            $(".timepicker").timepicker("option","step",$("#"+formName+"_time_slots").val());
        });
        
        // Change dropdown box “Week Day” for the selected of the clinic
        $("#"+formName+"_address_id").change(function(){
            $(".alert").remove();
            appointments_timeBlocks_changeWeekDay(formName);
        });
        
        // Change “From time” and “To Time” for the selected of the week day
        $("#"+formName+"_week_day").change(function(){
            $(".alert").remove();
            appointments_timeBlocks_changeTimepicker(formName);  
        });
        
        var clinicId = "'.$clinicId.'";
        if(clinicId !== 0){
            appointments_timeBlocks_changeWeekDay(formName, clinicId);
        }
    });',
    1
);