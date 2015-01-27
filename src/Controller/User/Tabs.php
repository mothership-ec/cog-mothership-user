<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class Tabs extends Controller
{
	public function tabs()
	{
		return $this->render('Message:Mothership:User::user:listing:tabs', [
			'tabs'    => $this->get('user.tabs'),
			'userID'  => $this->get('user.current')->id,
			'current' => $this->get('http.request.master')->get('_route'),
		]);
	}
}