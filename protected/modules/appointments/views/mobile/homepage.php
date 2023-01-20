<!-- banner -->
<?php if(!empty($banners)): ?>
    <div class="banner-silder">
        <div class="callbacks_container">
            <ul class="rslides callbacks callbacks1" id="slider4">
                <?php foreach($banners as $banner): ?>
                    <li>
                        <div class="w3layouts-banner-top">
                            <div class="container">
                                <img src="<?= $baseUrl; ?>assets/modules/banners/images/items/<?= $banner['image_file']; ?>" alt="image-<?= $banner['id']; ?>"  data-bgposition="center top" data-bgfit="cover" data-bgrepeat="no-repeat">
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="clearfix"> </div>
    </div>
<?php endif; ?>
<!-- //banner -->

<!-- banner-bottom -->
<div class="banner-bottom">
    <?php if($sitePhone): ?>
        <div class="bnr-btm-grids-agileits">
            <div class="bnr-btm-icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>
            <div class="bnr-btm-info">
                <h3><?= A::t('appointments', 'Give Us A Call'); ?></h3>
                <a href="tel:<?= preg_replace('/[^0-9]/', '', $sitePhone); ?>"><?= CHtml::encode($sitePhone); ?></a>
            </div>
            <div class="clearfix"> </div>
        </div>
    <?php endif; ?>
    <?php if($siteEmail): ?>
        <div class="bnr-btm-grids-agileits">
            <div class="bnr-btm-icon">
                <i class="fa fa-envelope-o" aria-hidden="true"></i>
            </div>
            <div class="bnr-btm-info">
                <h3><?= A::t('appointments', 'Send Us A Message'); ?></h3>
                <a href="mailto:<?= $siteEmail; ?>"><?= CHtml::encode($siteEmail); ?></a>
            </div>
            <div class="clearfix"> </div>
        </div>
    <?php endif; ?>
	<?php if($siteAddress): ?>
        <div class="bnr-btm-grids-agileits">
            <div class="bnr-btm-icon">
                <i class="fa fa-map-marker" aria-hidden="true"></i>
            </div>
            <div class="bnr-btm-info">
                <h3><?= A::t('appointments', 'Visit Our Location'); ?></h3>
                <p><?= $siteAddress; ?></p>
            </div>
            <div class="clearfix"> </div>
        </div>
    <?php endif; ?>
	<div class="clearfix"></div>
</div>
<!-- //banner-bottom -->

<!--Services-->
<div class="options-wthree">
	<div class="container">
		<ul>
			<li>
				<a href="mobile/appointments" class="opt-grids">
					<div class="icon-agileits-w3layouts">
						<i class="fa fa-calendar" aria-hidden="true"></i>
					</div>
					<div class="opt-text-w3layouts">
						<h6><?= A::t('appointments', 'Appointments'); ?></h6>
					</div>
				</a>
			</li>
			<li>
				<a href="mobile/doctors" class="opt-grids">
					<div class="icon-agileits-w3layouts">
						<i class="fa fa-stethoscope" aria-hidden="true"></i>
					</div>
					<div class="opt-text-w3layouts">
						<h6><?= A::t('appointments', 'Doctors'); ?></h6>
					</div>
				</a>
			</li>
            <li>
				<a href="mobile/clinics" class="opt-grids">
					<div class="icon-agileits-w3layouts">
						<i class="fa fa-home" aria-hidden="true"></i>
					</div>
					<div class="opt-text-w3layouts">
						<h6><?= A::t('appointments', 'Clinics'); ?></h6>
					</div>
				</a>
			</li>
			<li>
				<a href="mobile/services" class="opt-grids">
					<div class="icon-agileits-w3layouts">
						<i class="fa fa-ambulance" aria-hidden="true"></i>
					</div>
					<div class="opt-text-w3layouts">
						<h6><?= A::t('appointments', 'Services'); ?></h6>
					</div>
				</a>
			</li>
            <?php if(Modules::model()->isInstalled('news')): ?>
                <li>
                    <a href="mobile/news" class="opt-grids">
                        <div class="icon-agileits-w3layouts">
                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                        </div>
                        <div class="opt-text-w3layouts">
                            <h6><?= A::t('news', 'News'); ?></h6>
                        </div>
                    </a>
                </li>
            <?php endif; ?>
            <li>
				<a href="mobile/about" class="opt-grids">
					<div class="icon-agileits-w3layouts">
						<i class="fa fa-user-md" aria-hidden="true"></i>
					</div>
					<div class="opt-text-w3layouts">
						<h6><?= A::t('appointments', 'About Us'); ?></h6>
					</div>
				</a>
			</li>
			<li>
				<a href="mobile/contact" class="opt-grids">
					<div class="icon-agileits-w3layouts">
						<i class="fa fa-phone" aria-hidden="true"></i>
					</div>
					<div class="opt-text-w3layouts">
						<h6><?= A::t('appointments', 'Contact Us'); ?></h6>
					</div>
				</a>
			</li>
		</ul>
	</div>
</div>
<p><br></p>
<!--//Services-->

<script>
    // You can also use "$(window).load(function() {"
    $(function () {
        // Slideshow 4
        $("#slider4").responsiveSlides({
            auto: true,
            pager:true,
            nav:false,
            speed: 500,
            namespace: "callbacks",
            before: function () {
                $('.events').append("<li>before event fired.</li>");
            },
            after: function () {
                $('.events').append("<li>after event fired.</li>");
            }
        });

    });
</script>
<script>
    // You can also use "$(window).load(function() {"
    $(function () {
        // Slideshow 3
        $("#slider3").responsiveSlides({
            auto: true,
            pager:false,
            nav: true,
            speed: 500,
            namespace: "callbacks",
            before: function () {
                $('.events').append("<li>before event fired.</li>");
            },
            after: function () {
                $('.events').append("<li>after event fired.</li>");
            }
        });

    });
</script>

