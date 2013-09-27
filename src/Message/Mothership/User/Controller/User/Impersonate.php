<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form;
use Message\User\Event\Event as UserEvent;

class Impersonate extends Controller
{
	public function impersonate($userID)
	{
		// Get the user
		$user = $this->get('user.loader')->getById($userID);

		$form = $this->_getImpersonateForm($user);
		$data = $form->getFilteredData();

		// Set the user id to be impersonated
		$this->get('http.session')->set('impersonate.impersonateID', $userID);

		// Set the id of the admin who is impersonating the user
		$this->get('http.session')->set('impersonate.userID', $this->get('user.current')->id);

		// Add the form data to the session
		foreach ($data as $key => $value) {
			$this->get('http.session')->set('impersonate.data.' . $key, $value);
		}

		// Fire login attempt event
		// $this->get('event.dispatcher')->dispatch(
		// 	Event\Event::LOGIN_ATTEMPT,
		// 	new Event\LoginAttemptEvent($data['email'], $user)
		// );

		// Set the user session
		$this->get('http.session')->set($this->get('cfg')->user->sessionName, $user);

		// Fire the user login event
		$this->get('event.dispatcher')->dispatch(
			UserEvent::LOGIN,
			new UserEvent($user)
		);

		// Redirect the user to the homepage
		return $this->redirect('/');
	}

	protected function _getImpersonateForm($user)
	{
		$form = new Form\Impersonate($this->_services);
		$form = $form->buildForm($user, $this->generateUrl('ms.cp.user.admin.impersonate.action', array('userID' => $user->id)));

		return $form;
	}
}