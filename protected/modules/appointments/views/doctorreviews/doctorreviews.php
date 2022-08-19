<?php
    $this->_pageTitle = A::t('appointments', 'Reviews');
    $this->_activeMenu = 'patients/myAccount';
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label' => A::t('appointments', 'Reviews'))
    );

    use \Modules\Appointments\Components\DoctorsComponent;
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
					<?php DoctorsComponent::drawRating($doctorId); ?>
                    <h3 style="width: 100%"><?= A::t('appointments', 'Reviews'); ?></h3>
                    <?php
					$fields = array();
					$filterFields = array();

					$fields = array(
						'patient_name'          => array('title'=>A::t('appointments', 'Patient'), 'type'=>'label', 'align'=>'', 'width'=>'50px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
						'message'               => array('title'=>A::t('appointments', 'Message'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'maxLength'=>'100'),
						'created_at' 			=> array('title'=>A::t('appointments', 'Created at'), 'type'=>'datetime', 'align'=>'', 'width'=>'110px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(null=>'--'), 'format'=>$dateTimeFormat),
						'rating_price'          => array('title'=>A::t('appointments', 'Rating Price'), 'type'=>'label', 'align'=>'', 'width'=>'50px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
						'rating_wait_time'      => array('title'=>A::t('appointments', 'Rating Wait Time'), 'type'=>'label', 'align'=>'', 'width'=>'50px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
						'rating_bedside_manner' => array('title'=>A::t('appointments', 'Rating Bedside Manner'), 'type'=>'label', 'align'=>'', 'width'=>'50px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true),
						'status'          		=> array('title'=>A::t('appointments', 'Status'), 'type'=>'label', 'width'=>'', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>$labelStatusReviews, 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::te('appointments', 'Status')))
					);

					$filterFields['patient_name']   = array('title'=>A::t('appointments', 'Patient Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'50px', 'maxLength'=>'32');
					$filterFields['created_at']             = array('title'=>A::t('appointments', 'Date Created'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'50px', 'maxLength'=>'32');


					echo CWidget::create('CGridView', array(
						'model'=>'Modules\Appointments\Models\DoctorReviews',
						'actionPath'=>'doctorReviews/doctorReviews',
						'condition'	=> 'patient_id = '.$doctorId.' && status != 2',
						'defaultOrder'		=> array('created_at'=>'DESC'),
						'passParameters'=>true,
						'pagination'=>array('enable'=>true, 'pageSize'=>20),
						'sorting'=>true,
						'options'	=> array(
                            'filterDiv' 	=> array('class'=>'frmFilter'),
							'gridWrapper'	=> array('tag'=>'div', 'class'=>'table'),
                        ),
						'filters'=> $filterFields,
						'fields'=> $fields,
					));
					?>
                </div>
            </div>
        </div>
    </div>
</section>

