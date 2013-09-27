<?php

namespace Message\Mothership\User\Controller;

use Message\Cog\Controller\Controller;
use Message\User\Event;

/**
 * Class Account
 *
 * Controller for viewing user account details
 */
class Register extends Controller
{
	public function index($privacyText, $buttonText)
	{
		$form = $this->_getForm();

		return $this->render('Message:Mothership:User::register', array(
			'form' => $form,
			'privacyText' => $privacyText,
			'buttonText' => $buttonText
		));

	}

	public function registerAction()
	{
		$form = $this->_getForm();

		if (!$form->isValid() || !$data = $form->getFilteredData()) {
			return $this->redirectToReferer();
		}

		if ($data['password'] !== $data['password_conf']) {
			$this->addFlash('error', 'Your passwords do not match');

			return $this->redirectToReferer();
		}

		$user           = new \Message\User\User;
		$user->forename = $data['forename'];
		$user->surname  = $data['surname'];
		$user->title    = $data['title'];
		$user->email    = $data['email'];
		$user->password = $data['password'];

		$trans      = $this->get('db.transaction');
		$createUser = $this->get('user.create');
		$createUser->setTransaction($trans);
		$user = $createUser->create($user);

		if (isset($data['opt_in']) && $data['opt_in']) {
			$addSubscriber = $this->get('user.subscription.create');
			$addSubscriber->setTransaction($trans);
			$addSubscriber->create($user->email);
		}

		$trans->commit();

		// Set the user session
		$this->get('http.session')->set($this->get('cfg')->user->sessionName, $user);

		// Fire the user login event
		$this->get('event.dispatcher')->dispatch(
			Event\Event::LOGIN,
			new Event\Event($user)
		);

		return $this->redirectToReferer();

	}

	public function _getForm()
	{
		$userForm = $this->get('user.register.form');
		$url      = $this->generateUrl('user.register.action');
		$redirect = $this->generateUrl('user.register');

		$form = $userForm->buildForm($url, $redirect, $this->get('title.list'));
		$form->add('opt_in','checkbox','Send me email udpates')
			->val()->optional();

		return $form;
	}
}