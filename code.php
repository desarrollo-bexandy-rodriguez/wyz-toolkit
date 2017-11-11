<?php

function wyz_payment_complete( $order_id ){

		global $workstation;
		$workstation = '270bb211-55c8-488c-826a-2375770ef385';
		$data = array();


		$order = wc_get_order( $order_id );

		$user_id = $order->user_id;

		$user = get_userdata( $user_id );

		$data['username'] = $user->user_login;

		$data['workstation'] = $workstation;

		$data['date'] = get_the_time('c');

		$data['total_net'] = $order->prices_include_tax;

		$data['freight'] = $order->get_total_shipping();

		$data['invoice_total'] = $total_net + $freight;
		$data['price_indicator'] = '';
		$data['deleted'] = false;
		$data['repaid'] = false;
		$data['order_type'] = $order->order_type;
		$data['invoice_number'] = $order_id;
		$data['rest'] = 0.0;
		$data['printed'] = '';
		$data['extra'] = '';
		$data['original_invoice'] = '';

		$data['transaction_id'] = $order_id;

		$data['shipment_address'] = $order->get_shipping_first_name() . ', ' . 
							$order->get_shipping_last_name() . ', ' . 
							$order->get_shipping_company() . ', ' . 
							$order->get_shipping_address_1() . ', ' . 
							$order->get_shipping_address_2() . ', ' . 
							$order->get_shipping_city() . ', ' . 
							$order->get_shipping_state() . ', ' . 
							$order->get_shipping_postcode() . ', ' . 
							$order->get_shipping_country();

		$items = $order->get_items();
		$data['items_names'] = array();
		foreach ($items as $item) {
			$data['items_names'][] = $item->get_name();
		}

		$data['payments'] = array();


		$data = json_encode( $data );


		$url = 'https://91.192.196.237:57391/lfs_api/invoice/?token=Q2eXf/5\R[eBe34qH@!QLMUU[^.sGV,A&workstation=270bb211-55c8-488c-826a-2375770ef385&format=json';

		$args = array(
			"customer" => 'dsaads',
		 "total" => 'awdiuasa',
		 "type" => 'fyuud',
		 "transaction_id" => 'asduid',
		 "shipment_address" => 'adsd',
			'data' => $data,
			'sslverify'   => false,
						'timeout'     => 12000		
		);

	}
	add_action( 'woocommerce_order_status_completed', 'wyz_payment_complete' );