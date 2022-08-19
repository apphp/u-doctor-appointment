<?php
    $this->_pageTitle = A::t('appointments', 'My Account');
    $this->_activeMenu = 'patients/myAccount';
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Dashboard'), 'url'=>'patients/dashboard'),
        array('label' => A::t('appointments', 'My Account'))
    );
?>
<section id="content" role="main">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
                    <div id="my-account">
                    <?php
                        echo $actionMessage;

                        $personalInformation = '';
                        $personalInformation .= '<p class="'.(!empty($fullName) ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Name').':</span></label><label class="right-label">'.$fullName.'</label></p>';
                        $personalInformation .= '<p class="'.($patient->gender ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Gender').':</span></label><label class="right-label">'.$genders[$patient->gender].'</label></p>';
                        $personalInformation .= '<p class="'.($birthDate ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Birth Date').':</span></label><label class="right-label">'.$birthDate.'</label></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Personal Information').'</legend><div class="row">'.$personalInformation.'</div></fieldset>';

                        $contactInformation = '';
                        $contactInformation .= '<p class="'.($patient->phone ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Phone').':</span></label><label class="right-label">'.$patient->phone.'</label></p>';
                        $contactInformation .= '<p class="'.($patient->fax ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Fax').':</span></label><label class="right-label">'.$patient->fax.'</label></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Contact Information').'</legend><div class="row">'.$contactInformation.'</div></fieldset>';

                        $addressInformation = '';
                        $addressInformation .= '<p class="'.($patient->address ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Address').':</span></label><label class="right-label">'.$patient->address.'</label></p>';
                        $addressInformation .= '<p class="'.($patient->address_2 ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Address (line 2)').':</span></label><label class="right-label">'.$patient->address_2.'</label></p>';
                        $addressInformation .= '<p class="'.($patient->city ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'City').':</span></label><label class="right-label">'.$patient->city.'</label></p>';
                        $addressInformation .= '<p class="'.($patient->zip_code ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Zip Code').':</span></label><label class="right-label">'.$patient->zip_code.'</label></p>';
                        $addressInformation .= '<p class="'.($countryName ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Country').':</span></label><label class="right-label">'.$countryName.'</label></p>';
                        $addressInformation .= '<p class="'.($stateName ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'State/Province').':</span></label><label class="right-label">'.$stateName.'</label></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Address Information').'</legend><div class="row">'.$addressInformation.'</div></fieldset>';

                        $accountIformation = '';
                        $accountIformation .= '<p class="'.($patient->email ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Email').':</span></label><label class="right-label">'.$patient->email.'</label></p>';
                        $accountIformation .= '<p class="'.($patient->username ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Username').':</span></label><label class="right-label">'.$patient->username.'</label></p>';
                        $accountIformation .= '<p class="full"><label class="left-label"><span>'.A::t('appointments', 'Password').':</span></label><label class="right-label">*****</label></p>';
                        $accountIformation .= '<p class="'.($langName ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Preferred Language').':</span></label><label class="right-label">'.$langName.'</label></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Account Information').'</legend><div class="row">'.$accountIformation.'</div></fieldset>';

                        $otherIformation = '';
                        $otherIformation .= '<p class="'.($createdAt ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Created at').':</span></label><label class="right-label">'.$createdAt.'</label></p>';
                        $otherIformation .= '<p class="'.($lastVisitedAt ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Last visit at').':</span></label><label class="right-label">'.$lastVisitedAt.'</label></p>';
                        $otherIformation .= '<p class="'.($notifications ? 'full' : 'empty').'"><label class="left-label"><span>'.A::t('appointments', 'Notifications').':</span></label><label class="right-label">'.$notifications.'</label></p>';

                        $otherIformation .= '<p class="full"><label class="left-label"><span>'.A::t('appointments', 'Remove Account').':</span></label><label class="right-label"><a class="button button-remove white" href="patients/removeAccount">'.A::t('appointments', 'Remove').'</a></p>';
                        echo '<fieldset><legend>'.A::t('appointments', 'Other').'</legend><div class="row">'.$otherIformation.'</div></fieldset>';

                    ?>
                    <a class="button button-edit-account" href="patients/editAccount"><?= A::t('appointments', 'Edit Account'); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

