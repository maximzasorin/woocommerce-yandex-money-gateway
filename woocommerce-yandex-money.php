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

		foreach ( $this->settings as $setting_key => $value ) {
			$this->$setting_key = $value;
		}
		
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
			'notification_secret' => array(
				'title'		=> __( 'Notification secret code'),
				'type'		=> 'text'
			)
		);		
	}
}