jQuery(document).ready(function() {
    jQuery(".widgettitle").on("click", function(){
        var idEl = "#" + jQuery(this).data("id");
        var hideFooter = jQuery(idEl).hasClass("hide-footer");
        if(hideFooter == false){
            jQuery(idEl).addClass("hide-footer");
        }else{
            jQuery(idEl).removeClass("hide-footer");
        }
    });
    $('#create-patient').on('click', function(){
        $('#create-patient-modal').dialog({height:600,maxHeight:1500,width:450,maxWidth:600});
    });
});