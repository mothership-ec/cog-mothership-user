<?php

namespace Message\Mothership\User\Bootstrap\Routes;

use Message\Cog\Bootstrap\RoutesInterface;

class Administration implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.cp.user.user']->setParent('ms.cp')->setPrefix('/user');

		$router['ms.cp.user.user']->add('ms.cp.user.user', '/', 'Message:Mothership:User::Controller:User:Listing#dashboard');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.detail.edit.action', '/{userID}/detail', 'Message:Mothership:User::Controller:User:DetailsEdit#detailsFormProcess')
			->setMethod('post');
		$router['ms.cp.user.user']->add('ms.cp.user.admin.detail.edit', '/{userID}/details', 'Message:Mothership:User::Controller:User:DetailsEdit#index');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.address.edit.action', '/{userID}/address/{type}', 'Message:Mothership:User::Controller:User:AddressEdit#addressFormProcess')
			->setMethod('post');
		$router['ms.cp.user.user']->add('ms.cp.user.admin.address.edit', '/{userID}/addresses', 'Message:Mothership:User::Controller:User:AddressEdit#index');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.groups.edit.action', '/{userID}/groups-action', 'Message:Mothership:User::Controller:User:GroupsEdit#groupsUpdate');
		$router['ms.cp.user.user']->add('ms.cp.user.admin.groups.edit', '/{userID}/groups', 'Message:Mothership:User::Controller:User:GroupsEdit#index');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.orderhistory', '/{userID}/orders', 'Message:Mothership:User::Controller:User:OrderHistory#index');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.create.action', '/create', 'Message:Mothership:User::Controller:User:Create#newUserFormProcess')
			->setMethod('post');
		$router['ms.cp.user.user']->add('ms.cp.user.admin.create', '/create', 'Message:Mothership:User::Controller:User:Create#index');

		$router['ms.cp.user.user']->add('ms.cp.user.search', '/search', 'Message:Mothership:User::Controller:User:Listing#search');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.impersonate.action', '/{userID}/impersonate', 'Message:Mothership:User::Controller:User:Impersonate#impersonate')
			->setMethod('POST');
	}
}
