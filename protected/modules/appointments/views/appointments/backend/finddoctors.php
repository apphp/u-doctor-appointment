<?php
$this->_activeMenu = 'appointments/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
    array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
    array('label'=>A::t('appointments', 'Appointments Management'), 'url'=>'appointments/manage'),
    array('label'=>A::t('appointments', 'Find Doctors')),
);
?>

<h1><?= A::t('appointments', 'Find Doctors'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <h3><?= A::t('appointments', 'Search Results'); ?></h3>
    </div>

    <div class="content">
        <?= $actionMessage; ?>
        <?= $drawFindDoctorsBlock; ?>
        <?php if($maxPage > 1): ?>
            <div class="aligncenter">
                <a id="show_more" class="button_small" href="javascript:void(0);" onclick="javascript:findDoctorsShowMore(this)" data-page="1" data-doctor-id="<?= $doctorId; ?>" data-doctor-name="<?= $doctorName; ?>" data-location-id="<?= $locationId; ?>" data-location="<?= $location; ?>" data-specialty-id="<?= $specialtyId; ?>" data-max-page="<?= $maxPage; ?>"><?= A::t('appointments', 'Show More'); ?></a>
            </div>
        <?php endif; ?>
        <?php if($showAllSpecialties && !empty($allSpecialties)): ?>
            <h3><?= A::t('appointments', 'Find a Doctor by Specialty'); ?></h3>
            <table>
                <tbody>
                    <?php
                    $i = 0;
                    foreach($allSpecialties as $id => $specialtyName):
                        $numCol = $i++ % 2;
                        ?>
                        <div class="one_half<?= $numCol == 0 ? ' first_column' : ''; ?>">
                            <?php if($specialtyName['count']): ?>
                                <tr><td><a href="javascript:void(0);" class="link-find-doctor-by-specialty" data-id="<?= $id; ?>"><?= $specialtyName['name'].' ('.$specialtyName['count'].')'; ?> </a></td></tr>
                            <?php else: ?>
                                <tr><td><?= $specialtyName['name']; ?></td></tr>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?= CHtml::openForm('appointments/findDoctors', 'get', array('id'=>'find_doctors_form')); ?>
        <input type="hidden" name="doctorId" value="" id="find_doctors_form_doctor_id" />
        <input type="hidden" name="locationId" value="" id="find_doctors_form_doctor_id" />
        <input type="hidden" name="specialtyId" value="" id="find_doctors_form_specialty_id" />
        <?= CHtml::closeForm(); ?>
    </div>
</div>

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

