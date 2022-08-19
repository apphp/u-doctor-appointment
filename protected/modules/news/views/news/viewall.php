<?php 
	Website::setMetaTags(array('title'=>A::t('news', 'All News')));

	$this->_activeMenu = 'news/viewAll';
	$this->_pageTitle = A::t('news', 'All News');	
	
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('news', 'All News'))
    );
	
?>
<!--_________________________ Start Content _________________________ -->
<section id="content" role="main">
	<div class="entry">
		<div class="cmsms_cc"></div>
	</div>
	<div class="entry-summary">
		<section class="blog">

        <?php
            if($actionMessage != ''){
                echo $actionMessage;
            }else{
                $showNews = count($news);
                for($i=0; $i < $showNews; $i++){
                    $newsLink = Website::prepareLinkByFormat('news', 'news_link_format', $news[$i]['id'], $news[$i]['news_header']);
                    $created = strtotime($news[$i]['created_at']);
                    $createdDay = date('d', $created);
					$createdMonth = A::t('i18n', 'monthNames.abbreviated.'.date('n', $created));
                    $createdYear = date('Y', $created);
                    $createdTitle = A::t('i18n', 'monthNames.wide.'.date('n', $created)).' '.$createdDay.', '.$createdYear;
        ?>
			<!--_________________________ Start Standard Article _________________________ -->
            <article class="post type-post status-publish format-standard hentry category-blog-post category-medical category-text-posts tag-blog tag-image-2">
				<div class="cmsms_info">
					<span class="cmsms_post_format_img"></span>
					<div class="cmsms_like">
						<div class="cmsmsLike active">
							<span><?= CHtml::encode($news[$i]['hits']); ?></span>
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
							<a href="<?= $newsLink; ?>" class="preloader" title="<?= CHtml::encode($news[$i]['news_header']); ?>">
                                <?php if(!empty($news[$i]['intro_image'])): ?>
								    <img src="assets/modules/news/images/intro_images/<?= CHtml::encode($news[$i]['intro_image']); ?>" class="fullwidth wp-post-image" alt="<?= CHtml::encode($news[$i]['news_header']); ?>" title="" />
								<?php else: ?>
                                    <img src="assets/modules/news/images/intro_images/no_image_frontend.png" class="fullwidth wp-post-image" alt="<?= CHtml::encode($news[$i]['news_header']); ?>" title="" />
								<?php endif; ?>
							</a>
						</figure>
						<h2 class="entry-title">
							<a href="<?= CHtml::encode($newsLink); ?>"><?= CHtml::encode($news[$i]['news_header']); ?></a>
						</h2>
						<div class="cmsms_post_info"></div>
					</header>
					<div class="entry-content">
						<p>
                            <?= CHtml::encode(CString::substr(strip_tags($news[$i]['news_text']), 700, '', true)); ?>
						</p>
					</div>
					<a class="more_button" href="<?= CHtml::encode($newsLink); ?>"><?= A::t('appointments', 'Read More'); ?></a>
					<div class="cl"></div>
					<div class="divider"></div>
					<footer class="entry-meta"></footer>
				</div>
			</article>
			<!--_________________________ Finish Standard Article _________________________ -->				

<?php
        }
        if($totalNews > 1){
            echo CWidget::create('CPagination', array(
                'actionPath'   => 'news/viewAll',
                'currentPage'  => $currentPage,
                'pageSize'     => $pageSize,
                'totalRecords' => $totalNews,
                'showResultsOfTotal' => false,
                'linkType' => 0,
                'paginationType' 	=> 'prevNext|justNumbers',
                'linkNames' 		=> array('previous' => A::t('appointments', 'previous'), 'next'=> A::t('appointments', 'next')),
                'showEmptyLinks' 	=> true,
                'htmlOptions' 		=> array('linksWrapperTag' => 'div', 'linksWrapperClass' => 'links-part'),
            ));            
        }
    }
?>    

		</section>
	</div>
</section>
<!-- _________________________ Finish Content _________________________ -->



