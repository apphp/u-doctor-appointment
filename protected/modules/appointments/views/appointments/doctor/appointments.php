<?php
$this->_pageTitle = A::t('appointments', 'Book Appointment');
$this->_activeMenu = 'doctors/dashboard';
$this->_breadCrumbs = array(
    array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=> A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
    array('label'=>A::t('appointments', 'Appointments'), 'url'=>'doctors/appointments/'),
    array('label'=>A::t('appointments', 'Book Appointment')),
);
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="one_fourth first_column margin-left-0">
                    <figure class="box">
                        <?php if($profileDoctor->avatar): ?>
                            <img class="fullwidth" src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar); ?>" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
                        <?php else: ?>
                            <img src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar_by_gender); ?>" class="fullwidth" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
                        <?php endif; ?>
                    </figure>
                    <div class="cl"></div>
                    <div class="cl"></div>
                </div>
                <div class="three_fourth">
                    <div class="cmsms_features">
                        <div class="cmsms_features_item padding-5-0">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Name'); ?></span>
                            <span class="cmsms_features_item_desc"><?= $fullname ? CHtml::encode($fullname) : '--'; ?></span>
                        </div>
                        <div class="cmsms_features_item padding-5-0">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Gender'); ?></span>
                            <span class="cmsms_features_item_desc"><?= $profileDoctor->gender ? ($profileDoctor->gender == 'm') ? 'Male' : 'Famale' : '--'; ?></span>
                        </div>
                        <div class="cmsms_features_item padding-5-0">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Degree'); ?></span>
                            <span class="cmsms_features_item_desc"><?= $profileDoctor->degrees_name ? CHtml::encode($profileDoctor->degrees_name) : '--'; ?></span>
                        </div>
                        <div class="cmsms_features_item padding-5-0">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Specialty'); ?></span>
                            <span class="cmsms_features_item_desc">
                                <?php
                                $countSpecialty = count($specialty);
                                if($countSpecialty):
                                    foreach($specialty as $key => $specialt):
                                        echo CHtml::encode($specialt).'<br/>';
                                    endforeach;
                                else: echo '--';
                                endif;
                                ?>
                            </span>
                        </div>
                        <?php if(!empty($clinicTime)): ?>
                            <div class="cmsms_features_item padding-5-0">
                                <span class="cmsms_features_item_title"><?= A::t('appointments', 'Clinic Time'); ?></span>
                                <span class="cmsms_features_item_desc"><?= $clinicTime['time'].' '.$clinicTime['offset']; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="cl"></div>
                </div>
                <?php if($multiClinics && !empty($clinics) && count($clinics) > 1): ?>
                    <div class="s_filter margin-bottom-5">
                        <div class="s_filter_container">
                            <a class="s_cat_filter button_small" data-filter="article.service" title="<?= (!empty($clinicId) && !empty($clinics[$clinicId])) ? CHtml::encode($clinics[$clinicId]['address']) : ''; ?>" href="javascript:void(0);">
                        <span>
                            <?= (!empty($clinicId) && !empty($clinics[$clinicId])) ? CHtml::encode($clinics[$clinicId]['clinic_name']) : A::t('appointments', 'All Clinics'); ?>
                        </span>
                            </a>
                            <ul class="s_filter_list">
                                <li <?= empty($clinicId) ? 'class="current"' : ''; ?>>
                                    <a href="doctors/addAppointment/" <?= empty($clinicId) ? 'class="current"' : ''; ?>><?= A::t('appointments', 'All Clinics'); ?></a>
                                </li>
                                <?php foreach($clinics as $key => $clinic): ?>
                                    <li <?= ($clinicId == CHtml::encode($key)) ? 'class="current"' : ''; ?>>
                                        <a href="doctors/addAppointment/clinicId/<?= CHtml::encode($key); ?>" title="<?= CHtml::encode($clinic['address']); ?>" <?= ($clinicId == CHtml::encode($key)) ? 'class="current"' : ''; ?>><?= CHtml::encode($clinic['clinic_name']); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="cl"></div>
                <?php endif; ?>
                <div id="book_appointment">
                    <?= $drawDoctorSchedules; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
A::app()->getClientScript()->registerScript(
    'banned-appointment',
    'function onBannedAppointment(el){return confirm("'.A::t('appointments', 'You have reached a maximum allowed number of the appointments: {number}', array('{number}'=>$maxAppointmentPerPatient)).'");}',
    2
);

A::app()->getClientScript()->registerScript(
    'banned-appointment-to-specialist',
    'function onBannedAppointmentToSpecialist(el){return confirm("'.A::t('appointments', 'You have reached a maximum allowed number of the appointments to this specialist: {number}', array('{number}'=>$maxAppointmentToSpecialist)).'");}',
    2
);
?>