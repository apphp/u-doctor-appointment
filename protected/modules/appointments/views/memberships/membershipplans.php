<?php
$this->_breadCrumbs = array(
    array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
	array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
	array('label'=>A::t('appointments', 'Membership Plans')),
);

$count = 0;
?>

<section id="content" role="main">
	<div class="entry">
		<div class="cmsms_cc">
			<?php if(!empty($membershipPlans)): ?>
                <?php if(!empty($currentMembership)):
                    echo '<div class="one_first">';
                    $alert = A::t('appointments', 'Your current membership plan').': '.CHtml::encode($currentMembership['name']).'. '.A::t('appointments', 'Expires').': '.CLocale::date($dateFormat, $doctor->membership_expires) ;
                    echo CWidget::create('CMessage', array('info', $alert, array('button'=>true)));
                    echo '</div>';
                endif; ?>
                <?php foreach($membershipPlans as $membershipPlan):?>
                    <div class="one_third <?= ($count%3 == 0) ? 'first_column' : '' ; ?>" data-folder="column">
                        <div class="cmsms_pricing_table">
                            <?php if($membershipPlan['id'] == $currentMembership['id']): ?>
                                <div class="new-label new-top-right"><?= A::t('appointments', 'Current'); ?></div>
                            <?php endif; ?>
                            <h2 class="title"><?= CHtml::encode($membershipPlan['name']); ?></h2>
                            <div class="cmsms_price_outer">
                                <div>
                                    <span class="cmsms_price price"><?= ($membershipPlan['price'] != 0) ? CCurrency::format($membershipPlan['price']) : A::t('appointments', 'Free'); ?></span>
                                    <span class="cmsms_period period"><?= $durations[CHtml::encode($membershipPlan['duration'])]; ?></span>
                                </div>
                            </div>
                            <ul>
                                <li class="price_table_list_item">
                                    <?= A::t('appointments', 'Images Count').': '; ?>
                                    <?= (($membershipPlan['images_count'] > 0) ? '<span class="plan-features yes-features">' : '<span class="plan-features no-features">').CHtml::encode($membershipPlan['images_count']).'</span>'; ?>
                                </li>
                                <?php if($multiClinics): ?>
                                    <li class="price_table_list_item">
                                        <?= A::t('appointments', 'Clinics Count').': ';?>
                                        <?= (($membershipPlan['clinics_count'] > 0) ? '<span class="plan-features yes-features">' : '<span class="plan-features no-features">').CHtml::encode($membershipPlan['clinics_count']).'</span>'; ?>
                                    </li>
                                <?php endif; ?>
                                <li class="price_table_list_item">
                                    <?= A::t('appointments', 'Schedules Count').': ';?>
                                    <?= (($membershipPlan['schedules_count'] > 0) ? '<span class="plan-features yes-features">' : '<span class="plan-features no-features">').CHtml::encode($membershipPlan['schedules_count']).'</span>'; ?>
                                </li>
                                <li class="price_table_list_item">
                                    <?= A::t('appointments', 'Specialties Count').': ';?>
                                    <?= (($membershipPlan['specialties_count'] > 0) ? '<span class="plan-features yes-features">' : '<span class="plan-features no-features">').CHtml::encode($membershipPlan['specialties_count']).'</span>'; ?>
                                </li>
                                <li class="price_table_list_item">
                                    <?= A::t('appointments', 'Show In Search').': '; ?>
                                    <?= $membershipPlan['show_in_search'] ? '<span class="plan-features yes-features">'.A::t('appointments', 'Yes').'</span>' : '<span class="plan-features no-features">'.A::t('appointments', 'No').'</span>' ; ?>
                                </li>
                                <li class="price_table_list_item">
                                    <?= A::t('appointments', 'Enable Reviews').': '; ?>
                                    <?= $membershipPlan['enable_reviews'] ? '<span class="plan-features yes-features">'.A::t('appointments', 'Yes').'</span>' : '<span class="plan-features no-features">'.A::t('appointments', 'No').'</span>' ; ?>
                                </li>
                            </ul>
                            <div class="pricing_footer">
                                <a class="pricing_button buy" href="orders/checkout/<?= $membershipPlan['id']; ?>"><?= ($membershipPlan['price'] != 0) ? A::t('appointments', 'Buy It!') : A::t('appointments', 'Get Free'); ?></a>
                            </div>
                        </div>
                    </div>
			    <?php
                $count++;
                endforeach;
                ?>
            <?php else: ?>

            <?php endif; ?>
		</div>
	</div>
</section>