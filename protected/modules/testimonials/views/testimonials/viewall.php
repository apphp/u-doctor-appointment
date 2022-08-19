<?php 
    Website::setMetaTags(array('title'=>A::t('testimonials', 'All Testimonials')));
	
	$this->_pageTitle = A::t('testimonials', 'All Testimonials');

	// Define active menu	
	$this->_activeMenu = 'testimonials/viewAll';

	// Define breadcrumbs title
	$this->_breadcrumbsTitle = A::t('testimonials', 'All Testimonials');

	// Define breadcrumbs for this page
	$this->_breadCrumbs = array(
		array('label'=>A::t('testimonials', 'Home'), 'url'=>Website::getDefaultPage()),
		array('label'=>A::t('testimonials', 'All Testimonials')),
	);
?>


<section id="content" role="main">
    <div class="entry-summary">
        <section class="testimonials">
            <?php
            if($actionMessage != ''):
                echo $actionMessage;
            else:
                $showTestimonials = count($testimonials);
                for($i=0; $i < $showTestimonials; $i++):
                    $country = ($testimonials[$i]['author_country'] != '') ? ', '.$testimonials[$i]['author_country'] : '';
                    $city = ($testimonials[$i]['author_city'] != '') ? ', '.$testimonials[$i]['author_city'] : '';
                    $company = ($testimonials[$i]['author_company'] != '') ? ' &laquo;'.$testimonials[$i]['author_company'].'&raquo;' : '';
                    $author_position = ($testimonials[$i]['author_position'] != '') ? ', '.$testimonials[$i]['author_position'] : '';
            ?>
            <!--_________________________ Start Aside Article _________________________ -->
            <article class="testimonial type-testimonial status-publish hentry">
                <div class="tl-content_wrap">
                    <div class="tl-content">
                        <blockquote>
                            <?= $testimonials[$i]['testimonial_text']; ?>
                        </blockquote>
                    </div>
                </div>
                <figure class="tl_author_img">
                    <img src="assets/modules/testimonials/images/authors/<?= ($testimonials[$i]['author_image']) ? $testimonials[$i]['author_image'] : 'no_image.png'; ?>" class="attachment-thumbnail wp-post-image" />
                </figure>
                <a target="_blank" href="#" class="tl_author"><?= $testimonials[$i]['author_name'].$company.$author_position.$city.$country; ?></a>
                <p class="tl_company"><?= date($dateTimeFormat, strtotime($testimonials[$i]['created_at'])); ?></p>
                <div class="cl"></div>
                <div class="divider"></div>
            </article>

            <!--_________________________ Finish Testimonial Article _________________________ -->
            <?php
                endfor;

                if($totalTestimonials > 1):
                    echo CWidget::create('CPagination', array(
                        'actionPath'   => 'testimonials/viewAll',
                        'currentPage'  => $currentPage,
                        'pageSize'     => $pageSize,
                        'totalRecords' => $totalTestimonials,
                        'showResultsOfTotal' => false,
                        'linkType' => 0,
						'paginationType' 	=> 'prevNext|justNumbers',
						'linkNames' 		=> array('previous' => A::t('appointments', 'previous'), 'next'=> A::t('appointments', 'next')),
						'showEmptyLinks' 	=> true,
						'htmlOptions' 		=> array('linksWrapperTag' => 'div', 'linksWrapperClass' => 'links-part'),
                    ));
                endif;
            endif;
            ?>
        </section>
    </div>
    <div class="entry">
        <div class="cmsms_cc"></div>
    </div>
</section>
