<?php
	Website::setMetaTags(array('title'=>A::t('news', 'Unsubscribe from Newsletter')));
	//A::app()->getClientScript()->registerCssFile('assets/modules/news/css/news.css');
?>
<section id="content" role="main">
    <div class="entry">
        <div class="cmsms_cc">
            <div class="one_first first_column">
                <div class="cmsms-form-builder login">
                <?php if(isset($alertType) && $alertType == 'success'): echo $actionMessage; ?>
                <?php else: ?>
                    <?= $actionMessage; ?>
                    <p id="messageInfo"><?= A::t('news', 'Are you sure you want to unsubscribe') ?></p>
                    <?php
                    // Open form
                    $formName = 'frmNewsUnsubscribe';
                    echo CHtml::openForm(
                        'newsSubscribers/unsubscribe',
                        'post',
                        array('id' => 'subscribe-form', 'name'=>$formName, 'autoGenerateId'=>true, 'class' => 'unsubscribe')
                    );
                    ?>
                    <input type="hidden" name="act" value="send" />
                    <div class="row">
                        <label for="news_unsubscribe_email"><?= A::t('news', 'Email'); ?>: </label>
                        <input id="news_unsubscribe_email"<?= ($errorField == 'email') ? ' autofocus="autofocus"' : ''; ?> type="text" maxLength="128" value="<?= CHtml::encode($email); ?>" name="email" autocomplete="off"  class="large" />
                    </div>
                    <div class="clear"></div>
                    <div class="buttons-wrapper">
                        <input value="<?= A::te('news', 'Unsubscribe'); ?>" type="submit" />
                    </div>
                    <?= CHtml::closeForm(); ?>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

