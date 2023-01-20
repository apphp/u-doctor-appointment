<?php
$drawMap = false;

if($clinic->latitude && $clinic->longitude){
    $drawMap = true;
    A::app()->getClientScript()->registerScriptFile('//maps.google.com/maps/api/js?sensor=false&language=en&ver=3.8.3&key='.Bootstrap::init()->getSettings('mapping_api_key'), 2);
    A::app()->getClientScript()->registerScriptFile('templates/mobile/js/gmap3.infobox.js', 2);
    A::app()->getClientScript()->registerScriptFile('templates/mobile/js/gmap3.min.js', 2);
}

?>
<div class="locations-w3-agileits">
    <div class="container">
        <div class="back-arrow">
            <h4><a href="mobile/clinics"> < <?= A::t('appointments', 'Clinics'); ?></a></h4>
        </div>
        <div class="left-blog left-single">
            <div class="blog-left">
                <h4 class="mb10"><?= CHtml::encode($clinic->clinic_name); ?></h4>
                <div class="single-left-left">
                    <?php if($drawMap): ?>
                            <div class="clinic-map" style="width: 100%; min-height: 300px;" data-longitude="<?= $clinic->longitude; ?>" data-latitude="<?= $clinic->latitude; ?>"></div>
                    <?php else: ?>
                        <img class="clinic-map" src="templates/mobile/images/map-placeholder.png" />
                    <?php endif; ?>
                </div>
                <div class="profile-info blog-left-bottom">
                    <div class="container">
                        <h4 class="tittle-w3layouts mb10"><?= A::t('appointments', 'Clinic Info'); ?></h4>
                    </div>
                    <table>
                        <tr>
                            <td class="table-label"><?= A::t('appointments', 'Clinic Name'); ?>: </td>
                            <td><?= $clinic->clinic_name ? CHtml::encode($clinic->clinic_name) : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="table-label"><?= A::t('appointments', 'Address'); ?>: </td>
                            <td><?= $clinic->address ? CHtml::encode($clinic->address) : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="table-label"><?= A::t('appointments', 'Phone'); ?>: </td>
                            <td><?= $clinic->phone ? CHtml::encode($clinic->phone) : '--'; ?></td>
                        </tr>
                        <tr>
                            <td class="table-label"><?= A::t('appointments', 'Fax'); ?>: </td>
                            <td><?= $clinic->fax ? CHtml::encode($clinic->fax) : '--'; ?></td>
                        </tr>
                    </table>
                    <?php if($clinic->description): ?>
                        <div class="container">
                            <h4 class="tittle-w3layouts mb10"><?= A::t('appointments', 'Clinic Description'); ?></h4>
                        </div>
                        <table>
                            <tr>
                                <td><?= $clinic->description; ?></td>
                            </tr>

                        </table>
                    <?php endif; ?>
                    <?php if(!empty($workingDays)): ?>
                        <div class="container">
                            <h4 class="tittle-w3layouts mb10"><?= A::t('appointments', 'Opening Hours'); ?></h4>
                        </div>
                        <table>
                            <?php foreach($workingDays as $workingDay): ?>
                            <tr>
                                <td class="table-label">
                                    <?= A::t('i18n', 'weekDayNames.wide.'.$workingDay['week_day']); ?>
                                    <?php
                                    if(!empty($workingDay['week_end_day'])){
                                        echo ' - '.A::t('i18n', 'weekDayNames.wide.'.$workingDay['week_end_day']);
                                    }
                                    ?>:
                                </td>
                                <td><?= CHtml::encode($workingDay['start_time']); ?> - <?= CHtml::encode($workingDay['end_time']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if($drawMap){
    A::app()->getClientScript()->registerScript(
        'clinicMapView',
        'jQuery(document).ready(function(){
        var $ = jQuery;
        var latitude = $(".clinic-map").data("latitude");
        var longitude = $(".clinic-map").data("longitude");
        smallMapDiv = $(".clinic-map");
        smallMapDiv.gmap3({
            map: {
                options: {
                    center: [latitude, longitude],zoom: 16,scrollwheel: false
                }
            },
            marker: {values: [{latLng: [latitude, longitude]}]}
        });
    });',
    3
    );
}