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

		$services['user.address.types'] = function($c) {
			return [];
		};

		$services['user.address.create'] = function($c) {
			return new User\Address\Create($c['db.query'], $c['user.address.loader'], $c['user.current']);
		};

		$services['user.address.loader'] = function($c) {
			return new User\Address\Loader(
				$c['db.query'],
				$c['country.list'],
				$c['state.list']
			);
		};

		$services['user.address.edit'] = function($c) {
			return new User\Address\Edit($c['db.query'], $c['user.current']);
		};

		$services['user.address.delete'] = function($c) {
			return new User\Address\Delete($c['db.query']);
		};

		$services['user.tabs'] = function($c) {
			$tabs = [];
			$tabs['ms.cp.user.admin.detail.edit'] = 'Details';
			if (count($c['user.address.types'])) {
				$tabs['ms.cp.user.admin.address.edit'] = 'Addresses';
			}
			$tabs['ms.cp.user.admin.groups.edit'] = 'Groups';

			return $tabs;
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