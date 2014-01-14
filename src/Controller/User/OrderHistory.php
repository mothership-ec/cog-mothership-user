<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class OrderHistory extends Controller
{
	public function index($userID)
	{
		$user = $this->get('user.loader')->getByID($userID);
		$groups = array_reduce($this->get('user.group.loader')->getByUser($user), function($result, $group) {
			return ((null === $result) ? '' : $result . ', ') . $group->getDisplayName();
		});

		$orders = $this->get('order.loader')->getByUser($user);

		return $this->render('Message:Mothership:User::user:order-history', array(
			'orders' => $orders,
			'userID' => $userID,
			'user'   => $user,
			'groups' => $groups,
		));
	}

}