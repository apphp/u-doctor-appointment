<div class="locations-w3-agileits">
    <div class="container">
        <div class="loc-left">
            <div class="back-arrow">
                <h4><a href="mobile/doctors?page=<?= $currentPage; ?>"> < <?= A::t('appointments', 'Doctors'); ?></a></h4>
            </div>
            <h4><?= CHtml::encode($fullname); ?></h4>
            <img style="width: 90%; margin: 10px 0" src="assets/modules/appointments/images/doctors/<?= $profileDoctor->avatar ?  CHtml::encode($profileDoctor->avatar) : CHtml::encode($profileDoctor->avatar_by_gender) ; ?>" class="img-response" alt="<?= $fullname; ?>" title=""/>
<!--            <a href="mobile/bookAppointment/id/--><?//= $profileDoctor->id; ?><!--"class="blog-more-agile" >--><?//= A::t('appointments', 'Book Appointment'); ?><!--</a>-->
            <a href="mobile/timeBlockAppointments/id/<?= $profileDoctor->id; ?>"class="blog-more-agile" ><?= A::t('appointments', 'Book Appointment'); ?></a>
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
        <div class="profile-info">
            <div class="container">
                <h4 class="tittle-w3layouts"><?= A::t('appointments', 'Information'); ?></h4>
            </div>
            <table>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Phone'); ?>: </td>
                    <td><?= $profileDoctor->work_phone ? '<a href="tel:'.preg_replace('/[^0-9]/', '', $profileDoctor->work_phone).'">'.CHtml::encode($profileDoctor->work_phone).'</a>' : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Mobile Phone'); ?>: </td>
                    <td><?= $profileDoctor->work_mobile_phone ? '<a href="tel:'.preg_replace('/[^0-9]/', '', $profileDoctor->work_mobile_phone).'">'.CHtml::encode($profileDoctor->work_mobile_phone).'</a>' : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Email'); ?>: </td>
                    <td><?= $profileDoctor->email ? '<a href="mailto:'.CHtml::escapeHex($profileDoctor->email).'">'.CHtml::escapeHexEntity($profileDoctor->email).'</a>' : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Address'); ?>: </td>
                    <td>
                        <?php
                        if(!empty($profileDoctor->address)):
                            echo CHtml::encode($profileDoctor->address).' '.CHtml::encode($profileDoctor->city).' '.CHtml::encode($profileDoctor->zip_code);
                            if($profileDoctor->address_2):
                                echo '<br/>'.CHtml::encode($profileDoctor->address_2).', '.CHtml::encode($profileDoctor->city).', '.CHtml::encode($profileDoctor->zip_code);
                            endif;
                        else: echo '--';
                        endif;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Education'); ?>: </td>
                    <td><?= $profileDoctor->education ? CHtml::encode($profileDoctor->education) : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Experience'); ?>: </td>
                    <td><?= $profileDoctor->experience_years ? CHtml::encode($profileDoctor->experience_years).' years' : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Hospital Affiliations'); ?>: </td>
                    <td><?= $profileDoctor->hospital_affiliations ? CHtml::encode($profileDoctor->hospital_affiliations) : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Board Certifications'); ?>: </td>
                    <td><?= $profileDoctor->board_certifications ? CHtml::encode($profileDoctor->board_certifications) : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Awards and Publications'); ?>: </td>
                    <td><?= $profileDoctor->awards_and_publications ? CHtml::encode($profileDoctor->awards_and_publications) : '--'; ?></td>
                </tr>
                <tr>
                    <td class="table-label"><?= A::t('appointments', 'Languages Spoken'); ?>: </td>
                    <td>
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
                    </td>
                </tr>
                <?php if(empty($profileDoctor->default_visit_price) && $profileDoctor->default_visit_price != 0): ?>
                    <tr>
                        <td class="table-label"><?= A::t('appointments', 'Default Price per Visit'); ?>: </td>
                        <td><?= CCurrency::format($profileDoctor->default_visit_price); ?>: </td>
                    </tr>
                <?php endif; ?>
            </table>
            <?php if(!empty($openHours)): ?>
                <div class="container">
                    <h4 class="tittle-w3layouts"><?= A::t('appointments', 'Opening Hours'); ?></h4>
                </div>
                <table>
                    <?php foreach($openHours as $key => $openHour): ?>
                        <tr>
                            <td class="table-label opening-hours"><?= CHtml::encode($openHour['week_day_name']); ?>: </td>
                            <td><?= CHtml::encode($openHour['time_from']).' - '.CHtml::encode($openHour['time_to']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
A::app()->getClientScript()->registerScript(
    'loading',
    '$(document).ready(function() {
            $(".blog-more-agile").on("click", function(){
               var existImage = $("img").hasClass("ml10");
               if(existImage == false){
                  $(".blog-more-agile").after(\'<img class="ml10" src = "templates/mobile/images/ajax_loading.gif" alt = "loading" />\');
               }
            });
        });',
    2
);