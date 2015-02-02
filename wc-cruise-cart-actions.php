<?php

class WC_CRUISE_Cart extends WC_Cart {

    public function __construct() {

        // get plugin options values
        $this->options = get_option('wc_cruise_options');
        
        add_filter('woocommerce_add_cart_item_data', array( $this, 'wc_cruise_add_cart_item_data'), 10, 2);
        add_filter('woocommerce_get_cart_item_from_session', array( $this, 'wc_cruise_get_cart_item_from_session'), 10, 2);
        add_filter('woocommerce_get_item_data', array( $this, 'wc_cruise_get_item_data'), 10, 2);
        add_filter('woocommerce_add_cart_item', array( $this, 'wc_cruise_add_cart_item'), 10, 1);
        //add_filter('woocommerce_add_to_cart_action', array( $this, 'wc_cruise_validate_cart' ), 1, 5 );
        
    }
    
    function wc_cruise_validate_cart() {
        var_dump($cart_item_meta);
        exit;
        return;
    }


    function wc_cruise_add_cart_item_data($cart_item_meta, $product_id) {
        global $woocommerce;
        $guests = $_POST['attribute_pa_guests'];
        switch (intval($guests)) {
          case 1:
            $custom_price = get_post_meta($product_id, '_cruise_price_single', true);
          break;
          case 2:
            $custom_price = get_post_meta($product_id, '_cruise_price_double', true);
          break;
          case 3:
            $custom_price = get_post_meta($product_id, '_cruise_price_triple', true);
          break;
          case 4:
            $custom_price = get_post_meta($product_id, '_cruise_price_quad', true);
          break;
          case 5:
            $custom_price = get_post_meta($product_id, '_cruise_price_quint', true);
          break;
        }
        $booking_price = $custom_price;
        $base_price = get_post_meta($product_id, '_price', true);
        $guests = get_post_meta($product_id, 'attribute_pa_guests', true);
        $cart_item_meta['_new_price'] = $custom_price;
        $cart_item_meta['_guests'] = $guests;
        //wp_send_json($cart_item_meta);
        // var_dump($cart_item_meta);
        // die;
        // $this->wc_cruise_reset_product_meta( $product_id, $base_price, $price );
        
        return $cart_item_meta;
    }
 
    function wc_cruise_get_cart_item_from_session($cart_item, $values) {
        // Add the form options meta to the cart item in case you want to do special stuff on the check out page.
        $guests = intval($values['variation']['attribute_pa_guests']);
        //wp_send_json();
        
        switch ($guests) {
          case 1:
            $custom_price = intval($cart_item['data']->product_custom_fields['_cruise_price_single'][0]);
          break;
          case 2:
            $custom_price = intval($cart_item['data']->product_custom_fields['_cruise_price_double'][0]);
          break;
          case 3:
            $custom_price = intval($cart_item['data']->product_custom_fields['_cruise_price_triple'][0]);
          break;
          case 4:
            $custom_price = intval($cart_item['data']->product_custom_fields['_cruise_price_quad'][0]);
          break;
          case 5:
            $custom_price = intval($cart_item['data']->product_custom_fields['_cruise_price_quint'][0]);
          break;
        }
        
        $cart_item['data']->set_price($custom_price*$guests);
        //wp_send_json($cart_item);
        //$this->wc_cruise_add_cart_item($cart_item);
     
        return $cart_item;
    }

    // Reset meta data after adding to cart
    // function wc_cruise_reset_product_meta( $product_id, $base_price, $price) {

    //     if ( get_post_meta( $product_id, '_new_price', true ) ) {
    //         update_post_meta($product_id, '_new_price', $base_price);
    //     }

    //     if ( get_post_meta( $product_id, '_guests', true ) ) {
    //         delete_post_meta($product_id, '_guests', $price);
    //     }

    // }
 
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
        $product_id = $cart_item['product_id'];
        $guests = $_POST['attribute_pa_guests'];
        switch (intval($guests)) {
          case 1:
            $custom_price = get_post_meta($product_id, '_cruise_price_single', true);
          break;
          case 2:
            $custom_price = get_post_meta($product_id, '_cruise_price_double', true);
          break;
          case 3:
            $custom_price = get_post_meta($product_id, '_cruise_price_triple', true);
          break;
          case 4:
            $custom_price = get_post_meta($product_id, '_cruise_price_quad', true);
          break;
          case 5:
            $custom_price = get_post_meta($product_id, '_cruise_price_quint', true);
          break;
        }
        $booking_price = $custom_price;
        //wp_send_json($custom_price*$guests);
        $cart_item['data']->set_price($custom_price*$guests);
        //wp_send_json($booking_price);
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
