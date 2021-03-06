<?php
/**
 * Tealium Data Layer Wordpress Plugin
 * @link              https://github.com/jaredhislop30
 * @since             0.1.0
 * @package           Tealium Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Tealium
 * Plugin URI:        https://github.com/jaredhislop30/tealium-wordpress-plugin
 * Description:       Tealium Plugin
 * Version:           3.0.0
 * Author:            Ian Hampton
 * Author URI:        https://github.com/jaredhislop30
 * License:           Public
 * Text Domain:       Tealium
 * GitHub Plugin URI: https://github.com/jaredhislop30/tealium-wordpress-plugin
 *
 */
/*
Plugin Name: Tealium
Plugin URI: http://tealium.com
Description: TEMP - Adds the Tealium tag and creates a data layer for your WordPress site.
Version: 3.0.0
Author: Ian Hampton
Author URI: http://tealium.com
Text Domain: tealium
Domain Path: /languages
*/

define( 'TEAL_FILE_VERSION',    '0.0.1' );
define( 'DIR_PATH',       plugin_dir_path( __FILE__ ) );

$teal_globals['plugin_url'] = plugin_dir_url( __FILE__ );
$teal_globals['$teal_plugin_basename'] = plugin_basename( __FILE__ );
// $teal_globals['woo_enabled'] = in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
$teal_globals['woo_enabled'] = get_option( 'tealiumIncludeWooCommerceTracking' );
$teal_globals['teal_cart_item_proddata'] = '';


function activate_tealium() {

    // Only set data style to underscore for fresh installations
    if ( !get_option( 'tealiumTag' ) ) {
        update_option( 'tealiumDataStyle', '1' );
    }
    else {
        add_option( 'tealiumDataStyle', '' );
    }

    add_option( 'tealiumTag', '' );
    add_option( 'tealiumTagCode', '' );
    add_option( 'tealiumTagLocation', '' );
    add_option( 'tealiumExclusions', '' );
    add_option( 'tealiumAccount', '' );
    add_option( 'tealiumProfile', '' );
    add_option( 'tealiumEnvironment', '' );
    add_option( 'tealiumTagType', '' );
    add_option( 'tealiumCacheBuster', '' );
    add_option( 'tealiumUtagSync', '' );
    add_option( 'tealiumDNSPrefetch', '1' );
    add_option( 'tealiumEUOnly', '' );
    add_option( 'tealiumExcludeMetaData', '1' );
    add_option( 'tealiumIncludeWooCommerceTracking', '1' );
    add_option( 'tealiumTrackCustomerData', '1' );
    add_option( 'tealiumNamespace', '' );
}

function deactive_tealium() {
    delete_option( 'tealiumExclusions' );
    delete_option( 'tealiumDataStyle', '' );
    delete_option( 'tealiumTagLocation' );
    delete_option( 'tealiumTagCode' );
    delete_option( 'tealiumTag' );
    delete_option( 'tealiumAccount' );
    delete_option( 'tealiumProfile' );
    delete_option( 'tealiumEnvironment' );
    delete_option( 'tealiumTagType' );
    delete_option( 'tealiumCacheBuster' );
    delete_option( 'tealiumUtagSync' );
    delete_option( 'tealiumDNSPrefetch' );
    delete_option( 'tealiumEUOnly' );
    delete_option( 'tealiumExcludeMetaData' );
    delete_option( 'tealiumIncludeWooCommerceTracking' );
    delete_option( 'tealiumTrackCustomerData' );
    delete_option( 'tealiumNamespace' );
}

function admin_init_tealium() {
    register_setting( 'tealiumTagBasic', 'tealiumAccount' );
    register_setting( 'tealiumTagBasic', 'tealiumProfile' );
    register_setting( 'tealiumTagBasic', 'tealiumEnvironment' );
    register_setting( 'tealiumTagAdvanced', 'tealiumTagCode' );
    register_setting( 'tealiumTagAdvanced', 'tealiumTagLocation' );
    register_setting( 'tealiumTagAdvanced', 'tealiumDataStyle' );
    register_setting( 'tealiumTagAdvanced', 'tealiumExclusions' );
    register_setting( 'tealiumTagAdvanced', 'tealiumTagType' );
    register_setting( 'tealiumTagAdvanced', 'tealiumCacheBuster' );
    register_setting( 'tealiumTagAdvanced', 'tealiumUtagSync' );
    register_setting( 'tealiumTagAdvanced', 'tealiumDNSPrefetch' );
    register_setting( 'tealiumTagAdvanced', 'tealiumEUOnly' );
    register_setting( 'tealiumTagAdvanced', 'tealiumExcludeMetaData' );
    register_setting( 'tealiumTagAdvanced', 'tealiumIncludeWooCommerceTracking' );
    register_setting( 'tealiumTagAdvanced', 'tealiumTrackCustomerData' ); 
    register_setting( 'tealiumTagAdvanced', 'tealiumNamespace' );

    wp_register_style( 'tealium-stylesheet', plugins_url( 'tealium.css', __FILE__ ) );
}

function admin_menu_tealium() {
    $page = add_options_page( __( 'Tealium Tag Settings', 'tealium' ), __( 'Tealium Settings', 'tealium' ), 'manage_options' , 'tealium', 'options_page_tealium' );
    add_action( 'admin_print_styles-' . $page, 'admin_styles_tealium' );
}

function options_page_tealium() {
    include plugin_dir_path( __FILE__ ).'tealium.options.php';
}

function admin_styles_tealium() {
    wp_enqueue_style( 'tealium-stylesheet' );
}

function load_plugin_textdomain_tealium() {
    load_plugin_textdomain( 'tealium', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_plugin_textdomain_tealium' );

/*
 * Admin messages
 */
function admin_notices_tealium() {
    global $pagenow;
    $currentScreen = get_current_screen();
    $tealiumTagCode = get_option( 'tealiumTagCode' );
    $tealiumAccount = get_option( 'tealiumAccount' );
    $tealiumProfile = get_option( 'tealiumProfile' );
    $tealiumEnvironment = get_option( 'tealiumEnvironment' );

    // Add an admin message when looking at the plugins page if the Tealium tag is not found
    if ( $pagenow == 'plugins.php' ) {
        if ( empty( $tealiumTagCode ) && ( empty( $tealiumAccount ) || empty( $tealiumProfile ) || empty( $tealiumEnvironment ) ) ) {
            $html = '<div class="updated">';
            $html .= '<p>';
            $html .= sprintf( __( 'Please enter your Tealium account details or tag code <a href="%s">over here &raquo;</a>', 'tealium' ), esc_url( 'options-general.php?page=tealium' ) );
            $html .= '</p>';
            $html .= '</div>';
            echo $html;
        }
    }

    // Add an error message if utag.sync is enabled but no account is specified
    if ( $currentScreen->base == 'settings_page_tealium' ) {
        $utagSync = get_option( 'tealiumUtagSync' );
        if ( "1" == $utagSync ) {
            if ( empty( $tealiumAccount ) || empty( $tealiumProfile ) || empty( $tealiumEnvironment ) ) {
                $html = '<div class="error">';
                $html .= '<p>';
                $html .= 'You must provide account/profile/environment details to use utag.sync.js.';
                $html .= '</p>';
                $html .= '</div>';
                echo $html;
            }
        }
    }

    // Add an error message if the cache buster is enabled but no account is specified
    if ( $currentScreen->base == 'settings_page_tealium' ) {
        $tealiumCacheBuster = get_option( 'tealiumCacheBuster' );
        if ( "1" == $tealiumCacheBuster ) {
            if ( empty( $tealiumAccount ) || empty( $tealiumProfile ) || empty( $tealiumEnvironment ) ) {
                $html = '<div class="error">';
                $html .= '<p>';
                $html .= 'You must provide account/profile/environment details to use a cache buster.';
                $html .= '</p>';
                $html .= '</div>';
                echo $html;
            }
        }
    }
}

/*
 * Removes exclusions listed in admin setting
 */
function tealiumRemoveExclusions( $utagdata ) {
    $exclusions = get_option( 'tealiumExclusions' );
    if ( !empty( $exclusions ) ) {

        // Convert list to array and trim whitespace
        $exclusions = array_map( 'trim', explode( ',', $exclusions ) );

        foreach ( $exclusions as $exclusion ) {
            if ( array_key_exists( $exclusion, $utagdata ) ) {
                // Remove from utag data array
                unset( $utagdata[$exclusion] );
            }
        }
    }
    return $utagdata;
}
add_filter( 'tealium_removeExclusions', 'tealiumRemoveExclusions' );


/*
 * Convert camel case to underscores
 */
function tealiumConvertCamelCase( $utagdata, $arrayHolder = array() ) {
    $underscoreArray = !empty( $arrayHolder ) ? $arrayHolder : array();
    foreach ( $utagdata as $key => $val ) {
        $newKey = preg_replace( '/[A-Z]/', '_$0', $key );
        $newKey = strtolower( $newKey );
        $newKey = ltrim( $newKey, '_' );
        if ( !is_array( $val ) ) {
            $underscoreArray[$newKey] = $val;
        } else if ( isset( $underscoreArray[$newKey] ) ) {
            $underscoreArray[$newKey] = tealiumConvertCamelCase( $val, $underscoreArray[$newKey] );
        } else if ( isset( $underscoreArray[$key] ) ) {
            $underscoreArray[$newKey] = tealiumConvertCamelCase( $val, $underscoreArray[$key] );
        } else {
            $underscoreArray[$newKey] = $val;
        }
    }
    return $underscoreArray;
}
add_filter( 'tealium_convertCamelCase', 'tealiumConvertCamelCase' );

/*
*  Add product data for each product available.
*/
function getProductData($prodID,$productData,$cartItem){
    global $product;

    if(!isset($productData)){
        $productData = array();
    }

    $product = wc_get_product( $prodID );
    $productData['product_id'][] = strval($product->get_id());
    $productData['product_sku'][] = $product->get_sku();
    $productData['product_type'][] = $product->get_type();
    $productData['product_name'][] = $product->get_name();
    $productData['product_brand'][] = $product->get_attribute('brand');
    $productData['product_unit_price'][] = $product->get_price();
    $productData['product_list_price'][] = $product->get_regular_price();
    $productData['product_image_url'][] = get_the_post_thumbnail_url( $product->get_id(), 'full' );
    $productData['product_quantity'][] = strval($cartItem['quantity']);
    $productData['product_url'][] = get_permalink( $product->get_id() );
    $productData['product_stocklevel'][] = strval($product->get_stock_quantity());
    $productData['product_currency'][] = get_woocommerce_currency();
    if($product->get_regular_price() != $product->get_price() && ($product->get_regular_price() !== "" && null !==$product->get_regular_price())){
        $productData['product_discount'][] = strval((float)$product->get_regular_price() - (float)$product->get_price());
    }else{
        $productData['product_discount'][] = "0";
    }

    //Set Product Categories
    $cats = explode(",", wc_get_product_category_list($product->get_id()));
    $productData['product_category'][] = strip_tags($cats[0]);
    $productData['product_subcategory'][] = isset($cats[1])?strip_tags($cats[1]):"";
    $productData['product_subcategory1'][] = isset($cats[2])?strip_tags($cats[2]):"";

    // Can add more categories if needed
    // $productData['product_subcategory2'][] = strip_tags($cats[3]);
    // $productData['product_subcategory3'][] = strip_tags($cats[4]);

    return $productData;
}


/*
 * Adds WooCommerce data to data layer
 */
function tealiumWooCommerceData( $utagdata ) {
    global $woocommerce;
    global $post;
    global $product;
    global $wp;

    $items = $woocommerce->cart->get_cart();

    //Get Cart Details on Each Page
    $woocart =array();
    $cart_total_value = 0;
    $cart_total_items = 0;

    foreach($items as $key => $value){
        $cart_total_value += $items[$key]['line_total'];
        $cart_total_items += $items[$key]['quantity'];
    }

    $woocart['cart_total_items'] = (string)$cart_total_items;
    $woocart['cart_total_value'] = (string)$cart_total_value;


    $productData = array();

    // Remove the extensive individual product details
    unset( $woocart['cart_contents'] );
    unset( $woocart['cart_session_data'] );
    unset( $woocart['tax'] );

    // Get currency in use
    $woocart['site_currency'] = get_woocommerce_currency();

    // Add order data on order confirmation page
    if ( is_order_received_page() ) {
        $utagdata['pageName'] = "checkout - order confirmation";
        $utagdata['checkout_step'] = "3";
        $utagdata['pageType'] = "checkout";
        $utagdata['siteSection'] = "checkout";
        $utagdata['tealium_event'] = "purchase";
        $orderId  = apply_filters( 'woocommerce_thankyou_order_id', empty( $_GET['order'] ) ? ( $GLOBALS["wp"]->query_vars["order-received"] ? $GLOBALS["wp"]->query_vars["order-received"] : 0 ) : absint( $_GET['order'] ) );
        $orderKey = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : woocommerce_clean( $_GET['key'] ) );
        $orderData = array();

        if ( $orderId > 0 ) {
            $order = new WC_Order( $orderId );
            if ( $order->order_key != $orderKey ) {
                unset( $order );
            }
        }

        if ( isset( $order ) ) {
            $orderData["order_id"] = strval($order->get_id());
            $orderData["order_currency_code"] = $order->get_currency();
            $orderData["order_discount_amount"] = $order->get_discount_total();
            $orderData["order_grand_total"] = $order->get_total();
            $orderData["order_subtotal"] = strval(round($order->get_subtotal(),2));
            $orderData["order_payment_type"] = $order->payment_method_title;
            $orderData["order_promo_code"] = implode( ", ", $order->get_used_coupons() );
            $orderData["order_shipping_amount"] = $order->get_shipping_total();
            $orderData["order_shipping_type"] = $order->get_shipping_method();
            $orderData["order_tax_amount"] = $order->get_total_tax();

            // Customer Data
            $orderData["customer_city"] = $order->get_billing_city();
            $orderData["customer_country"] = $order->get_billing_country();
            $orderData["customer_email"] = $order->get_billing_email();
            $orderData["customer_first_name"] = $order->get_billing_first_name();
            $orderData["customer_id"] = $order->get_customer_id();
            $orderData["customer_last_name"] = $order->get_billing_last_name();
            $orderData["customer_postal_code"] = $order->get_billing_postcode();
            $orderData["customer_state"] = $order->get_billing_state();

            //get Product Items
            $items = $order->get_items();
                foreach ( $items as $item ) {
                    $orderData = getProductData($item['product_id'],$orderData,$item);  
                }
        }

        $utagdata = array_merge( $utagdata, $orderData );
    // Add product data on product details page 
    }else if($utagdata['pageType'] == "product" && $utagdata['pageType'] != "category"){
        $utagdata['site_section'] = "shop";
        $utagdata['tealium_event'] = "product_view";
        $productData = getProductData($post->ID,$productData,null);

    // Add cart data on Cart Page 
    }else if($utagdata['pageType'] == "page" && ($utagdata['pageName'] == "Cart" || $utagdata['pageName'] == "Checkout")){
        $utagdata['checkout_step'] = "1";
        $utagdata['checkout_step_name'] = "cart";
        $utagdata['pageType'] = "cart";
        $utagdata['siteSection'] = "checkout";
        $utagdata['tealium_event'] = "cart_view";

        if($utagdata['pageName'] == "Checkout"){
            $utagdata['pageName'] = "checkout - billing information";
            $utagdata['checkout_step'] = "2";
            $utagdata['checkout_step_name'] = "billing information";
            $utagdata['pageType'] = "checkout";
            $utagdata['siteSection'] = "checkout";
            $utagdata['tealium_event'] = "checkout";
        }

        //Get Cart Contents
        $woocomCart = (array) $woocommerce->cart;
        
        // Set cart contents.
        //TODO Remove this line:
        // $utagdata['cartContents'] = $woocomCart['cart_contents'];
        // $utagdata['cartItems'] = $items;
        if ( !empty( $woocomCart['cart_contents'] ) ) {
            $test = array();
            // Get cart product IDs, SKUs, Titles etc.
            foreach ( $woocomCart['cart_contents'] as $cartItem ) {
                $productData = getProductData($cartItem['product_id'],$productData,$cartItem);  
            }
        }
    }


    // Merge shop and cart details into utagdata
    $utagdata = array_merge( $utagdata, $woocart );
    $utagdata = array_merge( $utagdata, $productData );

    return $utagdata;
}
add_filter( 'tealium_wooCommerceData', 'tealiumWooCommerceData' );

/*
 * Creates the data object as an array
 */
function tealiumDataObject() {
    global $utagdata,$teal_globals;
    $utagdata = array();

    // Set Default Data. May be overwritten below / later
    $utagdata['siteName'] = get_bloginfo( 'name' );
    $utagdata['siteDescription'] = get_bloginfo( 'description' );
    $utagdata['languageCode'] = explode("_",get_locale())[0];
    $utagdata['countryCode'] = strtolower(explode("_",get_locale())[1]);
    $utagdata['pageName'] = get_the_title();
    $utagdata['tealium_event'] = "page_view";
    $utagdata['currencyCode'] = get_woocommerce_currency();

    //Track if user is logged in
    if( "1" == get_option('tealiumTrackCustomerData' )){
        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
             if ( $current_user->exists() ) {
                $utagdata['userLogin'] = $current_user->user_login;
                $utagdata['userEmail'] = $current_user->user_email;
                $utagdata['userDisplayName'] = $current_user->display_name;
                $utagdata['userId'] = $current_user->ID;
             }
        }
    }
    

    if ( ( is_single() ) || is_page() ) {
        global $post;

        // Get categories
        $categories = get_the_category();
        $catout = array();

        if ( $categories ) {
            foreach ( $categories as $category ) {
                $catout[] = $category->slug;
            }
            $utagdata['postCategory'] = $catout;
        }

        // Get tags
        $tags = get_the_tags();
        $tagout = array();
        if ( $tags ) {
            foreach ( $tags as $tag ) {
                $tagout[] = $tag->slug;
            }
            $utagdata['postTags'] = $tagout;
        }

        // Misc post/page data
        $utagdata['pageType'] = get_post_type();
        $utagdata['postId'] = get_the_ID();
        $utagdata['pageName'] = get_the_title();
        $utagdata['postAuthor'] = get_userdata( $post->post_author )->display_name;
        $utagdata['postDate'] = get_the_time( 'Y/m/d' );
        $utagdata['postTitle'] = get_the_title();

        if ( ( is_home() ) || ( is_front_page() ) ) {
            $utagdata['pageName'] = "homepage";
            $utagdata['pageType'] = "home";
        }

        // Get and merge post meta data
        if ( "1" !== get_option( 'tealiumExcludeMetaData' ) ) {
            $meta = get_post_meta( get_the_ID() );
            if ( $meta ) {
                $utagdata = array_merge( $utagdata, $meta );
            }
        }
    }else if ( is_category() ) {
            $utagdata['pageName'] = "category-archive";
            $utagdata['postTitle'] = single_cat_title( 'Category archive: ', false );
    }else if ( is_tag() ) {
            $utagdata['pageName'] = "tag-archive";
            $utagdata['postTitle'] = single_tag_title( 'Tag archive: ', false );
    }else if ( is_archive() ) {
            $utagdata['pageType'] = "category";
            $utagdata['siteSection'] = "shop";
            //Accounts for top level "Shop" page
            if(get_the_archive_title() == "Archives: Products"){
                $utagdata['pageName'] = "shop";
            }else if(get_post_type()=="product" && strpos(get_the_archive_title(),"Category")>-1){
                $cat = explode(": ",strtolower(get_the_archive_title()));
                $utagdata['pageName'] = $cat[1];
                $utagdata['pageType'] = "archive";
                $term = get_queried_object();
                // Get product categories used for page section and categories
                if($term && isset($term->term_id)){
                    $term = get_queried_object();
                    $parent_ids = get_ancestors($term->term_id, 'product_cat');
                    //Get number of parent categories
                    $len = sizeof($parent_ids);
                    //If no parent categories then set top level category directly. 
                    if($len==0){
                        $utagdata['categoryName'] = $term->slug;
                    }else{
                        // Reverse the order of categories so the variable hierarchy matches the category hiearchy
                        $parent_ids = array_reverse($parent_ids);
                        array_push($parent_ids,$term->term_id);
                        for($ind=0; $ind < sizeof($parent_ids); $ind++){
                            if($ind==0){
                                $utagdata['categoryName'] = get_term_by( 'id', $parent_ids[$ind], 'product_cat' )->slug;
                            }else{
                                $utagdata['categoryName_'.$ind] = get_term_by( 'id', $parent_ids[$ind], 'product_cat' )->slug; 
                            }
                        }
                    }           
                }
            }
        }else if ( is_search() ) {

            global $wp_query;
            
            // Collect search and result data
            $searchQuery = get_search_query();
            $searchCount = $wp_query->found_posts;

            // Add to udo
            $utagdata['pageName'] = "search";
            $utagdata['searchKeyword'] = $searchQuery;
            $utagdata['searchResults'] = $searchCount;
            $utagdata['tealium_event'] = "search";
        }

    // Add shop data if WooCommerce is installed
    if ( "1" == $teal_globals['woo_enabled'] ) {
        $utagdata = apply_filters( 'tealium_wooCommerceData', $utagdata );
    }

    // Include data layer additions from action if set
    if ( has_action( 'tealium_addToDataObject' ) ) {
        do_action( 'tealium_addToDataObject' );
    }

    if ( get_option( 'tealiumDataStyle' ) == '1' ) {
        // Convert camel case to underscore
        $utagdata = apply_filters( 'tealium_convertCamelCase', $utagdata );
    }

    // Remove excluded keys
    $utagdata = apply_filters( 'tealium_removeExclusions', $utagdata );

    return $utagdata;
}


/*
 * Add data to product page for tracking add to cart
 */
function teal_add_to_cart() {
    global $product;

    $product_id = $product->get_id();

    $remarketing_id = $product_id;
    $product_sku    = $product->get_sku();

    $_temp_productdata = getProductData($product_id,null,null);

    foreach( $_temp_productdata as $_temp_productdata_key => $_temp_productdata_value ) {
        echo '<input type="hidden" name="tealium_' . esc_attr( $_temp_productdata_key ). '" value="' . esc_attr( $_temp_productdata_value[0] ). '" />'."\n";
    }
}

/*
 * Load data on listing pages to track add to cart
 */

function teal_product_data_on_list_page() {
    global $product;

    if ( !isset( $product ) ) {
        return;
    }

    $product_id  = $product->get_id();

    $product_cat = "";
    if ( is_product_category() ) {
        global $wp_query;
        $cat_obj = $wp_query->get_queried_object();
        $product_cat = $cat_obj->name;
    } else {
        $product_cat = get_product_category( $product_id);
    }

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $posts_per_page = get_query_var('posts_per_page');
    if ( $posts_per_page < 1 ) {
        $posts_per_page = 1;
    }

    $remarketing_id = $product_id;
    $product_sku    = $product->get_sku();

    $_temp_productdata = array(
        "product_id"           => $remarketing_id,
        "product_name"         => $product->get_title(),
        "product_unit_price"        => $product->get_price(),
        "product_category"     => $product_cat,
        "product_url"  => apply_filters( 'the_permalink', get_permalink(), 0),
        "product_stocklevel"   => $product->get_stock_quantity()
    );
    // $eec_product_array = apply_filters( teal_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "productlist" );

    printf('<span class="teal_productdata" style="display:none; visibility:hidden;" data-teal_product_id="%s" data-teal_product_name="%s" data-teal_product_price="%s" data-teal_product_cat="%s" data-teal_product_url="%s" data-teal_product_stocklevel="%s"></span>',
        esc_attr( $_temp_productdata[ "product_id" ] ),
        esc_attr( $_temp_productdata[ "product_name" ] ),
        esc_attr( $_temp_productdata[ "product_unit_price" ] ),
        esc_attr( $_temp_productdata[ "product_category" ] ),
        esc_url(  $_temp_productdata[ "product_url" ] ),
        // esc_attr( $_temp_productdata[ "listposition" ] ),
        // esc_attr( $_temp_productdata[ "listname" ] ),
        esc_attr( $_temp_productdata[ "product_stocklevel" ] )
    );
}

function teal_cart_item_product_filter( $product, $cart_item="", $cart_id="" ) {
    global $teal_globals;

    $product_id   = $product->get_id();
    $product_type = $product->get_type();

    if ( "variation" == $product_type ) {
        $parent_product_id = $product->id;
        $product_cat = get_product_category( $product_id );
    } else {
        $product_cat = get_product_category( $product_id );
    }

    $remarketing_id = $product_id;
    $product_sku    = $product->get_sku();

    $product_data = getProductData($product_id,null,null);

    if ( "variation" == $product_type ) {
        $product_data[ "product_variant" ] = implode(",", $product->get_variation_attributes());
    }else{
        $product_data[ "product_variant" ] = [""];
    }

    $teal_globals['teal_cart_item_proddata'] = $product_data;

    return $product;
}

function teal_woocommerce_cart_item_remove_link_filter( $remove_from_cart_link ) {
    global $teal_globals;

    $cartlink_with_data = sprintf('data-teal_product_id="%s" data-teal_product_name="%s" data-teal_product_price="%s" data-teal_product_cat="%s" data-teal_product_url="%s" data-teal_product_variant="%s" data-teal_product_stocklevel="%s" data-teal_product_list_price="%s" data-teal_product_sku="%s" data-teal_product_brand="%s" data-teal_product_discount="%s" data-teal_product_image_url="%s" href="',
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_id"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_name"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_unit_price"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_category"][0] ),
        esc_url(  $teal_globals['teal_cart_item_proddata']["product_url"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_variant"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_stocklevel"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_list_price"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_sku"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_brand"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_discount"][0] ),
        esc_attr( $teal_globals['teal_cart_item_proddata']["product_image_url"][0] )
    );
    $teal_globals['teal_cart_item_proddata'] = '';
    return teal_str_replace_first( 'href="', $cartlink_with_data, $remove_from_cart_link );
}

// utilizing the following source https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match
function teal_str_replace_first($from, $to, $subject) {
    $from = '/'.preg_quote($from, '/').'/';

    return preg_replace($from, $to, $subject, 1);
}

function get_product_category( $product_id) {
  $product_cat = "";

    $_product_cats = get_the_terms( $product_id, 'product_cat' );
    if ( ( is_array($_product_cats) ) && ( count( $_product_cats ) > 0 ) ) {
        $first_product_cat = array_pop( $_product_cats );
        $product_cat = $first_product_cat->name;
    }

    return $product_cat;
}

/*
 * Load JS Functions for Dynamic Event Tracking (Add to Cart, Remove from cart, etc)
 */
function tealiumWoocommerceEnqueueJS() {
    global $teal_globals;
    wp_enqueue_script( "tealium-woocommerce-tracking", $teal_globals['plugin_url'] . "/js/tealium-woocommerce-tracking.js", array( "jquery" ), "0.0.1", false);
}

/*
 * Encodes the data object array as JSON, outputs script tag
 */
function tealiumEncodedDataObject( $return = false ) {

    $utagdata = tealiumDataObject();

    // Encode data object
    if ( version_compare( phpversion(), '5.4.0', '>=' ) ) {
        // Pretty print JSON if PHP version supports it
        $jsondata = json_encode( $utagdata, JSON_PRETTY_PRINT );
    }
    else {
        $jsondata = json_encode( $utagdata );

        // Apply pretty print function
        $jsondata = tealiumPrettyPrintJSON( $jsondata );
    }
    
    // Output data object
    if ( json_decode( str_replace("\u0000*\u0000", "", $jsondata) ) !== null ) {
        
        // Get custom namespace value if set
        $tealiumNamespace = get_option( 'tealiumNamespace' , 'utag_data' );
        $tealiumNamespace = ( empty( $tealiumNamespace ) ? 'utag_data' : $tealiumNamespace );
        
        $utag_data = "<script type=\"text/javascript\">\nvar {$tealiumNamespace} = {$jsondata};\n</script>\n";
        if ( !$return ) {
            echo $utag_data;
        }
        else {
            return $utag_data;
        }
    }
}

/*
 * Pretty print JSON for PHP 5.3 and lower
 */
function tealiumPrettyPrintJSON( $json ) {
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for ( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if ( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if ( $char === '"' ) {
                $in_quotes = !$in_quotes;
            } else if ( ! $in_quotes ) {
                switch ( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
                }
            } else if ( $char === '\\' ) {
                $in_escape = true;
            }
        if ( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}

/*
 * Get the Tealium tag code, applying filters if necessary
 */
function getTealiumTagCode() {
    global $tealiumtag;
    $tealiumAdvanced = get_option( 'tealiumTagCode' );
    $tealiumAccount = get_option( 'tealiumAccount' );
    $tealiumProfile = get_option( 'tealiumProfile' );
    $tealiumEnvironment = get_option( 'tealiumEnvironment' );
    $tealiumTagType = get_option( 'tealiumTagType' );
    $tealiumCacheBuster = get_option( 'tealiumCacheBuster' );
    $cacheBuster = "";
    $tiqCDN = tealiumGetCDNURL();

    if ( ( current_user_can( 'edit_posts' ) ) && ( "1" == $tealiumCacheBuster ) ) {
        $cacheBuster = "?_cb=".time();
    }

    // Use the free text 'advanced' config if it appears to contain a tag
    if ( ( !empty( $tealiumAdvanced ) ) && ( strpos( $tealiumAdvanced, 'utag.js' ) !== false ) ) {
        $tealiumtag = $tealiumAdvanced;
    }
    else {
        if ( ( !empty( $tealiumAccount ) ) && ( !empty( $tealiumProfile ) ) && ( !empty( $tealiumEnvironment ) ) ) {
            if ( $tealiumTagType != '1' ) {
                $tealiumtag = "<!-- Loading script asynchronously -->\n";
                $tealiumtag .= "<script type=\"text/javascript\">\n";
                $tealiumtag .= " (function(a,b,c,d){\n";
                $tealiumtag .= " a='//{$tiqCDN}/utag/{$tealiumAccount}/{$tealiumProfile}/{$tealiumEnvironment}/utag.js{$cacheBuster}';\n";
                $tealiumtag .= " b=document;c='script';d=b.createElement(c);d.src=a;d.type='text/java'+c;d.async=true;\n";
                $tealiumtag .= " a=b.getElementsByTagName(c)[0];a.parentNode.insertBefore(d,a);\n";
                $tealiumtag .= " })();\n";
                $tealiumtag .= "</script>\n";
                $tealiumtag .= "<!-- END: T-WP -->\n";
            }
            else {
                $tealiumtag = "<!-- Loading script synchronously -->\n";
                $tealiumtag .= "<script type=\"text/javascript\" src=\"//{$tiqCDN}/utag/{$tealiumAccount}/{$tealiumProfile}/{$tealiumEnvironment}/utag.js{$cacheBuster}\"></script>\n";
                $tealiumtag .= "<!-- END: T-WP -->\n";
            }
        }
    }

    // Include tag action if set
    if ( has_action( 'tealium_tagCode' ) ) {
        do_action( 'tealium_tagCode' );
    }

    return $tealiumtag;
}

function outputTealiumTagCode() {
    echo getTealiumTagCode();
}

/*
 * Generate utag.sync.js tag
 */
function tealiumOutputUtagSync() {
    $tealiumAccount = get_option( 'tealiumAccount' );
    $tealiumProfile = get_option( 'tealiumProfile' );
    $tealiumEnvironment = get_option( 'tealiumEnvironment' );
    $tealiumCacheBuster = get_option( 'tealiumCacheBuster' );
    $cacheBuster = "";
    $utagSync = "";
    $tiqCDN = tealiumGetCDNURL();


    if ( ( current_user_can( 'edit_posts' ) ) && ( "1" == $tealiumCacheBuster ) ) {
        $cacheBuster = "?_cb=".time();
    }

    if ( ( !empty( $tealiumAccount ) ) && ( !empty( $tealiumProfile ) ) && ( !empty( $tealiumEnvironment ) ) ) {
        $utagSync = "<script src=\"//{$tiqCDN}/utag/{$tealiumAccount}/{$tealiumProfile}/{$tealiumEnvironment}/utag.sync.js{$cacheBuster}\"></script>\n";
    }

    echo $utagSync;
}


/*
 * Generate DNS Prefetch
 */
function tealiumOutputDNSPrefetch() {
    $dnsPrefetch = "<link rel=\"dns-prefetch\" href=\"//".tealiumGetCDNURL()."\">\n";
    echo $dnsPrefetch;
}

/*
 * Get the CDN URL based on EU setting
 */
function tealiumGetCDNURL() {
    $tiqCDN = ( "1" == get_option( 'tealiumEUOnly' ) ? "tags-eu.tiqcdn.com" : "tags.tiqcdn.com" );
    return $tiqCDN;
}

/*
 * Determine if the current page is using AMP
 */
function tealiumAMP() {
    global $wp_query;
    if ( function_exists( 'is_amp_endpoint' ) && $wp_query !== null && is_amp_endpoint() ) {
        return true;
    }
    else {
        if ( ( defined( 'AMPFORWP_VERSION' ) || defined( 'AMP__VERSION' ) ) && preg_match( '/\/amp\/?$/', $_SERVER['REQUEST_URI'] ) ) {
            return true;
        }
        return false;
    }
}


/*
 * Enable output buffer
 */
function tealiumOutputFilter( $template ) {
    ob_start();
    return $template;
}

/*
 * Used in combination with tealiumOutputFilter() to add Tealium tag after <body>
 */
function tealiumTagBody( $tealiumTagCode ) {
    $content = ob_get_clean();
    $tealiumTagCode = getTealiumTagCode();

    // Insert Tealium tag after body tag (sadly there is no wp_body hook)
    $content = preg_replace( '#<body([^>]*)>#i', "<body$1>\n\n{$tealiumTagCode}", $content, 1 );
    echo $content;
}

/*
 * Used in combination with tealiumOutputFilter() to add Tealium tag after <head>
 */
function tealiumTagHead( $tealiumTagCode ) {
    $content = ob_get_clean();
    $tealiumTagCode = getTealiumTagCode();
    $tealiumDataObject = tealiumEncodedDataObject( true );

    // Insert Tealium tag immediately after head tag
    $content = preg_replace( '#<head([^>]*)>#i', "<head$1>\n{$tealiumDataObject}\n{$tealiumTagCode}", $content, 1 );
    echo $content;
}

/*
 * Determine where the Tealium tag should be located and insert it
 */
function insertTealiumTag() {
    $tealiumTagLocation = get_option( 'tealiumTagLocation' );
    $tealiumTagCode = getTealiumTagCode();

    if ( !empty( $tealiumTagCode ) ) {
        switch ( $tealiumTagLocation ) {
        case '1':
            // Location - Header
            add_action( 'wp_head', 'outputTealiumTagCode', 10000 );
            break;
        case '2':
            // Location - Footer
            add_action( 'wp_footer', 'outputTealiumTagCode', 10000 );
            break;
        case '3':
            // Location - Header (Top)
            // Start content buffer
            add_filter( 'template_include', 'tealiumOutputFilter', 1 );
            // Inject Tealium tag, output page contents
            add_filter( 'shutdown', 'tealiumTagHead', 0 );
            break;
        case '0':
        default:
            // Location - After opening body tag
            // Start content buffer
            add_filter( 'template_include', 'tealiumOutputFilter', 1 );
            // Inject Tealium tag, output page contents
            add_filter( 'shutdown', 'tealiumTagBody', 0 );
            break;
        }
    }

    // Add utag.sync.js if required
    $utagSync = get_option( 'tealiumUtagSync' );
    if ( "1" == $utagSync ) {
        add_action( 'wp_head', 'tealiumOutputUtagSync', 2 );
    }

    // Add DNS Prefetch if required
    $dnsPrefetch = get_option( 'tealiumDNSPrefetch' );
    if ( "1" == $dnsPrefetch ) {
        add_action( 'wp_head', 'tealiumOutputDNSPrefetch', 0 );
    }
}

if ( is_admin() ) {
    register_activation_hook( __FILE__, 'activate_tealium' );
    register_deactivation_hook( __FILE__, 'deactive_tealium' );
    add_action( 'admin_init', 'admin_init_tealium' );
    add_action( 'admin_menu', 'admin_menu_tealium' );
    add_action( 'admin_notices', 'admin_notices_tealium' );
}else {
    if ( !tealiumAMP() ) {
        // Insert the Tealium tag
        add_action( 'init', 'insertTealiumTag' );
    }

    // Insert the data object
    if ( get_option( 'tealiumTagLocation' ) != '3' ) {
        add_action( 'wp_head', 'tealiumEncodedDataObject', 1 );
    }
}
if ( "1" == $teal_globals['woo_enabled']  ) {
    add_action('wp_head', 'tealiumWoocommerceEnqueueJS');
    add_action( "woocommerce_after_add_to_cart_button", "teal_add_to_cart" );
    add_action( "woocommerce_before_shop_loop_item", "teal_product_data_on_list_page" );
    add_filter( "woocommerce_cart_item_product",     "teal_cart_item_product_filter" );
    add_filter( "woocommerce_cart_item_remove_link", "teal_woocommerce_cart_item_remove_link_filter" );
}