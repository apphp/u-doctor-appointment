<?php
    $this->_pageTitle = A::t('appointments', 'Find Doctors');
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label' => A::t('appointments', 'Find Doctors'))
    );
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
                    <div class="one_first" id="find_doctors_content">
                    <?php if(!empty($actionMessage)): ?>
                        <h3><?= A::t('appointments', 'Search Results'); ?></h3>
                    <?php
                        echo $actionMessage;
                    endif; ?>
                    <?= $drawFindDoctorsBlock; ?>
                    </div>
                    <?php if($maxPage > 1): ?>
                        <div class="aligncenter">
                            <a id="show_more" class="button_small" href="javascript:void(0);" onclick="javascript:findDoctorsShowMore(this)" data-page="1" data-doctor-id="<?= $doctorId; ?>" data-doctor-name="<?= $doctorName; ?>" data-location-id="<?= $locationId; ?>" data-location="<?= $location; ?>" data-specialty-id="<?= $specialtyId; ?>" data-max-page="<?= $maxPage; ?>"><?= A::t('appointments', 'Show More'); ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if($showAllSpecialties && !empty($allSpecialties)): ?>
                    <div class="one_first first_column">
                        <h3><?= A::t('appointments', 'Find a Doctor by Specialty'); ?></h3>
                    <?php
                    $i = 0;
                    foreach($allSpecialties as $id => $specialtyName):
                        $numCol = $i++ % 2;
                    ?>
                        <div class="one_half<?= $numCol == 0 ? ' first_column' : ''; ?>">
                            <?php if($specialtyName['count']): ?>
                                <a href="javascript:void(0);" class="link-find-doctor-by-specialty" data-id="<?= $id; ?>"><?= $specialtyName['name'].' ('.$specialtyName['count'].')'; ?> </a>
                            <?php else: ?>
                                <?= $specialtyName['name']; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= CHtml::openForm('appointments/findDoctors', 'get', array('id'=>'find_doctors_form')); ?>
    <input type="hidden" name="doctorId" value="" id="find_doctors_form_doctor_id" />
    <input type="hidden" name="locationId" value="" id="find_doctors_form_doctor_id" />
    <input type="hidden" name="specialtyId" value="" id="find_doctors_form_specialty_id" />
<?= CHtml::closeForm(); ?>

<?php
A::app()->getClientScript()->registerScript(
    'findDoctors',
    'jQuery(document).ready(function(){
        var $ = jQuery;
        $(".link-find-doctor-by-specialty").click(function(){
            var id = $(this).data("id");
            $("#find_doctors_form_specialty_id").val(id);
            sendForm("find_doctors_form");
        });
    });
    function sendForm(frmNameId){
        jQuery("#"+frmNameId).submit();
    }
    ',
    2
);

