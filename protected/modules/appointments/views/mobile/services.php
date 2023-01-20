<div class="services-w3-agileits">
    <div class="container back-arrow">
        <h4><a href="mobile/"> < <?= A::t('appointments', 'Home'); ?></a></h4>
    </div>
    <div class="container">
        <h4 class="tittle-w3layouts"><?= A::t('appointments', 'Services'); ?><h4>
                <?php
                    if(!empty($services)):
                        $countService = 0;
                        foreach($services as $service):
                            $tags = explode(",", $service['tags']);
                            $link = 'mobile/serviceView/'.CHtml::encode($service['id']);
							$countService++;
                        ?>
                            <a href="<?= $link; ?>">
                                <div class="col-md-12">
                                    <div class="services-grids">
                                        <div class="pull-left">
                                            <img
                                                style="height:100px;"
                                                src="assets/modules/appointments/images/services/<?= (!empty($service['image_file']) ? CHtml::encode($service['image_file']) : 'no_image.png') ?>" class="fullwidth wp-post-image" alt="<?= CHtml::encode($service['name']); ?>"
                                                title="<?= CHtml::encode($service['name']); ?>"
                                            />
                                        </div>
                                        <div class="pull-left text-left padding-left-20">
                                            <h4 style="max-width:170px;word-break: normal"><?= CHtml::encode($service['name']); ?></h4>
                                            <p><?= CHtml::encode(implode(', ', $tags)); ?></p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                <?php else:
                    echo CWidget::create('CMessage', array('warning', A::t('appointments', 'No services found!')));
                endif; ?>
            <div class="clearfix"> </div>
    </div>
</div>