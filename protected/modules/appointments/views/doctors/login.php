<?php
    $this->_pageTitle = A::t('appointments', 'Doctor Login');
    $this->_breadCrumbs = array(
        array('label' => A::t('appointments', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label' => A::t('appointments', 'Doctor Login'))
    );

    A::app()->getClientScript()->registerScriptFile('assets/modules/appointments/js/appointments.js', 2);
?>
    <section id="content" role="main">
        <div class="entry">
            <div class="cmsms_cc">
                <div class="one_first first_column">
                    <div class="cmsms-form-builder login">
                    <?php
                        if(A::app()->getCookie()->get('doctorLoginAttemptsAuth') != ''){
                            echo CWidget::create('CMessage', array('info', A::t('appointments', 'Please confirm you are a human by clicking the button below!')));
                            echo CWidget::create('CFormView', array(
                                'action'=>'doctors/login',
                                'method'=>'post',
                                'htmlOptions'=>array(
                                    'name'=>'frmLogin',
                                    'id'=>'frmLogin',
                                    'class'=>'doctor-form'
                                ),
                                'fields'=>array(
                                    'doctorLoginAttemptsAuth' =>array('type'=>'hidden', 'value'=>A::app()->getCookie()->get('doctorLoginAttemptsAuth')),
                                ),
                                'buttons'=>array(
                                    'submit'=>array('type'=>'submit', 'value'=>A::t('appointments', 'Confirm'), 'htmlOptions'=>array('class'=>'button')),
                                ),
                                'return'=>true,
                            ));
                        }else{
                            echo $actionMessage;

                            echo CHtml::openForm('doctors/login', 'post', array('onsubmit'=>'return doctors_LoginForm(this)', 'id'=>'frmDoctorLogin', 'class'=>'doctor-form')).PHP_EOL;
                            echo CHtml::hiddenField('act', 'send').PHP_EOL;

                            echo '<div class="form_info cmsms_input">'.PHP_EOL;
                            echo '<label>'.A::t('appointments', 'Username').':</label>'.PHP_EOL;
                            echo '<div class="form_field_wrap">'.PHP_EOL;
                            echo '<input id="login_username" type="text" autofocus name="login_username" value="" maxlength="25" data-required="true" autocomplete="off" />'.PHP_EOL;
                            echo CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'usernameErrorEmpty'), A::t('appointments', 'Username field cannot be empty!')).PHP_EOL;
                            echo '</div>'.PHP_EOL;
                            echo '</div>'.PHP_EOL;

                            echo '<div class="form_info cmsms_input">'.PHP_EOL;
                            echo '<label>'.A::t('appointments', 'Password').':</label>'.PHP_EOL;
                            echo '<div class="form_field_wrap">'.PHP_EOL;
                            echo '<input id="login_password" type="password" name="login_password" value="" maxlength="25" data-required="true" autocomplete="off" />'.PHP_EOL;
                            echo CHtml::tag('p', array('class'=>'error', 'style'=>'display:none', 'id'=>'passwordErrorEmpty'), A::t('appointments', 'Password field cannot be empty!')).PHP_EOL;
                            echo '</div>'.PHP_EOL;
                            echo '</div>'.PHP_EOL;

                            if($allowRememberMe){
                                echo '<div class="form_info cmsms_input">'.PHP_EOL;
                                echo '<input id="remember" type="checkbox" name="remember" /> <label for="remember" class="remember">'.A::t('appointments', 'Remember Me').'</label><br/>'.PHP_EOL;
                                echo '</div>'.PHP_EOL;
                            }

                            echo '<input type="submit" value="'.A::t('appointments', 'Login').'" class="button" />'.PHP_EOL;

                            echo '<div class="row">'.PHP_EOL;
                            if(!empty($allowRegistration)){
                                echo '<a href=doctors/registration>'.A::t('appointments', 'Create account').'</a><br/>'.PHP_EOL;
                            }
                            if(!empty($allowResetPassword)){
                                echo '<a href="doctors/restorePassword">'.A::t('appointments', 'Forgot your password?').'</a>'.PHP_EOL;
                            }
                            if(!empty($buttons)){
                                echo '<div class="row">'.PHP_EOL;
                                echo $buttons.PHP_EOL;
                                echo '</div>'.PHP_EOL;
                            }
                            echo '</div>'.PHP_EOL;

                            echo CHtml::closeForm().PHP_EOL;
                        }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
