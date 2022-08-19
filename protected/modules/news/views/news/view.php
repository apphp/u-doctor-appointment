<?php
Website::setMetaTags(array('title' => CHtml::encode($newsHeader)));

$this->_pageTitle = CHtml::encode($newsHeader);

$this->_breadCrumbs = array(
    array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=> A::t('news', 'All News'), 'url'=> 'news/viewAll'),
    array('label'=> CHtml::encode($newsHeader))
);

?>
<?= $actionMessage; ?>


<!--_________________________ Start Content _________________________ -->
<section id="content" role="main">
    <div class="entry">
        <section class="blog opened-article">

            <!--_________________________ Start Standard Article _________________________ -->
            <article class="post type-post status-publish format-standard hentry category-blog-post category-medical category-text-posts tag-blog tag-image-2">
                <div class="cmsms_info">
                    <span class="cmsms_post_format_img"></span>
                    <div class="cmsms_like">
                        <div class="cmsmsLike active">
                            <span><?= CHtml::encode($hits); ?></span>
                        </div>
                    </div>
                    <abbr class="published" title="<?= CHtml::encode($createdTitle); ?>">
                        <span class="cmsms_page_day"><?= CHtml::encode($createdDay); ?></span>
                        <span class="cmsms_page_year"><?= CHtml::encode($createdYear); ?></span>
                        <span class="cmsms_page_month"><?= CHtml::encode($createdMonth); ?></span>
                    </abbr>
                </div>
                <div class="ovh">
                    <header class="entry-header">
                        <figure>
                            <?= !empty($introImage) ? '<img class="fullwidth wp-post-image" src="assets/modules/news/images/intro_images/'.CHtml::encode($introImage).'" alt="news intro" />' : '<img class="fullwidth wp-post-image" src="assets/modules/news/images/intro_images/no_image_frontend.png" alt="news intro" />'; ?>
                        </figure>
                    </header>
                    <div class="entry-content">
                        <p>
                            <?= $newsText; ?>
                        </p>
                        <div class="cmsms_cc"></div>
                    </div>
                    <div class="cl"></div>
                    <footer class="entry-meta"></footer>
                </div>
            </article>
            <!--_________________________ Finish Standard Article _________________________ -->

        </section>
    </div>
</section>
<!-- _________________________ Finish Content _________________________ -->

