<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.user.account']->setPrefix('/account');

		$router['ms.user.account']->add('ms.user.account', '/', 'Message:Mothership:User::Controller:Account:Account#index');
		$router['ms.user.account']->add('ms.user.order.listing', '/orders', 'Message:Mothership:User::Controller:Account:Account#orderListing');
		$router['ms.user.account']->add('ms.user.order.detail', '/orders/view/{orderID}', 'Message:Mothership:User::Controller:Account:Account#orderDetail')
			->setRequirement('orderID', '\d+');

		$router['ms.user.account']->add('ms.user.detail.edit.action', '/edit/detail', 'Message:Mothership:User::Controller:Account:Edit#processDetail')
			->setMethod('post');
		$router['ms.user.account']->add('ms.user.detail.edit', '/edit/detail', 'Message:Mothership:User::Controller:Account:Edit#detail');

		$router['ms.user.account']->add('ms.user.address.edit.action', '/edit/address/{type}', 'Message:Mothership:User::Controller:Account:Edit#processAddress')
			->setMethod('post');
		$router['ms.user.account']->add('ms.user.address.edit', '/edit/address/{type}', 'Message:Mothership:User::Controller:Account:Edit#address');
		$router['ms.user.account']->add('ms.user.address.delete', '/edit/address/{type}/delete', 'Message:Mothership:User::Controller:Account:Edit#deleteAddress')
			->setMethod('DELETE');

		$router['ms.user.account']->add('ms.user.password.edit.action', '/edit/password', 'Message:Mothership:User::Controller:Account:Edit#processPassword')
			->setMethod('post');

		$router['ms.user.account']->add('ms.user.edit', '/edit', 'Message:Mothership:User::Controller:Account:Edit#index');



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

		$router['ms.user']->add('user.register.action', '/register/action', 'Message:Mothership:User::Controller:Register#registerAction')
			->setMethod('POST');

		$router['ms.user']->add('user.register', '/register', 'Message:Mothership:User::Controller:Register#index')
			->setMethod('POST');
	}
}
