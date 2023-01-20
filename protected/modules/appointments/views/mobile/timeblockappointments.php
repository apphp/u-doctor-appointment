<?php A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2); ?>
<div class="blog" id="blog">
    <div class="container">
        <?= $actionMessage; ?>
    </div>
    <div class="container">
        <div class="loc-left">
            <div class="back-arrow">
                <h4><a href="mobile/doctorView/<?= CHtml::encode($profileDoctor->id); ?>"> < <?= CHtml::encode($fullname); ?></a></h4>
            </div>
            <img style="width: 90%; margin: 10px 0" src="assets/modules/appointments/images/doctors/<?= $profileDoctor->avatar ?  CHtml::encode($profileDoctor->avatar) : CHtml::encode($profileDoctor->avatar_by_gender) ; ?>" class="img-response" alt="<?= $fullname; ?>" title=""/>
        </div>
        <div class="loc-right profile">
            <div class="container">
                <h4 class="tittle-w3layouts"><?= A::t('appointments', 'Doctor'); ?></h4>
            </div>
            <table>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Name'); ?>: </td>
                    <td><?= !empty($fullname) ? CHtml::encode($fullname) : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Gender'); ?>: </td>
                    <td><?= $profileDoctor->gender ? ($profileDoctor->gender == 'm') ? 'Male' : 'Female' : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Degree'); ?>: </td>
                    <td><?= $profileDoctor->degrees_name ? CHtml::encode($profileDoctor->degrees_name) : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Specialty'); ?>: </td>
                    <td>
                        <?php
                        if(!empty($specialty)):
                            $specialtyString = implode('<br/>', $specialty);
                            echo $specialtyString;
                        else:
                            echo '--';
                        endif;
                        ?>
                    </td>
                </tr>
                <?php if(!empty($doctorClinics)): ?>
                    <tr>
                        <td class="table-label"><?= A::t('appointments', $countClinic > 1 ? 'Clinics' : 'Clinic'); ?>:</td>
                        <td>
                            <?php
                            foreach($doctorClinics as $clinicId => $doctorClinic){
                                $clinicLink = 'mobile/clinicView/'.CHtml::encode($clinicId);
                                echo '<a href="'.$clinicLink.'" class="link-find-doctor-by-specialty" data-id="1">'.CHtml::encode($doctorClinic['clinic_name']).(!empty($doctorClinic['address'])? ', '.CHtml::encode($doctorClinic['address']) : '').'</a><br>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
    <div class="container">
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
                            <a href="appointments/<?= $profileDoctor->id; ?>" <?= empty($clinicId) ? 'class="current"' : ''; ?>><?= A::t('appointments', 'All Clinics'); ?></a>
                        </li>
                        <?php foreach($clinics as $key => $clinic): ?>
                            <li <?= ($clinicId == CHtml::encode($key)) ? 'class="current"' : ''; ?>>
                                <a href="appointments/<?= $profileDoctor->id; ?>/clinicId/<?= CHtml::encode($key); ?>" title="<?= CHtml::encode($clinic['address']); ?>" <?= ($clinicId == CHtml::encode($key)) ? 'class="current"' : ''; ?>><?= CHtml::encode($clinic['clinic_name']); ?></a>
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
