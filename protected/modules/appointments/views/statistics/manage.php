<?php
    $this->_activeMenu = 'statistics/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Statistics')),
    );

    // Register Morris files
    A::app()->getClientScript()->registerScriptFile('assets/vendors/morris/raphael-min.js', 1);
    A::app()->getClientScript()->registerCssFile('assets/vendors/morris/morris.css');
    A::app()->getClientScript()->registerScriptFile('assets/vendors/morris/morris.js', 1);
?>

<h1><?= A::t('appointments', 'Statistics'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
        <form id="frmStatistics" action="statistics/manage" method="get">
        <fieldset>
            <label><?= A::t('appointments', 'Select Year'); ?>:</label>
            <select name="year">
                <?php
                    for($i= $currentYear; $i >= $currentYear-5; $i--){
                        echo '<option'.($selectedYear == $i ? ' selected="selected"' : '').' value="'.$i.'">'.$i.'</option>';
                    }
                ?>
            </select>
        </fieldset>
        </form>
        <br><br>


        <div style="width:100%;float:left;">
            <h4 style="padding:0 20px"><?= A::t('appointments', 'Registered and Approved Appointments'); ?></h4>
            <div id="graphAppointmentsCount"></div>
        </div>
        <div style="clear:both"></div>

        <div style="width:49%;float:left;">
            <h4 style="padding:0 20px"><?= A::t('appointments', 'Orders Count'); ?></h4>
            <div id="graphOrdersCount"></div>
        </div>

        <div style="width:49%;float:left;">
            <h4 style="padding:0 20px"><?= A::t('appointments', 'Orders Income'); ?></h4>
            <div id="graphOrderIncome"></div>
        </div>

        <div style="clear:both"></div>
        <br><br>


    </div>


</div>


<script>
// Use Morris.Bar

Morris.Bar({
  element: 'graphOrdersCount',
  data: [
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.1'); ?>', y: <?= isset($ordersCount[1]) ? $ordersCount[1] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.2'); ?>', y: <?= isset($ordersCount[2]) ? $ordersCount[2] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.3'); ?>', y: <?= isset($ordersCount[3]) ? $ordersCount[3] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.4'); ?>', y: <?= isset($ordersCount[4]) ? $ordersCount[4] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.5'); ?>', y: <?= isset($ordersCount[5]) ? $ordersCount[5] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.6'); ?>', y: <?= isset($ordersCount[6]) ? $ordersCount[6] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.7'); ?>', y: <?= isset($ordersCount[7]) ? $ordersCount[7] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.8'); ?>', y: <?= isset($ordersCount[8]) ? $ordersCount[8] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.9'); ?>', y: <?= isset($ordersCount[9]) ? $ordersCount[9] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.10'); ?>', y: <?= isset($ordersCount[10]) ? $ordersCount[10] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.11'); ?>', y: <?= isset($ordersCount[11]) ? $ordersCount[11] : 0; ?>},
    {x: '<?= A::t('i18n', 'monthNames.abbreviated.12'); ?>', y: <?= isset($ordersCount[12]) ? $ordersCount[12] : 0; ?>},
  ],
  xkey: 'x',
  ykeys: ['y'],
  labels: ["<?= A::te('appointments', 'Orders Count'); ?>"]
}).on('click', function(i, row){
  console.log(i, row);
});

Morris.Area({
  element: 'graphOrderIncome',
  data: [
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.1'); ?>", "value": <?= isset($ordersIncome[1]) ? $ordersIncome[1] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.2'); ?>", "value": <?= isset($ordersIncome[2]) ? $ordersIncome[2] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.3'); ?>", "value": <?= isset($ordersIncome[3]) ? $ordersIncome[3] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.4'); ?>", "value": <?= isset($ordersIncome[4]) ? $ordersIncome[4] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.5'); ?>", "value": <?= isset($ordersIncome[5]) ? $ordersIncome[5] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.6'); ?>", "value": <?= isset($ordersIncome[6]) ? $ordersIncome[6] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.7'); ?>", "value": <?= isset($ordersIncome[7]) ? $ordersIncome[7] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.8'); ?>", "value": <?= isset($ordersIncome[8]) ? $ordersIncome[8] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.9'); ?>", "value": <?= isset($ordersIncome[9]) ? $ordersIncome[9] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.10'); ?>", "value": <?= isset($ordersIncome[10]) ? $ordersIncome[10] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.11'); ?>", "value": <?= isset($ordersIncome[11]) ? $ordersIncome[11] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.12'); ?>", "value": <?= isset($ordersIncome[12]) ? $ordersIncome[12] : 0; ?>},
  ],
  xkey: 'elapsed',
  ykeys: ['value'],
  labels: ["<?= A::te('appointments', 'Orders Income'); ?> $"],
  parseTime: false
});


Morris.Area({
  element: 'graphAppointmentsCount',
  data: [
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.1'); ?>", "value": <?= isset($appointmentsCount[1]) ? $appointmentsCount[1] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[1]) ? $appointmentsApprovedCount[1] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.2'); ?>", "value": <?= isset($appointmentsCount[2]) ? $appointmentsCount[2] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[2]) ? $appointmentsApprovedCount[2] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.3'); ?>", "value": <?= isset($appointmentsCount[3]) ? $appointmentsCount[3] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[3]) ? $appointmentsApprovedCount[3] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.4'); ?>", "value": <?= isset($appointmentsCount[4]) ? $appointmentsCount[4] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[4]) ? $appointmentsApprovedCount[4] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.5'); ?>", "value": <?= isset($appointmentsCount[5]) ? $appointmentsCount[5] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[5]) ? $appointmentsApprovedCount[5] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.6'); ?>", "value": <?= isset($appointmentsCount[6]) ? $appointmentsCount[6] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[6]) ? $appointmentsApprovedCount[6] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.7'); ?>", "value": <?= isset($appointmentsCount[7]) ? $appointmentsCount[7] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[7]) ? $appointmentsApprovedCount[7] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.8'); ?>", "value": <?= isset($appointmentsCount[8]) ? $appointmentsCount[8] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[8]) ? $appointmentsApprovedCount[8] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.9'); ?>", "value": <?= isset($appointmentsCount[9]) ? $appointmentsCount[9] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[9]) ? $appointmentsApprovedCount[9] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.10'); ?>", "value": <?= isset($appointmentsCount[10]) ? $appointmentsCount[10] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[10]) ? $appointmentsApprovedCount[10] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.11'); ?>", "value": <?= isset($appointmentsCount[11]) ? $appointmentsCount[11] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[11]) ? $appointmentsApprovedCount[11] : 0; ?>},
      {"elapsed": "<?= A::t('i18n', 'monthNames.abbreviated.12'); ?>", "value": <?= isset($appointmentsCount[12]) ? $appointmentsCount[12] : 0; ?>, "value1": <?= isset($appointmentsApprovedCount[12]) ? $appointmentsApprovedCount[12] : 0; ?>},
  ],
  xkey: 'elapsed',
  ykeys: ['value', 'value1'],
  labels: ["<?= A::te('appointments', 'Registered Appointments'); ?>", "<?= A::te('appointments', 'Approved Appointments'); ?>"],
  barColors: '#f00',
  parseTime: false
});


</script>


<?php
A::app()->getClientScript()->registerScript(
    'statistics',
    'jQuery(document).ready(function(){
        var $ = jQuery;
        $(\'select[name="year"]\').change(function(){
            $("#frmStatistics").submit();
        });
    });
    ',
    2
);
