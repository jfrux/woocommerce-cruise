<?php

class WC_CRUISE extends WC_AJAX {

    public function __construct() {

        // get plugin options values
        $this->options = get_option('wc_cruise_options');
        //add_action( 'woocommerce_cart_calculate_fees', 'wc_cruise_add_tax_gratuity' );
        add_action( 'wp_enqueue_scripts', array( $this, 'wc_cruise_enqueue_scripts' ));
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'wc_cruise_add_product_option_pricing' ));
        add_action( 'woocommerce_process_product_meta', array( $this, 'wc_cruise_add_custom_price_fields_save' ));
        add_filter( 'woocommerce_before_add_to_cart_button', array( $this, 'wc_cruise_before_add_to_cart_button' ));
        // add_filter( 'woocommerce_get_price_html', array( $this, 'wc_cruise_add_price_html' ), 10, 2 );
        add_action( 'wp_ajax_cruise_check_avail', array( $this, 'wc_cruise_check_avail' ));
        // add_action( 'wp_ajax_cruise_nopriv_add_new_price', array( $this, 'wc_cruise_get_new_price' ));
        // add_filter( 'add_to_cart_fragments', array( $this, 'wc_cruise_new_price_fragment' ));
        add_filter( 'woocommerce_loop_add_to_cart_link', array($this, 'wc_cruise_custom_loop_add_to_cart' ), 10, 2 );
        
    }

    
    public function wc_cruise_enqueue_scripts() {
        global $woocommerce, $post;

        // Get page language in order to load Pickadate translation
        $site_language = get_bloginfo( 'language' );
        $lang = str_replace("-","_", $site_language);
        
        // Load scripts only on product page if "booking" option is checked
        $wc_cruise_options = get_post_meta($post->ID, '_cruise_booking_option', true);

        if ( is_product() && $wc_cruise_options) {

            // Concatenated and minified script including datepick.js, legacy.js, picker.js and picker.date.js
            wp_enqueue_script( 'guests', plugins_url( '/js/guests.js', __FILE__ ), array('jquery'), '1.0', true);

            /*wp_enqueue_script( 'picker', plugins_url( '/js/picker.js', __FILE__ ), array('jquery'), '1.0', true);
            wp_enqueue_script( 'picker.date', plugins_url( '/js/picker.date.js', __FILE__ ), array('jquery'), '1.0', true);
            wp_enqueue_script( 'legacy', plugins_url( '/js/legacy.js', __FILE__ ), array('jquery'), '1.0', true);
            wp_enqueue_script( 'datepick', plugins_url( '/js/datepick.js', __FILE__ ), array('jquery'), '1.0', true);*/

            wp_enqueue_script( 'guests.language', plugins_url( '/js/translations/' . $lang . '.js', __FILE__ ), array('jquery'), '1.0', true);

            // wp_enqueue_style( 'picker.date' );

            // in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
            wp_localize_script( 'guests', 'ajax_object',
                    array( 
                        'ajax_url' => admin_url( 'admin-ajax.php' )
                    )
                );
        }
    }

    // Add checkbox to the product admin page
    public function wc_cruise_add_product_option_pricing() {

        global $woocommerce, $post;
        echo '<div class="options_group">';

            // Checkbox
            woocommerce_wp_checkbox(array(
                'id' => '_cruise_booking_option', 
                'class' => 'wc_booking_option checkbox', 
                'wrapper_class' => '',
                'label' => __( 'Enable Cruise Booking Option', 'wc_cruise' )
            ));
            woocommerce_wp_text_input(array(
                'id' => '_cruise_price_single', 
                'class' => 'wc_booking_price text', 
                'wrapper_class' => '',
                'label' => __( 'Single Price', 'wc_cruise' )
            ));
            woocommerce_wp_text_input(array(
                'id' => '_cruise_price_double', 
                'class' => 'wc_booking_price text', 
                'wrapper_class' => '',
                'label' => __( 'Double Price', 'wc_cruise' )
            ));
            woocommerce_wp_text_input(array(
                'id' => '_cruise_price_triple', 
                'class' => 'wc_booking_price text',
                'wrapper_class' => '',
                'label' => __( 'Triple Price', 'wc_cruise' )
            ));

            woocommerce_wp_text_input(array(
                'id' => '_cruise_price_quad', 
                'class' => 'wc_booking_price text', 
                'wrapper_class' => '',
                'label' => __( 'Quad Price', 'wc_cruise' )
            ));

            woocommerce_wp_text_input(array(
                'id' => '_cruise_price_quint', 
                'class' => 'wc_booking_price text', 
                'wrapper_class' => '',
                'label' => __( 'Quint Price', 'wc_cruise' )
            ));


        echo '</div>';
    }

    // Save checkbox value to the product admin page
    public function wc_cruise_add_custom_price_fields_save( $post_id ) {
        $woocommerce_checkbox = isset( $_POST['_cruise_booking_option'] ) ? 'yes' : '';
        update_post_meta( $post_id, '_cruise_booking_option', $woocommerce_checkbox );
        update_post_meta( $post_id, '_cruise_price_single', $_POST['_cruise_price_single'] );
        update_post_meta( $post_id, '_cruise_price_double', $_POST['_cruise_price_double'] );
        update_post_meta( $post_id, '_cruise_price_triple', $_POST['_cruise_price_triple'] );
        update_post_meta( $post_id, '_cruise_price_quad', $_POST['_cruise_price_quad'] );
        update_post_meta( $post_id, '_cruise_price_quint', $_POST['_cruise_price_quint'] );

    }
    /**
     * WooCommerce Extra Feature
     * --------------------------
     *
     * Add custom fee times the guest number
     *
     */
    function wc_cruise_add_tax_gratuity() {
      global $woocommerce;
      // var_dump($_REQUEST);
      // die;
        $this->options['wc_cruise_tax_gratuity_amount'];
        $wc_cruise_options = get_post_meta($post->ID, '_cruise_booking_option', true);
        if (isset($wc_cruise_options) && $wc_cruise_options) {
            $taxes_gratuity = ( $_POST['guests'] + $_POST['guests'] ) * $percentage;
            $woocommerce->cart->add_fee( __('Taxes and Gratuity ($200 x # of guests)', 'woocommerce'), $taxes_gratuity, true, 'standard' );
        }
    }
    // Add custom form to the product page.
    public function wc_cruise_before_add_to_cart_button() {
        global $woocommerce, $post, $product;
        $wc_cruise_options = get_post_meta($post->ID, '_cruise_booking_option', true);
        if (isset($wc_cruise_options) && $wc_cruise_options) {
            echo '<div class="wc_cruise_errors">' . wc_print_notices() . '</div>
                <p>
                    <label for="pa_guests">' . __( $this->options['wc_cruise_guests_text'], 'wc_cruise' ) . ' : </label>
                    <select name="attribute_pa_guests" id="pa_guests" data-product_id="' . $product->id . '" data-value="">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </p>';
        }
    }
    
    // Display base price or new price
    public function wc_cruise_add_price_html($content) {

        global $woocommerce, $post, $product;

        $guests = isset($_POST['guests']) ? $_POST['guests'] : 1;
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : $product->id;

        $currency = get_woocommerce_currency_symbol();
        switch ($guests) {
            case 1:
            $price = get_post_meta($product_id,'_cruise_price_single', true);
            break;
            case 2:
            $price = get_post_meta($product_id,'_cruise_price_double', true);
            break;
            case 3:
            $price = get_post_meta($product_id,'_cruise_price_triple', true);
            break;
            case 4:
            $price = get_post_meta($product_id,'_cruise_price_quad', true);
            break;
            case 5:
            $price = get_post_meta($product_id,'_cruise_price_quint', true);
            break;
        }

        $new_price = $price * $guests;
        $new_price_formatted = sprintf( get_woocommerce_price_format(), $currency, $new_price );

        $price_formatted = sprintf( get_woocommerce_price_format(), $currency, $price );
        $wc_cruise_options = get_post_meta($post->ID, '_cruise_booking_option', true);

        // Return either the new price or a price / day or normal price
        if ( isset($_POST['guests']) && $_POST['guests'] > 0 ) {
            return $price_formatted . __(' / person', 'wc_cruise') . ' (Total: ' . $new_price_formatted . ')';
        } else if ( isset($wc_cruise_options) && $wc_cruise_options ) {
            return $price_formatted . __(' / person', 'wc_cruise');
        } else {
            return $content;
        }

    }

    //GET BEST AVAILABLE ROOM & PRICE
    public function wc_cruise_check_avail() {
        global $woocommerce, $wpdb, $product, $post;
        $product = new WC_Product(the_ID());
        $product_id = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : $product->id;
        $guests = isset($_REQUEST['guests']) ? $_REQUEST['guests'] : '';
        
        // $product->get_post_data();
        $output = isset($_REQUEST['guests']) ? $_REQUEST['guests'] : 1;
        // $available_variations = $product->get_available_variations();
        $querystr = "
            SELECT
                `post`.`ID`,
                (SELECT meta_value FROM wp_postmeta As `meta` WHERE post_id=`post`.`ID` AND `meta`.`meta_key` = '_stock') As stock, 
                (SELECT meta_value FROM wp_postmeta As `meta` WHERE post_id=`post`.`ID` AND `meta`.`meta_key` = 'attribute_pa_guests') As max_guests,
                (SELECT meta_value FROM wp_postmeta As `meta` WHERE post_id=`post`.`post_parent` AND `meta`.`meta_key` = '_cruise_price_single') As price_1, 
                (SELECT meta_value FROM wp_postmeta As `meta` WHERE post_id=`post`.`post_parent` AND `meta`.`meta_key` = '_cruise_price_double') As price_2,  
                (SELECT meta_value FROM wp_postmeta As `meta` WHERE post_id=`post`.`post_parent` AND `meta`.`meta_key` = '_cruise_price_triple') As price_3,  
                (SELECT meta_value FROM wp_postmeta As `meta` WHERE post_id=`post`.`post_parent` AND `meta`.`meta_key` = '_cruise_price_quad') As price_4,  
                (SELECT meta_value FROM wp_postmeta As `meta` WHERE post_id=`post`.`post_parent` AND `meta`.`meta_key` = '_cruise_price_quint') As price_5
            FROM wp_posts As post
            WHERE
                post_parent=" . $product_id . " AND
                post_type='product_variation' AND
                (SELECT meta_value FROM wp_postmeta As `meta` WHERE post_id=`post`.`ID` AND `meta`.`meta_key` = '_max_occupancy') >= " . $guests . "
            ORDER BY 
                (SELECT meta_value FROM wp_postmeta As `meta` WHERE post_id=`post`.`ID` AND `meta`.`meta_key` = '_max_occupancy')
            LIMIT 1
         ";
        $rooms = $wpdb->get_results($querystr, OBJECT);
        
        wp_send_json($rooms[0]);
        
        switch ($guests) {
            case 1:
            $price = get_post_meta($product_id,'_cruise_price_single', true);
            break;
            case 2:
            $price = get_post_meta($product_id,'_cruise_price_double', true);
            break;
            case 3:
            $price = get_post_meta($product_id,'_cruise_price_triple', true);
            break;
            case 4:
            $price = get_post_meta($product_id,'_cruise_price_quad', true);
            break;
            case 5:
            $price = get_post_meta($product_id,'_cruise_price_quint', true);
            break;
        }

        $new_price = $price * $guests;
        // If number of days is inferior to 0
        if ( $output <= 0 ) {
            $error_code = 1;
        }
        
        // Show error message
        if ( $error_code ) {
            $error_message = $this->wc_cruise_get_error( $error_code );
            
            wc_add_notice( $error_message, $notice_type = 'error' );
            $this->wc_cruise_error_fragment($messages);

        } else {
            // Update product price
            $product->get_price_html();
            // Update product meta
            $this->wc_cruise_update_product_meta( $product_id, $price, $new_price, $guests );

            // Return fragments
            $this->get_refreshed_fragments();
        }

        die();

    }

    // Get error messages
    public function wc_cruise_get_error( $error_code ) {

        switch ( $error_code ) {
            case 1:
                $err = __( 'Please choose how many guests.', 'wc_cruise' );
            break;
            default:
                $err = '';
            break;
        }

        return $err;
    }

    // Update product meta (New price, start date and end date)
    // public function wc_cruise_update_product_meta( $product_id, $price, $new_price, $guests ) {

    //     global $woocommerce, $post, $product;

    //     if ( get_post_meta($product_id, '_new_price', true ) ) {
    //         update_post_meta($product_id, '_new_price', $new_price);
    //     } else {
    //         add_post_meta($product_id, '_new_price', $new_price, true);
    //     }

    //     if ( get_post_meta($product_id, '_price_per_person', true ) ) {
    //         update_post_meta($product_id, '_price_per_person', $price);
    //     } else {
    //         add_post_meta($product_id, '_price_per_person', $price, true);
    //     }

    //     if ( get_post_meta($product_id, '_guests', true ) ) {
    //         update_post_meta($product_id, '_guests', $guests);
    //     } else {
    //         add_post_meta($product_id, '_guests', $guests, true);
    //     }

    // }

    // Update error messages with Ajax
    public function wc_cruise_error_fragment( $messages ) {

        global $woocommerce;

        header( 'Content-Type: application/json; charset=utf-8' );

        ob_start();
        wc_print_notices();
        $messages = ob_get_clean();

            $data = array(
                'errors' => array(
                    'div.wc_cruise_errors' => '<div class="wc_cruise_errors">' . $messages . '</div>'
                )
            );

        echo json_encode( $data );

        die();

    }

    // Update price fragment
    public function wc_cruise_new_price_fragment( $fragments ) {

        global $woocommerce, $post, $product;
        $product = new WC_Product(the_ID());
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : $product->id;
        
        if ( isset($_POST['guests']) ) {
            ob_start();
            echo '<p itemprop="price" class="price">' . $product->get_price_html() . '</p>';
            $fragments['p.price'] = ob_get_clean();
        }

        return $fragments;

    }

    // Add custom text link on product archive
    public function wc_cruise_custom_loop_add_to_cart($content, $product) {

        global $woocommerce, $post, $product;
        $wc_cruise_options = get_post_meta($post->ID, '_cruise_booking_option', true);

        if (isset($wc_cruise_options) && $wc_cruise_options) {

            $link = get_permalink( $product->id );
            $label = __( 'Select guests', 'wc_cruise' );

            return '<a href="' . $link . '" rel="nofollow" class="product_type_variable button"><span>' . $label . '</span></a>';
        } else {
            return $content;
        }
    }

}

$wccruise = new WC_CRUISE;
