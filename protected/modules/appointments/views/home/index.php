<?php
use \Modules\Appointments\Models\Doctors,
    \Modules\Appointments\Models\DoctorSpecialties,
    \Modules\Appointments\Models\Services,
    \Modules\Appointments\Components\AppointmentsComponent,
    \Modules\Appointments\Components\DoctorsComponent;
    

$baseUrl = A::app()->getRequest()->getBaseUrl();
$dateFormat = Bootstrap::init()->getSettings('date_format');

// BANNERS
if(!empty($banners)):
?>
    <section id="top">
        <div class="wrap_rev_slider">
            <!-- START REVOLUTION SLIDER 4.1.4 fullwidth mode -->    
            <div id="rev_slider_1_1_wrapper" class="rev_slider_wrapper fullwidthbanner-container" style="margin:0px auto;background-color:#E9E9E9;padding:0px;margin-top:0px;margin-bottom:0px;max-height:600px;">
                <div id="rev_slider_1_1" class="rev_slider fullwidthabanner" style="display:none;max-height:600px;height:600px;">
                    <ul>
                        <?php foreach($banners as $banner): ?>                    
                            <!-- SLIDE  -->
                            <li data-transition="random" data-slotamount="7" data-masterspeed="300" >
                                <!-- MAIN IMAGE -->
                                <img src="<?= $baseUrl; ?>assets/modules/banners/images/items/<?= $banner['image_file']; ?>" alt="image-<?= $banner['id']; ?>"  data-bgposition="center top" data-bgfit="cover" data-bgrepeat="no-repeat">
                                <!-- LAYERS -->
                                <?= $banner['banner_description']; ?>
                            </li>                    
                        <?php endforeach; ?>                    
                    </ul>
                </div>
            </div>
            <!-- END REVOLUTION SLIDER -->
            <div class="cl"></div>
        </div>
    </section>
<?php endif; ?>

<!-- _________________________ Start Top Sidebar _________________________ -->      
<section class="top_sidebar">
    <div class="top_sidebar_inner">
        <div class="one_first">
            <aside id="custom-colored-blocks-2" class="widget widget_custom_colored_blocks_entries">
                <div id="box_color_1" class="widget_colored_cell four_box">
                    <div class="widget_colored_cell_inner">
                        <a href="news/ViewAll"><h2 class="widgettitle"><?= A::t('appointments', 'Clinic News'); ?></h2></a>
                        <p>
                            <?= A::t('appointments', 'Clinic News Short Description'); ?>
                        </p>
                        <a href="news/ViewAll" class="button_widget" title="<?= A::te('appointments', 'Read More'); ?>">
                            <span><?= A::t('appointments', 'Read More'); ?></span>
                        </a>
                    </div>
                </div>
                <div id="box_color_2" class="widget_colored_cell four_box">
                    <div class="widget_colored_cell_inner">
                        <a href="doctors/ourStaff/top/10"><h2 class="widgettitle"><span><?= A::t('appointments', 'Top Doctors'); ?></span></h2></a>
                        <p>
                            <?= A::t('appointments', 'Top Doctors Short Description'); ?>
                        </p>
                        <a href="doctors/ourStaff/top/10" class="button_widget" title="<?= A::te('appointments', 'Read More'); ?>">
                            <span><?= A::t('appointments', 'Read More'); ?></span>
                        </a>
                    </div>
                </div>
                <div id="box_color_3" class="widget_colored_cell four_box">
                    <div class="widget_colored_cell_inner">
                        <a href="<?= Website::prepareLinkByFormat('cms', 'page_link_format', 4, A::te('appointments', 'Read More')); ?>"><h2 class="widgettitle"><?= A::t('appointments', '24 Hours Service'); ?></h2></a>
                        <p>
                            <?= A::t('appointments', '24 Hours Service Description'); ?>                            
                        </p>                        
                        <a href="<?= Website::prepareLinkByFormat('cms', 'page_link_format', 4, A::te('appointments', 'Read More')); ?>" class="button_widget" title="">
                            <span><?= A::t('appointments', 'Read More'); ?></span>
                        </a>
                    </div>
                </div>
                <?php if(!empty($workingDays) && is_array($workingDays)): ?>
                    <div id="box_color_4" class="widget_colored_cell four_box">
                        <div class="widget_colored_cell_inner">
                            <h2 class="widgettitle"><?= A::t('appointments', 'Opening Hours')?></h2><br>
                            <ul class="timeline-list">
                                <?php foreach($workingDays as $workingDay): ?>
                                    <li>
                                        <span class="fl">
                                            <?php
                                                echo A::t('i18n', 'weekDayNames.wide.'.$workingDay['week_day']);
                                                if(!empty($workingDay['week_end_day'])){
                                                    echo ' - '.A::t('i18n', 'weekDayNames.wide.'.$workingDay['week_end_day']);
                                                }
                                            ?>
                                        </span>
                                        <span class="fr"><?= $workingDay['start_time']; ?> - <?= $workingDay['end_time']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
        <div class="one_first">
            <?= $drawAppointmentsBlock; ?>
        </div>
        <div class="one_first">
            <aside id="text-2" class="widget widget_text">
                <div class="textwidget">
                    <h1><?= A::t('appointments', 'Awesome Staff that is Always Ready to Help You'); ?></h1>
                    <h4><?= A::t('appointments', 'Our goal is to provide state of the art and compassionate medical care'); ?></h4>
                </div>
            </aside>
        </div>
    </div>
</section>
<!-- _________________________ Finish Top Sidebar _________________________ -->     


<!--_________________________ Start Content _________________________ -->
<div class="content_wrap fullwidth">
    <section id="middle_content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_fourth first_column">
                    <div class="tac"><span class="content_icon icon-glyph-2">&nbsp;</span></div>
                    <h3 class="block_title tac"><?= A::t('appointments', 'Cardio Monitoring'); ?></h3>
                    <p class="tac"><?= A::t('appointments', 'Cardio Monitoring Description'); ?></p>
                </div>
                <div class="one_fourth">
                    <div class="tac"><span class="content_icon icon-glyph-3">&nbsp;</span></div>
                    <h3 class="block_title tac"><?= A::t('appointments', 'Medical Treatment'); ?></h3>
                    <p class="tac"><?= A::t('appointments', 'Medical Treatment Description'); ?></p>
                </div>
                <div class="one_fourth">
                    <div class="tac"><span class="content_icon icon-glyph-4">&nbsp;</span></div>
                    <h3 class="block_title tac"><?= A::t('appointments', 'Emergency Help'); ?></h3>
                    <p class="tac"><?= A::t('appointments', 'Emergency Help Description'); ?></p>
                </div>
                <div class="one_fourth">
                    <div class="tac"><span class="content_icon icon-glyph-5"></span></div>
                    <h3 class="block_title tac"><?= A::t('appointments', 'Second Opinion'); ?></h3>
                    <p class="tac"><?= A::t('appointments', 'Second Opinion Description'); ?></p>
                </div>
                

                <!--_________________________ Start Services _________________________ -->
                <div class="one_first first_column">
                    <section id="services_shortcode_services" class="post_type_shortcode">
                        <div class="post_type_shortcode_inner">
                            <h3><a href="services/viewAll"><?= A::t('appointments', 'Our Services'); ?></a></h3>
                            <?php if(!empty($services)): ?>
                            <ul class="post_type_list services_container responsiveContentSlider">
                                <li>
                                <?php
                                $servicesCount = 0;
                                $servicesTotal = count($services);
                                foreach($services as $service):
                                    $servicesCount++;
                                ?>
                                    <article class="service type-service hentry one_fourth format-slider">
                                        <a href="services/view/id/<?= $service['id']; ?>"></a>
                                        <figure>
                                            <a class="preloader" href="services/view/id/<?= $service['id']; ?>" title="<?= CHtml::encode($service['name']); ?>">
                                                <img width="440" height="440" src="assets/modules/appointments/images/services/<?= $service['image_file']; ?>" class="fullwidth post-image" alt="<?= CHtml::encode($service['name']); ?>" title="<?= CHtml::encode($service['name']); ?>" />
                                            </a>
                                        </figure>
                                        <div class="service_rollover">
                                            <header class="entry-header">
                                                <h5 class="entry-title">
                                                    <a href="services/view/id/<?= $service['id']; ?>" title="<?= CHtml::encode($service['name']); ?>"><?= CHtml::encode($service['name']); ?></a>
                                                </h5>
                                            </header>
                                            <footer class="entry-meta">
                                                <span class="post_category">
                                                    <?php
                                                        if(!empty($service['tags'])):
                                                            $tagsArray = explode(',',$service['tags']);
                                                            $count = 0;
                                                            foreach($tagsArray as $tag):                                                            
                                                                echo ($count++ > 0 ? ', ' : '').'<a href="services/view/id/'.$service['id'].'" rel="tag">'.CHtml::encode($tag).'</a>';
                                                            endforeach;                                                        
                                                        endif;
                                                    ?>&nbsp;
                                                </span>
                                            </footer>
                                            <div class="entry-content">
                                                <p><?= CHtml::encode($service['description']); ?></p>
                                            </div>
                                        </div>
                                    </article>
                                    <?php 
                                        echo (!empty($servicesCount) && $servicesCount % 4 == 0)
                                            ? "\r\n".'</li>'.(($servicesCount < $servicesTotal) ? "\r\n".'<li>' : '')
                                            : '';
                                    ?>
                                <?php endforeach; ?>
                                </li>
                            </ul>
                            <?php endif; ?>
                            <div class="cl"></div>
                        </div>
                    </section>
                </div>
               
                
                <!--_________________________ Start News _________________________ -->
                <?php if(Modules::model()->isInstalled('news')): ?>
                <div class="one_half first_column">
                    <section class="post_type_shortcode type_post">
                        <div class="post_type_shortcode_inner">
                            <h3><?= A::t('news', 'Latest News'); ?></h3>
                            <?php
                            if(!empty($news)):
                                $newsCount = 0;
                                $newsTotal = count($news);
                                foreach($news as $newsRecord):
                                    $newsCount++;
                                    $newsLink = Website::prepareLinkByFormat('news', 'news_link_format', $newsRecord['id'], $newsRecord['news_header']);
                            ?>
                                <article class="post type-post format-standard hentry category-home-post category-medical one_fourth">
                                    <?php if(!empty($newsRecord['intro_image'])): ?>
                                    <figure class="cmsms_post_type_img">
                                        <img width="150" height="150" src="assets/modules/news/images/intro_images/<?=CHtml::encode($newsRecord['intro_image']);?>" class="attachment-thumbnail post-image" alt="" title="" />
                                    </figure>
                                    <?php endif; ?>
                                    <header class="entry-header">
                                        <h4 class="entry-title">
                                            <a href="<?= $newsLink; ?>"><?= CHtml::encode($newsRecord['news_header']); ?></a>
                                        </h4>
                                        <div class="cmsms_post_info">
                                            <abbr class="published"><?= CLocale::date($dateFormat, $newsRecord['created_at']); ?></abbr>
                                            <span class="cmsms_comments_wrap">
                                                <?= A::t('news', 'Views'); ?> (<a class="cmsms_comments" href="<?= $newsLink; ?>" title=""><?= (int)$newsRecord['hits']; ?></a>)
                                            </span>
                                        </div>
                                    </header>
                                    <div class="entry-content">
                                        <p><?= CHtml::encode(CString::substr(strip_tags($newsRecord['news_text']), 220, '', true)); ?></p>
                                    </div>
                                </article>                            
                                <?php endforeach; ?>

                                <a href="news/ViewAll" class="button_view_all" title="<?= A::te('appointments', 'Read More'); ?>">
                                    <span><?= A::t('appointments', 'Read More'); ?></span>
                                </a>
                            <?php
                                else: 
                                    echo A::t('news', 'No news yet');    
                                endif;
                            ?>
                            <div class="cl"></div>
                        </div>
                    </section>
                </div>
                <?php endif; ?>
               
                
                <!--_________________________ Start Testimonials _________________________ -->
                <?php if(Modules::model()->isInstalled('testimonials')): ?>
                <div class="one_half">
                    <section id="services_shortcode_testimonials" class="post_type_shortcode type_testimonial">
                        <div class="post_type_shortcode_inner">
                            <h3><?= A::t('testimonials', 'Testimonials'); ?></h3>
                            <?php
                                if(!empty($testimonials)):
                                    $testimonialsTotal = count($testimonials);
                                    ?>
                                    <ul class="post_type_list services_container responsiveContentSlider">                                    
                                    <?php foreach($testimonials as $testimonialsRecord):
                                        $authorImage = !empty($testimonialsRecord['author_image']) ? $testimonialsRecord['author_image'] : '';
                                    ?>
                                        <li>
                                            <article class="testimonial type-testimonial hentry one_first">
                                                <div class="tl-content_wrap">
                                                    <div class="tl-content">
                                                        <blockquote>
                                                            <?= $testimonialsRecord['testimonial_text']; ?>
                                                        </blockquote>
                                                    </div>
                                                </div>
                                                <?php if(!empty($testimonialsRecord['author_image'])): ?>
                                                    <img width="100" height="100" src="assets/modules/testimonials/images/authors/<?= $testimonialsRecord['author_image']; ?>" class="attachment-thumbnail post-image" alt="Fredrick Keen" title="Fredrick Keen" />
                                                <?php endif; ?>
                                                <a class="tl_author"><?= $testimonialsRecord['author_name']; ?></a>
                                                <p class="tl_company"><?= $testimonialsRecord['author_company'].' '.$testimonialsRecord['author_position']; ?></p>
                                            </article>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                    
                                    <a href="testimonials/viewAll" class="button_view_all" title="<?= A::te('appointments', 'View All'); ?>">
                                        &nbsp;<span><?= A::t('appointments', 'View All'); ?> &raquo;</span>
                                    </a>
                                <?php
                                else: 
                                    echo A::t('testimonials', 'No testimonials yet');    
                                endif;
                            ?>                            
                            <div class="cl"></div>
                        </div>
                    </section>
                </div>
                <?php endif; ?>
                
                
                <?php if(Modules::model()->isInstalled('appointments')): ?>
                <div class="one_first first_column">
                    <h3><?= A::t('appointments', 'Our Doctors'); ?></h3>
                </div>
                <?php
                if(!empty($doctors)):
                    foreach($doctors as $doctor):
                ?>
                    <div class="one_fourth _first_column">
                        <div class="cmsms_our_team_wrap">
                            <div class="cmsms_our_team">
                                <div class="wrap_person">
                                    <figure>
                                        <img src="<?= DoctorsComponent::getDoctorImage($doctor); ?>" class="fullwidth img-profile-home" alt="" />
                                    </figure>
                                    <div class="cmsms_team_rollover glow_blue">
                                        <a href="<?= Website::prepareLinkByFormat('appointments', 'profile_link_format', $doctor['id'], DoctorsComponent::getDoctorName($doctor)); ?>" class="cmsms_link"><span></span></a>
                                    </div>
                                </div>
                                <header class="entry-header">
                                    <div>
                                        <h6 class="person_title"><a href="<?= Website::prepareLinkByFormat('appointments', 'profile_link_format', $doctor['id'], DoctorsComponent::getDoctorName($doctor)); ?>"><?= DoctorsComponent::getDoctorName($doctor); ?></a></h6>
                                        <p class="person_subtitle person_subtitle_h20">
                                        <?php
                                            $doctorSpecialtiesArray = array();
                                            if(!empty($doctorSpecialties[$doctor['id']])):
                                                foreach($doctorSpecialties[$doctor['id']] as $doctorSpecialty):
                                                    $doctorSpecialtiesArray[] = $doctorSpecialty['specialty_name'];
                                                endforeach;
                                            endif;
                                            if(count($doctorSpecialtiesArray) > 2):
                                                echo '<span class="pull-right cursor-pointer">&#x25BE;</span>';
                                            endif;
                                            echo implode(', ', $doctorSpecialtiesArray);
                                        ?>
                                        </p>
                                    </div>
                                </header>
                            </div>
                        </div>
                    </div>

                <?php
                    endforeach;
                endif;
                ?>
               <?php endif; ?>
            </div>
        </div>
    </section>
    <div class="cl"></div>
</div>
<!-- _________________________ Finish Content _________________________ -->     


<?php
A::app()->getClientScript()->registerScript(
    'homepage-general',
    "var tpj=jQuery;
    tpj(document).ready(function() {    
        tpj('.person_subtitle').on('click', function(){
            tpj(this).toggleClass('person_subtitle_h20');
        });
    });
	",
	3
);

if(!empty($banners)):
    A::app()->getClientScript()->registerScript(
        'homepage-banner',
        "var tpj=jQuery;
        tpj.noConflict();
        var revapi1;
    
        tpj(document).ready(function() {
        
        if(tpj('#rev_slider_1_1').revolution == undefined)
            revslider_showDoubleJqueryError('#rev_slider_1_1');
        else
            revapi1 = tpj('#rev_slider_1_1').show().revolution({
                dottedOverlay:'none',
                delay:".(! empty($rotationDelay) ? $rotationDelay * 1000 : 9000).",
                startwidth:1160,startheight:600,hideThumbs:200,
                thumbWidth:100,thumbHeight:50,thumbAmount:3,
                navigationType:'bullet',navigationArrows:'solo',navigationStyle:'round',
                touchenabled:'on',onHoverStop:'on',
                navigationHAlign:'center',navigationVAlign:'bottom',navigationHOffset:0,navigationVOffset:20,
                soloArrowLeftHalign:'left',soloArrowLeftValign:'center',soloArrowLeftHOffset:20,soloArrowLeftVOffset:0,
                soloArrowRightHalign:'right',soloArrowRightValign:'center',soloArrowRightHOffset:20,soloArrowRightVOffset:0,
                shadow:2,
                fullWidth:'on',fullScreen:'off',
                stopLoop:'off',stopAfterLoops:-1,stopAtSlide:-1,
                shuffle:'off',
                autoHeight:'off',
                forceFullWidth:'off',
                hideThumbsOnMobile:'off',hideBulletsOnMobile:'off',hideArrowsOnMobile:'off',hideThumbsUnderResolution:0,
                hideSliderAtLimit:0,hideCaptionAtLimit:0,hideAllCaptionAtLilmit:0,
                startWithSlide:0,
                videoJsPath:'#',
                fullScreenOffsetContainer: ''
            });
        }); //ready    
        ",
        3
    );
endif;

A::app()->getClientScript()->registerScript(
	'homepage-services',
	"jQuery('#services_shortcode_services .post_type_list').cmsmsResponsiveContentSlider({
        sliderWidth: '100%',
        sliderHeight: 'auto',
        animationSpeed: 500,
        animationEffect: 'slide',
        animationEasing: 'easeInOutExpo',
        pauseTime: 0,
        activeSlide: 1,
        touchControls: false,
        pauseOnHover: false,
        arrowNavigation: true,
        slidesNavigation: false
    });
	",
	3
);

if(Modules::model()->isInstalled('testimonials')):
    A::app()->getClientScript()->registerScript(
        'homepage-testimonials',
        "jQuery('#services_shortcode_testimonials .post_type_list').cmsmsResponsiveContentSlider({
            sliderWidth: '100%',
            sliderHeight: 'auto',
            animationSpeed: 500,
            animationEffect: 'slide',
            animationEasing: 'easeInOutExpo',
            pauseTime: 0,
            activeSlide: 1,
            touchControls: false,
            pauseOnHover: false,
            arrowNavigation: true,
            slidesNavigation: false
        });
        ",
        3
    );
endif;

?>