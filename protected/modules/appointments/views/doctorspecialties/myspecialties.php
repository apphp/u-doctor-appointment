<?php
    $this->_pageTitle = A::t('appointments', 'Specialties');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Specialties')),
    );
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php
                        echo $actionMessage;

                    if($checkUploadSpecialtiesCountAccess):
                        if($checkAccessAccountUsingMembershipPlan):
                            echo '<div class="margin-bottom-20">';
                            echo '<a href="doctorSpecialties/addMySpecialty" class="add-new button">'.A::t('appointments', 'Add Specialty').'</a>';
                            echo '</div>';
                        endif;
                    else:
                        echo CWidget::create('CMessage', array('warning', A::t('appointments', 'You have reached the maximum number of {param} allowed by your current membership plan.', array('{param}'=>A::t('appointments', 'Specialties'))), array('button'=>true)));
                    endif;

                        $fields = array();
                        $condition = '';

                        CWidget::create('CGridView', array(
                            'model'=>'Modules\Appointments\Models\DoctorSpecialties',
                            'actionPath'=>'doctorSpecialties/mySpecialties',
                            'condition'=>'doctor_id = '.(int)$doctorId,
                            'passParameters'=>true,
                            'pagination'=>array('enable'=>true, 'pageSize'=>20),
                            'defaultOrder'=>array('id'=>'DESC'),
                            'options'=>array(
                                'gridTable'=>array('class'=>'table'),
                            ),
                            'sorting'=>true,
                            'fields'=>array(
                                'specialty_name'        => array('title'=>A::t('appointments', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'200px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                                'specialty_description' => array('title'=>A::t('appointments', 'Description'), 'type'=>'label', 'align'=>'', 'maxLength'=>130, 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                                'sort_order'             => array('title'=>A::t('appointments', 'Sort Order'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
								'is_default'             => array('title'=>A::t('appointments', 'Default'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
                            ),
                            'actions'=>array(
                                'edit'    => array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorSpecialties/editMySpecialty/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                                ),
                                'delete'=>array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorSpecialties/deleteMySpecialty/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
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
