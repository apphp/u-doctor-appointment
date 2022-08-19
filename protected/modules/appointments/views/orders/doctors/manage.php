<?php
    $this->_activeMenu = 'appointments/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Orders Management')),
    );

    use \Modules\Appointments\Models\Orders;
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
        $tableNameOrders = CConfig::get('db.prefix').Orders::model()->getTableName();
        echo CWidget::create('CGridView', array(
            'model'             => 'Modules\Appointments\Models\Orders',
            'relationType'      => 'doctors',
            'actionPath'        => 'orders/doctorsManage',
            'condition'         => $tableNameOrders.'.status >= 0 AND '.$tableNameOrders.'.payer = \'doctor\'',
            'defaultOrder'		=> array('id'=>'DESC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'order_number' => array('title'=>A::t('appointments', 'Order Number'), 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>''),
                'created_date' => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'80px', 'maxLength'=>'', 'format'=>''),
                'status'       => array('title'=>A::t('appointments', 'Status'), 'type'=>'enum', 'operator'=>'=', 'width'=>'100px', 'emptyOption'=>true, 'emptyValue'=>'--', 'source'=>$allStatus, 'emptyOption'=>true),
            ),
            'fields'=>array(
                'order_number'  => array('title'=>A::t('appointments', 'Order Number'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'created_date'  => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'align'=>'', 'width'=>'140px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'format'=>$dateTimeFormat),
                'doctor_id'     => array('title'=>A::t('appointments', 'Doctor Name'), 'type'=>'link', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'linkUrl'=>'doctors/manage/?id={doctor_id}&last_name=&username=&email=&is_active=&but_filter=Filter', 'definedValues'=>array(), 'linkText'=>'{doctor_name}', 'htmlOptions'=>array()),
                'membership_plan_id' => array('title'=>A::t('appointments', 'Plan'), 'type'=>'enum', 'align'=>'', 'width'=>'110PX', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$membershipPlans),
                'payment_id'    => array('title'=>A::t('appointments', 'Payment Method'), 'type'=>'enum', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$allPaymentTypes),
                'total_price'   => array('title'=>A::t('appointments', 'Price').' &nbsp;&nbsp;', 'type'=>'label', 'align'=>'', 'width'=>'110px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>true, 'prependCode'=>$pricePrependCode, 'appendCode'=>$priceAppendCode. ' &nbsp;&nbsp;'),
                'status'        => array('title'=>A::t('appointments', 'Status'), 'type'=>'enum', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'source'=>array(0=>'<span class="label-gray label-square">'.$allStatus[0].'</span>', 1=>'<span class="label-gray label-square">'.$allStatus[1].'</span>', 2=>'<span class="label-green label-square">'.$allStatus[2].'</span>', 3=>'<span class="label-red label-square">'.$allStatus[3].'</span>', 4=>'<span class="label-red label-square">'.$allStatus[4].'</span>')),
            ),
            'actions'=>array(
                'edit'       => array(
                    'disabled'      => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('order', 'edit'),
                    'link'          => 'orders/doctorEdit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                ),
                'delete'=>array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('order', 'delete'),
                    'link'=>'orders/doctorDelete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));

    ?>
    </div>
</div>
