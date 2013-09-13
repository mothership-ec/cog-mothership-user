<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class OrderHistory extends Controller
{
	public function index($userID)
	{
		$user = $this->get('user.loader')->getByID($userID);
		$orders = $this->get('order.loader')->getByUser($user);

		return $this->render('Message:Mothership:User::User:order-history', array(
			'orders' => $orders,
			'userID' => $userID,
		));
	}

}