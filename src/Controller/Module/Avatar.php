<?php

namespace Message\Mothership\User\Controller\Module;

use Message\User\UserInterface;
use Message\Cog\Controller\Controller;

class Avatar extends Controller
{
	/**
	 * Create a hash and get the default image for a gravatar.
	 *
	 * Gravatar does not render local images correctly, instead for local
	 * environments we will just render an identicon.
	 */
	public function index(UserInterface $user, $size = 100, $default = null)
	{
		$hash = md5(strtolower(trim($user->email)));

		if ('local' == $this->get('env')) {
			$default = 'identicon';
		} else {
			$default = ($default) ?: urlencode('/cogules/Message:Mothership:User/images/avatar.png');
		}

		return $this->render('Message:Mothership:User::module:avatar', [
			'hash'    => $hash,
			'size'    => $size,
			'default' => $default,
		]);
	}
}