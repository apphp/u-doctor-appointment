<?php
    $this->_activeMenu = 'appointments/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Orders Management')),
    );

?>

<h1><?= A::t('appointments', 'Orders Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Appointments\Models\Orders',
            'relationType' => 'all',
            'actionPath'=>'orders/doctorsManage',
            'condition'=>CConfig::get('db.prefix').'appt_orders.status > 0 AND '.CConfig::get('db.prefix').'appt_orders.payer = \'patient\'',
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'order_number' => array('title'=>A::t('appointments', 'Order Number'), 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>''),
                'created_date' => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'80px', 'maxLength'=>'', 'format'=>''),
                'status'       => array('title'=>A::t('appointments', 'Status'), 'type'=>'enum', 'operator'=>'=', 'width'=>'100px', 'emptyOption'=>true, 'emptyValue'=>'--', 'source'=>$allStatus, 'emptyOption'=>true, 'emptyValue'=>''),
            ),
            'fields'=>array(
                'order_number' => array('title'=>A::t('appointments', 'Order Number'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'created_date' => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'align'=>'', 'width'=>'140px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'format'=>$dateTimeFormat),
                'patient_id'   => array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'link', 'align'=>'', 'width'=>'130px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'linkUrl'=>'patients/manage/?id={patient_id}&but_filter=Filter', 'definedValues'=>array(), 'linkText'=>'{patient_name}', 'htmlOptions'=>array('target'=>'_blank')),
                'doctor_id'    => array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'link', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'linkUrl'=>'doctors/manage/?id={doctor_id}&but_filter=Filter', 'definedValues'=>array(), 'linkText'=>'{doctor_name}', 'htmlOptions'=>array('target'=>'_blank')),
//                'appointment_id' => array('title'=>'', 'type'=>'link', 'align'=>'', 'width'=>'90px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'linkUrl'=>'appointments/manage/?id={appointment_id}&but_filter=Filter', 'definedValues'=>array(), 'linkText'=>A::t('appointments', 'Appointment'), 'htmlOptions'=>array('target'=>'_blank')),
                'total_price'=> array('title'=>A::t('appointments', 'Price'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>true, 'prependCode'=>$pricePrependCode, 'appendCode'=>$priceAppendCode),
                'status' => array('title'=>A::t('appointments', 'Status'), 'type'=>'enum', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'source'=>array(1=>'<span class="label-gray label-square">'.$allStatus[1].'</span>', 2=>'<span class="label-green label-square">'.$allStatus[2].'</span>', 3=>'<span class="label-red label-square">'.$allStatus[3].'</span>', 4=>'<span class="label-red label-square">'.$allStatus[4].'</span>', 5=>'<span class="label-red label-square">'.$allStatus[5].'</span>')),
            ),
            'actions'=>array(
                'edit'       => array(
                    'disabled'      => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('order', 'edit'),
                    'link'          => 'orders/patientEdit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'=>array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('order', 'delete'),
                    'link'=>'orders/patientDelete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));

    ?>
    </div>
</div>
