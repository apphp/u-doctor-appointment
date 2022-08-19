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
            </div>
            <?php if($providerSettings): ?>
                <div class="one_first first_column margin-bottom-20">
                    <div class="cmsms_features">
                        <h3><?= A::t('appointments', 'Payment Info'); ?></h3>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Name'); ?></span>
                            <span class="cmsms_features_item_desc"><?= CHtml::encode($providerSettings->name); ?></span>
                                <?php if($providerSettings->instructions != ''): ?>
                                    <span class="cmsms_features_item_title"><?= A::t('appointments', 'Instructions'); ?></span>
                                    <span class="cmsms_features_item_desc">
                                       <?= $providerSettings->instructions; ?>
                                    </span>
								<?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($doctor): ?>
                <div class="one_first first_column margin-bottom-20">
                    <div class="cmsms_features">
                        <h3><?= A::t('appointments', 'Doctor Info'); ?></h3>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Name'); ?></span>
                            <span class="cmsms_features_item_desc"><?= CHtml::encode($doctor->getFullName()); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($membershipPlan): ?>
                <div class="one_first first_column margin-bottom-20">
                    <div class="cmsms_features">
                        <h3><?= A::t('appointments', 'Membership Plan Info'); ?></h3>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Name'); ?></span>
                            <span class="cmsms_features_item_desc"><?= CHtml::encode($membershipPlan->name); ?></span>
                        </div>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Duration'); ?></span>
                            <span class="cmsms_features_item_desc"><?= CHtml::encode($durations[$membershipPlan->duration]); ?></span>
                        </div>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Price'); ?></span>
                            <span class="cmsms_features_item_desc"><?=  CHtml::encode(CCurrency::format($membershipPlan->price)); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="one_first first_column margin-bottom-20">
				<?= $form; ?>
            </div>
        </div>
    </div>
</section>