<?php
A::app()->getClientScript()->registerCssFile('templates/mobile/css/custom.css');
?>
<div class="locations-w3-agileits">
    <div class="container">
        <div class="left-blog left-single">
            <div class="blog-left">
                <div class="back-arrow">
                    <h4><a href="mobile/services"> < <?= A::t('appointments', 'Services'); ?></a></h4>
                </div>
                <h3 class="margin-bottom-20"><?= $service->name; ?></h3>
                <div class="single-left-left">
                    <?php if($service->image_file): ?>
                        <img src="assets/modules/appointments/images/services/<?= CHtml::encode($service->image_file); ?>" alt="<?= CHtml::encode($service->name); ?>" title="<?= CHtml::encode($service->name); ?>" />
                    <?php else: ?>
                        <img src="<?= A::app()->getRequest()->getBaseUrl(); ?>assets/modules/appointments/images/services/no_image.png" alt="<?= CHtml::encode($service->name); ?>" title="<?= CHtml::encode($service->name); ?>" />
                    <?php endif; ?>
                </div>
                <div class="blog-left-bottom">
                    <?php if($service->tags): ?>
                        <div class="Categories stand-w3ls">
                            <h3><?= A::t('appointments', 'Tags').': '; ?><span><?= $service->tags; ?></span></h3>
                            <p></p>
                        </div>
                    <?php endif; ?>

                    <?php if($service->description): ?>
                        <div class="Categories stand-w3ls">
                            <h3><?= A::t('appointments', 'Description').': '; ?></h3>
                            <p><?= $service->description; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>