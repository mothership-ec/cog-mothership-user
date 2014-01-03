<?php

namespace Message\Mothership\User\Bootstrap\Routes;

use Message\Cog\Bootstrap\RoutesInterface;

class Password implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.user.password']->setPrefix('/password');

		$router['ms.user.password']->add('ms.user.password.index', '', 'Message:Mothership:User::Controller:Password#index');
		$router['ms.user.password']->add('ms.user.password.request', '/request', 'Message:Mothership:User::Controller:Password#request');
		$router['ms.user.password']->add('ms.user.password.reset', '/reset/{email}/{hash}', 'Message:Mothership:User::Controller:Password#reset');
	}
}
