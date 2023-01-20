<?php ///A::app()->getClientScript()->registerCssFile('assets/modules/news/css/news.css'); ?>
<h3 class='title widgettitle' data-id="subscription"><?= A::t('news', 'Subscription') ?><span class="pull-right hide-footer">&#x25BE;</span></h3>
<div id="subscription" class="hide-footer">
    <aside class="widget widget_custom_contact_form_entries">
        <div class='side-panel-block'>
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
    </aside>
    <a class="icon-rss" target="_blank" rel="noopener noreferrer" href="feeds/news_rss.xml" title="RSS"> RSS</a>
</div>
