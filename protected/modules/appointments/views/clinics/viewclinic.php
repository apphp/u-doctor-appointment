<?php
    $this->_pageTitle = CHtml::encode($clinic->clinic_name);
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Our Clinics'), 'url'=>'clinics/ourClinics'),
        array('label'=> CHtml::encode($clinic->clinic_name))
    );
A::app()->getClientScript()->registerScriptFile('templates/default/js/gmap3.infobox.js', 1);
A::app()->getClientScript()->registerScriptFile('templates/default/js/gmap3.min.js', 1);

$drawMap = false;

if($clinic->latitude && $clinic->longitude){
    $drawMap = true;
}

?>

<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first">
                <?php if($drawMap): ?>
                <div class="one_first first_column">
                    <div class="clinic-map" data-longitude="<?= $clinic->longitude; ?>" data-latitude="<?= $clinic->latitude; ?>"></div>
                    <div class="cl"></div>
                </div>
                <?php endif; ?>
                <div class="one_first first_column">
                    <div class="cmsms_features">
                        <h3><?= A::t('appointments', 'Clinic Info'); ?></h3>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Clinic Name'); ?></span>
                            <span class="cmsms_features_item_desc"><?= CHtml::encode($clinic->clinic_name); ?></span>
                        </div>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Address'); ?></span>
                            <span class="cmsms_features_item_desc"><?= ($clinic->address) ? CHtml::encode($clinic->address) : '--'; ?></span>
                        </div>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Phone'); ?></span>
                            <span class="cmsms_features_item_desc"><?= ($clinic->phone) ? CHtml::encode($clinic->phone) : '--'; ?></span>
                        </div>
                        <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title"><?= A::t('appointments', 'Fax'); ?></span>
                            <span class="cmsms_features_item_desc"><?= ($clinic->fax) ? CHtml::encode($clinic->fax) : '--'; ?></span>
                        </div>
                    </div>
                    <?php if($clinic->description): ?>
                        <div class="cmsms_features">
                            <h3><?= A::t('appointments', 'Clinic Description'); ?></h3>
                            <div class="cmsms_features_item">
                                <p><?= $clinic->description; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($workingDays)): ?>
                    <div class="cmsms_features">
                        <h3><?= A::t('appointments', 'Opening Hours'); ?></h3>
                        <?php foreach($workingDays as $workingDay): ?>
                            <div class="cmsms_features_item">
                            <span class="cmsms_features_item_title">
                                <?= A::t('i18n', 'weekDayNames.wide.'.$workingDay['week_day']); ?>
                                <?php
                                if(!empty($workingDay['week_end_day'])){
                                    echo ' - '.A::t('i18n', 'weekDayNames.wide.'.$workingDay['week_end_day']);
                                }
                                ?>
                            </span>
                                <span class="cmsms_features_item_desc"><?= $workingDay['start_time']; ?> - <?= $workingDay['end_time']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
if($drawMap){
    A::app()->getClientScript()->registerScriptFile('//maps.google.com/maps/api/js?sensor=false&language=en&ver=3.8.3&key='.Bootstrap::init()->getSettings('mapping_api_key'));
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
    });'
    );
}