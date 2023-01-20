<?php
    $this->_activeMenu = 'orders/doctorsManage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Orders Management'), 'url'=>'orders/doctorsManage'),
        array('label'=>A::t('appointments', 'Edit Order')),
    );

    A::app()->getClientScript()->registerCss('order-edit', '
        span.label-blue, span.label-lightblue { width:auto; display:inline-block; padding:2px 9px; -webkit-border-radius:9px; -moz-border-radius:9px; border-radius:9px; font-size:11px; font-weight:normal; line-height:14px; color:#ffffff;   vertical-align:baseline; white-space:nowrap; text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25); }
        span.label-blue { background-color:#385cad;}
        span.label-lightblue { background-color:#789ced;}
        .invoice-box > table > tbody tr:nth-child(2n+1) > td{background:none;}
        .invoice-box > table > tbody tr:nth-child(2n) > td{background:none;}
    ');

    $statusColor = array('0'=>'gray', '1'=>'yellow', '2'=>'blue', '3'=>'green', '4'=>'red', '5'=>'red');
?>

<h1><?= A::t('appointments', 'Orders Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <?= $subTabs; ?>

    <div class="content">
    <?php
        echo $actionMessage;

        $fields = array();
        $showDataForm = true;
        $showInvoice = false;

        switch($subTabName):
            case 'invoice':
                $showDataForm = false;
                $showInvoice = true;

                break;

            case 'general':
            default:

                $fields = array(
                    'plan_name'         => array('type'=>'label',  'title'=>A::t('appointments', 'Membership Plan'), 'tooltip'=>'', 'default'=>$planName, 'validation'=>array(), 'htmlOptions'=>array()),
                    'doctor_name'       => array('type'=>'label',  'title'=>A::t('appointments', 'Doctor Name'), 'tooltip'=>'', 'default'=>$doctorName, 'validation'=>array(), 'htmlOptions'=>array()),
                    'order_number'      => array('type'=>'label',  'title'=>A::t('appointments', 'Order Number'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'htmlOptions'=>array()),
                    'payment_id'        => array('type'=>'label',  'title'=>A::t('appointments', 'Payment Type'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>$allPaymentTypes, 'htmlOptions'=>array()),
                    'payment_method'    => array('type'=>'label',  'title'=>A::t('appointments', 'Payment Method'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>$allPaymentMethods, 'htmlOptions'=>array()),
                    'description'       => array('type'=>'label',  'title'=>A::t('appointments', 'Description'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'htmlOptions'=>array()),
                    'status'            => array('type'=>'select', 'title'=>A::t('appointments', 'Status'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($allStatus)), 'data'=>$allStatus, 'htmlOptions'=>array()),
                    'created_date'      => array('type'=>'label',  'title'=>A::t('appointments', 'Date Created'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'definedValues'=>array(''=>A::t('appointments', 'Unknown'), null=>A::t('appointments', 'Unknown')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat),
                    'payment_date'      => array('type'=>'label',  'title'=>A::t('appointments', 'Payment Date'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'definedValues'=>array(''=>A::t('appointments', 'Unknown'), null=>A::t('appointments', 'Unknown')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat),
                    'status_changed'    => array('type'=>'label',  'title'=>A::t('appointments', 'Status Changed'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'definedValues'=>array(''=>A::t('appointments', 'Unknown'), null=>A::t('appointments', 'Unknown')), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat),
                    'total_price'       => array('type'=>'html',   'title'=>A::t('appointments', 'Price'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'definedValues'=>array(), 'htmlOptions'=>array(), 'prependCode'=>$beforePrice, 'appendCode'=>$afterPrice),
                );

                break;
        endswitch;

        if($showDataForm):
            echo CWidget::create('CDataForm', array(
                'model'=>'Modules\Appointments\Models\Orders',
                'primaryKey'=>$id,
                'relationType'=>'',
                'operationType'=>'edit',
                'action'=>'orders/doctorEdit/id/'.$id,
                'successUrl'=>'orders/doctorsManage',
                'cancelUrl'=>'orders/doctorsManage',
                'passParameters'=>false,
                'method'=>'post',
                'htmlOptions'=>array(
                    'id'        => 'frmOrderPreview',
                    'name'      => 'frmOrderPreview',
                    'enctype'   => 'multipart/form-data',
                    'autoGenerateId' => true
                ),
                'requiredFieldsAlert'=>true,
                'fields'            => $fields,
                'buttons'=>array(
                    'submitUpdateClose' => array('type'=>'submit', 'value'=>A::t('appointments', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                    'submitUpdate' => array('type'=>'submit', 'value'=>A::t('appointments', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                    'cancel' => array('type'=>'button', 'value'=>A::t('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                ),
                'messagesSource' 	=> 'core',
                'showAllErrors'     => false,
                'alerts'            => array('type'=>'flash', 'itemName'=>A::t('appointments', 'Order')),
                'return'            => true,
            ));
        endif;
?>

    <?php if($showInvoice): ?>
        <div class="invoice-box">
            <div class="buttons-wrapper bw-bottom">
                <a href="orders/doctorDownloadInvoice/orderId/<?= $id; ?>" class="export-data align-right"><b class="icon-export">&nbsp;</b> <?= A::t('appointments', 'Download Invoice'); ?></a>
            </div>

            <table class="pb10">
            <tr>
                <td class="title" colspan="2"><?= A::t('appointments', 'General'); ?>:</td>
            </tr>
            <tr>
                <td><?= A::t('appointments', 'Membership Plan'); ?>: </td><td><a href="memberships/manage?id=<?= $order->membership_plan_id; ?>&but_filter=Filter" target="_blank" rel="noopener noreferrer"><?= $planName; ?></a></td>
            </tr>
            <tr>
                <td width="30%"><?= A::t('appointments', 'Order Number'); ?>: </td><td><?= ($order->order_number ? $order->order_number : A::t('appointments', 'Unknown')); ?></td>
            </tr>
            <tr>
                <td><?= A::t('appointments', 'Status'); ?>: </td><td><?= isset($allStatus[$order->status]) ? $allStatus[$order->status] : A::t('appointments', 'Unknown'); ?></td>
            </tr>
            <tr>
                <td><?= A::t('appointments', 'Date Created'); ?>: </td><td><?= CLocale::date($dateTimeFormat, $order->created_date); ?></td>
            </tr>
            <tr>
                <td><b><?= A::t('appointments', 'Subtotal'); ?>: </b></td><td><b><?= $beforePrice.CNumber::format($order->order_price, $numberFormat, array('decimalPoints'=>2)).$afterPrice; ?></b></td>
            </tr>
            <tr>
                <td><b><?= A::t('appointments', 'Grand Total'); ?>: </b></td><td><b><?= $beforePrice.CNumber::format($order->total_price, $numberFormat, array('decimalPoints'=>2)).$afterPrice; ?></b></td>
            </tr>
            </table>

            <?php if(!empty($doctor)): ?>
                <table class="pb10">
                <tr>
                    <td class="title" colspan="2"><?= A::t('appointments', 'Doctor'); ?>:</td>
                </tr>
                <tr>
                    <td width="30%"><?= A::t('appointments', 'First Name'); ?>: </td><td><?= ($doctor->doctor_first_name ? $doctor->doctor_first_name : A::t('appointments', 'Unknown')); ?></td>
                </tr>
                <tr>
                    <td><?= A::t('appointments', 'Last Name'); ?>: </td><td><?= ($doctor->doctor_last_name ? $doctor->doctor_last_name : A::t('appointments', 'Unknown')); ?></td>
                </tr>
                <tr>
                    <td><?= A::t('appointments', 'Email'); ?>: </td><td><?= ($doctor->email ? $doctor->email : A::t('appointments', 'Unknown')); ?></td>
                </tr>
                <tr>
                    <td><?= A::t('appointments', 'Phone'); ?>: </td><td><?= ($doctor->phone ? $doctor->phone : A::t('appointments', 'Unknown')); ?></td>
                </tr>
                <tr>
                    <td><?= A::t('appointments', 'Address'); ?>: </td><td><?= ($doctor->address ? $doctor->address : A::t('appointments', 'Unknown')); ?></td>
                </tr>
                <tr>
                    <td><?= A::t('appointments', 'City'); ?>: </td><td><?= ($doctor->city ? $doctor->city : '--'); ?></td>
                </tr>
                <tr>
                    <td><?= A::t('appointments', 'Zip Code'); ?>: </td><td><?= ($doctor->zip_code ? $doctor->zip_code : '--'); ?></td>
                </tr>
                <tr>
                    <td><?= A::t('appointments', 'State/Province'); ?>: </td><td><?= (isset($arrStateNames[$doctor->state]) ? $doctor->state.' ('.$arrStateNames[$doctor->state].')' : $doctor->state); ?></td>
                </tr>
                <tr>
                    <td><?= A::t('appointments', 'Country'); ?>: </td><td><?= (isset($arrCountryNames[$doctor->country_code]) ? $arrCountryNames[$doctor->country_code] : A::t('appointments', 'Unknown')); ?></td>
                </tr>
                </table>
            <?php endif; ?>

            <table class="pb10">
            <tr>
                <td class="title" colspan="2"><?= A::t('appointments', 'Payment'); ?>:</td>
            </tr>
            <tr>
                <td width="30%"><?= A::t('appointments', 'Payment Type'); ?>: </td><td><?= isset($allPaymentTypes[$order->payment_id]) ? $allPaymentTypes[$order->payment_id] : A::t('appointments', 'Unknown'); ?></td>
            </tr>
            <tr>
                <td><?= A::t('appointments', 'Payment Method'); ?>: </td><td><?= isset($allPaymentMethods[$order->payment_method]) ? $allPaymentMethods[$order->payment_method] : A::t('appointments', 'Unknown'); ?></td>
            </tr>
            <tr>
                <td><?= A::t('appointments', 'Payment Date'); ?>: </td><td><?= ! CTime::isEmptyDateTime($order->payment_date) ? CLocale::date($dateTimeFormat, $order->payment_date) : A::t('appointments', 'Unknown'); ?></td>
            </tr>
            <tr>
                <td><?= A::t('appointments', 'Transaction ID'); ?>: </td><td><?= $order->transaction_number ? $order->transaction_number : ''; ?></td>
            </tr>
            </table>

        </div>
    <?php endif; ?>
    </div>
</div>