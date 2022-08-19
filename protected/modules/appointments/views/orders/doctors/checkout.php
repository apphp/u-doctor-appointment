<?php
$this->_breadCrumbs = array(
    array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
    array('label'=>A::t('appointments', 'Membership Plans'), 'url'=>'memberships/membershipPlans'),
    array('label'=>A::t('appointments', 'Membership Plan Checkout')),
);
?>

<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <?= $actionMessage; ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th class="text-left"><?=  A::t('appointments', 'Doctor Name'); ?></th>
                        <th class="text-left"><?=  A::t('appointments', 'Membership Plan'); ?></th>
                        <th class="text-left"><?=  A::t('appointments', 'Duration'); ?></th>
                        <th class="text-right"><?= A::t('appointments', 'Price'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= CHtml::encode($doctorFullName); ?></td>
                        <td><?= CHtml::encode($membershipPlan->name); ?></td>
                        <td><?= CHtml::encode($durations[$membershipPlan->duration]); ?></td>
                        <td><?= CHtml::encode(CCurrency::format($membershipPlan->price)); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="one_first first_column">
                <?php
                if(APPHP_MODE == 'demo'):
                    echo CWidget::create('CMessage', array('warning', A::t('core', 'This operation is blocked in Demo Mode!')));
                    echo CHtml::submitButton(A::t('appointments', 'Go To Payment'), array('class'=>'button'));
                elseif($membershipPlan->price == 0):
                    echo '<a class="button_small" href="orders/paymentForm/'.CHtml::encode($membershipPlan->id).'">'.A::t('appointments', 'Get Free').'</a>';
                else:
                    echo CHtml::openForm('orders/paymentForm/'.CHtml::encode($membershipPlan->id), 'post', array('id'=>'orderPayMembershipPlan'));
                    echo CHtml::hiddenField('act', 'send', array());
                    echo CHtml::hiddenField('membership', CHtml::encode($membershipPlan->id), array());
                    ?>
                    <fieldset>
                        <legend><?= A::t('appointments', 'Payment Method'); ?>:</legend>
                        <select name="type">
                            <option value=""><?= '-- '.A::t('appointments', 'select').' --'; ?></option>
                            <?php
                            if(is_array($providers)):
                                foreach($providers as $key => $provider):
                                    echo '<option value="'.$provider['code'].'">'.$provider['name'].'</option>';
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </fieldset>
                    <?php
                    echo CHtml::submitButton(A::t('appointments', 'Go To Payment'), array('class'=>'button'));
                    echo CHtml::closeForm();
                endif;
                ?>
            </div>
        </div>
    </div>
</section>
