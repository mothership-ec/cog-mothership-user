<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.user.account']->setPrefix('/account');

		$router['ms.user.account']->add('ms.user.account', '/', '::Controller:Account:Account#index');
		$router['ms.user.account']->add('ms.user.order.listing', '/orders', '::Controller:Account:Account#orderListing');
		$router['ms.user.account']->add('ms.user.order.detail', '/orders/view/{orderID}', '::Controller:Account:Account#orderDetail')
			->setRequirement('orderID', '\d+');

		$router['ms.user.account']->add('ms.user.detail.edit.action', '/edit/detail', '::Controller:Account:Edit#processDetail')
			->setMethod('post');
		$router['ms.user.account']->add('ms.user.detail.edit', '/edit/detail', '::Controller:Account:Edit#detail');

		$router['ms.user.account']->add('ms.user.address.edit.action', '/edit/address/{type}', '::Controller:Account:Edit#processAddress')
			->setMethod('post');
		$router['ms.user.account']->add('ms.user.address.edit', '/edit/address/{type}', '::Controller:Account:Edit#address');
		$router['ms.user.account']->add('ms.user.address.delete', '/edit/address/{type}/delete', '::Controller:Account:Edit#deleteAddress')
			->setMethod('DELETE');

		$router['ms.user.account']->add('ms.user.password.edit.action', '/edit/password', '::Controller:Account:Edit#processPassword')
			->setMethod('post');

		$router['ms.user.account']->add('ms.user.edit', '/edit', '::Controller:Account:Edit#index');
	}
}
