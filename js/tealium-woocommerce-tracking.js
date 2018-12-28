var teal_current_prod_variation;
var teal_variation_changed_pageload=false;

// track add to cart events for simple products in product lists
jQuery( document ).on( 'click', '.add_to_cart_button:not(.product_type_variable, .product_type_grouped, .single_add_to_cart_button)', function() {
    var productdata = jQuery( this ).closest( '.product' ).find( '.teal_productdata' );

    utag.link({
        'tealium_event': 'cart_add',
        'product_name':       [productdata.data( 'teal_product_name' )],
        'product_id':         [productdata.data( 'teal_product_id' ).toString()],
        'product_unit_price': [productdata.data( 'teal_product_price' ).toString()],
        'product_category':   [productdata.data( 'teal_product_cat' )],
        'product_stocklevel': [productdata.data( 'teal_product_stocklevel' ).toString()],
        'product_quantity':   ["1"]
    });
});

// track add to cart events for products on product detail pages
jQuery( document ).on( 'click', '.single_add_to_cart_button', function() {
    var _product_form       = jQuery( this ).closest( 'form.cart' );
    var _product_var_id     = jQuery( '[name=variation_id]', _product_form );
    var _product_id         = jQuery( '[name=tealium_id]', _product_form ).val();
    var _product_name       = jQuery( '[name=tealium_name]', _product_form ).val();
    var _product_sku        = jQuery( '[name=tealium_sku]', _product_form ).val();
    var _product_category   = jQuery( '[name=tealium_category]', _product_form ).val();
    var _product_price      = jQuery( '[name=tealium_price]', _product_form ).val();
    var _product_currency   = jQuery( '[name=tealium_currency]', _product_form ).val();
    var _product_stocklevel = jQuery( '[name=tealium_stocklevel]', _product_form ).val();

    if ( _product_var_id.length > 0 ) {
        if ( teal_current_prod_variation ) {
            var teal_data = {}
            teal_data.tealium_event = "cart_add";
            teal_data.currency_code = _product_currency;
            teal_data.product_quantity = [jQuery( 'form.cart:first input[name=quantity]' ).val()];
            var prod_data = Object.assign({}, teal_data, teal_current_prod_variation);
            utag.link(prod_data);
        }
    } else {
        utag.link({
            'tealium_event': 'cart_add',
            'currency_code' : _product_currency,
            'product_id': _product_id,
            'product_name': _product_name,
            'product_unit_price': _product_price,
            // 'category': _product_category,
            'product_quantity': jQuery( 'form.cart:first input[name=quantity]' ).val(),
            'product_stocklevel': _product_stocklevel
        });
    }
});

// track remove links in mini cart widget and on cart page
jQuery( document ).on( 'click', '.mini_cart_item a.remove,.product-remove a.remove', function() {
    var productdata = jQuery( this );

    var qty = 0;
    var qty_element = jQuery( this ).closest( '.cart_item' ).find( '.product-quantity input.qty' );
    if ( qty_element.length === 0 ) {
        qty_element = jQuery( this ).closest( '.mini_cart_item' ).find( '.quantity' );
        if ( qty_element.length > 0 ) {
            qty = parseInt( qty_element.text() );

            if ( Number.isNaN( qty ) ) {
                qty = 0;
            }
        }
    } else {
        qty = qty_element.val();
    }

    if ( qty === 0 ) {
        return true;
    }

    utag.link({
        'tealium_event': 'cart_remove',
        'name':       productdata.data( 'gtm4wp_product_name' ),
        'id':         productdata.data( 'gtm4wp_product_id' ),
        'price':      productdata.data( 'gtm4wp_product_price' ),
        'category':   productdata.data( 'gtm4wp_product_cat' ),
        'variant':    productdata.data( 'gtm4wp_product_variant' ),
        'stocklevel': productdata.data( 'gtm4wp_product_stocklevel' ),
        'quantity':   qty
    });
});

jQuery( document ).on( 'found_variation', function( event, product_variation ) {
    if ( "undefined" == typeof product_variation ) {
        // some plugins trigger this event without variation data
        return;
    }

    if ( (document.readyState === "interactive") && teal_variation_changed_pageload ) {
        // some custom attribute rendering plugins fire this event multiple times during page load
        return;
    }

    var _product_form       = event.target;
    var _product_var_id     = jQuery( '[name=variation_id]', _product_form );
    var _product_id         = jQuery( '[name=tealium_id]', _product_form ).val();
    var _product_name       = jQuery( '[name=tealium_name]', _product_form ).val();
    var _product_sku        = jQuery( '[name=tealium_sku]', _product_form ).val();
    var _product_category   = jQuery( '[name=tealium_category]', _product_form ).val();
    var _product_price      = jQuery( '[name=tealium_price]', _product_form ).val();
    var _product_currency   = jQuery( '[name=tealium_currency]', _product_form ).val();
    var _product_stocklevel = jQuery( '[name=tealium_stocklevel]', _product_form ).val();

    var current_product_detail_data   = {
        product_name: [_product_name],
        product_id: 0,
        product_unit_price: 0,
        product_category: [_product_category],
        product_stocklevel: [_product_stocklevel],
        product_variant: ''
    };

    current_product_detail_data.product_id = [product_variation.variation_id.toString()];

    // Use Sku Instead Setting
    // if ( gtm4wp_use_sku_instead && product_variation.sku && ('' !== product_variation.sku) ) {
    //     current_product_detail_data.id = product_variation.sku;
    // }

    current_product_detail_data.product_unit_price = [product_variation.display_price.toString()];

    var _tmp = [];
    for( var attrib_key in product_variation.attributes ) {
        _tmp.push( product_variation.attributes[ attrib_key ] );
    }
    current_product_detail_data.product_variant = _tmp;
    teal_current_prod_variation = current_product_detail_data;

    // Event for changing the product details
    // utag.link({
    //     'event': 'gtm4wp.changeDetailViewEEC',
    //     'ecommerce': {
    //         'currencyCode': _product_currency,
    //         'detail': {
    //             'products': [current_product_detail_data]
    //         },
    //     },
    //     'ecomm_prodid': gtm4wp_id_prefix + current_product_detail_data.id,
    //     'ecomm_pagetype': 'product',
    //     'ecomm_totalvalue': current_product_detail_data.price,
    // });

    if ( document.readyState === "interactive" ) {
        teal_variation_changed_pageload = true;
    }
});