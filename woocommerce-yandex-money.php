<?php

class WC_Yandex_Money_Gateway extends WC_Payment_Gateway {
	function __construct() {
		$this->id = 'wc_yandex_money_gateway';
		$this->method_title = __( 'WC Yandex.Money Gateway');
		$this->method_description = __( 'Yandex.Money Gateway Plugin for WooCommerce');
		$this->title = __( 'WC Yandex.Money Gateway');
		$this->icon = null;
		$this->has_fields = true;
		$this->supports = array();

		$this->init_form_fields();
		$this->init_settings();

		$this->title = $this->get_option('title');
		$this->description = $this->get_option('description');
		$this->wallet_number = $this->get_option('wallet_number');
		$this->formcomment = $this->get_option('formcomment');
		$this->notification_secret = $this->get_option('notification_secret');

		$this->url = 'https://money.yandex.ru/quickpay/confirm.xml';

		foreach ( $this->settings as $setting_key => $value ) {
			$this->$setting_key = $value;
		}
		
		add_action( 'woocommerce_receipt_' . $this->id, array($this, 'receipt_page') );
		add_action( 'woocommerce_api_wc_' . $this->id, array($this, 'check_ipn_response') );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// Save settings
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}		
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'		=> __( 'Enable / Disable'),
				'label'		=> __( 'Enable this payment gateway'),
				'type'		=> 'checkbox',
				'default'	=> 'no',
			),
			'title' => array(
				'title'		=> __( 'Title'),
				'type'		=> 'text',
				'desc_tip'	=> __( 'Payment title the customer will see during the checkout process.'),
				'default'	=> __( 'Yandex.Money Gateway'),
			),
			'description' => array(
				'title'		=> __( 'Description'),
				'type'		=> 'textarea',
				'desc_tip'	=> __( 'Payment description the customer will see during the checkout process.'),
				'default'	=> __( 'Pay using your credit card via Yandex.')
			),
			'wallet_number' => array(
				'title'		=> __( 'Wallet number'),
				'type'		=> 'text'
			),
			'formcomment' => array(
				'title'		=> __( 'Form Comment'),
				'type'		=> 'text'
			),
			'notification_secret' => array(
				'title'		=> __( 'Notification secret code'),
				'type'		=> 'text'
			)
		);
	}

	public function generate_form($order_id) {
		$order = new WC_Order( $order_id );

		$order_name = __('Order No. ') . $order_id;

		$args = array(
				'receiver' => $this->wallet_number,
				'formcomment' => $this->formcomment,
				'short-dest' => $order_name,
				'quickpay-form' => 'shop',
				'targets' => $order_name,
				'sum' => $order->order_total,
				'paymentType' => 'AC',
			);

		$paypal_args = apply_filters('woocommerce_robokassa_args', $args);

		$args_array = array();

		foreach ($args as $key => $value) {
			$args_array[] = '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'" />';
		}

		return
			'<form action="'.esc_url($this->url).'" method="POST" class="order_actions">'."\n".
			implode("\n", $args_array).
			'<input type="submit" class="button alt" value="'.__('Pay').'" /> <a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Cancel Order').'</a>'."\n".
			'</form>';
	}

	public function process_payment($order_id) {
		$order = new WC_Order($order_id);

		return array(
			'result' => 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
		);
	}

	public function receipt_page($order) {
		print '<p>'.__('Thank you for your order, push button below to pay.', 'woocommerce').'</p>';
		print $this->generate_form($order);
	}
}