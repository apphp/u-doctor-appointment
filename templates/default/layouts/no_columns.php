<!--  Start Middle  -->
<section id="middle" class="services_page">

	<div class="headline">
        <h1>
        <?php
            $pageTitle = explode('|', $this->_pageTitle);
            echo !empty($pageTitle[0]) ? $pageTitle[0] : '';
        ?>
        </h1>
	</div>
    
	<?php
		$breadCrumbs = $this->_breadCrumbs;
		CWidget::create('CBreadCrumbs', array(
			'links' => $breadCrumbs,
			'wrapperClass' => 'cmsms_breadcrumbs',
			'linkWrapperTag' => 'span',
			'separator' => '<span class="breadcrumbs_sep">&nbsp;/&nbsp;</span>',
			'return' => false
		));
	?>

	<div class="content_wrap service_page">		
		<?= A::app()->view->getContent(); ?>
		<div class="cl"></div>
	</div>

</section>
<!--  Finish Middle  -->