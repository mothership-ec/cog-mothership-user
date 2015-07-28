<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class Tabs extends Controller
{
	const PROFILE_ROUTE = 'ms.cp.user.admin.profile';

	public function tabs($userID)
	{
		$tabs = $this->get('user.tabs');
		$userType = $this->get('user.profile.type.loader')->getByUserID($userID);

		if ($userType->getName() === 'none') {
			unset($tabs[self::PROFILE_ROUTE]);
		}

		return $this->render('Message:Mothership:User::user:listing:tabs', [
			'tabs'    => $tabs,
			'userID'  => (int) $userID,
			'current' => $this->get('http.request.master')->get('_route'),
		]);
	}
}