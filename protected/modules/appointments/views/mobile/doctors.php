<?php
use Modules\Appointments\Components\DoctorsComponent;
?>
<!-- Doctors -->
<div class="blog" id="blog">
    <div class="container back-arrow">
        <h4><a href="mobile/" class="back-arrow"> < <?= A::t('appointments', 'Home'); ?></a></h4>
    </div>
    <div class="container">
        <?= $drawAppointmentsBlock; ?>
    </div>
    <div class="container">
        <h4 class="tittle-w3layouts"><?= A::t('appointments', 'Our Doctors'); ?></h4>
        <?php
            echo $actionMessage;
            if(!empty($doctors)):
                $countDoctor = 1;
                foreach($doctors as $doctor):
                    $link = 'mobile/doctorView/'.$doctor['id'].'?page='.$currentPage;
                    ?>
                    <a href="<?= $link; ?>">
                        <div class="col-md-12" style="padding-right:0px;">
                            <div class="services-grids doctors-grids">
                                <div class="pull-left">
                                    <img
                                        style="height:100px;"
                                        src="assets/modules/appointments/images/doctors/<?= $doctor['avatar'] ?  CHtml::encode($doctor['avatar']) : CHtml::encode($doctor['avatar_by_gender']) ; ?>" class="fullwidth wp-post-image" alt="<?= DoctorsComponent::getDoctorName($doctor); ?>"
                                        title="<?= DoctorsComponent::getDoctorName($doctor); ?>"
                                    />
                                </div>
                                <div class="pull-left text-left padding-left-20">
                                    <h4>
                                        <?= DoctorsComponent::getDoctorName($doctor); ?>
                                        <?= !empty($doctor['degrees_name']) ? '<br>'.$doctor['degrees_name'] : ''; ?>
                                    </h4>
                                    <?php
                                        $doctorSpecialtiesArray = array();
                                        if(!empty($doctorSpecialties[$doctor['id']])):
                                            foreach($doctorSpecialties[$doctor['id']] as $doctorSpecialty):
                                                $doctorSpecialtiesArray[] = $doctorSpecialty['specialty_name'];
                                            endforeach;
                                            echo '<p>'.implode('<br>', $doctorSpecialtiesArray).'</p>';
                                        endif;
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </a>
                <?php $countDoctor++; endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="container">
        <?php
        if(!empty($doctors)):
            if($totalDoctors > 1):
                echo CWidget::create('CPagination', array(
                    'actionPath'   => $pageLink,
                    'currentPage'  => $currentPage,
                    'pageSize'     => $pageSize,
                    'totalRecords' => $totalDoctors,
                    'showResultsOfTotal' => false,
                    'linkType' => 0,
                    'paginationType' 	=> 'prevNext|justNumbers',
                    'linkNames' 		=> array('previous' => '', 'next'=>''),
                    'showEmptyLinks' 	=> true,
                    'htmlOptions' 		=> array('linksWrapperTag' => 'div', 'linksWrapperClass' => 'page-numbers'),
                ));
            endif;
        endif;
        ?>
    </div>
</div>
<?php
A::app()->getClientScript()->registerScript(
    'loading',
    '$(document).ready(function() {
        $("#find_doctors").on("click", function(){
            if($(this).hasClass("loading")) return false;
            var existImage = $("img").hasClass("ml10");
            if(existImage == false){
                $("#find_doctors").after(\'<img class="ml10" src="templates/mobile/images/ajax_loading.gif" alt="loading" />\');
                $(this).addClass("loading");
            }
        });
    });',
    2
);
