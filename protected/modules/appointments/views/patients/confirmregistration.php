<?php
    $this->_pageTitle = A::t('appointments', 'Patient Registration');
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Patient Login'), 'url'=>'patients/login'),
        array('label' => A::t('appointments', 'Patient Registration'))
    );
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
                    <p><?= $actionMessage; ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
