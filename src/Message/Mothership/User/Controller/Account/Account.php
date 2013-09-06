<?php

namespace Message\Mothership\User\Controller\Account;

use Message\Cog\Controller\Controller;
use Message\Mothership\Ecommerce\Form\UserRegister;

/**
 * Class Account
 *
 * Controller for viewing user account details
 */
class Account extends Controller
{
	public function index()
	{
		$user = $this->get('user.current');
		$billingAddress = $this->get('commerce.user.address.loader')->getByUserAndType($user, 'billing');
		$deliveryAddress = $this->get('commerce.user.address.loader')->getByUserAndType($user, 'delivery');

		return $this->render('Message:Mothership:User::Account:account', array(
			'user'    => $user,
			'billingAddress' => $billingAddress,
			'deliveryAddress' => $deliveryAddress,
		));
	}

	public function orderListing()
	{
		// Load the logged in user
		$user = $this->get('user.current');
		// return their orders
		$orders = $this->get('order.loader')->getByUser($user);

		return $this->render('Message:Mothership:User::account:order-listing', array(
			'orders' => $orders,
		));
	}

	public function orderDetail($orderID)
	{
		// Load the current order 
		$order = $this->get('order.loader')->getByID($orderID);
		$address = $this->get('order.address.loader')->getByOrder($order);
		$returns = $this->get('return.loader')->getByOrder($order);
		
		return $this->render('Message:Mothership:User::account:order-details', array(
			'order'   => $order,
			'returns' => $returns,
			'address' => $address,
		));
		
	}
}