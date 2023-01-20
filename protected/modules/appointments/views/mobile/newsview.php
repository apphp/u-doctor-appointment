<div class="locations-w3-agileits">
    <div class="container">
        <div class="left-blog left-single">
            <div class="blog-left">
                <div class="back-arrow">
                    <h4><a href="mobile/news?page=<?= $currentPage; ?>"> < <?= A::t('news', 'News'); ?></a></h4>
                </div>
                <h4><?= CHtml::encode($news->news_header); ?></h4>
                <div class="single-left-left">
                    <p><i class="fa fa-eye"></i> <?= CHtml::encode($news->hits); ?> | <?= date($dateFormat, strtotime($news->created_at)); ?></p>
                    <img src="assets/modules/news/images/intro_images/<?= $news->intro_image ? CHtml::encode($news->intro_image) : 'no_image_frontend.png' ; ?>" alt="<?= CHtml::encode($news->news_header); ?>" title="<?= CHtml::encode($news->news_header); ?>"/>
                </div>
                <div class="blog-left-bottom">
                    <p><?= $news->news_text; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>