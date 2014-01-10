<?php

namespace Message\Mothership\User\Bootstrap\Routes;

use Message\Cog\Bootstrap\RoutesInterface;

class Account implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.user.account']->setPrefix('/account');

		$router['ms.user.account']->add('ms.user.account', '/', 'Message:Mothership:User::Controller:Account:Account#index');
		$router['ms.user.account']->add('ms.user.order.listing', '/orders', 'Message:Mothership:User::Controller:Account:Account#orderListing');
		$router['ms.user.account']->add('ms.user.order.detail', '/orders/view/{orderID}', 'Message:Mothership:User::Controller:Account:Account#orderDetail')
			->setRequirement('orderID', '\d+');

		$router['ms.user.account']->add('ms.user.detail.edit.action', '/edit/detail', 'Message:Mothership:User::Controller:Account:Edit#processDetail')
			->setMethod('post');
		$router['ms.user.account']->add('ms.user.detail.edit', '/edit/detail', 'Message:Mothership:User::Controller:Account:Edit#detail');

		$router['ms.user.account']->add('ms.user.address.edit.action', '/edit/address/{type}', 'Message:Mothership:User::Controller:Account:Edit#processAddress')
			->setMethod('post');
		$router['ms.user.account']->add('ms.user.address.edit', '/edit/address/{type}', 'Message:Mothership:User::Controller:Account:Edit#address');
		$router['ms.user.account']->add('ms.user.address.delete', '/edit/address/{type}/delete', 'Message:Mothership:User::Controller:Account:Edit#deleteAddress')
			->setMethod('DELETE');

		$router['ms.user.account']->add('ms.user.password.edit.action', '/edit/password', 'Message:Mothership:User::Controller:Account:Edit#processPassword')
			->setMethod('post');

		$router['ms.user.account']->add('ms.user.edit', '/edit', 'Message:Mothership:User::Controller:Account:Edit#index');
	}
}