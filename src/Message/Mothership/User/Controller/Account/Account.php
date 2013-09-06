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
		// Load the user
		$user = $this->get('user.current');
		// Load the user addresses
		$billingaddress = $this->get('commerce.user.address.loader')->getByUserAndType($user, 'billing');
		$deliveryaddress = $this->get('commerce.user.address.loader')->getByUserAndType($user, 'delivery');

		return $this->render('Message:Mothership:User::Account:account', array(
			'user'    => $user,
			'billingaddress' => $billingaddress,
			'deliveryaddress' => $deliveryaddress,
		));
	}

	public function orderListing()
	{
		// Load the logged in user
		$user = $this->get('user.current');
		// return their orders
		$orders = $this->get('order.loader')->getByUser($user);

		return $this->render('Message:Mothership:Commerce::order:order:summary', array(
			'orders' => $orders,
		));
	}

	public function orderDetail($orderID)
	{
		// Load the current order 
		$order = $this->get('order.loader')->getByID($orderID);
		$address = $this->get('order.address.loader')->getByOrder($order);
		
		return $this->render('Message:Mothership:User::Account:orderdetails', array(
			'order' => $order,
			'address' => $address,
		));
		
	}

}