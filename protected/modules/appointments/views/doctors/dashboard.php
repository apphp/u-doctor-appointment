<?php
    $this->_pageTitle = A::t('appointments', 'Dashboard');
    $this->_activeMenu = 'doctors/dashboard';
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'))
    );
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
                    <fieldset>
                        <legend><?= A::t('appointments', 'Hi').', '.CAuth::getLoggedName(); ?></legend>
                        <p>
                            <?= A::t('appointments', 'Welcome to the Doctors Dashboard!'); ?>
                        </p>
                        <?php if($doctor && empty($actionMessage)):
                            $alert = A::t('appointments', 'Your current membership plan').': <a href="memberships/membershipPlans">'.CHtml::encode($doctor->membership_plan_name).'</a>. '.A::t('appointments', 'Expires').': '.CLocale::date($dateFormat, $doctor->membership_expires) ;
                            echo CWidget::create('CMessage', array('info', $alert, array('button'=>true)));
                        endif; ?>
                        <?= $actionMessage; ?>
                        <h4><?= A::t('appointments', 'General'); ?></h4>
                        <ul class="dashboard-links">
                            <li><a href="doctors/dashboard"><?= A::t('appointments', 'Dashboard'); ?></a><br></li>
                            <li><a href="doctors/logout"><?= A::t('appointments', 'Logout'); ?></a></li>
                        </ul>
                        <h4><?= A::t('appointments', 'Profile Details'); ?></h4>
                        <ul class="dashboard-links">
                            <li><a href="doctors/myAccount"><?= A::t('appointments', 'My Account'); ?></a></li>
                            <li><a href="doctorImages/myImages"><?= A::t('appointments', 'Images'); ?></a></li>
                            <?php if(ModulesSettings::model()->param('appointments', 'show_rating') && $showInReview): ?>
                                <li><a href="doctorReviews/doctorReviews"><?= A::t('appointments', 'Reviews'); ?></a><br></li>
                            <?php endif; ?>
                            <li><a href="memberships/membershipPlans"><?= A::t('appointments', 'Membership Plans'); ?></a><br></li>
                            <li><a href="orders/orders"><?= A::t('appointments', 'Orders'); ?></a><br></li>
                        </ul>
                        <h4><?= A::t('appointments', 'Appointments Management'); ?></h4>
                        <ul class="dashboard-links">
                            <li><a href="doctorClinics/myClinics"><?= A::t('appointments', 'Clinics'); ?></a></li>
                            <li><a href="doctorSchedules/mySchedules"><?= A::t('appointments', 'Schedules'); ?></a></li>
                            <li><a href="doctorSpecialties/mySpecialties"><?= A::t('appointments', 'Specialties'); ?></a></li>
                            <li><a href="doctorTimeoffs/myTimeoffs"><?= A::t('appointments', 'Timeoffs'); ?></a></li>
                            <li><a href="doctors/appointments"><?= A::t('appointments', 'Appointments'); ?></a></li>
                        </ul>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</section>
