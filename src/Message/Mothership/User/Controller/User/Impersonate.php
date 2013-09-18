<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form;

class Impersonate extends Controller
{
	public function impersonate($userID)
	{
		// Set the user id to be impersonated
		$this->get('http.session')->set('impersonate.impersonateID', $userID);

		// Set the id of the admin who is impersonating the user
		$this->get('http.session')->set('impersonate.userID', $this->get('user.current')->id);

		// Get the user
		$user = $this->get('user.loader')->getById($userID);

		// Fire login attempt event
		// $this->get('event.dispatcher')->dispatch(
		// 	Event\Event::LOGIN_ATTEMPT,
		// 	new Event\LoginAttemptEvent($data['email'], $user)
		// );

		// Set the user session
		$this->get('http.session')->set($this->get('cfg')->user->sessionName, $user);

		// Fire the user login event
		$this->get('event.dispatcher')->dispatch(
			Event\Event::LOGIN,
			new Event\Event($user)
		);

		// Redirect the user to the homepage
		return $this->redirect('/');
	}
}