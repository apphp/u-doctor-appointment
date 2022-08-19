<?php
    header('content-type: text/html; charset=utf-8');
    $direction = A::app()->getLanguage('direction');
?>
<?php
    $socialNetworks = SocialNetworks::model()->findAll('is_active = 1');
?>
<!DOCTYPE html>
<html<?= ($direction == 'rtl') ? ' dir="RTL"' : ''; ?>>
<head>
    <meta content="text/html;charset=utf-8" http-equiv="content-type" />
    <meta name="keywords" content="<?= CHtml::encode($this->_pageKeywords); ?>" />
    <meta name="description" content="<?= CHtml::encode($this->_pageDescription); ?>" />
    <meta name="generator" content="<?= CConfig::get('name').' v'.CConfig::get('version'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <!-- don't move it -->
    <base href="<?= A::app()->getRequest()->getBaseUrl(); ?>" />
    <title><?= CHtml::encode($this->_pageTitle); ?></title>

    <link rel="shortcut icon" href="templates/default/images/favicon.ico" type="image/x-icon" />

    <?= CHtml::cssFile('templates/default/css/style.css'); ?>
    <?= CHtml::cssFile('templates/default/css/styles/fonts.css'); ?>
    <?= CHtml::cssFile('templates/default/css/styles/adaptive.css'); ?>
    <?= CHtml::cssFile('templates/default/css/fonts/css/fontello.css'); ?>
    <?= CHtml::cssFile('http://fonts.googleapis.com/css?family=Roboto:400,300,400italic,300italic,500,700,500italic'); ?>
    <?= CHtml::cssFile('templates/default/css/styles/jackbox.css'); ?>
    <?= CHtml::cssFile('templates/default/revolution/css/dynamic-captions.css'); ?>
    <?= CHtml::cssFile('templates/default/revolution/css/static-captions.css'); ?>
    <?= CHtml::cssFile('templates/default/revolution/css/settings.css'); ?>
    <?= CHtml::cssFile('assets/vendors/jquery/jquery.modal.min.css'); ?>
    <?= CHtml::cssFile('assets/vendors/jquery/jquery-ui.min.css'); ?>
    <!--[if lt IE 9]>
        <link rel="stylesheet" href="templates/default/css/styles/ie.css" type="text/css" />
    <?= CHtml::cssFile('templates/default/css/styles/ieCss3.css'); ?>
    <![endif]-->
    <?php
        if(Modules::model()->isInstalled('appointments')):
            echo CHtml::cssFile('assets/vendors/toastr/toastr.min.css');
        endif;
    ?>
    <?= CHtml::cssFile('templates/default/css/custom.css'); ?>
    <?= ($direction == 'rtl') ? CHtml::cssFile('templates/default/css/style.rtl.css') : ''; ?>

    <!-- globalDebug option which allows you to display additional information about the script work in the browser console -->
    <?= CHtml::script("var globalDebug = ".(APPHP_MODE == 'debug' ? 'true' : 'false')); ?>

    <!-- jquery files -->
    <?= CHtml::scriptFile('templates/default/js/jquery.min.js'); ?>

    <!-- template files -->
    <?= CHtml::scriptFile('templates/default/js/modernizr.custom.all.min.js'); ?>
</head>
<body class="page">

<div class="login-block">
<?php
    if(CAuth::isLoggedInAsAdmin()):
        echo CHtml::link(A::t('app', 'Back to Admin Panel'), 'backend/index', array('class'=>'back-to'));
    endif;
    if(Modules::model()->isInstalled('appointments')):
        echo \Modules\Appointments\Components\AppointmentsComponent::drawLoginBlock();
    endif;
?>
</div>

<!--  Start Page  -->
<section id="page" class="csstransition cmsms_resp hfeed site">

    <!--  Start Container  -->
    <div class="container">

        <?php include('header.php'); ?>

        <!--  Content  -->
        <?= A::app()->view->getLayoutContent(); ?>
        
        <?php include('bottom.php'); ?>

        <a href="javascript:void(0);" id="slide_top"></a>
    </div>
    <!--  Finish Container  -->

    <?php include('footer.php'); ?>

</section>
<!--  Finish Page  -->

<?= CHtml::scriptFile("templates/default/js/respond.min.js"); ?>
<?= CHtml::scriptFile("templates/default/revolution/js/jquery.themepunch.revolution.js"); ?>
<?= CHtml::scriptFile("templates/default/revolution/js/jquery.themepunch.revolution.min.js"); ?>
<?= CHtml::scriptFile("templates/default/revolution/js/jquery.themepunch.plugins.min.js"); ?>
<?= CHtml::scriptFile('templates/default/js/jquery.easing.min.js?ver=1.3.0'); ?>
<?= CHtml::scriptFile("templates/default/js/jquery.script.js"); ?>
<?= CHtml::scriptFile("templates/default/js/jackbox-lib.js"); ?>
<?= CHtml::scriptFile("templates/default/js/jackbox.js"); ?>
<?= CHtml::scriptFile('assets/vendors/jquery/jquery.modal.min.js'); ?>
<?= CHtml::scriptFile('assets/vendors/jquery/jquery-ui.min.js', 2); ?>
<!-- JavaScript -->

<?php
    if(Modules::model()->isInstalled('appointments')):
        echo CHtml::scriptFile('assets/vendors/toastr/toastr.min.js');
        echo CHtml::scriptFile('assets/modules/appointments/js/appointments.js');
    endif;
?>
</body>
</html>
