<?php
	Website::setMetaTags(array('title'=>A::t('news', 'Subscribe to news')));
    //A::app()->getClientScript()->registerCssFile('assets/modules/news/css/news.css');
?>

<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder login">
                    <?php if(isset($alertType) && $alertType == 'success'): echo $actionMessage; ?>
                    <?php else: ?>
                        <?= !empty($actionMessage) ? $actionMessage : '<p class="alert alert-info">'.A::t('news', 'View Message Subscribe').'</p>'; ?>
                        <?php
                        // Open form
                        $formName = 'frmNewsSubscribe';
                        echo CHtml::openForm('newsSubscribers/subscribe', 'post', array('id' => 'subscribe-form', 'name'=>$formName, 'autoGenerateId'=>true, 'class'=>'subscribe form-horizontal'));
                        ?>
                        <input type="hidden" name="act" value="send" />
                        <?php if('no' == $typeFullName && 'no' != $typeFirstName){ ?>
                            <div class="form-group col-sm-12">
                                <label for="news_subscribers_first_name" class="col-sm-2 control-label"><?= A::t('news', 'First Name'); ?>
                                    <?php if($typeFirstName == 'allow-required'){ ?>
                                        <span class="required">*</span>
                                    <?php } ?>
                                    : </label>
                                <input id="news_subscribers_first_name" type="text" maxLength="32" value="<?= CHtml::encode($firstName); ?>" name="first_name", autocomplete="off"  class="form-control" />
                            </div>
                        <?php } ?>
                        <?php if('no' == $typeFullName && 'no' != $typeLastName){ ?>
                            <div class="form-group col-sm-12">
                                <label for="news_subscribers_last_name" class="col-sm-2 control-label"><?= A::t('news', 'Last Name'); ?>
                                    <?php if($typeLastName == 'allow-required'){ ?>
                                        <span class="required">*</span>
                                    <?php } ?>
                                    : </label>
                                <input id="news_subscribers_last_name" type="text" maxLength="32" value="<?= CHtml::encode($lastName); ?>" name="last_name", autocomplete="off"  class="form-control" />
                            </div>
                        <?php } ?>
                        <?php if('no' != $typeFullName){ ?>
                            <div class="form-group col-sm-12">
                                <label for="news_subscribers_full_name" class="col-sm-2 control-label"><?= A::t('news', 'Full Name'); ?>
                                    <?php if($typeLastName == 'allow-required'){ ?>
                                        <span class="required">*</span>
                                    <?php } ?>
                                    : </label>
                                <input id="news_subscribers_full_name" type="text" maxLength="64" value="<?= CHtml::encode($fullName); ?>" name="full_name", autocomplete="off"  class="form-control" />
                            </div>
                        <?php } ?>
                        <div class="form-group col-sm-12">
                            <label for="news_subscribe_email" class="col-sm-2 control-label"><?= A::t('news', 'Email'); ?> <span class="required">*</span> : </label>
                            <input id="news_subscribe_email" type="text" maxLength="128" value="<?= CHtml::encode($email); ?>" name="email" autocomplete="off"  class="form-control" />
                        </div>
                        <div class="buttons-wrapper">
                            <input value="<?= A::te('news', 'Subscribe'); ?>" class="button" type="submit" style="" />
                            <input onclick="window.location='newsSubscribers/unsubscribe'" class="button" value="<?= A::te('news', 'Unsubscribe'); ?>" type="button" name="ap0" />
                        </div>
                        <?= CHtml::closeForm(); ?>


                        <?php
                        if(!empty($errorField)){
                            A::app()->getClientScript()->registerScript($formName, 'document.forms["'.$formName.'"].'.$errorField.'.focus();', 2);
                        }
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
