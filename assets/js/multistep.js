jQuery(document).ready(function($) {

    var pmsc_div_show = '';

    jQuery( ".pmsc_tabs " ).each( function( index, element ){

        pmsc_div_show = jQuery(this).find('li.selected').attr('data-step');

    });

    jQuery('#pmsc_'+pmsc_div_show).show();
    
    jQuery('.login_form form.login').submit(function (evt)
    {
        if (wmclm_wizard.include_login != "false") {

            evt.preventDefault();
            var form = 'form.login';
            var error = false;

            if (jQuery(form + ' input#username').val() == false) {
                error = true;
                addRequiredClasses('username');
            }

            if (jQuery(form + ' input#password').val() == false) {
                error = true;
                addRequiredClasses('password');
            }

            if (error != false)
                {
                return false;
            }

            var formSelector = this;

            if (jQuery(form + ' input#rememberme').is(':checked') == false) {
                rememberme = false;
            } else {
                rememberme = true;
            }

            jQuery(".login_form ").block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            var data = {
                action: 'wmclm_check_user_login',
                username: jQuery(form + ' input#username').val(),
                password: jQuery(form + ' input#password').val(),
                rememberme: rememberme,
                _ajax_nonce: wmclm_wizard.login_nonce
            };

            jQuery.post(wmclm_wizard.ajaxurl, data, function (response) {
                jQuery(".login_form").unblock();
                if (response == 'successfully') {
                    location.reload();
                } else {
                    if (!$("form.login > .error-msg").length) {
                        jQuery('form.login').prepend(response);
                    }
                }
            })
        }
    });
});
jQuery(window).load(function(){
    jQuery(".woocommerce-info a.showcoupon").parent().detach();
    jQuery("form.checkout_coupon").appendTo('.coupon_form');
    jQuery("form.checkout_coupon").show();   
});
function addRequiredClasses(selector)
{
    jQuery('form.login input#' + selector).parent().removeClass("woocommerce-validated");
    jQuery('form.login input#' + selector).parent().addClass("woocommerce-invalid woocommerce-invalid-required-field");
    jQuery('form.login input#' + selector).parent().addClass("validate-required");
}
function checkZipCode(type)
{
    result = jQuery(".form-row#" + type + "_postcode_field").length > 0 && jQuery("#" + type + "_postcode").val() != false
    && jQuery("#" + type + "_country").length > 0 && jQuery("#" + type + "_country").val() != false;

    if (result) {

        var data = {
            action: 'check_zip_code',
            country: jQuery("#" + type + "_country").val(),
            postCode: jQuery("#" + type + "_postcode").val()
        };
        jQuery.ajax({
            url: wmclm_wizard.ajaxurl, 
            type: 'POST', //I want a type as POST
            data: data, 
            async : false,
            success: function(response){ 
            if (response == '') {
                jQuery("#" + type + "_postcode").parent().addClass("woocommerce-invalid woocommerce-invalid-required-field");
                alert("Zip Code is Not Valid");
                return false;
            } else {
                jQuery("#" + type + "_postcode").parent().removeClass("woocommerce-invalid woocommerce-invalid-required-field");
                return true;
            } 
            }
        });
    }
}
