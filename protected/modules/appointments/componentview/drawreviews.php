<?php A::app()->getClientScript()->registerCssFile('assets/vendors/bar-rating/css/css-stars.css'); ?>

<!-- Review Form -->
<div class="margin-bottom-20">
    <?php if($showRatingForm): ?>
        <?php if(CAuth::getLoggedId() && CAuth::getLoggedRole() == 'patient'): ?>
            <?php if($drawForm): ?>
                <!-- if $drawForm = true draw Review Form -->
                <h3><?= A::t('appointments', 'Leave a Review'); ?></h3>
                <aside class="first_column margin-bottom-20 margin-top-20 box success_box" style="display:none" id="messageSuccess"><table><tbody>
                    <tr>
                        <td>&nbsp;</td>
                        <td><p><?= A::t('appointments', 'You have successfully added a new review!').' '.($reviewModeration ? A::t('appointments', 'Your review will be published after moderation.') : ''); ?></p></td>
                    </tr>
                    </tbody></table>
                </aside>
                <aside class="first_column margin-bottom-20 margin-top-20 box error_box" style="display:none" id="messageError"><table><tbody>
                    <tr>
                        <td>&nbsp;</td>
                        <td><p id="messageErrorText"><?= A::t('appointments', 'An error occurred! Please try again later.'); ?></p></td>
                    </tr>
                    </tbody></table>
                </aside>
                <?php
                $ratingValue = array(1=>'1',2=>'2',3=>'3',4=>'4',5=>'5');
                echo CWidget::create('CDataForm', array(
                    'model'			=> 'Modules\Appointments\Models\DoctorReviews',
                    'method'		=> 'post',
                    'htmlOptions'	=> array(
                        'id'	=> 'frmDoctorReviewsAdd',
                        'name'	=> 'frmDoctorReviewsAdd',
                        'class'	=> 'frmAppointments',
                        'autoGenerateId'=>true
                    ),
                    'requiredFieldsAlert'=>true,
                    'fields'=>array(
                        'rating_price'=>array('type'=>'select', 'title'=>A::t('appointments', 'Rating Price'),     'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($ratingValue)), 'data'=>$ratingValue, 'emptyOption'=>true, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id' => 'rating_price', 'data-label' => A::t('appointments', 'Price'))),
                        'rating_wait_time'=>array('type'=>'select', 'title'=>A::t('appointments', 'Rating Price'),     'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($ratingValue)), 'data'=>$ratingValue, 'emptyOption'=>true, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id' => 'rating_wait_time', 'data-label' => A::t('appointments', 'Wait Time'))),
                        'rating_bedside_manner'=>array('type'=>'select', 'title'=>A::t('appointments', 'Rating Bedside Manner'),     'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($ratingValue)), 'data'=>$ratingValue, 'emptyOption'=>true, 'emptyValue'=>'', 'viewType'=>'dropdownlist', 'multiple'=>false, 'htmlOptions'=>array('id' => 'rating_bedside_manner', 'data-label' => A::t('appointments', 'Bedside Manner'))),
                        'message'           =>array('type'=>'textarea', 'title'=>A::t('appointments', 'Message'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>500), 'htmlOptions'=>array('maxLength'=>'500', 'id'=>'message')),
                        'captcha'           =>array('type'=>'captcha', 'title'=>'', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'captcha'), 'htmlOptions'=>array()),
                        'doctor_id'         => array('type'=>'hidden', 'default'=>$doctorId, 'htmlOptions'=>array('id' => 'doctor_id')),
                        'appointment_id'    => array('type'=>'hidden', 'default'=>$appointmentId, 'htmlOptions'=>array('id' => 'appointment_id')),
                    ),
                    'buttons'=>array(
                        'custom' => array('type'=>'button', 'value'=>A::t('appointments', 'Submit'), 'htmlOptions'=>array('onclick'=>'javascript:review_SubmitForm(this)', 'class' => 'button_small', 'data-review-moderation' => $reviewModeration, 'data-created-at' => date($dateFormat), 'data-patient-name' => CAuth::getLoggedName())),
                    ),
                    'buttonsPosition'   => 'bottom',
                    'return'            => true,
                ));
                ?>
            <?php else: echo $actionMessage; ?>
            <?php endif; ?>
        <?php elseif(CAuth::getLoggedRole() != 'doctor'):
            $alert = A::t('appointments', 'You can not leave a review, please login!');
            $alertType =  'warning';
            echo CWidget::create('CMessage', array($alertType, $alert, array('button'=>true)));
            ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<!-- Review Form -->

<?php if(!$showReviews): ?>
    <h3 style="width: 100%"><?= A::t('appointments', 'Reviews'); ?></h3>
    <p><?= A::t('appointments', 'No Reviews'); ?></p>
<?php else:

    if($showRating):
        \Modules\Appointments\Components\DoctorsComponent::drawRating($doctorId);
        ?>

        <h3 style="width: 100%"><?= A::t('appointments', 'Reviews'); ?></h3>
        <div id="draw_review">
            <?php foreach($doctorReviews as $doctorReview): ?>
                <div class="featured_block">
                    <div class="colored_title">
                        <div class="colored_title_inner">
                            <blockquote><?= CHtml::encode($doctorReview['message']); ?></blockquote>
                            <div class="one_first">
                                <p class="one_third"><?= A::t('appointments', 'Price'); ?>: <img src="templates/default/images/small_star/smallstar-<?= CHtml::encode($doctorReview['rating_price']); ?>.png" /></p>
                                <p class="one_third"><?= A::t('appointments', 'Wait Time'); ?>: <img src="templates/default/images/small_star/smallstar-<?= CHtml::encode($doctorReview['rating_wait_time']); ?>.png" /></p>
                                <p class="one_third"><?= A::t('appointments', 'Bedside Manner'); ?>: <img src="templates/default/images/small_star/smallstar-<?= CHtml::encode($doctorReview['rating_bedside_manner']); ?>.png" /></p>
                            </div>
                            <p><?= CHtml::encode($doctorReview['patient_name']); ?> • <?= CLocale::date($dateFormat, $doctorReview['created_at']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if($totalReviews > 1 && $currentPage < ceil($totalReviews/$pageSize)): ?>
        <div class="aligncenter">
            <a id="show_more" class="button_small" href="javascript:void(0);" onclick="javascript:review_ShowMore(this)" data-doctor-id ="<?= $doctorReviews[0]['doctor_id']; ?>" data-current-page ="1"  data-rating-bedside-manner-label ="<?= A::t('appointments', 'Bedside Manner'); ?>" data-rating-wait-time-label ="<?= A::t('appointments', 'Wait Time'); ?>" data-rating-price-label ="<?= A::t('appointments', 'Price'); ?>" ><?= A::t('appointments', 'Show More'); ?></a>
        </div>
    <?php endif;
    endif;
endif;

A::app()->getClientScript()->registerScriptFile('assets/vendors/bar-rating/jquery.barrating.min.js',2);

A::app()->getClientScript()->registerScript(
    'barRating',
    '$(function() {
        $("#rating_price").barrating({
            theme: "css-stars"
        });
        $("#rating_wait_time").barrating({
            theme: "css-stars"
        });
        $("#rating_bedside_manner").barrating({
            theme: "css-stars"
        });
    });',
    3
);