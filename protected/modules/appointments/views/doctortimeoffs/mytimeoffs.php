<?php
    $this->_pageTitle = A::t('appointments', 'Timeoffs');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Timeoffs')),
    );
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php
                        echo $actionMessage;
                        if($checkAccessAccountUsingMembershipPlan):
                            echo '<div class="margin-bottom-20">';
                            echo '<a href="doctorTimeoffs/addMyTimeoff/doctorId/'.$doctorId.'" class="add-new button">'.A::t('appointments', 'Add Timeoff').'</a>';
                            echo '</div>';
                        endif;

                        $fields = array();
                        $condition = '';

                        CWidget::create('CGridView', array(
                            'model'=>'Modules\Appointments\Models\DoctorTimeoffs',
                            'actionPath'=>'doctorTimeoffs/myTimeoffs',
                            'condition'=>'doctor_id = '.(int)$doctorId,
                            'passParameters'=>true,
                            'pagination'=>array('enable'=>true, 'pageSize'=>20),
                            'defaultOrder'=>array('id'=>'DESC'),
                            'options'=>array(
                                'gridTable'=>array('class'=>'table'),
                            ),
                            'sorting'=>true,
                            'fields'=>array(
                                'description'   => array('title'=>A::t('appointments', 'Description'), 'type'=>'label', 'maxLength'=>'50', 'align'=>'', 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                                'date_from'     => array('title'=>A::t('appointments', 'Valid From Date'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$dateFormat),
                                'time_from'     => array('title'=>A::t('appointments', 'Valid From Time'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(''=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat),
                                'date_to'       => array('title'=>A::t('appointments', 'Valid To Date'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('appointments', 'Unknown')), 'format'=>$dateFormat),
                                'time_to'       => array('title'=>A::t('appointments', 'Valid To Time'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(''=>A::t('appointments', 'Unknown')), 'format'=>$timeFormat),
                            ),
                            'actions'=>array(
                                'edit'    => array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorTimeoffs/editMyTimeoff/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                                ),
                                'delete'=>array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorTimeoffs/deleteMyTimeoff/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
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
