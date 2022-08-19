/**
 * Validates form fields and submit the form
 */

function review_SubmitForm(el)
{
    if(el == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;

    var token = $(el).closest('form').find('input[name=APPHP_CSRF_TOKEN]').val();

    var ratingPrice = $('#rating_price').val();
    var ratingWaitTime = $('#rating_wait_time').val();
    var ratingBedsideManner = $('#rating_bedside_manner').val();

    var message = $.trim($('#message').val());
    var captcha = $('#captcha').val();
    var createdAt = $(el).data("createdAt");
    var patientName = $(el).data("patientName");
    var reviewModeration = $(el).data("reviewModeration");
    var doctorId = $('#doctor_id').val();
    var appointmentId = $('#appointment_id').val();

    var ratingPriceLabel = $('#rating_price').data("label");
    var ratingWaitTimeLabel = $('#rating_wait_time').data("label");
    var ratingBedsideMannerLabel = $('#rating_bedside_manner').data("label");

    if($(el).attr('disabled')){
        return false;
    }

    $(el).attr('disabled', 'disabled');

    $.ajax({
        url: 'doctorReviews/add',
        global: false,
        type: 'POST',
        data: ({
            APPHP_CSRF_TOKEN: token,
            message: message,
            ratingPrice: ratingPrice,
            ratingWaitTime: ratingWaitTime,
            ratingBedsideManner: ratingBedsideManner,
            doctorId: doctorId,
            appointmentId: appointmentId,
            captcha: captcha
        }),
        dataType: 'html',
        async: true,
        error: function (html) {
            $('#messageError').show();
        },
        success: function (html) {
            try {
                var obj = $.parseJSON(html);
                if (obj.status == '1') {
                    $('#rating_price').val('');
                    $('#rating_wait_time').val('');
                    $('#rating_bedside_manner').val('');
                    $('#message').val('');
                    $('#doctor_id').val('');
                    $('#appointment_id').val('');
                    $('#frmDoctorReviewsAdd').slideUp();
                    $('#messageError').hide();
                    $('#messageSuccess').show();
                    scroll_to('#messageSuccess');

                    if (reviewModeration != 1) {
                        var featured_block = $(
                            '<div class="featured_block">' +
                            '<div class="colored_title">' +
                            '<div class="colored_title_inner">' +
                            '<blockquote>' +
                            message +
                            '</blockquote>' +
                            '<div class="one_first">' +
                            '<p class="one_third">' + ratingPriceLabel + ': <img src="templates/default/images/small_star/smallstar-' + ratingPrice + '.png"></p>' +
                            '<p class="one_third">' + ratingWaitTimeLabel + ': <img src="templates/default/images/small_star/smallstar-' + ratingWaitTime + '.png"></p>' +
                            '<p class="one_third">' + ratingBedsideMannerLabel + ': <img src="templates/default/images/small_star/smallstar-' + ratingBedsideManner + '.png"></p>' +
                            '</div>' +
                            '<p>' + patientName + ' • ' + createdAt + '</p>' +
                            '</div>' +
                            '</div>' +
                            '</div>'
                        );
                        $('#draw_review').prepend(featured_block);
                    }

                }else{
                    review_RaiseError(el, obj.error);
                    if(!ratingPrice){
                        $('#rating_price').focus();
                    }else if(!ratingWaitTime){
                        $('#rating_wait_time').focus();
                    }else if(!ratingBedsideManner){
                        $('#rating_bedside_manner').focus();
                    }else if(!message){
                        $('#message').focus();
                    }else if(!captcha){
                        $('#captcha').focus();
                    }
                    scroll_to('#messageError');
                }
            }catch(err){
                review_RaiseError(el);
            }
        }
    });
    // prevent the default form submission occurring
    return false;
}

/**
 * Raise error message
 */
function review_RaiseError(el, errorDescription)
{
    // define this to prevent name overlapping
    var $ = jQuery;

    if(errorDescription !== null) $('#messageErrorText').html(errorDescription);
    $('#messageError').show();

    $(el).removeAttr('disabled');
}

function review_ShowMore(el)
{
    if(el == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;

    var doctorId = $(el).data("doctorId");
    var currentPage = $(el).data("currentPage");
    var ratingPriceLabel = $(el).data("ratingPriceLabel");
    var ratingWaitTimeLabel = $(el).data("ratingWaitTimeLabel");
    var ratingBedsideMannerLabel = $(el).data("ratingBedsideMannerLabel");

    $.ajax({
        url: 'doctorReviews/ajaxShowMoreDoctorReview',
        global: false,
        type: 'GET',
        data: ({
            doctorId: doctorId,
            currentPage: currentPage
        }),
        dataType: 'html',
        async: true,
        error: function(html){

        },
        success: function(html){
            var obj = $.parseJSON(html);
            $('#show_more').data("currentPage", currentPage + 1);
            $.each(obj, function(){
                var featured_block = $(
                    '<div class="featured_block" id="page-'+ $('#show_more').data("currentPage") +'">' +
                    '<div class="colored_title">' +
                    '<div class="colored_title_inner">' +
                    '<blockquote>' +
                    this.message +
                    '</blockquote>' +
                    '<div class="one_first">' +
                    '<p class="one_third">' + ratingPriceLabel + ': <img src="templates/default/images/small_star/smallstar-'+ this.ratingPrice +'.png"></p>' +
                    '<p class="one_third">' + ratingWaitTimeLabel + ': <img src="templates/default/images/small_star/smallstar-'+ this.ratingWaitTime +'.png"></p>' +
                    '<p class="one_third">' + ratingBedsideMannerLabel + ': <img src="templates/default/images/small_star/smallstar-'+ this.ratingBedsideManner +'.png"></p>' +
                    '</div>' +
                    '<p>'+ this.patientName +' • '+ this.createdAt +'</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
                $('#draw_review').append(featured_block);

                if(this.nextPageExist == '0'){
                    $('#show_more').hide();
                    $('#show_more').attr('disabled','disabled');
                }

            });
            scroll_to('#page-'+ $('#show_more').data("currentPage"));

        }
    });

    // prevent the default form submission occurring
    return false;
}

function scroll_to(id_el)
{
    $('html, body').animate({ scrollTop: $(id_el).offset().top-50 }, 500);
    // prevent the default form submission occurring
    return false;
}