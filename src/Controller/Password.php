<?php

namespace Message\Mothership\User\Controller;

use Message\Cog\Controller\Controller;
use Message\User\Event;

/**
 * Controllers for standard frontend password reset.
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
class Password extends Controller
{
	public function index()
	{
		return $this->redirect($this->generateUrl('ms.user.password.request'));
	}

	public function request()
	{
		return $this->render('Message:Mothership:User::password:request');
	}

	public function reset($email, $hash)
	{
		$user = $this->get('user.loader')->getByEmail($email);

		return $this->render('Message:Mothership:User::password:reset', array(
			'email' => $email,
			'hash'  => $hash,
			'user'  => $user,
		));
	}
}