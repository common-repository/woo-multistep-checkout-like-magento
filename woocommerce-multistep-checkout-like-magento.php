<?php
    /**
    * Plugin Name: WooCommerce MultiStep Checkout Like Magento
    * Description: WooCommerce Multi-Step-Checkout enable multi-step-checkout functionality on WooCommerce checkout page same like magento checkout Page.
    * Version: 1.0.0
    * Author: Commercepundit
    * Author URI: http://commercepundit.com/
    * Text Domain: woocommerce-multistep-checkout-like-magento
    * Domain Path: /languages/
    */
    if (!defined('ABSPATH'))
        die();

    define("CPMC_VERSION", "2.3.3");

    function dependentplugin_activate() {

        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            // deactivate dependent plugin
            deactivate_plugins(plugin_basename(__FILE__));

            exit('<strong>WooCommerce MultiStep Checkout Like Magento</strong> requires <a target="_blank" href="http://wordpress.org/plugins/woocommerce/">WooCommerce</a> Plugin to be installed first.');
        }else{
            //default option for plugin
            update_option('wmclm_merge_order_payment_tabs', 'true');
            update_option('wmclm_add_code_footer', 'true');
            update_option('wmclm_zipcode_validation', 'false');
            update_option('wmclm_add_coupon_form', 'false');  
        }

    }

    register_activation_hook(__FILE__, 'dependentplugin_activate');


    load_plugin_textdomain('woocommerce-multistep-checkout-like-magento', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    add_filter('woocommerce_locate_template', 'wcmultichecout_woocommerce_locate_template', 1, 3);

    function wcmultichecout_woocommerce_locate_template($template, $template_name, $plugin_path) {

        $plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/woocommerce/';
        if (file_exists($plugin_path . $template_name)) {
            $template = $plugin_path . $template_name;
            return $template;
        }

        return $template;
    }

    function enque_woocommerce_multistep_checkout_scripts() {
        $wizard_type = get_option('wmclm_wizard_type');    
        wp_register_script('jquery-validate', plugins_url('/assets/js/jquery.validate.min.js', __FILE__), array('jquery'), CPMC_VERSION);
        wp_register_style('jquery-custom-main', plugins_url('/assets/css/wmclm.css', __FILE__), null, CPMC_VERSION);

        /*     * *Only add on WooCommerce checkout page * */
        if (is_checkout() || defined('ICL_LANGUAGE_CODE')) {
            wp_enqueue_script('jquery-validate');
            wp_enqueue_style('jquery-custom-main');
        }
    }

    add_action('wp_enqueue_scripts', 'enque_woocommerce_multistep_checkout_scripts');

    /* * *Loading variables to wizard file ** */

    function wmclm_load_scripts() {
        $vars = array(
        'next' => get_option('wmclm_btn_next') ? __(get_option('wmclm_btn_next'), 'woocommerce-multistep-checkout-like-magento') : __('Next', 'woocommerce-multistep-checkout-like-magento'),
        'previous' => get_option('wmclm_btn_prev') ? __(get_option('wmclm_btn_prev'), 'woocommerce-multistep-checkout-like-magento') : __('Previous', 'woocommerce-multistep-checkout-like-magento'),
        'finish' => get_option('wmclm_btn_finish') ? __(get_option('wmclm_btn_finish'), 'woocommerce-multistep-checkout-like-magento') : __('Place Order', 'woocommerce-multistep-checkout-like-magento'),
        'zipcode_validation' => get_option('wmclm_zipcode_validation'),        
        'isAuthorizedUser' => isAuthorizedUser(),
        'ajaxurl' => admin_url('admin-ajax.php'),
        'include_login' => get_option('wmclm_add_login_form'),
        'include_coupon_form' => get_option('wmclm_add_coupon_form'),
        'woo_include_login' => get_option('woocommerce_enable_checkout_login_reminder'),
        'no_account_btn' => get_option('wmclm_no_account_btn') ? __(stripslashes(get_option('wmclm_no_account_btn')), 'woocommerce-multistep-checkout-like-magento') : __("I don't have an account", 'woocommerce-multistep-checkout-like-magento'),
        'login_nonce' => wp_create_nonce('wmc-login-nonce')
        );
        if (is_checkout()) {
            
            if((get_option('wmclm_add_code_footer') == 'true') || (get_option('wmclm_add_code_footer') == "")){
                $location = true;
            }else{
                $location = false;
            }
            wp_register_script('wmc-multistep', plugins_url('/assets/js/multistep.js', __FILE__), array('jquery'), CPMC_VERSION, $location);
            
            wp_register_script('wmc-multistep1', plugins_url('/assets/js/multistep-1.js', __FILE__), array('jquery'), CPMC_VERSION, $location);
            wp_localize_script('wmc-multistep1', 'wmclm_wizard', $vars);
            wp_enqueue_script('wmc-multistep');
            wp_enqueue_script('wmc-multistep1');
        }
    }

    add_action('wp_enqueue_scripts', 'wmclm_load_scripts');


    /* * **********Plugin Options Page ** */
    add_action('admin_menu', 'woocommercemultichekout_menu_page');

    function woocommercemultichekout_menu_page() {
        add_submenu_page('woocommerce', 'WooCommerce MultiStepCheckout', 'Multistep Checkout', 'manage_options', 'wcmlmultichekout', 'wcmultichekout_options');
    }
    /* * * Add Color Picker * */
    add_action('admin_enqueue_scripts', 'wmclm_enqueue_color_picker');

    function wmclm_enqueue_color_picker() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker-script', plugins_url('assets/js/script.js', __FILE__), array('wp-color-picker'), false, true);
    }

    function add_jquery_steps_options() {
            ?>
            <style>
                div.phoen_checkout_page_button{
                    background: <?php echo get_option('wmclm_tabs_color') ? get_option('wmclm_tabs_color'):"#cccccc"; ?>;
                    color: <?php echo get_option('wmclm_inactive_font_color') ? get_option('wmclm_inactive_font_color'):"#000000"; ?>;
                }
                .phoen_checkout_page_button.phoen_slider_active{
                    background: <?php echo get_option('wmclm_completed_tabs_color') ? get_option('wmclm_completed_tabs_color'):"#3b00d1"; ?>;
                    color: <?php echo get_option('wmclm_font_color') ?>;
                }
                .pmsc_tabs .phoe_checkout_page_slider .button{
                    background: <?php echo get_option('wmclm_buttons_bg_color') ?>;
                    color: <?php echo get_option('wmclm_buttons_font_color') ?>;
                }
            </style>
            <?php
    }

    add_action('wp_head', 'add_jquery_steps_options');

    function wcmultichekout_options() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        //Submit form
        if (isset($_POST['send_form']) && $_POST['send_form'] == 'Y') {

            $do_not_save = array('send_form', 'submit', 'wmclm_restore_default');
            foreach ($_POST as $option_name => $option_value) {
                if (in_array($option_name, $do_not_save))
                    continue;

                // Save the posted value in the database
                update_option($option_name, sanitize_text_field($option_value));
            }

            // If restore to default
            if (isset($_POST['wmclm_restore_default']) && $_POST['wmclm_restore_default']) {
                $do_not_save = array('wmclm_merge_order_payment_tabs', 'wmclm_zipcode_validation', 'wmclm_add_coupon_form');
                foreach ($_POST as $option_name => $option_value) {
                    /*if (in_array($option_name, $do_not_save))
                        continue;*/
                    $option_value = sanitize_text_field($option_value);
                    delete_option(sanitize_text_field($option_name));
                }
            }
        ?>
        <div class="updated"><p><strong><?php _e('settings saved.', 'woocommerce-multistep-checkout-like-magento'); ?></strong></p></div>
        <?php
        }
        $logintab = get_option('wmclm_add_login_form') ? get_option('wmclm_add_login_form') : "false";
        $coupontab = get_option('wmclm_add_coupon_form') ? get_option('wmclm_add_coupon_form') : "false";
    ?>
    <div class="wrapper">
        <div id="icon-edit" class="icon32"></div><h2><?php _e('WooCommerce MultiStep Checkout Like Magento', 'woocommerce-multistep-checkout-like-magento') ?></h2>
        <form name="wccheckout_options" method="post" action="">
            <input type="hidden" name="send_form" value="Y">
            <table class="form-table">
                <tr>
                    <td width="200"><?php _e('Add Login to Tabs', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><select name="wmclm_add_login_form">
                            <option value="true" <?php selected($logintab, 'true', true); ?>><?php _e('Yes', 'woocommerce-multistep-checkout-like-magento') ?></option>
                            <option value="false" <?php selected($logintab, 'false', true); ?>><?php _e('No', 'woocommerce-multistep-checkout-like-magento') ?></option>
                        </select>
                        <span class="description"><?php _e('Add Login form to tabs', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td width="200"><?php _e('Add Coupon to Tabs', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><select name="wmclm_add_coupon_form">
                            <option value="true" <?php selected($coupontab, 'true', true); ?>><?php _e('Yes', 'woocommerce-multistep-checkout-like-magento') ?></option>                                                        
                            <option value="false" <?php selected($coupontab, 'false', true); ?>><?php _e('No', 'woocommerce-multistep-checkout-like-magento') ?></option>
                        </select>
                        <span class="description"><?php _e('Add Coupon form to tabs', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td width="200"><?php _e('Combine order Infomation and Payment tabs', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><select name="wmclm_merge_order_payment_tabs">
                            <option value="true" <?php selected(get_option('wmclm_merge_order_payment_tabs'), 'true', true); ?>><?php _e('Yes', 'woocommerce-multistep-checkout-like-magento') ?></option>
                            <option value="false" <?php selected(get_option('wmclm_merge_order_payment_tabs'), 'false', true); ?>><?php _e('No', 'woocommerce-multistep-checkout-like-magento') ?></option>
                        </select>
                        <span class="description"><?php _e('If you want to combine Order information and Payment tabs then set this to "Yes"', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td width="200"><?php _e('Tabs Color', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><input name="wmclm_tabs_color" id="tabs_color" type="text" value="<?php echo get_option('wmclm_tabs_color') ? get_option('wmclm_tabs_color'):"#cccccc"; ?>" class="regular-text" /><br /><span class="description"><?php _e('Select background color for active tabs', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>
                <tr>
                    <td><?php _e('Inactive Tabs Font Color', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><input name="wmclm_inactive_font_color" id="inactive_tabs_color" type="text" value="<?php echo get_option('wmclm_inactive_font_color') ? get_option('wmclm_inactive_font_color'):"#000000"; ?>" class="regular-text" /><br /><span class="description"><?php _e('Select background color for inactive tabs', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>
                <tr>
                    <td><?php _e('Active tabs color', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><input name="wmclm_completed_tabs_color" id="completed_tabs_color" type="text" value="<?php echo get_option('wmclm_completed_tabs_color') ? get_option('wmclm_completed_tabs_color'):"#3b00d1"; ?>" class="regular-text" /><br /><span class="description"><?php _e('Select background color for completed tabs', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Active Tabs Font Color', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><input name="wmclm_font_color" id="font_color" type="text" value="<?php echo get_option('wmclm_font_color') ? get_option('wmclm_font_color'):"#ffffff"; ?>" class="regular-text" /><br />
                        <span class="description"><?php _e('Select Tabs Font Color', '') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Buttons Color', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><input name="wmclm_buttons_bg_color" id="buttons_bg_color" type="text" value="<?php echo get_option('wmclm_buttons_bg_color') ? get_option('wmclm_buttons_bg_color'):"#cccccc"; ?>" class="regular-text" /><br />
                        <span class="description"><?php _e('Next/Previous/Login buttons color', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Buttons Font color', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><input name="wmclm_buttons_font_color" id="buttons_font_color" type="text" value="<?php echo get_option('wmclm_buttons_font_color') ? get_option('wmclm_buttons_font_color'):"#000000";  ?>" class="regular-text" /><br />
                        <span class="description"><?php _e('Next/Previous/Login/Coupon button font color', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>
                <tr>
                    <td><?php _e('Next Button', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_btn_next" value="<?php echo get_option('wmclm_btn_next') ? get_option('wmclm_btn_next') : _e("Next") ?>" />
                        <span class="description"><?php _e('Enter text for Next button', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Previous Button', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_btn_prev" value="<?php echo get_option('wmclm_btn_prev') ? get_option('wmclm_btn_prev') : _e("Previous") ?>" />
                        <span class="description"><?php _e('Enter text for Previous button', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Place Order Button', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_btn_finish" value="<?php echo get_option('wmclm_btn_finish') ? get_option('wmclm_btn_finish') : _e("Place Order") ?>" />
                        <span class="description"><?php _e('Enter text for Place Order Button', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('No Account Button', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_no_account_btn" value="<?php echo get_option('wmclm_no_account_btn') ? stripslashes(get_option('wmclm_no_account_btn')) : _e("I don't have an account") ?>" />
                        <span class="description"><?php _e('Enter text for No Account Button', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Zip/Postcode Validation', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><select name="wmclm_zipcode_validation">
                            <option value="false" <?php selected(get_option('wmclm_zipcode_validation'), 'false', true); ?>><?php _e('No', 'woocommerce-multistep-checkout-like-magento') ?></option>
                            <option value="true" <?php selected(get_option('wmclm_zipcode_validation'), 'true', true); ?>><?php _e('Yes', 'woocommerce-multistep-checkout-like-magento') ?></option>
                        </select>
                        <span class="description"><?php _e('Zip/Postcode validation done by this plugin', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td colspan="2"><h3 style="margin: 0;padding: 0"><?php _e('Step Titles', 'woocommerce-multistep-checkout-like-magento') ?></h3></td>
                </tr>

                <tr>
                    <td><?php _e('Login', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_login_label" value="<?php echo get_option('wmclm_login_label') ? get_option('wmclm_login_label') : _e("Login") ?>" />
                        <span class="description"><?php _e('Enter text for Login label', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>
                
                <tr>
                    <td><?php _e('Coupon', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_coupon_label" value="<?php echo get_option('wmclm_coupon_label') ? get_option('wmclm_coupon_label') : _e("Coupon") ?>" />
                        <span class="description"><?php _e('Enter text for Coupon label', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Billing', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_billing_label" value="<?php echo get_option('wmclm_billing_label') ? get_option('wmclm_billing_label') : _e("Billing") ?>" />
                        <span class="description"><?php _e('Enter text for Billing label', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Shipping', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_shipping_label" value="<?php echo get_option('wmclm_shipping_label') ? get_option('wmclm_shipping_label') : _e("Shipping") ?>" />
                        <span class="description"><?php _e('Enter text for Shipping label', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Order Information', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_orderinfo_label" value="<?php echo get_option('wmclm_orderinfo_label') ? get_option('wmclm_orderinfo_label') : _e("Order Information") ?>" />
                        <span class="description"><?php _e('Enter text for Order Information label', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Payment Info', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td>
                        <input type="text" name="wmclm_paymentinfo_label" value="<?php echo get_option('wmclm_paymentinfo_label') ? get_option('wmclm_paymentinfo_label') : _e("Payment Info") ?>" />
                        <span class="description"><?php _e('Enter text for Payment Info label', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td colspan="2"><h3 style="margin: 0;padding: 0"><?php _e('Code Location', 'woocommerce-multistep-checkout-like-magento') ?></h3></td>
                </tr>

                <tr>
                    <td><?php _e('Add multistep code to theme', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><select name="wmclm_add_code_footer">
                            <option value="true" <?php selected(get_option('wmclm_add_code_footer'), 'true', true); ?>><?php _e('Footer', 'woocommerce-multistep-checkout-like-magento') ?></option>
                            <option value="false" <?php selected(get_option('wmclm_add_code_footer'), 'false', true); ?>><?php _e('Header', 'woocommerce-multistep-checkout-like-magento') ?></option>
                        </select>
                        <span class="description"><?php _e('Add WooCommerce Multistep JS files to Footer/Header', 'woocommerce-multistep-checkout-like-magento') ?></span></td>
                </tr>

                <tr>
                    <td><?php _e('Restore Plugin Defaults', 'woocommerce-multistep-checkout-like-magento') ?></td>
                    <td><input type="checkbox" name="wmclm_restore_default" value="yes" /></td>
                </tr>

            </table>


            <p class="submit">
                <input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
            </p>

        </form>

    </div>        

    <?php
    }

    add_action('woocommerce_checkout_order_review', 'update_shipping_info');

    function update_shipping_info() {
    ?>
    <?php if (get_option('wmclm_merge_order_payment_tabs') != "true" || get_option('wmclm_merge_order_payment_tabs') == ""): ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery(".shipping-tab .shop_table").empty();
                jQuery(".shop_table").appendTo(".shipping-tab");

            })
        </script>
        <?php
            endif;
    }

    add_action('after_setup_theme', 'avada_checkoutfix');

    function avada_checkoutfix() {
        if (function_exists('avada_woocommerce_checkout_after_customer_details')) {
            remove_action('woocommerce_checkout_after_customer_details', 'avada_woocommerce_checkout_after_customer_details');
        }

        if (function_exists('avada_woocommerce_checkout_before_customer_details')) {
            remove_action('woocommerce_checkout_before_customer_details', 'avada_woocommerce_checkout_before_customer_details');
        }
    }

    /* * * Add login tow wizard * */
    $add_login = get_option('wmclm_add_login_form');
    if ($add_login == 'true' || $add_login == "") {

        function wmclm_add_checkout_login_form() {
            if (!has_action('woocommerce_before_checkout_form')) {
                add_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
            }
        }

        //add login form to wizard
        add_action('woocommerce_multistep_checkout_before', 'add_login_to_wizard');

        function add_login_to_wizard() {
            if (is_user_logged_in() || 'no' === get_option('woocommerce_enable_checkout_login_reminder')) {
                return;
            }
        ?>
        <script>
            jQuery(function () {
                jQuery(".woocommerce-info a.showlogin").parent().detach();
                jQuery("form.login").appendTo('.login_form');
                jQuery(".login_form form.login").show();
            });</script>    
        <h1 class="title-login-wizard"><?php _e('Login', 'woocommerce') ?></h1>
        <div class="login-step">


        </div>
        <?php
        }

    }

    /* * ***************Add Coupon form to wizard * */
    $add_coupon_form = get_option('wmclm_add_coupon_form');
    
    if ($add_coupon_form == 'true') {
        /*     * Check if coupons are enabled. */
        if (get_option('woocommerce_enable_coupons') != "yes") {
            return;
        }
        add_action('woocommerce_multistep_checkout_before', 'wmclm_add_coupon_form', 20);

        function wmclm_add_coupon_form() {

        ?>
        <script>
            jQuery(function () {
                jQuery(".woocommerce-info a.showcoupon").parent().detach();
                jQuery("form.checkout_coupon").appendTo('.coupon-step');
                jQuery(".coupon-step form.checkout_coupon").show();
            });</script>

        <h1 class="title-coupon-wizard"><?php echo get_option('wmclm_coupon_label') ? __(get_option('wmclm_coupon_label'), 'woocommerce-multistep-checkout-like-magento') : __('Coupon', 'woocommerce-multistep-checkout-like-magento'); ?></h1>
        <div class="coupon-step">

            <?php  do_action('woocommerce_multistep_checkout_before'); ?>
        </div>
        <?php
    }

}

function isAuthorizedUser() {
    return get_current_user_id();
}

add_action('wp_ajax_check_zip_code', 'wmclm_validate_post_code');
add_action('wp_ajax_nopriv_check_zip_code', 'wmclm_validate_post_code');

//validate PostCode
function wmclm_validate_post_code() {
    $country = sanitize_text_field($_POST['country']);
    $postCode = strtoupper( str_replace( ' ', '', esc_html(trim($_POST['postCode']))));
    echo WC_Validation::is_postcode($postCode, $country);

    exit;
}

add_action('wp_ajax_validate_phone', 'wmclm_validate_phone_number');
add_action('wp_ajax_nopriv_validate_phone', 'wmclm_validate_phone_number');

function wmclm_validate_phone_number() {
    $phone = $_POST['phone'];
    echo WC_Validation::is_phone($phone);

    exit();
}

//Handle Login form

add_action('wp_ajax_wmclm_check_user_login', 'wmclm_authenticate_user');
add_action('wp_ajax_nopriv_wmclm_check_user_login', 'wmclm_authenticate_user');

function wmclm_authenticate_user() {
    check_ajax_referer('wmc-login-nonce');
    if (is_email($_POST['username']) && apply_filters('woocommerce_get_username_from_email', true)) {
        $user = get_user_by('email', sanitize_email($_POST['username']));

        if (isset($user->user_login)) {
            $creds['user_login'] = $user->user_login;
        }
    } else {
        $creds['user_login'] = sanitize_text_field($_POST['username']);
    }

    $creds['user_password'] = sanitize_text_field($_POST['password']);
    $creds['remember'] = esc_html(trim(isset($_POST['rememberme'])));
    $secure_cookie = is_ssl() ? true : false;
    $user = wp_signon(apply_filters('woocommerce_login_credentials', $creds), $secure_cookie);


    if (wmclm_is_eruser_authenticate($user)) {
        echo '<p class="error-msg">' . __('Incorrect username/password.', 'woocommerce-multistep-checkout-like-magento') . ' </p>';
    } else {
        echo 'successfully';
    }

    exit();
}

function wmclm_is_eruser_authenticate($result) {
    return is_wp_error($result);
}

/* * ************* Add plugin info to the plugin listing page * */
if (isset($_GET['page']) && $_GET['page'] == "wcmultichekout") {
    add_filter('admin_footer_text', 'wmclm_admin_footer_text');

    function wmclm_admin_footer_text() {

        echo sprintf(__('If you like <strong>WooCommerce MultiStep Checkout</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating.'), '<a href="http://codecanyon.net/item/woocommerce-multistep-checkout-like-magento/8125187" target="_blank" class="wc-rating-link" data-rated="' . __('Thanks :)', 'woocommerce') . '">', '</a>');
    }

}

add_filter('plugin_row_meta', 'wmclm_Register_Plugins_Links', 10, 2);

function wmclm_Register_Plugins_Links($links, $file) {
    $base = plugin_basename(__FILE__);
    if ($file == $base) {
        $links[] = '<a href="http://woocommerce-multistep-checkout-like-magento.mubashir09.com/documentation/">' . __('Documentation') . '</a>';
        $links[] = '<a href="http://woocommerce-multistep-checkout-like-magento.mubashir09.com/faq/">' . __('FAQ') . '</a>';
    }
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wmclm_link_action_on_plugin');

function wmclm_link_action_on_plugin($links) {

    return array_merge(array('settings' => '<a href="' . admin_url('admin.php?page=wcmultichekout') . '">' . __('Settings', 'domain') . '</a>'), $links);
}

add_filter( 'woocommerce_order_button_text', 'woo_multistep_order_button_text' ); 

function woo_multistep_order_button_text() {
    $txt = get_option('wmclm_btn_finish') ? get_option('wmclm_btn_finish') : "Place Order"; 
    return __( $txt, 'woocommerce' ); 
}