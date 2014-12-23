<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\ServicesInterface;
use Message\Mothership\Report\Report\Collection as ReportCollection;
use Message\Mothership\User;

class Services implements ServicesInterface
{
	public function registerServices($services)
	{
		$this->registerReports($services);

		$services['avatar.provider.collection'] = function($c) {
			return new User\Avatar\ProviderCollection([
				new User\Avatar\Gravatar,
			]);
		};
	}

	public function registerReports($services)
	{
		$services['user.user_summary'] = $services->factory(function($c) {
			return new User\Report\UserSummary(
				$c['db.query.builder.factory'],
				$c['routing.generator']
			);
		});

		$services['user.reports'] = function($c) {
			$reports = new ReportCollection;
			$reports
				->add($c['user.user_summary'])
			;

			return $reports;
		};
	}
}