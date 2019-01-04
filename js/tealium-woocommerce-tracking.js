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
    var prod_data_json = getProductData(jQuery( this ).closest( 'form.cart' ));

    if ( prod_data_json.product_variant.length > 0 && prod_data_json.product_variant[0] !== "" ) {
        if ( teal_current_prod_variation ) {
            var teal_data = {}
            teal_data.tealium_event = "cart_add";
            teal_data.currency_code = _product_currency;
            teal_data.product_quantity = [jQuery( 'form.cart:first input[name=quantity]' ).val()];
            var prod_data = Object.assign({}, teal_data, teal_current_prod_variation);
            utag.link(prod_data);
        }
    } else {
        prod_data_json['tealium_event'] = "cart_add";
        prod_data_json['currency_code'] = prod_data_json.product_currency;
        utag.link(prod_data_json);
    }
});

// track remove links in mini cart widget and on cart page
jQuery( document ).on( 'click', '.mini_cart_item a.remove,.product-remove a.remove', function() {
    var productdata = jQuery( this );

    var qty = ["0"];
    var qty_element = jQuery(this).closest('.cart_item').find('.product-quantity input.qty');
    if (qty_element.length === 0) {
        qty_element = jQuery(this).closest('.mini_cart_item').find('.quantity');
        if (qty_element.length > 0) {
            qty_text = qty_element.text();
            // Account for some themes that have quantities like "N x $30.00" i.e. 1 x $35.00"
            if(isNaN(qty_text) && qty_text.indexOf("$")>-1){
                qty = qty_text.split(" ")[0]
            }else{
                qty = qty_element.text();
            }
            if (isNaN(qty)) {
                qty = ["1"];
            } 
        }
    } else {
        qty = [qty_element.val()];
    }

    // if ( qty === 0 ) {
    //     return true;
    // }

    utag.link({
        'tealium_event': 'cart_remove',
        'product_name':       [productdata.data( 'teal_product_name' )],
        'product_id':         [productdata.data( 'teal_product_id' ).toString()],
        'product_unit_price': [productdata.data( 'teal_product_price' ).toString()],
        'product_category':   [productdata.data( 'teal_product_cat' )],
        'product_variant':    [productdata.data( 'teal_product_variant' ).toString()],
        'product_stocklevel': [productdata.data( 'teal_product_stocklevel' ).toString()],
        'product_quantity':   [qty.toString()]
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


    current_product_detail_data = getProductData(event.target);

    current_product_detail_data.product_id = [product_variation.variation_id.toString()];

    current_product_detail_data.product_unit_price = [product_variation.display_price.toString()];

    var _tmp = [];
    for( var attrib_key in product_variation.attributes ) {
        _tmp.push( product_variation.attributes[ attrib_key ] );
    }
    current_product_detail_data.product_variant = _tmp;
    teal_current_prod_variation = current_product_detail_data;

    // Event for changing the product details
    current_product_detail_data['tealium_event'] = "product_variation";
    utag.link(current_product_detail_data);

    if ( document.readyState === "interactive" ) {
        teal_variation_changed_pageload = true;
    }
});

function getProductData(data){
    var _product_form = data;
    var _product_var_id     = jQuery( '[name=variation_id]', _product_form ).length ? jQuery( '[name=variation_id]', _product_form ) : "";
    var _product_id         = jQuery( '[name=tealium_product_id]', _product_form ).val();
    var _product_name       = jQuery( '[name=tealium_product_name]', _product_form ).val();
    var _product_sku        = jQuery( '[name=tealium_product_sku]', _product_form ).val();
    var _product_category   = jQuery( '[name=tealium_product_category]', _product_form ).val();
    var _product_unit_price = jQuery( '[name=tealium_product_unit_price]', _product_form ).val();
    var _product_list_price = jQuery( '[name=tealium_product_list_price]', _product_form ).val();
    var _product_url        = jQuery( '[name=tealium_product_url]', _product_form ).val();
    var _product_image_url  = jQuery( '[name=tealium_product_image_url]', _product_form ).val();
    var _product_currency   = jQuery( '[name=tealium_product_currency]', _product_form ).val() ? jQuery( '[name=tealium_currency]', _product_form ).val() : "";
    var _product_stocklevel = jQuery( '[name=tealium_product_stocklevel]', _product_form ).val();
    var _product_discount   = jQuery( '[name=tealium_product_discount]', _product_form ).val();

    var product_data   = {
        product_name: [_product_name],
        product_id: _product_id,
        product_sku: [_product_sku],
        product_category: [_product_category],
        product_unit_price: [_product_unit_price],
        product_list_price: [_product_list_price],
        product_stocklevel: [_product_stocklevel],
        product_url : [_product_url],
        product_image_url : [_product_image_url],
        product_discount : [_product_discount],
        product_variant: [_product_var_id],
        product_currency : [_product_currency]
    }

    return product_data;

}