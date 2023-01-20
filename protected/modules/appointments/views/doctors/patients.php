<?php
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Patients')),
    );

    use \Modules\Appointments\Models\Patients;

    $tableName = CConfig::get('db.prefix').Patients::model()->getTableName();
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
					<?= $actionMessage; ?>
                    <div class="margin-bottom-20">
                        <a href="doctors/addPatient" class="add-new button margin-right-5"><?= A::t('appointments', 'Add Patient'); ?></a>
                    </div>

                    <?php

                    $filterFields = array();
                    $condition = '';
                    $tablePatientsName = CConfig::get('db.prefix').Patients::model()->getTableName();
                    $filterFields['id']         = array('title'=>'', 'visible'=>false, 'table'=>$tablePatientsName, 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>'32');
                    $filterFields['patient_first_name,patient_last_name'] = array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32');
                    $filterFields['email']      = array('title'=>A::t('appointments', 'Email'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'100');

                    echo CWidget::create('CGridView', array(
                        'model'=>'Modules\Appointments\Models\Patients',
                        'actionPath'=>'doctors/patients',
                        'condition'	=> '',
                        'defaultOrder'=>array('created_at'=>'DESC'),
                        'passParameters'=>true,
                        'pagination'=>array('enable'=>true, 'pageSize'=>20),
                        'sorting'=>true,
                        'options'	=> array(
                            'filterDiv' 	=> array('class'=>'frmFilter'),
                            'gridWrapper'   => array('tag'=>'div', 'class'=>'table-responsive'),
                            'gridTable'     => array('class'=>'table'),
                        ),
                        'filters'=>$filterFields,
                        'fields'=>array(
                            'patient_last_name' => array('title'=>A::t('appointments', 'Last Name'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                            'patient_first_name' => array('title'=>A::t('appointments', 'First Name'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                            //'username' => array('title'=>A::t('appointments', 'Username'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                            'email' => array('title'=>A::t('appointments', 'Email'), 'type'=>'label', 'align'=>'', 'width'=>'150px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                            'phone' => array('title'=>A::t('appointments', 'Phone'), 'type'=>'label', 'align'=>'', 'width'=>'70px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                            'appt_link'       => array('title'=>'', 'type'=>'link', 'width'=>'90px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'doctors/appointments/status/all?patient_id={id}&but_filter=Filter', 'linkText'=>A::t('appointments', 'Appointments'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                            'medcard_link'    => array('title'=>'', 'type'=>'link', 'width'=>'100px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'patients/medicalCard/patientId/{id}', 'linkText'=>A::t('appointments', 'Medical Card'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                            //'is_active' => array('title'=>A::t('appointments', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
                        ),
                        'actions'=>array(
                            'edit'    => array(
                                'disabled'=>false,
                                'link'=>'doctors/editPatient/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                            ),
                        ),
                        'return'=>true,
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>