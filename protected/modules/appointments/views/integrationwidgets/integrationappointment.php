<?php
header('content-type: text/html; charset=utf-8');
$direction = A::app()->getLanguage('direction');
?>

<!DOCTYPE html>
<html<?= ($direction == 'rtl') ? ' dir="RTL"' : ''; ?>>
<head>
    <meta content="text/html;charset=utf-8" http-equiv="content-type" />
    <meta name="keywords1" content="<?= CHtml::encode($this->_pageKeywords); ?>" />
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
<section id="middle" class="services_page">
    <div class="margin-top-10"></div>
    <div class="">
        <?= $drawAppointmentsBlock; ?>
    </div>
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
    echo '<script type="text/javascript">
                /*<![CDATA[*/
                jQuery(document).ready(function(){
                        var $ = jQuery;
                        $(".link-find-doctor-by-specialty").click(function(){
                            var id = $(this).data("id");
                            $("#find_doctors_form_specialty_id").val(id);
                            sendForm("find_doctors_form");
                        });
                    });
                    function sendForm(frmNameId){
                        jQuery("#"+frmNameId).submit();
                    }
                    
                jQuery(function($){
                jQuery("#appointments_form_doctor_name").autocomplete({
                    source: function(request, response){
                        $.ajax({
                            url: "doctors/ajaxGetDoctorNames",
                            global: false,
                            type: "POST",
                            data: ({
                                APPHP_CSRF_TOKEN: "c2185922654256ca393dfe5d1a941685",
                                act: "send",
                                search : jQuery("#appointments_form_doctor_name").val(),
                            }),
                            dataType: "json",
                            async: true,
                            error: function(html){
                                
                            },
                            success: function(data){
                                if(data.length == 0){
                                    jQuery("#appointments_form_doctor_id").val("");
                                    response({label: "No matches found"});
                                }else{
                                    response($.map(data, function(item){
                                        if(item.label !== undefined){
                                            return {id: item.id, label: item.label, spec: item.spec}
                                        }else{
                                            // Empty search value if nothing found
                                            jQuery("#appointments_form_doctor_id").val("");
                                        }
                                    }));
                                }
                            }
                        });
                    },
                    minLength: 3,
                    select: function(event, ui) {
                        jQuery("#appointments_form_location_id").val("");
                        jQuery("#appointments_form_location").val("");
                        jQuery("#appointments_form_specialty").val(ui.item.spec);
                        jQuery("#appointments_form_doctor_id").val(ui.item.id);
                        if(typeof(ui.item.id) == "undefined"){
                            jQuery("#appointments_form_doctor_name").val("");
                            return false;
                        }
                    }
                });
                jQuery("#appointments_form_location").autocomplete({
                    source: function(request, response){
                        $.ajax({
                            url: "clinics/ajaxGetClinicNames",
                            global: false,
                            type: "POST",
                            data: ({
                                APPHP_CSRF_TOKEN: "c2185922654256ca393dfe5d1a941685",
                                act: "send",
                                search : jQuery("#appointments_form_location").val(),
                            }),
                            dataType: "json",
                            async: true,
                            error: function(html){
                                
                            },
                            success: function(data){
                                if(data.length == 0){
                                    jQuery("#appointments_form_location_id").val("");
                                    response({label: "No matches found"});
                                }else{
                                    response($.map(data, function(item){
                                        if(item.label !== undefined){
                                            return {id: item.id, label: item.label}
                                        }else{
                                            // Empty search value if nothing found
                                            jQuery("#appointments_form_location_id").val("");
                                        }
                                    }));
                                }
                            }
                        });
                    },
                    minLength: 2,
                    select: function(event, ui) {
                        jQuery("#appointments_form_specialty").val("");
                        jQuery("#appointments_form_doctor_id").val("");
                        jQuery("#appointments_form_doctor_name").val("");
                        jQuery("#appointments_form_location_id").val(ui.item.id);
                        if(typeof(ui.item.id) == "undefined"){
                            jQuery("#appointments_form_location").val("");
                            return false;
                        }
                    }
                });
                $(function() {
                    $("#appointments_form_specialty").on("change", function() {
                        $("#appointments_form_doctor_name").val("");
                        $("#appointments_form_doctor_id").val("");
                        $("#appointments_form_location").val("");
                        $("#appointments_form_location_id").val("");
                    })
                });
                });
                jQuery(window).load(function(){
                jQuery(".form-search").each(function(){
                var self = jQuery(this);
                self.find(".btn").click(function(){
                var keywords = self.find(\'input[name="keywords"]\').val();
                if(keywords == ""){						
                    return false;	
                }
                });
                });
                });
                /*]]>*/
                </script>';
endif;
?>
</body>
</html>