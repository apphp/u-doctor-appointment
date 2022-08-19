<?php ///A::app()->getClientScript()->registerCssFile('assets/modules/news/css/news.css'); ?>

<div class='side-panel-block'>
    <h3 class='title'><?= A::t('news', 'Subscription') ?></h3>
    <?php
        $formName = 'frmNewsSubscribeBlock';
        $i = 0;
        echo CHtml::openForm('newsSubscribers/subscribe', 'post', array('name' => $formName, 'id' => 'subscription-side-form', 'autGenerateId' => true));
        ?>
        <input id="frmNewsSubscribeBlock_APPHP_FORM_ACT" type="hidden" value="send" name="APPHP_FORM_ACT" />

        <div class="row" id="frmNewsSubscribeBlock_row_<?= $i++?>">
            <label for="news_subscribers_email"><?= A::t('news', 'Sign up for our newsletter'); ?>:</label>
            <input maxLength="128" placeholder="<?= A::t('news', 'Enter your email address');?>" id="news_subscribers_email" type="text" value="" name="email" />
        </div>
    <?php
if('no' == $typeFullName):
    if('allow-required' == $typeFirstName):
    ?>
        <div class="row" id="frmNewsSubscribeBlock_row_<?= $i++?>">
            <label for="news_subscribers_last_name"><?= A::t('news', 'First Name'); ?>:</label>
            <input maxLength="32" placeholder="" id="news_subscribers_first_name" type="text" value="" name="first_name" />
        </div>
    <?php
    endif;
    if('allow-required' == $typeLastName):
    ?>
        <div class="row" id="frmNewsSubscribeBlock_row_<?= $i++?>">
            <label for="news_subscribers_last_name"><?= A::t('news', 'Last Name'); ?>:</label>
            <input maxLength="32" placeholder="" id="news_subscribers_last_name" type="text" value="" name="last_name" />
        </div>
    <?php
    endif;
elseif('allow-required' == $typeFullName):
    ?>
        <div class="row" id="frmNewsSubscribeBlock_row_<?= $i++?>">
            <label for="news_subscribers_last_name"><?= A::t('news', 'Full Name'); ?>:</label>
            <input maxLength="64" placeholder="" id="news_subscribers_full_name" type="text" value="" name="full_name" />
        </div>
<?php
endif;
?>
        <input id="frmNewsSubscribeBlock_email_send" type="hidden" value="send" name="act" />

        <div class="buttons-wrapper bw-bottom">
            <input name="" value="<?= A::t('news', 'Subscribe'); ?>" type="submit" />
        </div>
        <?php
        echo CHtml::closeForm();
    ?>
</div>
