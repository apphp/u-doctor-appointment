<?php
$this->_activeMenu = 'appointments/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
    array('label'=>A::t('appointments', 'Appointments'), 'url'=>'modules/settings/code/appointments'),
    array('label'=>A::t('appointments', 'Appointments Management'), 'url'=>'appointments/manage'),
    array('label'=>A::t('appointments', 'Book Appointment')),
);
?>

<h1><?= A::t('appointments', 'Book Appointment'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <h3><?= A::t('appointments', 'Book Appointment'); ?></h3>
    </div>
    <div class="content">
        <div class="book-appointment mb20">
            <table>
                <tbody>
                    <tr>
                        <td width="220px">
                            <?php if($profileDoctor->avatar): ?>
                                <img src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar); ?>" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
                            <?php else: ?>
                                <img src="assets/modules/appointments/images/doctors/<?= CHtml::encode($profileDoctor->avatar_by_gender); ?>" alt="<?= CHtml::encode($fullname); ?>" title="<?= CHtml::encode($fullname); ?>" />
                            <?php endif; ?>
                        </td>
                        <td>
                            <p><strong><?= A::t('appointments', 'Name'); ?>: </strong><?= $fullname ? CHtml::encode($fullname) : '--'; ?></p>
                            <p><strong><?= A::t('appointments', 'Gender'); ?>: </strong><?= $profileDoctor->gender ? ($profileDoctor->gender == 'm') ? A::t('appointments', 'Male') : A::t('appointments', 'Famale') : '--'; ?></p>
                            <p><strong><?= A::t('appointments', 'Degree'); ?>: </strong><?= $profileDoctor->degrees_name ? CHtml::encode($profileDoctor->degrees_name) : '--'; ?></p>
                            <p>
                                <strong><?= A::t('appointments', 'Specialty'); ?>: </strong>
                                <?php
                                $countSpecialty = count($specialty);
                                if($countSpecialty):
                                    echo '<br/>';
                                    foreach($specialty as $key => $specialt):
                                        echo CHtml::encode($specialt).'<br/>';
                                    endforeach;
                                else: echo '--';
                                endif;
                                ?>
                            </p>
                            <?php if(!empty($clinicTime)): ?>
                                    <p><strong><?= A::t('appointments', 'Clinic Time'); ?></strong>: <?= $clinicTime['time'].' '.$clinicTime['offset']; ?></p>
                            <?php endif; ?>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php if($multiClinics && !empty($clinics) && count($clinics) > 1): ?>
            <div class="mb20">
                <form>
                    <select id="select-clinic" name="select-clinic" class="chosen-select-filter">
                        <option <?= empty($clinicId) ? 'selected' : ''; ?> value="0"><?= A::t('appointments', 'All Clinics'); ?></option>
                        <?php foreach ($clinics as $key => $clinic): ?>
                            <option <?= $clinicId == CHtml::encode($key) ? 'selected' : ''; ?> value="<?= $key; ?>"><?= CHtml::encode($clinic['clinic_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        <?php endif; ?>
        <div id="book_appointment">
            <?= $drawDoctorSchedules; ?>
        </div>
    </div>
</div>

<?php
A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js',2);
A::app()->getClientScript()->registerScript(
    'change_clinic',
    'jQuery(document).ready(function(){
        $("#select-clinic").change(function(){
            var clinicId = $("#select-clinic").val();
            if(clinicId == 0){
                var link = "appointments/'.$profileDoctor->id.'";
            }else{
                var link = "appointments/'.$profileDoctor->id.'/clinicId/"+clinicId;
            }
            
            $(this).closest("form").find("input[name=act]").val("changeLang");
            $(this).closest("form").attr("action", link);
            $(this).closest("form").submit();
        });
    });'
);
?>
