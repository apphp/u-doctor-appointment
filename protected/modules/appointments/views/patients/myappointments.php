<?php
$this->_activeMenu = 'appointments/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
    array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'patients/dashboard'),
    array('label'=>A::t('appointments', 'My Appointments')),
);

use \Modules\Appointments\Models\Appointments;
use Modules\Appointments\Components\DoctorsComponent;
$tableName = CConfig::get('db.prefix').Appointments::model()->getTableName();

?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
					<?= $actionMessage; ?>
                    <div class="tab-link">
                        <ul class="tabs active">
							<?= $status == 'future' ? '<li class="current"><a href="javascript:void();">' : '<li><a href="patients/myAppointments/status/future">'; ?><span><?= A::t('appointments', 'Future'); ?></span></a></li>
							<?= $status == 'future' ? '<li><a href="patients/myAppointments/status/past">' : '<li class="current"><a href="javascript:void();">'; ?><span><?= A::t('appointments', 'Past'); ?></span></a></li>
                        </ul>
                        <div class="tab_content">
                            <div class="tabs_tab" style="display: block;">
								<?php
								if($status == 'future'){
									$condition	= $tableName.'.patient_id = '.$patientId.' AND ('.$tableName.'.status = 1 OR '.$tableName.'.status = 0) AND ('.$tableName.".appointment_date > '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableName.".appointment_time > '".LocalTime::currentDateTime('H:i:s')."'))";
								}else{
									$condition	= $tableName.'.patient_id = '.$patientId.' AND ('.$tableName.'.status = 1 OR '.$tableName.'.status = 0) AND ('.$tableName.".appointment_date < '".LocalTime::currentDateTime('Y-m-d')."'".' OR ('.$tableName.".appointment_date = '".LocalTime::currentDateTime('Y-m-d')."'".' AND '.$tableName.".appointment_time < '".LocalTime::currentDateTime('H:i:s')."'))";
								}

                                echo CWidget::create('CGridView', array(
                                    'model'=>'Modules\Appointments\Models\Appointments',
                                    'actionPath'=>'patients/myAppointments/status/'.$status,
                                    'condition'	=> $condition,
                                    'defaultOrder'=>array('appointment_date'=>'ASC'),
                                    'passParameters'=>true,
                                    'pagination'=>array('enable'=>true, 'pageSize'=>20),
                                    'sorting'=>true,
                                    'options'	=> array(
                                        'filterDiv' 	=> array('class'=>'frmFilter'),
										'gridWrapper'   => array('tag'=>'div', 'class'=>'table-responsive'),
                                        'gridTable'     => array('class'=>'table'),
                                    ),
                                    'filters'=>array(
                                        'appointment_number' => array('title'=>A::t('appointments', 'ID'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32'),
                                        'appointment_date' 	=> array('title'=>A::t('appointments', 'Appointment Date'), 'type'=>'datetime', 'align'=>'center', 'width'=>'', 'class'=>'center', 'headerClass'=>'left', 'isSortable'=>true, 'maxLength'=>'100', 'definedValues'=>array(null=>'--'), 'format'=>$dateTimeFormat),
                                        'doctor_first_name,doctor_middle_name,doctor_last_name' => array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32'),
                                    ),
                                    'fields'=>array(
                                        ///'appointment_number' => array('type'=>'label', 'title'=>A::t('appointments', 'Appointment ID'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                                        'doctor_name'        => array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'link', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'doctors/profile/{doctor_id}', 'linkText'=>'{doctor_name}<br>({specialty_name})', 'definedValues'=>array(), 'htmlOptions'=>array()),
                                        //'specialty_name'     => array('type'=>'label', 'title'=>A::t('appointments', 'Specialty'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                                        'appointment_date' 	 => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'maxLength'=>'', 'format'=>$dateFormat, 'htmlOptions'=>array()),
                                        'appointment_time' 	 => array('title'=>A::t('appointments', 'Time'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'maxLength'=>'', 'format'=>$appointmentTimeFormat, 'htmlOptions'=>array()),
                                        'clinic_name'        => array('title'=>A::t('appointments', 'Clinic Name'), 'type'=>'link', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'linkUrl'=>'clinics/{doctor_address_id}/{clinic_name}', 'linkText'=>'{clinic_name}', 'definedValues'=>array(), 'htmlOptions'=>array()),
                                        'status'             => array('type'=>'label', 'title'=>A::t('appointments', 'Status'), 'definedValues'=>$labelStatusAppointments, 'width'=>'', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
                                        'change_link'        => array('title'=>'', 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'patients/changeMyAppointment/id/{id}', 'linkText'=>A::t('appointments', 'Change'), 'prependCode'=>'[ ', 'appendCode'=>' ]', 'htmlOptions'=>array('onclick'=>'return onChangeRecord(this)'), 'disabled'=>($status == 'future' ? false: true)),
                                        'cancel_link'        => array('title'=>'', 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'patients/cancelMyAppointment/id/{id}/status/'.$status, 'linkText'=>A::t('appointments', 'Cancel'), 'prependCode'=>'[ ', 'appendCode'=>' ]', 'htmlOptions'=>array('onclick'=>'return onCancelRecord(this)'), 'disabled'=>($status == 'future' ? false: true)),
                                    ),
									'actions'=>array(
										'edit'    => array(
											'link'=>'patients/editMyAppointment/id/{id}/status/'.$status, 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
										),
									),
									'return'=>true,
                                ));
								?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<?php
	A::app()->getClientScript()->registerScript(
		'cancel-appointment',
		'function onCancelRecord(el){return confirm("'.A::te('appointments', 'Are you sure you want to cancel this appointment?').'");}',
		2
	);
	A::app()->getClientScript()->registerScript(
		'change-appointment',
		'function onChangeRecord(el){return confirm("'.A::te('appointments', 'Are you sure you want to change this appointment?').'");}',
		2
	);
?>