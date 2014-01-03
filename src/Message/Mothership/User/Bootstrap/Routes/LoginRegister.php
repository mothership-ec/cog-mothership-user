<?php

namespace Message\Mothership\User\Bootstrap\Routes;

use Message\Cog\Bootstrap\RoutesInterface;

class LoginRegister implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.user.login_register']->add('ms.user.login', '/login', 'Message:Mothership:User::Controller:Authentication#login');

		$router['ms.user.login_register']->add('ms.user.register.action', '/register', 'Message:Mothership:User::Controller:Register#action')
			->setMethod('POST');
		$router['ms.user.login_register']->add('ms.user.register', '/register', 'Message:Mothership:User::Controller:Register#index');
	}
}