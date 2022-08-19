<?php
    Website::setMetaTags(array('title'=>A::t('appointments', 'Services')));
    $this->_pageTitle = A::t('appointments', 'Services');
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Services'))
    );
    A::app()->getClientScript()->registerScriptFile('templates/default/js/jquery.isotope.min.js', 2);
    A::app()->getClientScript()->registerScriptFile('templates/default/js/jquery.isotope.run.js', 2);
?>

<div class="s_sort_block">
    <div class="s_options_loader"></div>
    <div class="s_options_block">
        <div class="s_filter">
            <div class="s_filter_container">
                <a class="s_cat_filter button_small" data-filter="article.service" title="All Categories" href="javascript:void(0);">
                    <span><?= A::t('appointments', 'All Categories'); ?></span>
                </a>
                <ul class="s_filter_list">
                    <li class="current">
                        <a data-filter="article.service" title="All Categories" href="javascript:void(0);" class="current"><?= A::t('appointments', 'All Categories'); ?></a>
                    </li>
                    <?php foreach($tags as $tag): ?>
                    <li>
                        <a href="javascript:void(0);" data-filter="article.service[data-category~='<?= CHtml::encode(CString::strToLower($tag)); ?>']"><?= CHtml::encode($tag); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="cl"></div>
    </div>
</div>

<div class="content_wrap fullwidth">
    <!--_________________________ Start Content _________________________ -->
    <section id="middle_content" role="main">
        <div class="entry-summary">
            <section class="services three_columns">
                <?php
                if(!empty($services)):
                    foreach($services as $service):
                        $serviceLink = 'services/view/id/'.CHtml::encode($service['id']);
                    ?>
                    <!--_________________________ Start Slider Service _________________________ -->
                    <article class="service type-service status-publish hentry format-slider" data-category="<?= CHtml::encode(CString::strToLower(str_replace(",", " ", $service['tags']))) ?>">
                        <div class="services_inner">
                            <a href="<?= $serviceLink; ?>"></a>
                            <div class="media_box">
                                <figure>
                                    <a href="<?= $serviceLink; ?>" class="preloader">
                                        <img
                                            src="assets/modules/appointments/images/services/<?= (!empty($service['image_file']) ? CHtml::encode($service['image_file']) : 'no_image.png') ?>" class="fullwidth wp-post-image" alt="<?= CHtml::encode($service['name']); ?>"
                                            title="<?= CHtml::encode($service['name']); ?>"
                                        />
                                    </a>
                                </figure>
                            </div>
                            <div class="service_rollover">
                                <header class="entry-header">
                                    <h5 class="entry-title">
                                        <a href="<?= $serviceLink; ?>"><?= CHtml::encode($service['name']); ?></a>
                                    </h5>
                                </header>
                                <footer class="entry-meta">
                                    <span class="cmsms_category">
                                        <a href="javascript:void(0)" rel="tag"><?= CHtml::encode($service['tags']); ?></a>
                                    </span>
                                </footer>
                                <div class="entry-content">
                                    <p><?= CHtml::encode($service['description']); ?></p>
                                </div>
                            </div>
                        </div>
                    </article>
                    <!--_________________________ Finish Slider Service _________________________ -->
                    <?php endforeach; ?>
                <?php else:
                    echo CWidget::create('CMessage', array('warning', A::t('appointments', 'No services found!')));
                endif; ?>

            </section>
        </div>
        <div class="entry">
            <div class="cmsms_cc"></div>
        </div>
    </section>
    <!-- _________________________ Finish Content _________________________ -->

    <div class="cl"></div>
</div>
