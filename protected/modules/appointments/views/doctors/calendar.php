<?php
$this->_breadCrumbs = array(
    array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
    array('label'=>A::t('appointments', 'Calendar')),
);
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
                    <?= $actionMessage; ?>
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
A::app()->getClientScript()->registerCssFile('templates/default/calendar/css/fullcalendar.min.css');
A::app()->getClientScript()->registerCssFile('templates/default/calendar/css/fullcalendar.print.css', 'print');
A::app()->getClientScript()->registerScriptFile('templates/default/calendar/js/moment.min.js', CClientScript::POS_BODY_END);
A::app()->getClientScript()->registerScriptFile('templates/default/calendar/js/fullcalendar.min.js', CClientScript::POS_BODY_END);
A::app()->getClientScript()->registerCssFile('templates/default/calendar/css/events.css');

