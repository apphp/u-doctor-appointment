<?php
use \Modules\Appointments\Models\Services,
    \Modules\Appointments\Models\Doctors,
    \Modules\Appointments\Models\Clinics,
	\Modules\Appointments\Models\Titles,
    \Modules\Appointments\Components\DoctorsComponent;

$configModule = \CLoader::config('appointments', 'main');
$multiClinics = $configModule['multiClinics'];
?>
<!--  Start Bottom  -->
<section id="bottom">
    <div class="bottom_inner">
        <div class="one_fourth">
            <aside id="text-3" class="widget_text">
                <h3 class="widgettitle" data-id="medical_information"><?= A::t('appointments', 'Medical Information'); ?><span class="pull-right hide-footer">&#x25BE;</span></h3>
                <div id="medical_information" class="textwidget hide-footer">
                    <?php
                    if($multiClinics):
                        $clinics = Clinics::model()->findAll(array('order'=>'is_default DESC, id DESC', 'limit'=>3));
                        $clinicsCount = Clinics::model()->count();
                    ?>
                        <div class="margin-bottom-20">
                            <h5><?= A::t('appointments', 'Our Clinics'); ?></h5>
                            <?php if(!empty($clinics)): ?>
                                <ul id="menu-services" class="menu">
                                    <?php foreach($clinics as $clinic): ?>
                                        <li class="menu-item">
                                            <a href="clinics/viewClinic/<?= $clinic['id']; ?>"><?= $clinic['clinic_name']; ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if($clinicsCount > 3): ?>
                                    <li class="padding-left-0"><a href="clinics/ourClinics"><?= A::t('appointments', 'View All'); ?>  &raquo</a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
					<h5><?= A::t('appointments', 'Website Info'); ?></h5>
                    <aside class="widget_custom_contact_info_entries" id="custom-contact-info-2">
                        <?php if($siteAddress): ?>
                            <div class="contact_widget_info">
                                <span class="contact_widget_name"><?= $siteAddress; ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($sitePhone): ?>
                            <span class="contact_widget_phone">
                                <a href="tel:<?= preg_replace('/[^0-9]/', '', $sitePhone); ?>"><?= CHtml::encode($sitePhone); ?></a>
                            </span>
                        <?php endif; ?>
    
                        <?php if($siteEmail): ?>
                            <span class="contact_widget_email">
                                <a href="mailto:<?= CHtml::escapeHex($siteEmail); ?>"><?= CHtml::escapeHexEntity($siteEmail); ?></a>
                            </span>
                        <?php endif; ?>
                    </aside>                
                </div>
            </aside>
        </div>
        
        <?php if(Modules::model()->isInstalled('appointments')): ?>
        <?php
            $doctors = Doctors::model()->findAll(array('condition'=>'membership_show_in_search = 1 AND '.CConfig::get('db.prefix').'accounts.is_removed = 0 AND '.CConfig::get('db.prefix').'accounts.is_active= 1', 'order'=>'RAND()', 'limit'=>'0, 7'), array(), '');
            if(!empty($doctors)):
        ?>
            <div class="one_fourth">
                <aside id="nav_menu-2" class="widget_nav_menu">
                    <h3 class="widgettitle" data-id="doctors"><?= A::t('appointments', 'Doctors'); ?><span class="pull-right hide-footer">&#x25BE;</span></h3>
                    <div id="doctors" class="menu-doctors-container hide-footer">
                    <div class="menu-doctors-container">
                        <ul id="menu-doctors" class="menu">
                            <?php foreach($doctors as $doctor): ?>
                            <li class="menu-item">
                                <a href="<?= Website::prepareLinkByFormat('appointments', 'profile_link_format', $doctor['id'], DoctorsComponent::getDoctorName($doctor)); ?>"><?= DoctorsComponent::getDoctorName($doctor); ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </aside>
            </div>
            <div class="cl_resp"></div>
			<?php endif; ?>
		<?php endif; ?>
        
        <?php if(Modules::model()->isInstalled('appointments')): ?>
        <div class="one_fourth">
            <aside id="nav_menu-4" class="widget_nav_menu">
                <h3 class="widgettitle" data-id="services"><?= A::t('appointments', 'Services'); ?><span class="pull-right hide-footer">&#x25BE;</span></h3>
                <div id="services" class="menu-services-container hide-footer">
                    <?php
                        $services = Services::model()->findAll(array('condition'=>'is_active=1', 'order'=>'id ASC', 'limit'=>'0, 12'), array(), 'services-findall-12');
                        if(!empty($services)):
                    ?>
                    <ul id="menu-services" class="menu">
                        <?php
							$servicesCount = 0;
							$servicesTotal = count($services);
							foreach($services as $service):
								if($servicesCount++ > 10) break;
                        ?>
                            <li class="menu-item">
                                <a href="services/view/id/<?= $service['id']; ?>"><?= $service['name']; ?></a>
                            </li>
                        <?php endforeach; ?>
                        <?php if($servicesTotal > $servicesCount): ?>
                            <li class="padding-left-0"><a href="services/viewAll"><?= A::t('appointments', 'View All'); ?></a></li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
        <?php endif; ?>
        
        <?php if(Modules::model()->isInstalled('news')): ?>
        <div class="one_fourth">
            <?= NewsComponent::drawSubscriptionBlock(); ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<!--  Finish Bottom  -->