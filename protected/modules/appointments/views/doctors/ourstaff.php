<?php
    $this->_pageTitle = A::t('appointments', 'Our Doctors');
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Our Doctors'))
    );

use \Modules\Appointments\Components\DoctorsComponent;
?>

<section id="content" role="main">

    <div class="entry">
        <?php
            $count = 0;
            if(count($doctors) > 0):
        ?>
            <div class="cmsms_cc">
                <?php foreach($doctors as $doctor):
                    $link = Website::prepareLinkByFormat('appointments', 'profile_link_format', $doctor['id'], DoctorsComponent::getDoctorName($doctor));
                ?>
                <div class="one_third<?= ($count++ == 0 ? ' first_column' : ''); ?>" id="">
                    <div class="cmsms_our_team_wrap">
                        <div class="cmsms_our_team">
                            <div class="wrap_person">
                                <figure>
                                    <img src="assets/modules/appointments/images/doctors/<?= $doctor['avatar'] ?  CHtml::encode($doctor['avatar']) : CHtml::encode($doctor['avatar_by_gender']) ; ?>" class="fullwidth" />
                                </figure>
                                <div class="cmsms_team_rollover glow_blue">
                                    <a href="<?= $link; ?>" class="cmsms_link">
                                        <span></span>
                                    </a>
                                </div>
                            </div>
                            <header class="entry-header">
                                <div>
                                    <h6>
                                        <a href="<?= $link; ?>"><?= (!empty($doctor['title']) ? $doctor['title'] : '').' '.CHtml::encode($doctor['full_name']).(!empty($degrees[$doctor['medical_degree_id']]) ? ', '.$degrees[$doctor['medical_degree_id']]['title'] : ''); ?></a>
                                    </h6>
                                    <p class="person_subtitle person_subtitle_h20">
                                        <?php
                                        $doctorSpecialtiesArray = array();
                                        if(!empty($doctorSpecialties[$doctor['id']])):
                                            foreach($doctorSpecialties[$doctor['id']] as $doctorSpecialty):
                                                $doctorSpecialtiesArray[] = $doctorSpecialty['specialty_name'];
                                            endforeach;
                                        endif;
                                        if(count($doctorSpecialtiesArray) > 2):
                                            echo '<span class="pull-right cursor-pointer">&#x25BE;</span>';
                                        endif;
                                        echo implode(', ', $doctorSpecialtiesArray);
                                        ?>
                                    </p>
                                </div>
                            </header>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?php echo CWidget::create('CMessage', array('warning', A::t('appointments', 'No doctors found!'))); ?>
        <?php endif; ?>
    </div>
</section>


<?php

A::app()->getClientScript()->registerScript(
    'homepage-general',
    "var tpj=jQuery;
    tpj(document).ready(function() {    
        tpj('.person_subtitle').on('click', function(){
            tpj(this).toggleClass('person_subtitle_h20');
        });
    });
	",
    3
);

?>



