<div class="locations-w3-agileits">
    <div class="container back-arrow">
        <h4><a href="mobile/"> < <?= A::t('appointments', 'Home'); ?></a></h4>
    </div>
    <div class="container">
        <h4 class="tittle-w3layouts"><?= A::t('news', 'News'); ?></h4>
    </div>
    <div class="container">
        <?= $actionMessage; ?>
        <?php foreach($news as $oneNews):
            $link = 'mobile/newsView/id/'.CHtml::encode($oneNews['id']).'?page='.$currentPage;
            ?>
            <div class="location-agileits">
                <div class="">
                    <a href="<?= $link; ?>"><h4><?= CHtml::encode($oneNews['news_header']); ?></h4></a>
                    <div class="single-left-left">
                        <p><i class="fa fa-eye"></i> <?= CHtml::encode($oneNews['hits']); ?> | <?= CLocale::date($dateFormat, $oneNews['created_at']); ?></p>
                        <img src="assets/modules/news/images/intro_images/<?= !empty($oneNews['intro_image']) ?  CHtml::encode($oneNews['intro_image']) : 'no_image_frontend.png' ; ?>" alt="<?= CHtml::encode($oneNews['news_header']); ?>" title="<?= CHtml::encode($oneNews['news_header']); ?>"/>
                    </div>
                    <div class="blog-left-bottom">
                        <p><?= CHtml::encode(CString::substr(strip_tags($oneNews['news_text']), 400, '', true)); ?></p>
                    </div>
                    <a class="more_button" href="<?= $link; ?>"><?=A::t('appointments', 'Read More');?></a>
                </div>
            </div>
        <?php endforeach;
        if($totalNews > 1):
            echo CWidget::create('CPagination', array(
                'actionPath'   => 'mobile/news',
                'currentPage'  => $currentPage,
                'pageSize'     => $pageSize,
                'totalRecords' => $totalNews,
                'showResultsOfTotal' => false,
                'linkType' => 0,
                'paginationType' 	=> 'prevNext|justNumbers',
                'linkNames' 		=> array('previous' => '', 'next'=>''),
                'showEmptyLinks' 	=> true,
                'htmlOptions' 		=> array('linksWrapperTag' => 'div', 'linksWrapperClass' => 'page-numbers'),
            ));
        endif;
        ?>
    </div>
</div>