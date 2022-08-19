<?php
    $this->_pageTitle = A::t('appointments', 'Restore Password');
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('appointments', 'Doctor Login'), 'url'=>'doctors/login'),
        array('label' => A::t('appointments', 'Restore Password'))
    );

    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
                <?php
                    echo CHtml::tag('p', array(), A::t('appointments', 'Password recovery instructions'));
                    echo $actionMessage;

                    echo CHtml::openForm('doctors/restorePassword', 'post', array('name'=>'restore-form', 'id'=>'restore-form', 'class'=>'doctor-form')) ;
                    echo CHtml::hiddenField('act', 'send');

                    echo '<div class="row">';
                    echo CHtml::tag('label', array(), A::t('appointments', 'Email').': ');
                    echo CHtml::textField('email', '', array('maxlength'=>'100', 'autocomplete'=>'off'));
                    echo CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'emailErrorEmpty'), A::t('appointments', 'The field email cannot be empty!'));
                    echo CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'emailErrorValid'), A::t('appointments', 'You must provide a valid email address!'));
                    echo '</div>';

                    echo '<div class="row row-button">';
                    echo CHtml::tag('button', array('type'=>'button', 'class'=>'button', 'data-sending'=>A::t('appointments', 'Sending...'), 'onclick'=>'javascript:appointments_RestorePasswordForm(this)'), A::t('appointments', 'Get New Password'));
                    echo '</div>';

                    echo CHtml::closeForm();
                ?>
                </div>
            </div>
        </div>
    </div>
</section>
