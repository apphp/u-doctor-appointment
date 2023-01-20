<?php
    $this->_pageTitle = CHtml::encode($profileDoctor->getFullName());
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Our Doctors'), 'url'=>'doctors/ourstaff'),
        array('label'=> CHtml::encode($profileDoctor->getFullName()))
    );

    $dateFormat = Bootstrap::init()->getSettings('date_format');

    //Modules
    use \Modules\Appointments\Components\DoctorsComponent;
?>

<section id="content" class="profile-doctor" role="main">
    <div class="entry margin-bottom-20">
        <div class="cmsms_cc">
            <div class="two_fifth first_column">
                <div class="resize">
                    <figure class="box">
						<?php if($profileDoctor->avatar): ?>
                            <a href="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar); ?>" data-group="img_5906" title="<?= CHtml::encode($fullname); ?>" class="preloader highImg jackbox">
                                <img class="fullwidth" src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar); ?>" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
                            </a>
							<?php
							if($countDoctorImages>0): ?>
                                <div class="profile_image margin-top-10">
                                    <ul class="profile_image_thumb">
										<?php for($i=0;$i<$countDoctorImages;$i++): ?>
                                            <li>
                                                <a href="assets/modules/appointments/images/doctorimages/<?= CHtml::encode($doctorImages[$i]['image_file']); ?>" data-group="img_5906" title="<?= CHtml::encode($doctorImages[$i]['title']); ?>" class="preloader highImg jackbox">
                                                    <img src="assets/modules/appointments/images/doctorimages/thumbs/<?= CHtml::encode($doctorImages[$i]['image_file_thumb']); ?>" alt="<?= CHtml::encode($doctorImages[$i]['title']); ?>" title="<?= CHtml::encode($doctorImages[0]['title']); ?>" />
                                                </a>
                                            </li>
										<?php endfor; ?>
                                    </ul>
                                </div>
							<?php endif; ?>
						<?php elseif($countDoctorImages>0): ?>
                            <a href="assets/modules/appointments/images/doctorimages/<?= CHtml::encode($doctorImages[0]['image_file']); ?>" data-group="img_5906" title="<?= CHtml::encode($doctorImages[0]['title']); ?>" class="preloader highImg jackbox">
                                <img src="assets/modules/appointments/images/doctorimages/<?= CHtml::encode($doctorImages[0]['image_file']); ?>" class="fullwidth" alt="<?= CHtml::encode($doctorImages[0]['title']); ?>" title="<?= CHtml::encode($doctorImages[0]['title']); ?>" />
                            </a>
							<?php if($countDoctorImages>1): ?>
                                <div class="profile_image margin-top-10">
                                    <ul class="profile_image_thumb">
										<?php for($i=1;$i<$countDoctorImages;$i++): ?>
                                            <li>
                                                <a href="assets/modules/appointments/images/doctorimages/<?= CHtml::encode($doctorImages[$i]['image_file']); ?>" data-group="img_5906" title="<?= CHtml::encode($doctorImages[$i]['title']); ?>" class="preloader highImg jackbox">
                                                    <img src="assets/modules/appointments/images/doctorimages/thumbs/<?= CHtml::encode($doctorImages[$i]['image_file_thumb']); ?>" alt="<?= CHtml::encode($doctorImages[$i]['title']); ?>" title="<?= CHtml::encode($doctorImages[$i]['title']); ?>" />
                                                </a>
                                            </li>
										<?php endfor; ?>
                                    </ul>
                                </div>

							<?php endif; ?>
						<?php else: ?>
                            <img src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar_by_gender); ?>" class="fullwidth" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
						<?php endif; ?>
                    </figure>
                    <div class="cl"></div>
                </div>
                <div class="cl"></div>
            </div>
            <div class="three_fifth">
                    <div class="cmsms_features">
                        <h3><?= A::t('appointments', 'Doctor'); ?></h3>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Name'); ?></span>
                            <span class="cmsms_features_item_desc"><?= $fullname ? CHtml::encode($fullname) : '--'; ?></span>
                        </div>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Gender'); ?></span>
                            <span class="cmsms_features_item_desc"><?= $profileDoctor->gender ? ($profileDoctor->gender == 'm') ? A::t('appointments', 'Male') : A::t('appointments', 'Female') : '--'; ?></span>
                        </div>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Degree'); ?></span>
                            <span class="cmsms_features_item_desc"><?= $profileDoctor->degrees_name ? CHtml::encode($profileDoctor->degrees_name) : '--'; ?></span>
                        </div>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Specialty'); ?></span>
                            <span class="cmsms_features_item_desc">
                                <?php
								$countSpecialty = count($specialty);
								if($countSpecialty):
									foreach($specialty as $key => $specialt):
										echo CHtml::encode($specialt['specialty_name']);
										if(isset($specialty[$key + 1])){
											echo '<br/>';
										}
									endforeach;
								else: echo '--';
								endif;
								?>
                            </span>
                        </div>
                        <?php if(!empty($doctorClinics)): ?>
                            <div class="cmsms_features_item">
                                <span class="cmsms_features_item_title"><strong><?= A::t('appointments', $countClinic > 1 ? 'Clinics' : 'Clinic'); ?>:</strong></span>
                                <span class="cmsms_features_item_desc">
                                    <?php
                                    foreach($doctorClinics as $clinicId => $doctorClinic){
                                        $clinicLink = 'clinics/'.CHtml::encode($clinicId).'/'.\CString::seoString($doctorClinic['clinic_name']);
                                        echo '<a href="'.$clinicLink.'" class="link-find-doctor-by-specialty" data-id="1">'.CHtml::encode($doctorClinic['clinic_name']).(!empty($doctorClinic['address'])? ', '.CHtml::encode($doctorClinic['address']) : '').'</a><br>';
                                    }
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($profileDoctor->show_social_networks): ?>
                        <div class="wrap_social_icons">
                            <ul class="social_icons">
                                <?php if($profileDoctor->social_network_facebook): ?>
                                    <li><a href="<?= $profileDoctor->social_network_facebook; ?>" title="Facebook" target="_blank"><img alt="Facebook" src="images/social_networks/facebook.png"></a></li>
                                <?php endif; ?>
                                <?php if($profileDoctor->social_network_twitter): ?>
                                    <li><a href="<?= $profileDoctor->social_network_twitter; ?>" title="Twitter" target="_blank"><img alt="Twitter" src="images/social_networks/twitter.png"></a></li>
                                <?php endif; ?>
                                <?php if($profileDoctor->social_network_youtube): ?>
                                    <li><a href="<?= $profileDoctor->social_network_youtube; ?>" title="Youtube" target="_blank"><img alt="Youtube" src="images/social_networks/youtube.png"></a></li>
                                <?php endif; ?>
                                <?php if($profileDoctor->social_network_instagram): ?>
                                    <li><a href="<?= $profileDoctor->social_network_instagram; ?>" title="Instagram" target="_blank"><img alt="Instagram" src="images/social_networks/instagram.png"></a></li>
                                <?php endif; ?>




                            </ul>
                        </div>
                    <?php endif; ?>
                <div class="fl margin-top-20">
                    <a class="btn-book-appointment button_small" href="patients/addMyAppointment/doctorId/<?= $profileDoctor->id.'/seoLonk/'.CString::seoString($fullname); ?>">&#128197; &nbsp;<?= A::t('appointments', 'Book Appointment'); ?></a>
                </div>
                <div class="cl"></div>
            </div>
        </div>
    </div>

    <div class="entry">
        <div class="entry-content">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="tab">
                        <ul class="tabs active">
                            <li class="current"><a href="javascript:void(0);"><span><?= A::t('appointments', 'Information'); ?></span></a></li>
                            <?php if(!empty($openHours)): ?>
                                <li class=""><a href="javascript:void(0);"><span><?= A::t('appointments', 'Opening Hours'); ?></span></a></li>
                            <?php endif; ?>
							<?php if(empty($profileDoctor->default_visit_price) && $profileDoctor->default_visit_price != 0): ?>
                                <li class=""><a href="javascript:void(0);"><span><?= A::t('appointments', 'Prices'); ?></span></a></li>
                            <?php endif; ?>
                            <!-- Hide if ModuleSettings show_rating or show_rating_form = false and getLoggedRole() = doctor -->
                            <?php if($profileDoctor->membership_enable_reviews && $showRating || $showRatingForm && !$showRating && CAuth::getLoggedRole() != 'doctor'  || !$showRatingForm && CAuth::getLoggedRole() != 'doctor'): ?>
                                <li class=""><a href="javascript:void(0);"><span><?= A::t('appointments', 'Rating and Reviews'); ?></span></a></li>
                            <?php endif; ?>
                        </ul>
                        <div class="tab_content">
                            <div class="tabs_tab" style="display: block;">
                                <div class="cmsms_features">
                                    <?php
                                    if($showFields): ?>
                                        <div class="cmsms_features_item">
                                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Phone'); ?></span>
                                            <span class="cmsms_features_item_desc"><?= $profileDoctor->work_phone ? CHtml::encode($profileDoctor->work_phone) : '--'; ?></span>
                                        </div>
                                        <div class="cmsms_features_item">
                                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Mobile Phone'); ?></span>
                                            <span class="cmsms_features_item_desc"><?= $profileDoctor->work_mobile_phone ? CHtml::encode($profileDoctor->work_mobile_phone) : '--'; ?></span>
                                        </div>
                                        <div class="cmsms_features_item">
                                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Email'); ?></span>

                                            <span class="cmsms_features_item_desc"><?= $profileDoctor->email ? '<a href="mailto:'.CHtml::escapeHex($profileDoctor->email).'">'.CHtml::escapeHexEntity($profileDoctor->email).'</a>' : '--'; ?></span>
                                        </div>
                                        <div class="cmsms_features_item">
                                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Address'); ?></span>
                                            <span class="cmsms_features_item_desc">
                                                <?php
                                                if($profileDoctor->address):
                                                    echo CHtml::encode($profileDoctor->address).' '.CHtml::encode($profileDoctor->city).' '.CHtml::encode($profileDoctor->zip_code);
                                                    if($profileDoctor->address_2):
                                                        echo '<br/>'.CHtml::encode($profileDoctor->address_2).', '.CHtml::encode($profileDoctor->city).', '.CHtml::encode($profileDoctor->zip_code);
                                                    endif;
                                                else: echo '--';
                                                endif;
                                                ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="cmsms_features_item">
                                        <span class="cmsms_features_item_title"><?= A::t('appointments', 'Education'); ?></span>
                                        <span class="cmsms_features_item_desc"><?= $profileDoctor->education ? CHtml::encode($profileDoctor->education) : '--'; ?></span>
                                    </div>
                                    <div class="cmsms_features_item">
                                        <span class="cmsms_features_item_title"><?= A::t('appointments', 'Experience'); ?></span>
                                        <span class="cmsms_features_item_desc"><?= $profileDoctor->experience_years ? CHtml::encode($profileDoctor->experience_years).' years' : '--'; ?></span>
                                    </div>
                                    <div class="cmsms_features_item">
                                        <span class="cmsms_features_item_title"><?= A::t('appointments', 'Hospital Affiliations'); ?></span>
                                        <span class="cmsms_features_item_desc"><?= $profileDoctor->hospital_affiliations ? CHtml::encode($profileDoctor->hospital_affiliations) : '--'; ?></span>
                                    </div>
                                    <div class="cmsms_features_item">
                                        <span class="cmsms_features_item_title"><?= A::t('appointments', 'Board Certifications'); ?></span>
                                        <span class="cmsms_features_item_desc"><?= $profileDoctor->board_certifications ? CHtml::encode($profileDoctor->board_certifications) : '--'; ?></span>
                                    </div>
                                    <div class="cmsms_features_item">
                                        <span class="cmsms_features_item_title"><?= A::t('appointments', 'Awards and Publications'); ?></span>
                                        <span class="cmsms_features_item_desc"><?= $profileDoctor->awards_and_publications ? CHtml::encode($profileDoctor->awards_and_publications) : '--'; ?></span>
                                    </div>
                                    <div class="cmsms_features_item">
                                        <span class="cmsms_features_item_title"><?= A::t('appointments', 'Languages Spoken'); ?></span>
                                        <span class="cmsms_features_item_desc">
                                            <?php
                                            if($profileDoctor->languages_spoken):
                                                $languagesSpoken = explode(';', $profileDoctor->languages_spoken );
                                                foreach($languagesSpoken as $key => $language):
                                                    echo A::t('i18n', 'languages.'.CHtml::encode($language));
                                                    if(isset($languagesSpoken[$key + 1])){
                                                        echo ', ';
                                                    }
                                                endforeach;
                                            else: echo '--';
                                            endif;

                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php if(!empty($openHours)): ?>
                                <div class="tabs_tab" style="display: none;">
                                    <div class="cmsms_features">
                                        <?php foreach($openHours as $key => $openHour): ?>
                                            <div class="cmsms_features_item">
                                                <span class="cmsms_features_item_title"><?= CHtml::encode($openHour['week_day_name']); ?></span>
                                                <span class="cmsms_features_item_desc"><?= CHtml::encode($openHour['time_from']).' - '.CHtml::encode($openHour['time_to']); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(empty($profileDoctor->default_visit_price) && $profileDoctor->default_visit_price != 0): ?>
                                <div class="tabs_tab" style="display: none;">
                                    <div class="cmsms_features">
                                        <div class="cmsms_features_item">
                                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Default Price per Visit'); ?></span>
                                            <span class="cmsms_features_item_desc"><?= CCurrency::format($profileDoctor->default_visit_price); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if($profileDoctor->membership_enable_reviews && $showRating || $showRatingForm && !$showRating && CAuth::getLoggedRole() != 'doctor'  || !$showRatingForm && CAuth::getLoggedRole() != 'doctor'): ?>
                                <div class="tabs_tab" style="display: none;">
                                    <!-- Reviews -->
                                    <?php DoctorsComponent::drawReviews($profileDoctor->id); ?>
                                    <!-- Reviews -->
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="one_first first_column">
                    <div class="cl"></div>
                </div>
            </div>
        </div>
        <div class="cl"></div>
    </div>
</section>
<?php A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/doctorreviews.js'); ?>