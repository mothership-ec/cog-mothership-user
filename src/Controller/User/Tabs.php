<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class Tabs extends Controller
{
	public function tabs($userID)
	{
		return $this->render('Message:Mothership:User::user:listing:tabs', [
			'tabs'    => $this->get('user.tabs'),
			'userID'  => (int) $userID,
			'current' => $this->get('http.request.master')->get('_route'),
		]);
	}
}