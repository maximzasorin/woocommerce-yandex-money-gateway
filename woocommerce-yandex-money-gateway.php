<?php

/*
	Plugin Name: Yandex.Money Gateway
	Description: Simple way for get cash with Yandex.Money
	Version: 1
*/

add_action( 'plugins_loaded', 'yandex_money_gateway', 0 );
function yandex_money_gateway() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
	
	include_once( 'woocommerce-yandex-money.php' );

	add_filter( 'woocommerce_payment_gateways', 'add_yandex_money_gateway' );
	function add_yandex_money_gateway( $methods ) {
		$methods[] = 'WC_Yandex_Money_Gateway';
		return $methods;
	}
}