<?php

namespace Message\Mothership\User\Bootstrap\Routes;

use Message\Cog\Bootstrap\RoutesInterface;

class LoginRegister implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.user.login_register']->add('ms.user.login', '/login', 'Message:Mothership:User::Controller:Authentication#login');


		# THE BELOW NEEDS TWEAKING/CHANGING
		$router['ms.user']->add('user.register.action', '/register/action', 'Message:Mothership:User::Controller:Register#registerAction')
			->setMethod('POST');
	}
}