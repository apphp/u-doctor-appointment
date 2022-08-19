<?php 
    $this->_pageTitle = A::t('appointments', 'Remove Account');
    $this->_activeMenu = 'patients/myAccount';
    $this->_breadCrumbs = array(
        array('label'=> A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label' => A::t('appointments', 'My Account'), 'url'=>'patients/myAccount'),
        array('label' => A::t('appointments', 'Remove Account'))
    );
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder">
                <?php
                    echo $actionMessage;
                    
                    if($accountRemoved){
                        echo '<script type="text/javascript">setTimeout(function(){window.location.href = "patients/logout";}, 5000);</script>';
                    }else{
                        echo CHtml::openForm('patients/removeAccount', 'post', array('name'=>'remove-account-form', 'id'=>'remove-account-form', 'class'=>'patient-form')) ;
                        echo CHtml::hiddenField('act', 'send');
                        echo CHtml::tag('p', array(), A::t('appointments', 'Account removal notice'));
                        echo CHtml::openTag('div', array('class'=>'row row-button buttons-wrapper'));
                        echo CHtml::tag('button', array('type'=>'submit', 'class'=>'button'), A::t('appointments', 'Remove'));
                        echo CHtml::link(A::t('appointments', 'Cancel'), 'patients/myAccount', array('class'=>'button white'));
                        echo CHtml::closeTag('div');
                        echo CHtml::closeForm();
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</section>
