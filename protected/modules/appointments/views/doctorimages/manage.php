<?php
    $this->_activeMenu = 'doctors/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('appointments', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('appointments', 'Doctor Appointments'), 'url'=>'modules/settings/code/appointments'),
        array('label'=>A::t('appointments', 'Doctors'), 'url'=>'doctors/manage'),
        array('label'=>A::t('appointments', 'Images')),
    );
?>

<!-- Register fancybox files -->
<?php A::app()->getClientScript()->registerScriptFile('assets/vendors/fancybox/jquery.mousewheel.pack.js', 2); ?>
<?php A::app()->getClientScript()->registerScriptFile('assets/vendors/fancybox/jquery.fancybox.pack'.(A::app()->getLanguage('direction') == 'rtl' ? '.rtl' : '').'.js', 2); ?>
<?php A::app()->getClientScript()->registerCssFile('assets/vendors/fancybox/jquery.fancybox'.(A::app()->getLanguage('direction') == 'rtl' ? '.rtl' : '').'.css'); ?>

<h1><?= A::t('appointments', 'Doctors Management'); ?></h1>

<div class="bloc">

    <?= $tabs; ?>

    <div class="sub-title">
        <a class="sub-tab active"><?= A::t('appointments', 'Images').' | '.$doctorName; ?></a>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('doctor', 'add')){
			if($checkUploadImagesAccess){
				echo '<a href="doctorImages/add/doctorId/'.$doctorId.'" class="add-new">'.A::t('appointments', 'Add Single Image').'</a>';
				if($allowMultiImageUpload){
					echo '&nbsp;&nbsp;&nbsp;';
					echo '<a href="doctorImages/addMultiple/doctorId/'.$doctorId.'" class="add-new">'.A::t('appointments', 'Add Multiple Images').'</a>';
				}
            }else{
				echo CWidget::create('CMessage', array('warning', A::t('appointments', 'This doctor has the maximum allowed number of {param} for this membership plan!!!', array('{param}'=>A::t('appointments', 'Images'))), array('button'=>true)));
            }
        }

        $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'60px', 'linkUrl'=>'', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Active')));
        if(Admins::hasPrivilege('doctor', 'edit')){
            $isActive = array('title'=>A::t('appointments', 'Active'), 'type'=>'link', 'class'=>'center', 'headerClass'=>'center', 'width'=>'60px', 'linkUrl'=>'doctorImages/changeStatus/doctorId/'.$doctorId.'/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('appointments', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('appointments', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('appointments', 'Click to change status')));
        }

        echo CWidget::create('CGridView', array(
            'model'             => 'Modules\Appointments\Models\DoctorImages',
            'actionPath'        => 'doctorImages/manage/doctorId/'.$doctorId,
            'condition'         => 'doctor_id = '.$doctorId,
            'defaultOrder'      => array('sort_order'=>'ASC'),
            'passParameters'    => true,
            'pagination'        => array('enable'=>true, 'pageSize'=>20),
            'sorting'           => true,
            'filters'           => array(),
            'fields'            => array(
                'index'             => array('title'=>'', 'type'=>'index', 'align'=>'', 'width'=>'17px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false),
                'image_file_thumb'  => array('title'=>A::t('appointments', 'Image'), 'type'=>'image', 'width'=>'60px',  'align'=>'', 'imagePath'=>'assets/modules/appointments/images/doctorimages/thumbs/', 'defaultImage'=>'no_image.png', 'imageWidth'=>'50px', 'imageHeight'=>'35px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'htmlOptions'=>array(), 'showImageInfo'=>true, 'prependCode'=>'<a class="fancybox" rel="reference_picture" href="#">', 'appendCode'=>'</a>'),
                'title'             => array('title'=>A::t('appointments', 'Title'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'stripTags'=>true),
                'sort_order'        => array('title'=>A::t('appointments', 'Sort Order'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'90px'),
                'is_active'         => $isActive
            ),
            'actions'           => array(
                'edit'    => array(
                    'disabled'      => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'edit'),
                    'link'          => 'doctorImages/edit/doctorId/'.$doctorId.'/id/{id}/', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('appointments', 'Edit this Image')
                ),
                'delete'  => array(
                    'disabled'      => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('doctor', 'delete'),
                    'link'          => 'doctorImages/delete/doctorId/'.$doctorId.'/id/{id}/', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('appointments', 'Delete this Image'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));
    ?>
    </div>
</div>
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
