<div class="contact w3-agileits">
    <div class="container back-arrow">
        <h4><a href="mobile/"> < <?= A::t('appointments', 'Home'); ?></a></h4>
    </div>
    <div class="container">
        <h4 class="tittle-w3layouts"><?= A::t('appointments', 'Contact Us'); ?></h4>
        <div class="contact-w3lsrow">
            <?= str_ireplace(array('[iframe', '][/iframe]'), array('<iframe', '></iframe>'), $text); ?>
        </div>
    </div>
</div>