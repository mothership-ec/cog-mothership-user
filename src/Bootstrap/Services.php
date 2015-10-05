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
			$tabs['ms.cp.user.admin.profile'] = 'Profile';
			if (count($c['user.address.types'])) {
				$tabs['ms.cp.user.admin.address.edit'] = 'Addresses';
			}
			$tabs['ms.cp.user.admin.groups.edit'] = 'Groups';

			return $tabs;
		};

		$services['user.profile.type.loader'] = function ($c) {
			return new User\Type\TypeLoader(
				$c['db.query.builder.factory'],
				$c['user.profile.types']
			);
		};

		$services['user.profile.type.edit'] = $services->factory(function ($c) {
			return new User\Type\TypeEdit($c['db.transaction']);
		});

		$services['ms.user.loader'] = function ($c) {
			return new User\Loader(
				$c['db.query.builder.factory'],
				$c['user.loader']
			);
		};

		$services['user.profile.loader'] = function ($c) {
			return new User\Type\ProfileLoader(
				$c['db.query.builder.factory'],
				$c['user.profile.types'],
				$c['user.profile.type.loader'],
				$c['user.profile.factory']
			);
		};

		$services['user.profile.edit'] = $services->factory(function ($c) {
			return new User\Type\ProfileEdit(
				$c['db.transaction'],
				$c['user.edit'],
				$c['user.profile.type.edit'],
				$c['event.dispatcher'],
				$c['user.current']
			);
		});

		$services['user.profile.factory'] = function($c) {
			return new User\Type\ProfileFactory(
				$c['user.profile.types'],
				$c['field.factory']
			);
		};

		$services['user.profile.types'] = function($c) {
			return new User\Type\Collection([
				$c['user.profile.types.none'],
				$c['user.profile.types.team_member'],
			]);
		};

		$services['user.profile.types.none'] = function($c) {
			return new User\Type\NoneType;
		};

		$services['user.profile.types.team_member'] = function($c) {
			return new User\Type\TeamMemberType;
		};

		$services['user.fields.user'] = function ($c) {
			return new User\FieldType\User(
				$c['user.profile.types'],
				$c['ms.user.loader'],
				$c['user.profile.loader'],
				$c['user.loader']
			);
		};

		$services->extend('field.collection', function($fields, $c) {
			$fields->add($c['user.fields.user']);

			return $fields;
		});
	}

	public function registerReports($services)
	{
		$services['user.report.user_summary'] = $services->factory(function($c) {
			return new User\Report\UserSummary(
				$c['db.query.builder.factory'],
				$c['routing.generator'],
				$c['country.list'],
				$c['state.list'],
				$c['user.report.user_summary.filters'],
				$c['event.dispatcher']
			);
		});

		/**
		 * @deprecated use `user.report.user_summary` instead
		 */
		$services['user.user_summary'] = $services->factory(function ($c) {
			return $c['user.report.user_summary'];
		});

		$services['user.reports'] = function($c) {
			$reports = new ReportCollection;
			$reports
				->add($c['user.user_summary'])
			;

			return $reports;
		};

		$services['user.report.filter.address_type'] = $services->factory(function ($c) {
			return new User\Report\Filter\AddressTypeFilter;
		});

		$services['user.report.filter.country'] = $services->factory(function ($c) {
			return new User\Report\Filter\CountryFilter($c['country.list']);
		});

		$services['user.report.filter.date'] = $services->factory(function ($c) {
			return new User\Report\Filter\CreatedAtFilter;
		});

		$services['user.report.user_summary.filters'] = $services->factory(function ($c) {
			$collection = new \Message\Mothership\Report\Filter\Collection([
				$c['user.report.filter.address_type'],
				$c['user.report.filter.country'],
				$c['user.report.filter.date'],
			]);

			$collection->setSort();

			return $collection;
		});
	}
}