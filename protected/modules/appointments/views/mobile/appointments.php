<!-- Appointment -->
<div class="appointment">
    <div class="container back-arrow">
        <h4><a href="mobile/"> < <?= A::t('appointments', 'Home'); ?></a></h4>
    </div>
    <div class="container">
        <?= $drawAppointmentsBlock; ?>
    </div>
    <div class="container">
        <?= $actionMessage; ?>
    </div>
	<div class="clearfix"> </div>
</div>

<?php
A::app()->getClientScript()->registerScript(
    'loading',
    '$(document).ready(function() {
        $("#find_doctors").on("click", function(){
            if($(this).hasClass("loading")) return false;
            var existImage = $("img").hasClass("ml10");
            if(existImage == false){
                $("#find_doctors").after(\'<img class="ml10" src="templates/mobile/images/ajax_loading.gif" alt="loading" />\');
                $(this).addClass("loading");
            }
        });
    });',
    2
);
