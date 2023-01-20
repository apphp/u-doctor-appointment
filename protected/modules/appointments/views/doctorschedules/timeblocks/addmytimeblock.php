<?php
    $this->_pageTitle = A::t('appointments', 'Add Time Slot');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Schedules'), 'url'=>'doctorSchedules/mySchedules'),
        array('label'=>A::t('appointments', 'Time Slots'), 'url'=>'doctorSchedules/myTimeBlocks/scheduleId/'.$scheduleId),
        array('label'=>A::t('appointments', 'Add Time Slot')),
    );

    $formName = 'frmDoctorScheduleTimeBlockAdd';

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);
    A::app()->getClientScript()->registerScriptFile('templates/default/js/jquery.timepicker.min.js', 2);
    A::app()->getClientScript()->registerCssFile('templates/default/css/jquery.timepicker.min.css');
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder content">
                    <?php
                        if($multiClinics && is_array($doctorClinics)):
                            $address = array('type'=>'select', 'title'=>A::t('appointments', 'Address'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($doctorClinics)), 'data'=>$doctorClinics, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('style'=>'width:73%;'));
                        else:
                            $address = array('type'=>'data', 'default'=>$doctorClinics);
                        endif;

                        echo CWidget::create('CDataForm', array(
                            'model'=>'Modules\Appointments\Models\DoctorScheduleTimeBlocks',
                            'operationType'=>'add',
                            'action'=>'doctorSchedules/addMyTimeBlock/scheduleId/'.$scheduleId,
                            'successUrl'=>'doctorSchedules/myTimeBlocks/scheduleId/'.$scheduleId,
                            'cancelUrl'=>'doctorSchedules/myTimeBlocks/scheduleId/'.$scheduleId,
                            'method'=>'post',
                            'htmlOptions'=>array(
                                'id'=>$formName,
                                'class'=>'doctor-form',
                                'name'=>$formName,
                                'autoGenerateId'=>true
                            ),
                            'requiredFieldsAlert'=>false,
                            'fields'=>array(
                                'address_id'  => $address,
                                'time_slot_type_id' => array('type'=>'select', 'title'=>A::t('appointments', 'Time Slots Type'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($timeSlotsType)), 'data'=>$timeSlotsType, 'emptyOption'=>true, 'emptyValue'=>'- '.A::t('appointments', 'select').' -', 'htmlOptions'=>array('style'=>'width:73%;')),
                                'time_slots'  => array('type'=>'enum', 'title'=>A::t('appointments', 'Time Slots'), 'default'=>'15', 'validation'=>array('type'=>'set', 'source'=>array_keys($arrTimeSlots)), 'data'=>$arrTimeSlots, 'htmlOptions'=>array('style'=>'width:73%;')),
                                'week_day'    => array('type'=>'enum', 'title'=>A::t('appointments', 'Week Day'), 'default'=>'', 'validation'=>array('type'=>'set', 'source'=>array_keys($arrWeekDays)), 'data'=>$arrWeekDays, 'htmlOptions'=>array('style'=>'width:73%;')),
                                'time_from'   => array('type'=>'textbox', 'title'=>A::t('appointments', 'From Time'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'text'), 'htmlOptions'=>array('class'=>'small timepicker', 'style'=>'width:73%;'), 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'minDate'=>'', 'maxDate'=>''),
                                'time_to'     => array('type'=>'textbox', 'title'=>A::t('appointments', 'To Time'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'text'), 'htmlOptions'=>array('class'=>'small timepicker', 'style'=>'width:73%;'), 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'maxDate'=>'', 'minDate'=>''),
                                'doctor_id'   => array('type'=>'data', 'default'=>$doctorId),
                                'schedule_id' => array('type'=>'data', 'default'=>$scheduleId),
                            ),
                            'buttons'=>array(
                                'submit' => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'')),
                                'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
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