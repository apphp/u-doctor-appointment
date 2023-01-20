<div class="who-we-w3ls">
<div class="container about">
    <h2 class="error-title"><?= A::t('app', 'Error 404 Title'); ?></h2>
    <div class="error-description">
		<?= A::t('app', 'Error 404 Description'); ?>
        <br><br>
		<?= A::t('app', 'Error 404 Troubleshooting'); ?>
	
		<?= (APPHP_MODE == 'demo' ? $text : '' ); ?>
    </div>
</div>
</div>