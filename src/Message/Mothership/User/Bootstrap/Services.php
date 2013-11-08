<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\ServicesInterface;

class Services implements ServicesInterface
{
	public function registerServices($services)
	{
		$services['user.subscription.create'] = function($c) {
			return new \Message\Mothership\User\Subscription\Create($c['db.query'], $c['current.user']);
		};

		$services['user.subscription.loader'] = function($c) {
			return new \Message\Mothership\User\Subscription\Loader($c['db.query']);
		};

		$services['user.subscription.delete'] = function($c) {
			return new \Message\Mothership\User\Subscription\Delete($c['db.query'], $c['current.user']);
		};
	}
}