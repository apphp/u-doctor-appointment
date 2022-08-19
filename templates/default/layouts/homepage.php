<!--  Start Middle  -->
<section id="middle">
<?php
	echo A::app()->view->getContent();
?>
</section>
<!--  Finish Middle  -->

<!--  Twitter Start  -->
<?php
$twitter = SocialNetworks::model()->find("is_active = 1 AND code = 'twitter'");
if(!empty($twitter) && trim($twitter->link, '/') != 'http://twitter.com'):
	$content = CFile::getFileContent($twitter->link);
	preg_match_all("'<p class=\"TweetTextSize(.*?)>(.*?)</p>'si", $content, $match);
	$twits = !empty($match[2]) ? $match[2] : array(A::t('appointments', 'No twits found!'));
	$maxTwits = 10;
?>
<div id="cmsms_latest_bottom_tweets">
	<div class="cmsms_latest_bottom_tweets_inner">
		<span class="tweets_icon"></span>
		<ul class="jta-tweet-list responsiveContentSlider">
		<?php foreach($twits as $twit):
			if($maxTwits-- <= 0) break;
		?>
			<li class="jta-tweet-list-item">
				<?= str_replace('href="/', 'target="_blank" rel="noopener noreferrer" href="https://twitter.com/', $twit); ?>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php
A::app()->getClientScript()->registerScript(
	'homepage-services',
	"jQuery('#cmsms_latest_bottom_tweets .jta-tweet-list').cmsmsResponsiveContentSlider( {
		sliderWidth : '100%',
		sliderHeight : 'auto',
		animationSpeed : 500,
		animationEffect : 'fade',
		animationEasing : 'linear',
		pauseTime : 7000,
		activeSlide : 1, 
		touchControls : true,
		pauseOnHover : false, 
		arrowNavigation : true, 
		slidesNavigation : false
	});",
	3
);
?>
<?php endif; ?>
<!--  Twitter Finish  -->

