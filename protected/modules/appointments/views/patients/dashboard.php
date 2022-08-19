<?php
    $this->_pageTitle = A::t('appointments', 'Dashboard');
    $this->_activeMenu = 'patients/dashboard';
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
                            <p><?= A::t('appointments', 'Welcome on the Dashboard!'); ?></p>
                            <br>
                            <h4><?= A::t('appointments', 'General'); ?></h4>
                            <ul class="dashboard-links">
                                <li><a href="patients/dashboard"><?= A::t('appointments', 'Dashboard'); ?></a><br></li>
                                <li><a href="patients/logout"><?= A::t('appointments', 'Logout'); ?></a></li>
                            </ul>
                            <h4><?= A::t('appointments', 'Profile Details'); ?></h4>
                            <ul class="dashboard-links">
                                <li><a href="patients/myAccount"><?= A::t('appointments', 'My Account'); ?></a></li>
                            </ul>
                            <h4><?= A::t('appointments', 'Appointments'); ?></h4>
                            <ul class="dashboard-links">
                                <li><a href="patients/myAppointments"><?= A::t('appointments', 'My Appointments'); ?></a></li>
                            </ul>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </section>
