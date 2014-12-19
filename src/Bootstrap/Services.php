<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\ServicesInterface;
use Message\Mothership\User;

class Services implements ServicesInterface
{
	public function registerServices($services)
	{
		$services['avatar.provider.collection'] = function($c) {
			return new User\Avatar\ProviderCollection([
				new User\Avatar\Gravatar,
			]);
		};
	}
}