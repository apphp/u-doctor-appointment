<?php
    $this->_pageTitle = A::t('appointments', 'Doctor Registration');
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Doctor Login'), 'url'=>'doctors/login'),
        array('label' => A::t('appointments', 'Doctor Registration'))
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
