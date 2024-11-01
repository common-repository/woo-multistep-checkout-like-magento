jQuery(document).ready(function(){   
    checkoutForm(wmclm_wizard);
    jQuery( ".phoe_multistep_main_data" ).removeClass( "phoen_multi_step_hor_view" );  
    jQuery("form.checkout .validate-required :input").attr("required", "required");          
    jQuery('.phoen_msc_0').addClass('show');
    jQuery('.slider_0').addClass('phoen_slider_active');
    var i = 0;
    jQuery('.pmsc_tabs .phoen_checkout_page_button').each(function(){
        jQuery(this).attr("data-step",i)
        i++;
    });
    jQuery('.phoen_button_next').click(function(){
        var step = jQuery(this).data('step');
        if(jQuery(this).prev().find(".woocommerce-billing-fields").length > 0)
            {
            var goahead = nextStep(this,"billing",wmclm_wizard);
            if(goahead == "false"){
                return false;
            }
            var $article1 = jQuery('.phoen_checkout_page_button[data-step="' + step + '"]');
            if ($article1.length) {
            jQuery('html, body').animate({
            scrollTop: $article1.offset().top
            })
            }
            
        }
        
        //shipping    
        jQuery('#ship-to-different-address-checkbox').change(function(){ 

            var phoen_checked_value=jQuery('#ship-to-different-address-checkbox').attr('checked')

            if(phoen_checked_value == 'checked') {
                jQuery(".shipping_address").addClass("phoen_checked");
                //                callZipfunc("shipping");
            } else {

                jQuery(".shipping_address").removeClass("phoen_checked");
            } 

        }); 
        var goahead = nextStep(this,"shipping",wmclm_wizard);
        if(goahead == "false"){
            return false;
        }
        var $article2 = jQuery('.phoen_checkout_page_button[data-step="' + step + '"]');
        if ($article2.length) {
            jQuery('html, body').animate({
                scrollTop: $article2.offset().top
            })
        }
        successFun(this);
        var target = jQuery('.pmsc_tabs .phoen_checkout_page_button[data-step="' + step + '"]'),
        offset =  300

        jQuery('html, body').animate({
            scrollTop: jQuery(target).offset().top - offset
        }, 800);
    });
    jQuery('.phoen_button_prev').click(function(){

        jQuery('.phoen_checkout_page_button').removeClass('phoen_slider_active');

        var step = jQuery(this).data('step');

        jQuery('.phoe_checkout_page_slider').removeClass('show');
        jQuery('.phoen_msc_'+step).addClass('show');
        jQuery('.slider_'+step).addClass('phoen_slider_active');

        jQuery(this).parent().slideUp();
        jQuery('.phoen_msc_'+step).slideDown();

        var $article = jQuery('.phoen_checkout_page_button[data-step="' + step + '"]');
        if ($article.length) {
            jQuery('html, body').animate({
                scrollTop: $article.offset().top
            })
        }
    });

    var coupan_html = jQuery('.pmsc_coupan_form').html();

    jQuery('.phoen_msc_3').prepend(coupan_html);

});
function callZipfunc(type){
    if((jQuery("#"+type+"_country").val() != "") || (jQuery("#"+type+"_state").val() != ""))
        if (jQuery("#"+type+"_postcode").closest(".phoe_checkout_page_slider").hasClass("show")) {
        checkZipCode(type);            
    }
}
function checkoutForm(wmclm_wizard){
    if(jQuery(".phoen_checkout_page_button").length > 0 ){
        var counter = 0;
        var totalTab = jQuery(".phoen_checkout_page_button").length;
        jQuery(".phoe_checkout_page_slider").hide();
        jQuery(".pmsc_tabs .phoe_checkout_page_slider:first").slideDown();
        jQuery(".phoen_checkout_page_button").each(function(){
            jQuery(this).addClass("slider_"+counter);
            jQuery(this).next(".phoe_checkout_page_slider").addClass("phoen_msc_"+counter);
            jQuery(this).next(".phoe_checkout_page_slider").find(".ui-tabs-panel").attr("id","pmsc_"+counter);
            if(counter != 0){
                var prev = counter - 1;
                jQuery('<a href="javascript:void(0);" class="phoen_button_prev button">'+wmclm_wizard.previous+'</a>').insertAfter( jQuery(this).next(".phoe_checkout_page_slider").find(".ui-tabs-panel") );
                jQuery(this).next(".phoe_checkout_page_slider").find(".phoen_button_prev").data("step",prev);  
            }
            if(counter != (totalTab - 1)){
                var next = counter + 1; 
                var nxtxt = wmclm_wizard.next;
                if(jQuery(this).hasClass("login_sec")){
                    nxtxt = wmclm_wizard.no_account_btn;
                }
                jQuery('<a href="javascript:void(0);" class="phoen_button_next button">'+nxtxt+'</a>').insertAfter( jQuery(this).next(".phoe_checkout_page_slider").find(".ui-tabs-panel") );
                jQuery(this).next(".phoe_checkout_page_slider").find(".phoen_button_next").data("step",next);  
            }         
            counter = counter + 1;
        });
    }
}
function nextStep(obj,type,wmclm_wizard){
    jQuery(obj).addClass("loading");
    if ((wmclm_wizard.zipcode_validation == 'true')) {
        callZipfunc(type);
    }
    var retval;
    var form_valid = false;
    if (jQuery('#phoen_checkout_validate_form').valid()) {
        form_valid = true;
    }
    jQuery("#phoen_checkout_validate_form").validate().settings.ignore = ":disabled,:hidden";
    var valid_postcodes = jQuery("#"+type+"_postcode").parent().hasClass("woocommerce-invalid-required-field");
    if(form_valid == false){
        retval = "false";
    }else if(valid_postcodes !== true){
        successFun(obj);
        retval = "true";
    }else{
        retval = "false";
    }
    jQuery(obj).removeClass("loading");
    return retval;
}
function successFun(obj){
    jQuery('.phoen_checkout_page_button').removeClass('phoen_slider_active');
    var step = jQuery(obj).data('step');
    jQuery('.phoe_checkout_page_slider').removeClass('show');
    jQuery(obj).parent().slideUp();
    jQuery('.phoen_msc_'+step).addClass('show');
    jQuery('.phoen_msc_'+step).slideDown();
    jQuery('.slider_'+step).addClass('phoen_slider_active');
}