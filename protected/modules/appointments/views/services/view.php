<?php
Website::setMetaTags(array('title' => CHtml::encode($service->name)));
$this->_pageTitle = CHtml::encode($service->name);
$this->_breadCrumbs = array(
	array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
	array('label'=> A::t('appointments', 'Services'), 'url'=>'services/viewAll'),
	array('label'=> CHtml::encode($service->name))
);
?>
<section id="content" role="main">
    <div class="entry margin-bottom-20">
        <div class="cmsms_cc">
            <div class="one_half first_column">
                <figure>
                    <?php if(empty($service->image_file)): ?>
                        <img class="fullwidth" src="assets/modules/appointments/images/services/<?= CHtml::encode($service->image_file); ?>" alt="<?= CHtml::encode($service->name); ?>" title="<?= CHtml::encode($service->name); ?>" />
					<?php else: ?>
                        <img class="fullwidth" src="assets/modules/appointments/images/services/no_image.png" alt="<?= CHtml::encode($service->name); ?>" title="<?= CHtml::encode($service->name); ?>" />
					<?php endif; ?>
                </figure>
                <div class="cl"></div>
            </div>
            <div class="one_half">
                <div class="cmsms_features">
                    <h3><?= A::t('appointments', 'Special Info'); ?></h3>
                    <div class="cmsms_features_item">
                        <span class="cmsms_features_item_title"><?= A::t('appointments', 'Service Name'); ?></span>
                        <span class="cmsms_features_item_desc"><?= CHtml::encode($service->name); ?></span>
                    </div>
                    <div class="cmsms_features_item">
                        <span class="cmsms_features_item_title"><?= A::t('appointments', 'Tags'); ?></span>
                        <span class="cmsms_features_item_desc"><?= ($service->tags) ? CHtml::encode($service->tags) : '--'; ?></span>
                    </div>
                </div>
                <div class="cmsms_features">
                    <h3><?= A::t('appointments', 'Description'); ?></h3>
                    <span ><?= CHtml::encode($service->description); ?></span>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>
    <?= $shareLink;?>
</section>
