<?php
    $this->_pageTitle = A::t('appointments', 'Images');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Images')),
    );
?>
<!-- Register fancybox files -->
<?php A::app()->getClientScript()->registerScriptFile('assets/vendors/fancybox/jquery.mousewheel.pack.js', 2); ?>
<?php A::app()->getClientScript()->registerScriptFile('assets/vendors/fancybox/jquery.fancybox.pack'.(A::app()->getLanguage('direction') == 'rtl' ? '.rtl' : '').'.js', 2); ?>
<?php A::app()->getClientScript()->registerCssFile('assets/vendors/fancybox/jquery.fancybox'.(A::app()->getLanguage('direction') == 'rtl' ? '.rtl' : '').'.css'); ?>

    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder">
                    <?php
                        echo $actionMessage;
                        if($checkUploadImagesAccess):
                            if($checkAccessAccountUsingMembershipPlan):?>
                                <div class="margin-bottom-20">
                                    <a href="doctorImages/addMyImage" class="add-new button margin-right-5"><?= A::t('appointments', 'Add Single Image'); ?></a>
                                    <?php if($allowMultiImageUpload): ?>
                                        <a href="doctorImages/addMyMultiple" class="add-new button"><?= A::t('appointments', 'Add Multiple Images'); ?></a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
						<?php else:
							echo CWidget::create('CMessage', array('warning', A::t('appointments', 'You have reached the maximum number of {param} allowed by your current membership plan.', array('{param}'=>A::t('appointments', 'Images')))));
						endif;

                        $fields = array();
                        $condition = '';

                        CWidget::create('CGridView', array(
                            'model'=>'Modules\Appointments\Models\DoctorImages',
                            'actionPath'=>'doctorImages/myImages',
                            'condition'=>'doctor_id = '.(int)$doctorId,
                            'passParameters'=>true,
                            'pagination'=>array('enable'=>true, 'pageSize'=>20),
                            'defaultOrder'=>array('id'=>'DESC'),
                            'options'=>array(
                                'gridTable'=>array('class'=>'table'),
                            ),
                            'sorting'=>true,
                            'fields'=>array(
                                'index'             => array('title'=>'', 'type'=>'index', 'align'=>'', 'width'=>'17px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false),
                                'image_file_thumb'  => array('title'=>A::t('appointments', 'Image'), 'type'=>'image', 'width'=>'60px',  'align'=>'', 'imagePath'=>'assets/modules/appointments/images/doctorimages/thumbs/', 'defaultImage'=>'no_image.png', 'imageWidth'=>'50px', 'imageHeight'=>'35px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'htmlOptions'=>array(), 'showImageInfo'=>true, 'prependCode'=>'<a class="fancybox" rel="reference_picture" href="#">', 'appendCode'=>'</a>'),
                                'title'             => array('title'=>A::t('appointments', 'Title'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'stripTags'=>true),
                                'sort_order'        => array('title'=>A::t('appointments', 'Sort Order'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'90px'),
                                'is_active'         => array('title'=>A::t('appointments', 'Active'), 'type'=>'link', 'class'=>'center', 'headerClass'=>'center', 'width'=>'60px', 'linkUrl'=>'doctorImages/changeFrontendStatus/id/{id}/', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status'))),
                            ),
                            'actions'=>array(
                                'edit'    => array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorImages/editMyImage/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this record')
                                ),
                                'delete'=>array(
                                    'disabled'=>!$checkAccessAccountUsingMembershipPlan,
                                    'link'=>'doctorImages/deleteMyImage/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this record'), 'onDeleteAlert'=>true
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
<?php
A::app()->getClientScript()->registerScript(
    'autoportalFancyboxHandler',
    "$('.fancybox').each(function() {
        var src = $(this).find('img').attr('src').replace('thumbs/', '').replace('_thumb', ''),
            row_id = $(this).closest('tr').attr('id'),
            title = $(this).closest('tr').find('td').eq(2).text();
        $(this).attr('href', src);
        $(this).attr('title', title);
    });
    $('.fancybox').fancybox({
        'opacity'       : true,
        'overlayShow'   : false,
        'overlayColor'  : '#000',
        'overlayOpacity': 0.5,
        'titlePosition' : 'inside',
        'cyclic'        : true,
        'transitionIn'  : 'elastic',
        'transitionOut' : 'fade'
    });
    ",
    5
);
