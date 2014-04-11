<?php

namespace Message\Mothership\User\Controller\Module;

use Message\User\UserInterface;
use Message\Cog\Controller\Controller;

class Avatar extends Controller
{
	public function index(UserInterface $user, $size = 100, $default = 'identicon')
	{
		$hash = md5(strtolower(trim($user->email)));

		return $this->render('Message:Mothership:User::module:avatar', [
			'hash'    => $hash,
			'size'    => $size,
			'default' => $default,
		]);
	}
}