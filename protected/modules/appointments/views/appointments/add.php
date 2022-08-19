<?php
    $this->_activeMenu = 'modules/settings/code/appointments';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Appointments Management'), 'url'=>'appointments/manage'),
        array('label'=>A::t('appointments', 'Add Appointment')),
    );
A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
?>

<h1><?= A::t('appointments', 'Appointments Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title"><?= A::t('appointments', 'Add Appointment'); ?></div>
    <div class="content">
        <?= $drawAppointmentsBlock; ?>
    </div>
</div>
