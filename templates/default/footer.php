<?php
use \Modules\Appointments\Components\AppointmentsComponent;
?>

<!-- _________________________ Start Footer _________________________ -->
<footer id="footer" role="contentinfo">
    <span class="copyright">
        <?= $this->siteFooter; ?>
    </span>
    <ul id="footer_nav" class="footer_nav">
        <li class="menu-item"><a href="<?= Website::getDefaultPage(); ?>"><?= A::t('appointments', 'Home')?></a></li>
        <?php if(Modules::model()->isInstalled('appointments')): ?>
            <li class="menu-item"><a href="news/viewAll"><?= A::t('news', 'News'); ?></a></li>
        <?php endif; ?>
        <li class="menu-item"><a href="<?= Website::prepareLinkByFormat('cms', 'page_link_format', 2, A::t('appointments', 'About Us')); ?>"><?= A::t('appointments', 'About Us'); ?></a></li>
        <li class="menu-item"><a href="<?= Website::prepareLinkByFormat('cms', 'page_link_format', 3, A::t('appointments', 'Contact Us')); ?>"><?= A::t('appointments', 'Contact Us'); ?></a></li>
        <li class="menu-item"><a href="<?= Website::prepareLinkByFormat('cms', 'page_link_format', 5, A::t('appointments', 'Privacy Policy')); ?>"><?= A::t('appointments', 'Privacy Policy'); ?></a></li>
        <li class="menu-item"><a href="<?= Website::prepareLinkByFormat('cms', 'page_link_format', 6, A::t('appointments', 'Terms & Conditions')); ?>"><?= A::t('appointments', 'Terms & Conditions'); ?></a></li>
    </ul>
    <?php if(Modules::model()->isInstalled('appointments')): ?>
        <div class="clear"></div>
        <div class="center">
            <?= AppointmentsComponent::drawFooterLinks(); ?>
        </div>
    <?php endif; ?>
</footer>
<!-- _________________________ Finish Footer _________________________ -->
