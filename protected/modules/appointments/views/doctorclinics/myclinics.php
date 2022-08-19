<?php
    $this->_pageTitle = A::t('appointments', 'Clinics');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Clinics')),
    );
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
                <?php
                    echo $actionMessage;
                    if($checkUploadClinicsAccess):
                        if(!empty($addDoctorClinics)):
                            echo $addDoctorClinics;
                        elseif($checkAccessAccountUsingMembershipPlan && empty($addDoctorClinics)):?>
                            <div class="margin-bottom-20">
                                <a href="doctorClinics/addMyClinic" class="add-new button margin-right-5"><?= A::t('appointments', 'Add Clinic'); ?></a>
                            </div>
                        <?php endif; ?>
                    <?php else:
                        echo CWidget::create('CMessage', array('warning', A::t('appointments', 'You have reached the maximum number of {param} allowed by your current membership plan.', array('{param}'=>A::t('appointments', 'Clinics')))));
                    endif;

                    $fields = array();
                    $condition = '';

                    CWidget::create('CGridView', array(
                        'model'=>'Modules\Appointments\Models\DoctorClinics',
                        'actionPath'=>'doctorClinics/myImages',
                        'condition'=>'doctor_id = '.(int)$doctorId,
                        'passParameters'=>true,
                        'pagination'=>array('enable'=>true, 'pageSize'=>20),
                        'defaultOrder'=>array('id'=>'DESC'),
                        'options'=>array(
                            'gridTable'=>array('class'=>'table'),
                        ),
                        'sorting'=>true,
                        'fields'            => array(
                            'index'         => array('title'=>'', 'type'=>'index', 'align'=>'', 'width'=>'17px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false),
                            'clinic_name'   => array('title'=>A::t('appointments', 'Clinic'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'stripTags'=>true),
                            'clinic_address'   => array('title'=>A::t('appointments', 'Address'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'stripTags'=>true),
                            'sort_order'    => array('title'=>A::t('appointments', 'Sort Order'), 'type'=>'label', 'align'=>'right', 'width'=>'', 'class'=>'right', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'stripTags'=>true),
                        ),
                        'actions'           => array(
                            'edit'  => array(
                                'disabled'  => !$checkAccessAccountUsingMembershipPlan,
                                'link'      => 'doctorClinics/editMyClinic/id/{id}/', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this Image')
                            ),
                            'delete'  => array(
                                'disabled'  => !$checkAccessAccountUsingMembershipPlan,
                                'link'      => 'doctorClinics/deleteMyClinic/id/{id}/', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this Image'), 'onDeleteAlert'=>true
                            )
                        ),
                        'return'=>false,
                    ));

                ?>
                </div>
            </div>
        </div>
    </div>
</section>