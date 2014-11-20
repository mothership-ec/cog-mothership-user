<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\ServicesInterface;
use Message\Mothership\User;

class Services implements ServicesInterface
{
	public function registerServices($services)
	{
		$services['avatar.collection'] = function($c) {
			return new User\Avatar\Collection([
				new User\Avatar\Gravatar,
			]);
		};
	}
}