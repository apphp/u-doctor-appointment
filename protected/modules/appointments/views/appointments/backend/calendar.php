<?php
$this->_activeMenu = 'doctors/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
    array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
    array('label'=>A::t('appointments', 'Appointments Management'), 'url'=>'appointments/manage'),
    array('label'=>A::t('appointments', 'Calendar'), 'url'=>''),
);
?>
<h1><?= A::t('appointments', 'Calendar'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab " href="doctors/manage?page=<?= $page; ?>"><?= A::t('appointments', 'Doctors'); ?></a>
        »
        <a class="sub-tab active" href="javascript:void(0);"><?= (!empty($doctorFullName) ? $doctorFullName.' | ' : '').A::t('appointments', 'Calendar'); ?></a>
    </div>

    <div class="content">
        <?= $actionMessage; ?>
        <div id='calendar'></div>
    </div>
</div>

<?php
A::app()->getClientScript()->registerCssFile('templates/default/calendar/css/fullcalendar.min.css');
A::app()->getClientScript()->registerCssFile('templates/default/calendar/css/fullcalendar.print.css', 'print');
A::app()->getClientScript()->registerScriptFile('templates/default/calendar/js/moment.min.js', CClientScript::POS_BODY_END);
A::app()->getClientScript()->registerScriptFile('templates/default/calendar/js/fullcalendar.min.js', CClientScript::POS_BODY_END);
A::app()->getClientScript()->registerCssFile('templates/default/calendar/css/events.css');

A::app()->getClientScript()->registerCss('events-add', '
    .fc-unthemed .fc-today{background:#fcf8e3 !important;}
    table tbody tr:hover td { color:#353535 !important; background-color:#ffffff !important; border-bottom:1px solid #dadada !important; border-top:1px solid #dadada !important; }
    table tbody tr:nth-child(2n+1) td { background-color:unset !important; }
    table tbody tr td:first-child { padding-left:0 !important; }
    table tfoot tr td:first-child { padding-left:0 !important; }
    table thead tr th:first-child { padding-left:0 !important; }
');