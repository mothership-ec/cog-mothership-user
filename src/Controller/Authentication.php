<?php

namespace Message\Mothership\User\Controller;

use Message\Cog\Controller\Controller;

use Message\User\Event;
use Message\User\AnonymousUser;

/**
 * Controllers for standard frontend authentication (login).
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
class Authentication extends Controller
{
	public function login()
	{
		// If user is already logged in, send them to the account section
		if (!($this->get('user.current') instanceof AnonymousUser)) {
			return $this->redirectToRoute('ms.user.account');
		}

		$referer = ($this->get('http.request.master')->headers->get('referer')
			?: $this->generateUrl('ms.user.account'));

		return $this->render('Message:Mothership:User::login_register:login', array(
			'refererUrl' => $referer,
		));
	}
}