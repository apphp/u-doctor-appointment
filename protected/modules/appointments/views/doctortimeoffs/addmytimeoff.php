<?php
    $this->_pageTitle = A::t('appointments', 'Add Timeoff');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Timeoffs'), 'url'=>'doctorTimeoffs/myTimeoffs'),
        array('label'=>A::t('appointments', 'Add Timeoff')),
    );

    $formName = 'frmDoctorTimeoffAdd';

    // register module javascript
    A::app()->getClientScript()->registerScriptFile('templates/default/js/jquery.timepicker.min.js', 2);
    A::app()->getClientScript()->registerCssFile('templates/default/css/jquery.timepicker.min.css');
    A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
    A::app()->getClientScript()->registerScriptFile('assets/vendors/jquery/jquery-ui.min.js', 1);
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php

                    echo CWidget::create('CDataForm', array(
                        'model'=>'Modules\Appointments\Models\DoctorTimeoffs',
                        'operationType'=>'add',
                        'action'=>'doctorTimeoffs/addMyTimeoff',
                        'successUrl'=>'doctorTimeoffs/myTimeoffs',
                        'cancelUrl'=>'doctorTimeoffs/myTimeoffs',
                        'method'=>'post',
                        'htmlOptions'=>array(
                            'id'=>$formName,
                            'class'=>'doctor-form',
                            'name'=>$formName,
                            'autoGenerateId'=>true
                        ),
                        'requiredFieldsAlert'=>false,
                        'fields'=>array(
                            'description' => array('type'=>'textbox', 'title'=>A::t('appointments', 'Description'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxlength'=>'255'), 'htmlOptions'=>array('maxlength'=>'255', 'style'=>'width:73%;')),
                            'date_from'   => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid From Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>'10'), 'htmlOptions'=>array('maxLength'=>'10', 'class'=>'medium'), 'viewType'=>'date', 'dateFormat'=>'yy-mm-dd', 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'minDate'=>'', 'maxDate'=>''),
                            'time_from'   => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid From Time'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>'8'), 'htmlOptions'=>array('maxLength'=>'8', 'class'=>'medium'), 'viewType'=>'time', 'timeFormat'=>'HH:mm:ss', 'definedValues'=>array(), 'format'=>''),
                            'date_to'     => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid To Date'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'minValue'=>$minDate, 'maxLength'=>'10'), 'htmlOptions'=>array('maxLength'=>'10', 'class'=>'medium'), 'viewType'=>'date', 'dateFormat'=>'yy-mm-dd', 'definedValues'=>array(), 'format'=>'', 'buttonTrigger'=>true, 'maxDate'=>'', 'minDate'=>''),
                            'time_to'     => array('type'=>'datetime', 'title'=>A::t('appointments', 'Valid To Time'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>'8'), 'htmlOptions'=>array('maxLength'=>'8', 'class'=>'medium'), 'viewType'=>'time', 'timeFormat'=>'HH:mm:ss', 'definedValues'=>array(), 'format'=>''),
                            'doctor_id'   => array('type'=>'data', 'default'=>$doctorId),
                        ),
                        'buttons'=>array(
                            'submit' => array('type'=>'submit', 'value'=>A::t('appointments', 'Create'), 'htmlOptions'=>array('name'=>'', 'class'=>'button')),
                            'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                        ),
						'buttonsPosition'       => 'bottom',
						'messagesSource'        => 'core',
						'showAllErrors'         => false,
						'alerts'                => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Timeoff')),
						'return'                => true,
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
