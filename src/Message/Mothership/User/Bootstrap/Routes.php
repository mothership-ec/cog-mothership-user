<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.user']->add('user.register.action', '/register/action', 'Message:Mothership:User::Controller:Register#registerAction')
			->setMethod('POST');

		$router['ms.user']->add('user.register', '/register', 'Message:Mothership:User::Controller:Register#index')
			->setMethod('POST');
	}
}
