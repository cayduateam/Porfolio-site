<?php
/**
 * WooCommerce Manual Orders
 * Nguyen Tesing
 */


// The customer needs to add the order in the back end of the site for phone/fax/in store orders.
// Phần này thì dùng các hook có sẵn của Woo để render và save lại
add_action( 'woocommerce_admin_order_data_after_order_details', 'custom_order_metas' );

function custom_order_metas( $order ) {
    if( ! ( is_admin() ) ) {
    	return;
    }

	$custom_phone = get_post_meta( $order->get_id(), 'custom_phone', true );
	$custom_fax = get_post_meta( $order->get_id(), 'custom_fax', true );
	?>
		<br class="clear" />
		<h3>Custom metas</h3>
		<div class="custom-block">
		<?php
			woocommerce_wp_text_input( array(
				'id' 	=> 'custom_phone',
				'label' => 'Phone',
				'value' => $custom_phone,
				'wrapper_class' => 'form-field-wide',
			) );

			woocommerce_wp_text_input( array(
				'id' 	=> 'custom_fax',
				'label' => 'Fax',
				'value' => $custom_fax,
				'wrapper_class' => 'form-field-wide',
			) );
        ?>
		</div>
<?php }


add_action( 'woocommerce_process_shop_order_meta', 'save_custom_order_metas_details' );

function save_custom_order_metas_details( $order_id ) {
	update_post_meta( $order_id, 'custom_phone', wc_clean( $_POST[ 'custom_phone' ] ) );
	update_post_meta( $order_id, 'custom_fax', wc_clean( $_POST[ 'custom_fax' ] ) );
	update_post_meta( $order_id, '_transaction_id', 111222 );

}

// Credit card payment when adding new order in the back end. WooCommerce does not have credit card charge when adding new order in the back end. After the payment is successful, it automatically adds the transaction ID to the order.
//Phần này mình nghĩ có thể có nhiều cách làm tùy thuộc vào quy trình checkout nhưng theo mình thì có thể dùng woocommerce_payment_complete để lấy order_id đã được tạo mới, callback lúc này sẽ kết nối API để payment và lưu vào transaction ID của order.

add_action( 'woocommerce_payment_complete', 'order_payment_complete' );

function order_payment_complete( $order_id ){
    $order = wc_get_order( $order_id );
    
    //create an API session to payment

    //get API response

    //save order meta
    wc_add_order_item_meta($key, '_transaction_id', $API_ID );

}

// WooCommerce Manual Orders with Multiple Shipping Addresses
// The customer needs an ability to add multiple packages shipping addresses per order.
// From the screenshot, we need to a button/checkbox to activate multiple shipping addresses feature. After that, it will allow customer to choose address and add items per address and then pay the whole order at once.

//Phần này phức tạp, tạo multi addresses và gán product của order vào nó nữa là 1 ý tưởng hay và phức tạp 
// Nó cần code tốt và UI/UX tốt nên mình sẽ trình bày ý tưởng là

// Trong phần admin order (hay payment) có 1 button enable multiple addresses, 1 trang mới sẽ hiện ra để thêm xóa sửa address rồi lưu lại, sau đó trong mỗi item của order sẽ có item_meta cho phép chọn address
// tương tự như hình này https://2.pik.vn/20217de14dcc-0869-4817-be83-e324ce9f1c18.png

// hoặc thêm 1 item meta ghi address vào, nhưng cách này ko quản lý address được