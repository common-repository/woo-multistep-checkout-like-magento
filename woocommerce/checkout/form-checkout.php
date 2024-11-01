<?php
    /**
    * Checkout Form
    *
    * @author         WooThemes
    * @package     WooCommerce/Templates
    * @version     2.3.0
    */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    wc_print_notices();
    do_action( 'woocommerce_before_checkout_form', $checkout );
    // If checkout registration is disabled and not logged in, the user cannot checkout
    if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
        echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
        return;
    }
    $get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() );
?>  
<div class="phoe_multistep_main_data">
    <div class="pmsc_tabs">
        <?php if(get_option("wmclm_add_login_form") == "true"){ //if(!is_user_logged_in()){ ?>
            <div class="phoen_checkout_page_button login_sec phoen_slider_active" style="display:;"><?php echo get_option('wmclm_login_label') ? __(get_option('wmclm_login_label'), 'woocommerce-multistep-checkout-like-magento') : __('Login', 'woocommerce-multistep-checkout-like-magento'); ?></div>
            <div class="phoe_checkout_page_slider"> 
                <div class="login_form ui-tabs-panel">
                    <?php
                    add_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
                        do_action( 'woocommerce_checkout_before_customer_details' );
                    ?>
                    <script>
                        jQuery(function () {
                            jQuery(".woocommerce-info a.showlogin").parent().detach();
                            jQuery("form.login").appendTo('.login_form');
                            jQuery(".login_form form.login").show();
                        });
                    </script>
                </div>
            </div>
            <?php } ?>
        <?php
            if(function_exists("wmclm_add_coupon_form")){
            ?>
            <div class="phoen_checkout_page_button" style="display:;"><?php echo get_option('wmclm_coupon_label') ? __(get_option('wmclm_coupon_label'), 'woocommerce-multistep-checkout-like-magento') : __('Coupon', 'woocommerce-multistep-checkout-like-magento'); ?></div>
            <div class="phoe_checkout_page_slider"> 
                <div class="coupon_form ui-tabs-panel">
                    <?php
                    add_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
                    ?>            
                </div>
            </div>
            <?php
            }
        ?>
        <form name="checkout" method="post" id="phoen_checkout_validate_form" class="checkout woocommerce-checkout" action="<?php echo esc_url($get_checkout_url); ?>" enctype="multipart/form-data">
            <?php if (sizeof($checkout->checkout_fields) > 0) : ?>
                <?php do_action('woocommerce_checkout_before_customer_details'); ?>
                <?php if (WC()->cart->ship_to_billing_address_only() && WC()->cart->needs_shipping()) : ?>
                    <div class="phoen_checkout_page_button">Billing & Shipping</div>
                    <?php else: ?>
                    <div class="phoen_checkout_page_button"><?php echo get_option('wmclm_billing_label') ? __(get_option('wmclm_billing_label'), 'woocommerce-multistep-checkout-like-magento') : __('Billing', 'woocommerce-multistep-checkout-like-magento') ?></div>
                    <?php endif; ?>

                <div class="phoe_checkout_page_slider"> 
                    <div class="col-1_billing ui-tabs-panel" id="">
                        <?php
                            do_action( 'woocommerce_checkout_billing' );
                            //If cart don't needs shipping
                            if (!WC()->cart->needs_shipping_address()) :
                                do_action('woocommerce_checkout_after_customer_details');

                                do_action('woocommerce_before_order_notes', $checkout);
                                if (apply_filters('woocommerce_enable_order_notes_field', get_option('woocommerce_enable_order_comments', 'yes') === 'yes')) :

                                    if (!WC()->cart->needs_shipping() || WC()->cart->ship_to_billing_address_only()) :
                                    ?>

                                    <h3><?php _e('Additional Information', 'woocommerce'); ?></h3>

                                    <?php endif; ?>

                                <?php foreach ($checkout->checkout_fields['order'] as $key => $field) : ?>

                                    <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>

                                    <?php endforeach; ?>

                                <?php endif; ?>
                            <?php do_action('woocommerce_after_order_notes', $checkout); ?>
                            <?php endif; ?>
                    </div>
                </div>
                <div class="phoen_checkout_page_button"><?php echo get_option('wmclm_shipping_label') ? __(get_option('wmclm_shipping_label'), 'woocommerce-multistep-checkout-like-magento') : __('Shipping', 'woocommerce-multistep-checkout-like-magento') ?></div>    
                <div class="phoe_checkout_page_slider"> 
                    <div class="col-2_shipping ui-tabs-panel ui-tabs-hide" id="">
                        <?php if (!WC()->cart->ship_to_billing_address_only() && WC()->cart->needs_shipping()) : ?>
                            <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                            <?php do_action('woocommerce_checkout_after_customer_details'); ?>

                            <?php endif; ?>
                    </div>
                </div> 
                <?php if(get_option('wmclm_merge_order_payment_tabs') == "false"): ?>  
                    <div class="phoen_checkout_page_button"><?php echo get_option('wmclm_orderinfo_label') ? __(get_option('wmclm_orderinfo_label'), 'woocommerce-multistep-checkout-like-magento') : __('Order Information', 'woocommerce-multistep-checkout-like-magento'); ?></div>

                    <div class="phoe_checkout_page_slider"> 
                        <div id="" class="woocommerce-checkout-review-order ui-tabs-panel gbc_order_info">
                            <div class="order-review-tab">
                                <?php
                                    do_action( 'woocommerce_checkout_before_order_review' );
                                    woocommerce_order_review();
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="phoen_checkout_page_button"><?php echo get_option('wmclm_paymentinfo_label') ? __(get_option('wmclm_paymentinfo_label'), 'woocommerce-multistep-checkout-like-magento') : __('Payment Info', 'woocommerce-multistep-checkout-like-magento'); ?></div>

                    <div class="phoe_checkout_page_slider"> 

                        <div class="ui-tabs-panel ui-tabs-hide">
                            <div class="payment-tab-contents order-payment"> 
                                <div id="order_review" class="woocommerce-checkout-review-order">
                                    <?php //do_action('woocommerce_checkout_before_order_review'); ?>
                                    <?php do_action('woocommerce_checkout_order_review'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="phoen_checkout_page_button"><?php echo "Order & Payment Tab"; ?></div>
                    <div class="phoe_checkout_page_slider"> 

                        <div class="ui-tabs-panel ui-tabs-hide">
                            <div class="payment-tab-contents"> 
                                <div id="order_review" class="woocommerce-checkout-review-order">
                                    <?php //do_action('woocommerce_checkout_before_order_review'); ?>
                                    <?php do_action('woocommerce_checkout_order_review'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
        </form>
    </div>
        </div>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
