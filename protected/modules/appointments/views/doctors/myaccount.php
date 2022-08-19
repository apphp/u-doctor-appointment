<?php
    $this->_pageTitle = A::t('appointments', 'My Account');
    $this->_activeMenu = 'doctors/myAccount';
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label' => A::t('appointments', 'My Account'))
    );
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
                    <div id="my-account">
                    <?php
                        echo $actionMessage;

                        $personalInformation = '';
                        $personalInformation .= '<p class="'.(!empty($fullName) ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Name').':</span></label><label class="right-label">'.$fullName.'</label></p>';
                        $personalInformation .= '<p class="'.($doctor->gender ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Gender').':</span></label><label class="right-label">'.$genders[$doctor->gender].'</label></p>';
                        $personalInformation .= '<p class="'.($birthDate ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Birth Date').':</span></label><label class="right-label">'.$birthDate.'</label></p>';
                        $personalInformation .= '<p class="'.($doctor->avatar ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Avatar').':</span></label><label class="right-label">'.($doctor->avatar != '' ? '<img class="my-account-image avatar" src="assets/modules/appointments/images/doctors/'.$doctor->avatar.'">' : '').'</label></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Personal Information').'</legend><div class="row">'.$personalInformation.'</div></fieldset>';

                        $contactInformation = '';
                        $contactInformation .= '<p class="'.($doctor->work_phone ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Work Phone').':</span></label><label class="right-label">'.$doctor->work_phone.'</label></p>';
                        $contactInformation .= '<p class="'.($doctor->work_mobile_phone ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Work Mobile Phone').':</span></label><label class="right-label">'.$doctor->work_mobile_phone.'</label></p>';
                        $contactInformation .= '<p class="'.($doctor->phone ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Phone').':</span></label><label class="right-label">'.$doctor->phone.'</label></p>';
                        $contactInformation .= '<p class="'.($doctor->fax ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Fax').':</span></label><label class="right-label">'.$doctor->fax.'</label></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Contact Information').'</legend><div class="row">'.$contactInformation.'</div></fieldset>';

                        $addressInformation = '';
                        $addressInformation .= '<p class="'.($doctor->address ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Address').':</span></label><label class="right-label">'.$doctor->address.'</label></p>';
                        $addressInformation .= '<p class="'.($doctor->address_2 ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Address (line 2)').':</span></label><label class="right-label">'.$doctor->address_2.'</label></p>';
                        $addressInformation .= '<p class="'.($doctor->city ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'City').':</span></label><label class="right-label">'.$doctor->city.'</label></p>';
                        $addressInformation .= '<p class="'.($doctor->zip_code ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Zip Code').':</span></label><label class="right-label">'.$doctor->zip_code.'</label></p>';
                        $addressInformation .= '<p class="'.($countryName ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Country').':</span></label><label class="right-label">'.$countryName.'</label></p>';
                        $addressInformation .= '<p class="'.($stateName ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'State/Province').':</span></label><label class="right-label">'.$stateName.'</label></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Address Information').'</legend><div class="row">'.$addressInformation.'</div></fieldset>';

                        $accountIformation = '';
                        $accountIformation .= '<p class="'.($doctor->email ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Email').':</span></label><label class="right-label">'.$doctor->email.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->username ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Username').':</span></label><label class="right-label">'.$doctor->username.'</label></p>';
                        $accountIformation .= '<p class="full"><label class="left-label"><span>'.A::t('appointments', 'Password').':</span></label><label class="right-label">*****</label></p>';
                        $accountIformation .= '<p class="'.($langName ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Preferred Language').':</span></label><label class="right-label">'.$langName.'</label></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Account Information').'</legend><div class="row">'.$accountIformation.'</div></fieldset>';

                        $accountIformation = '';
                        $accountIformation .= '<p class="'.($doctor->medical_degree_id ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Degree').':</span></label><label class="right-label">'.$degree.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->additional_degree ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Additional Degree').':</span></label><label class="right-label">'.$doctor->additional_degree.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->license_number ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'License Number').':</span></label><label class="right-label">'.$doctor->license_number.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->education ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Education').':</span></label><label class="right-label">'.$doctor->education.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->experience_years ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Experience').':</span></label><label class="right-label">'.$experience.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->residency_training ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Residency Training').':</span></label><label class="right-label">'.$doctor->residency_training.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->hospital_affiliations ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Hospital Affiliations').':</span></label><label class="right-label">'.$doctor->hospital_affiliations.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->board_certifications ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Board Certifications').':</span></label><label class="right-label">'.$doctor->board_certifications.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->awards_and_publications ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Awards and Publications').':</span></label><label class="right-label">'.$doctor->awards_and_publications.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->languages_spoken ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Languages Spoken').':</span></label><label class="right-label">'.$languagesSpoken.'</label></p>';
                        $accountIformation .= '<p class="'.($doctor->insurances_accepted ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Insurances Accepted').':</span></label><label class="right-label">'.$insurancesAccepted.'</label></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Professional Information').'</legend><div class="row">'.$accountIformation.'</div></fieldset>';

                        $otherIformation = '';
                        $otherIformation .= '<p class="'.($createdAt ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Created at').':</span></label><label class="right-label">'.$createdAt.'</label></p>';
                        $otherIformation .= '<p class="'.($lastVisitedAt ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Last visit at').':</span></label><label class="right-label">'.$lastVisitedAt.'</label></p>';
                        $otherIformation .= '<p class="'.($notifications ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Notifications').':</span></label><label class="right-label">'.$notifications.'</label></p>';

                        $otherIformation .= '<p class="full"><label class="left-label"><span>'.A::t('appointments', 'Remove Account').':</span></label><label class="right-label"><a class="button button-remove white" href="doctors/removeAccount">'.A::t('appointments', 'Remove').'</a></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Other').'</legend><div class="row">'.$otherIformation.'</div></fieldset>';

                    ?>
                    <a class="button button-edit-account" href="doctors/editAccount"><?= A::t('appointments', 'Edit Account'); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

