<?php
$this->_breadCrumbs = array(
    array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
    array('label'=>A::t('appointments', 'Orders')),
);

use \Modules\Appointments\Models\Orders;
?>

<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <?= !empty($actionMessage) ? $actionMessage : ''; ?>

                <?php
                $tableNameOrders = CConfig::get('db.prefix').Orders::model()->getTableName();
                $condition = $tableNameOrders.'.doctor_id = '.$doctorId.' AND '.$tableNameOrders.'.status > 0';
                echo CWidget::create('CGridView', array(
                    'model'=>'Modules\Appointments\Models\Orders',
                    'actionPath'=>'orders/orders',
                    'condition'	=> $condition,
                    'defaultOrder'=>array('created_date'=>'DESC'),
                    'passParameters'=>true,
                    'pagination'=>array('enable'=>true, 'pageSize'=>20),
                    'sorting'=>true,
                    'options'	=> array(
                        'filterDiv' 	=> array('class'=>'frmFilter'),
						'gridTable'     => array('class'=>'table'),
                    ),
                    'filters'=>array(
                        'order_number' => array('title'=>A::t('appointments', 'Order Number'), 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>''),
                        'created_date' => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'80px', 'maxLength'=>'', 'format'=>''),
                        'status'       => array('title'=>A::t('appointments', 'Status'), 'type'=>'enum', 'operator'=>'=', 'width'=>'100px', 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('appointments', 'select').' --', 'source'=>$status),
                    ),
                    'fields'=>array(
                        'order_number' => array('title'=>A::t('appointments', 'Order Number'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                        'created_date' => array('title'=>A::t('appointments', 'Date'), 'type'=>'datetime', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'format'=>$dateTimeFormat),
                        'membership_plan_id' => array('title'=>A::t('appointments', 'Plan'), 'type'=>'enum', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$membershipPlans),
                        'payment_id' => array('title'=>A::t('appointments', 'Payment Type'), 'type'=>'enum', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$allPaymentTypes),
                        'total_price'=> array('title'=>A::t('appointments', 'Price'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>true, 'prependCode'=>$pricePrependCode, 'appendCode'=>$priceAppendCode),
                        'status'             => array('type'=>'label', 'title'=>A::t('appointments', 'Status'), 'definedValues'=>$status, 'width'=>'', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
                    ),
                    'return'=>true,
                ));
                ?>
            </div>
        </div>
    </div>
</section>
