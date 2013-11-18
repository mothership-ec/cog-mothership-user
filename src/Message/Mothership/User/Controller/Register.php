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

		// Clear out session
		$this->get('http.session')->remove('user.register.form');

		return $this->render('Message:Mothership:User::register', array(
			'form' => $form,
			'privacyText' => $privacyText,
			'buttonText' => $buttonText
		));

	}

	public function registerAction()
	{
		$form = $this->_getForm();

		if (!$data = $form->getFilteredData()) {
			return $this->redirectToReferer();
		}

		// Put the data in the session, excluding the passwords, to re-populate
		// the form on a redirect.
		$session = $data;
		if (isset($session['password']))      unset($session['password']);
		if (isset($session['password_conf'])) unset($session['password_conf']);
		$this->get('http.session')->set('user.register.form', $session);

		if (!$form->isValid()) {
			return $this->redirectToReferer();
		}

		if ($data['password'] !== $data['password_conf']) {
			$this->addFlash('error', 'Your passwords do not match');

			return $this->redirectToReferer();
		}

		if (null !== $this->get('user.loader')->getByEmail($data['email'])) {
			$this->addFlash('error', 'This email address is already in use, please try another.');

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
		$trans->setIDVariable('USER_ID');

		if (isset($data['opt_in']) && $data['opt_in']) {
			$addSubscriber = $this->get('user.subscription.create');
			$addSubscriber->setTransaction($trans);
			$addSubscriber->create($user->email);
		}

		$trans->commit();

		$user = $this->get('user.loader')->getByID($trans->getIDVariable('USER_ID'));

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

		$data = array();

		if ($this->get('http.session')->has('user.register.form')) {
			$data = $this->get('http.session')->get('user.register.form');
		}

		$form = $userForm->buildForm($url, $redirect, $this->get('title.list'), $data);
		$form->add('opt_in','checkbox','Send me email udpates')
			->val()->optional();

		return $form;
	}
}