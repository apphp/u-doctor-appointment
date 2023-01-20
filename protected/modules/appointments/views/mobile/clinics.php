<?php
A::app()->getClientScript()->registerScriptFile('//maps.google.com/maps/api/js?sensor=false&language=en&ver=3.8.3&key='.Bootstrap::init()->getSettings('mapping_api_key'), 2);
A::app()->getClientScript()->registerScriptFile('templates/mobile/js/gmap3.infobox.js', 2);
A::app()->getClientScript()->registerScriptFile('templates/mobile/js/gmap3.min.js', 2);
?>
<div class="gallery w3-agileits">
    <div class="container back-arrow">
        <h4><a href="mobile/"> < <?= A::t('appointments', 'Home'); ?></a></h4>
    </div>
    <div class="container">
        <h4 class="tittle-w3layouts"><?= A::t('appointments', 'Our Clinics'); ?></h4>
    </div>
    <div class="container">

        <?php
        if(!empty($clinics)):
            foreach($clinics as $clinic):
                $link = 'mobile/clinicView/'.$clinic['id'];
                ?>
                <div class="location-agileits">
                    <div class="loc-left">
                        <h4><a href="<?= $link; ?>"><?= !empty($clinic['clinic_name']) ? CHtml::encode($clinic['clinic_name']) : '--'; ?></a></h4>
                        <p><?= !empty($clinic['address']) ? CHtml::encode($clinic['address']) : '--'; ?></p>
                        <p><?= A::t('appointments', 'Phone'); ?>: <?= !empty($clinic['phone']) ? CHtml::encode($clinic['phone']) : '--'; ?></p>
                        <p><?= A::t('appointments', 'Fax'); ?>: <?= !empty($clinic['fax']) ? CHtml::encode($clinic['fax']) : '--'; ?></p>
                    </div>
                    <div class="loc-right">
                        <?php if(!empty($clinic['longitude']) && !empty($clinic['latitude'])): ?>
                            <div class="clinic-map-<?= $clinic['id']; ?>" style="width: 100%; height: 200px;" data-longitude="<?= $clinic['longitude']; ?>" data-latitude="<?= $clinic['latitude']; ?>"></div>
                        <?php else: ?>
                            <img class="clinic-map" src="templates/mobile/images/map-placeholder.png" />
                        <?php endif; ?>
                    </div>
                    <div class="clearfix"> </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
A::app()->getClientScript()->registerScript(
    'clinicMapFunction',
    'function clinicMap(clinicId){
        if(clinicId === "") return false;
        var $ = jQuery;
        var latitude = $(".clinic-map-"+clinicId).data("latitude");
        var longitude = $(".clinic-map-"+clinicId).data("longitude");
        smallMapDiv = $(".clinic-map-"+clinicId);
        smallMapDiv.gmap3({
            map: {
                options: {
                    center: [latitude, longitude],zoom: 16,scrollwheel: false
                }
            },
            marker: {values: [{latLng: [latitude, longitude]}]}
        });
    }',2
);
if(!empty($clinics)):
    foreach($clinics as $clinic):
        A::app()->getClientScript()->registerScript(
            'clinicMapView'.$clinic['id'],
            'jQuery(document).ready(function(){
                var clinicId = "'.$clinic['id'].'";
                clinicMap(clinicId);
        });',
            3
        );
    endforeach;
endif;