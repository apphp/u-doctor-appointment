<?php
    Website::setMetaTags(array('title'=>A::t('appointments', 'Our Clinics')));
    $this->_pageTitle = A::t('appointments', 'Our Clinics');
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Our Clinics'))
    );
A::app()->getClientScript()->registerScriptFile('templates/default/js/gmap3.infobox.js', 1);
A::app()->getClientScript()->registerScriptFile('templates/default/js/gmap3.min.js', 1);
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <?php
                $firstColumn = true;
                foreach($clinics as $clinic):
                    $clinicLink = 'clinics/'.CHtml::encode($clinic['id']).'/'.CString::seoString($clinic['clinic_name']);
                    ?>
                    <div class="one_half<?= $firstColumn ? ' first_column' : '' ?>">
                        <div class="featured_block">
                            <div class="colored_title">
                                <div class="colored_title_inner">
                                    <h4 class="tac">
                                        <a href="<?= $clinicLink; ?>" class="link-find-doctor-by-specialty" data-id="1"><?= CHtml::encode($clinic['clinic_name']); ?></a>
                                    </h4>
                                    <?php if(!empty($clinic['address'])): ?>
                                        <p class="tac">
                                            <?= A::t('appointments', 'Address').': '.CHtml::encode($clinic['address']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if(!empty($clinic['longitude']) && !empty($clinic['latitude'])): ?>
                                        <div class="clinic-map-<?= $clinic['id']; ?>" style="width: 100%; height: 250px;" data-longitude="<?= $clinic['longitude']; ?>" data-latitude="<?= $clinic['latitude']; ?>"></div>
                                    <?php else: ?>
                                        <img class="clinic-map" src="templates/default/images/map-placeholder.png" />
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    if($firstColumn == true){
                        $firstColumn = false;
                    }else{
                        $firstColumn = true;
                    }
                endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php
A::app()->getClientScript()->registerScriptFile('//maps.google.com/maps/api/js?sensor=false&language=en&ver=3.8.3&key='.Bootstrap::init()->getSettings('mapping_api_key'));
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
    }',1
);
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
