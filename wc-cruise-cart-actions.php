<?php

class WC_CRUISE_Cart extends WC_Cart {

    public function __construct() {

        // get plugin options values
        $this->options = get_option('wc_cruise_options');
        
        add_filter('woocommerce_add_cart_item_data', array( $this, 'wc_cruise_add_cart_item_data'), 10, 2);
        add_filter('woocommerce_get_cart_item_from_session', array( $this, 'wc_cruise_get_cart_item_from_session'), 10, 2);
        add_filter('woocommerce_get_item_data', array( $this, 'wc_cruise_get_item_data'), 10, 2);
        add_filter('woocommerce_add_cart_item', array( $this, 'wc_cruise_add_cart_item'), 10, 1);
    }

    function wc_cruise_add_cart_item_data($cart_item_meta, $product_id) {
        global $woocommerce;
 
        $booking_price = get_post_meta($product_id, '_new_price', true);
        $base_price = get_post_meta($product_id, '_price', true);
        $price = get_post_meta($product_id, '_guests', true);

        $cart_item_meta['_new_price'] = $booking_price;
        $cart_item_meta['_guests'] = $price;

        $this->wc_cruise_reset_product_meta( $product_id, $base_price, $price );
        
        return $cart_item_meta;
    }
 
    function wc_cruise_get_cart_item_from_session($cart_item, $values) {

        // Add the form options meta to the cart item in case you want to do special stuff on the check out page.
        if (isset($values['_new_price'])) {
            $cart_item['_new_price'] = $values['_new_price'];
        }

        if (isset($values['_guests'])) {
            $cart_item['_guests'] = $values['_guests'];
        }

        $this->wc_cruise_add_cart_item($cart_item);
     
        return $cart_item;
    }

    // Reset meta data after adding to cart
    function wc_cruise_reset_product_meta( $product_id, $base_price, $price) {

        if ( get_post_meta( $product_id, '_new_price', true ) ) {
            update_post_meta($product_id, '_new_price', $base_price);
        }

        if ( get_post_meta( $product_id, '_guests', true ) ) {
            delete_post_meta($product_id, '_guests', $price);
        }

    }
 
    function wc_cruise_get_item_data($other_data, $cart_item) {

        if ( isset($cart_item['_guests']) && $cart_item['_guests'] ) {
 
            $guests = $cart_item['_guests'];

            // Add custom data to product data
            $other_data[] = array('name' => __( $this->options['wc_cruise_guests_text'], 'wc_cruise' ), 'value' => $guests);
        }

        return $other_data;
    }
 
    function wc_cruise_add_cart_item($cart_item) {
        global $woocommerce;
 
        if ( isset($cart_item['_new_price']) && $cart_item['_new_price'] > 0 ) {
            $booking_price = $cart_item['_new_price'];
            $cart_item['data']->set_price($booking_price);
        }
 
        return $cart_item;
    }



}

new WC_CRUISE_Cart();

class WC_CRUISE_Checkout extends WC_Checkout {

    public function __construct() {

        // get plugin options values
        $this->options = get_option('wc_cruise_options');

        add_action('woocommerce_add_order_item_meta', array($this, 'wc_cruise_add_order_meta' ), 10, 2);

    }

    public function wc_cruise_add_order_meta($item_id, $values) {
        if ( ! empty( $values['_guests'] ) )
            woocommerce_add_order_item_meta( $item_id, $this->options['wc_cruise_guests_text'], $values['_guests'] );

    }

}

new WC_CRUISE_Checkout();
