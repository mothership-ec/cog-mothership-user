<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.user.account']->setPrefix('/account');
		$router['ms.user.account']->add('ms.user.edit.action', '/edit', '::Controller:Account:Edit#addressFormProcess')
			->setMethod('post');
		$router['ms.user.account']->add('ms.user.edit', '/edit', '::Controller:Account:Edit#index');

		$router['ms.user.account']->add('ms.user.account', '/', '::Controller:Account:Account#index');

	}
}
