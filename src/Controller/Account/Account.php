<?php

namespace Message\Mothership\User\Controller\Account;

use Message\Cog\Controller\Controller;
use Message\Mothership\Ecommerce\Form\UserRegister;
use Message\Mothership\Commerce\Order\Statuses as OrderStatuses;

use Message\Mothership\Mailing\Subscription\Subscriber;

/**
 * Class Account
 *
 * Controller for viewing user account details
 */
class Account extends Controller
{
	public function index()
	{
		$user            = $this->get('user.current');
		$billingAddress  = $this->get('user.address.loader')->getByUserAndType($user, 'billing');
		$deliveryAddress = $this->get('user.address.loader')->getByUserAndType($user, 'delivery');

		$details = [
			'user'            => $user,
			'billingAddress'  => $billingAddress,
			'deliveryAddress' => $deliveryAddress,
		];

		// Quick fix to pseudo decouple from mailing.
		if ($this->_services->offsetExists('mailing.subscription.loader')){
			$details['subscribed'] = $this->get('mailing.subscription.loader')->getByUser($user)->isSubscribed();
		}

		return $this->render('Message:Mothership:User::account:account', $details);
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
		// Load the logged in user
		$user = $this->get('user.current');

		// Load the current order
		$order = $this->get('order.loader')->getByID($orderID);

		// Check the order matches the user
		if ($order->user->id !== $user->id) {
			throw $this->createNotFoundException();
		}

		$address = $this->get('order.address.loader')->getByOrder($order);

		$returns = $this->get('module.loader')->exists('Message\\Mothership\\Returns') ?
			$this->get('return.loader')->getByOrder($order) :
			[];

		return $this->render('Message:Mothership:User::account:order-details', array(
			'order'   => $order,
			'returns' => $returns,
			'address' => $address,
			'despatchedStatus' => OrderStatuses::DISPATCHED,
		));

	}
}