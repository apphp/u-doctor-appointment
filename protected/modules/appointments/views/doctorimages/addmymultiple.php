<?php
    $this->_pageTitle = A::t('appointments', 'Add Multiple Images');
    $this->_breadCrumbs = array(
		array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=>A::t('appointments', 'Dashboard'), 'url'=>'doctors/dashboard'),
        array('label'=>A::t('appointments', 'Images'), 'url'=>'doctorImages/myImages'),
        array('label'=>A::t('appointments', 'Add Multiple Images')),
    );

    $formName = 'frmDoctorMultipleImagesAdd';

    A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
    A::app()->getClientScript()->registerScriptFile('assets/vendors/jquery/jquery-ui.min.js', 1);
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
					<?= $actionMessage; ?>
                    <div class="cmsms-form-builder">
                    <?php
                        echo CWidget::create('CFormView', array(
                            'action'        =>  'doctorImages/addMyMultiple',
                            'cancelUrl'     =>  'doctorImages/myImages',
                            'method'        =>  'post',
                            'htmlOptions'   =>  array(
                                'name'          =>  'form-contact',
                                'enctype'       =>  'multipart/form-data',
                                'autoGenerateId'=>  false
                            ),
                            'requiredFieldsAlert'=>true,
                            'fields'=>array(
                                'act'           =>  array('type'=>'hidden', 'value'=>'send', 'htmlOptions'=>array()),
                                'doctor_image[]'=> array('type'=>'file', 'title'=>'', 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>'', 'htmlOptions'=>array('multiple'=>'multiple', 'id'=>'doctor_image')),
                            ),
                            'buttons'=>array(
                               'submit'=>array('type'=>'submit', 'value'=>A::te('appointments', 'Start Upload'), 'htmlOptions'=>array('name'=>'', 'class'=>'button')),
                               'reset' =>array('type'=>'reset', 'value'=>A::te('appointments', 'Reset'), 'htmlOptions'=>array('class'=>'button white')),
                               'cancel'=>array('type'=>'button', 'value'=>A::te('appointments', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white', 'onclick'=>"$(location).attr('href','doctorImages/myImages')")),
                            ),
                            'buttonsPosition'=>'bottom',
                            'return'=>true
                        ));
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
A::app()->getClientScript()->registerScript(
    'autoportalMultiUpload',
    '$(document).ready(function(){
        $("input:submit").click(function(){
            if(parseInt($("#doctor_image").get(0).files.length) > '.(int)$maxImages.'){
                alert("'.A::te('appointments', 'You can only upload a maximum of {count} files!', array('{count}'=>(int)$maxImages)).'");
                return false;
            }
            $(this).val("'.A::te("appointments", "Uploading...").'");
            $(this).closest("form").submit();
            $(this).attr("disabled", true);
        });
    });
    ',
    4
);
