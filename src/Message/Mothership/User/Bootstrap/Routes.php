<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.user.account']->setPrefix('/account');

		$router['ms.user.account']->add('ms.user.account', '/', '::Controller:Account:Account#index');
		$router['ms.user.account']->add('ms.user.order.listing', '/orders', '::Controller:Account:Account#orderListing');
		$router['ms.user.account']->add('ms.user.order.detail', '/orders/view/{orderID}', '::Controller:Account:Account#orderDetail')
			->setRequirement('orderID', '\d+');

		$router['ms.user.account']->add('ms.user.detail.edit.action', '/edit/detail', '::Controller:Account:Edit#processDetail')
			->setMethod('post');
		$router['ms.user.account']->add('ms.user.detail.edit', '/edit/detail', '::Controller:Account:Edit#detail');

		$router['ms.user.account']->add('ms.user.address.edit.action', '/edit/address/{type}', '::Controller:Account:Edit#processAddress')
			->setMethod('post');
		$router['ms.user.account']->add('ms.user.address.edit', '/edit/address/{type}', '::Controller:Account:Edit#address');
		$router['ms.user.account']->add('ms.user.address.delete', '/edit/address/{type}/delete', '::Controller:Account:Edit#deleteAddress')
			->setMethod('DELETE');

		$router['ms.user.account']->add('ms.user.password.edit.action', '/edit/password', '::Controller:Account:Edit#processPassword')
			->setMethod('post');

		$router['ms.user.account']->add('ms.user.edit', '/edit', '::Controller:Account:Edit#index');



		$router['ms.cp.user.user']->setParent('ms.cp')->setPrefix('/user');

		$router['ms.cp.user.user']->add('ms.cp.user.user', '/', '::Controller:User:Listing#dashboard');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.detail.edit.action', '/{userID}/detail', '::Controller:User:DetailsEdit#detailsFormProcess')
			->setMethod('post');
		$router['ms.cp.user.user']->add('ms.cp.user.admin.detail.edit', '/{userID}/details', '::Controller:User:DetailsEdit#index');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.address.edit.action', '/{userID}/address/{type}', '::Controller:User:AddressEdit#addressFormProcess')
			->setMethod('post');
		$router['ms.cp.user.user']->add('ms.cp.user.admin.address.edit', '/{userID}/addresses', '::Controller:User:AddressEdit#index');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.groups.edit.action', '/{userID}/groups-action', '::Controller:User:GroupsEdit#groupsUpdate');
		$router['ms.cp.user.user']->add('ms.cp.user.admin.groups.edit', '/{userID}/groups', '::Controller:User:GroupsEdit#index');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.orderhistory', '/{userID}/orders', '::Controller:User:OrderHistory#index');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.create.action', '/create', '::Controller:User:Create#newUserFormProcess')
			->setMethod('post');
		$router['ms.cp.user.user']->add('ms.cp.user.admin.create', '/create', '::Controller:User:Create#index');

		$router['ms.cp.user.user']->add('ms.cp.user.search', '/search', '::Controller:User:Listing#search');

		$router['ms.cp.user.user']->add('ms.cp.user.admin.impersonate.action', '/{userID}/impersonate', '::Controller:User:Impersonate#impersonate')
			->setMethod('POST');

		$router['ms.user']->add('user.register.action', '/register/action', '::Controller:Register#registerAction')
			->setMethod('POST');

		$router['ms.user']->add('user.register', '/register', '::Controller:Register#index')
			->setMethod('POST');
	}
}
