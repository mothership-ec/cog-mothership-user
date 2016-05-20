<?php

namespace Message\Mothership\User\Controller;

use Message\Cog\Controller\Controller;

use Message\User\AnonymousUser;
use Message\User\Event;
use Message\Cog\Localisation\Translator;

/**
 * Controllers for user registration.
 */
class Register extends Controller
{
	public function index()
	{
		// If user is already logged in, send them to the account section
		if (!($this->get('user.current') instanceof AnonymousUser)) {
			return $this->redirectToRoute('ms.user.account');
		}

		return $this->render('Message:Mothership:User::login_register:register');
	}

	public function form()
	{
		$form = $this->_getForm();

		// Clear out session
		$this->get('http.session')->remove('user.register.form');

		return $this->render('Message:Mothership:User::login_register:register_form', array(
			'form' => $form,
		));
	}

	public function action()
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
			$this->addFlash('error', $this->get('translator')->trans('ms.user.user.password.match-error'));

			return $this->redirectToReferer();
		}

		if (null !== $this->get('user.loader')->getByEmail($data['email'])) {
			$this->addFlash('error', $this->get('translator')->trans('ms.user.user.email-in-use'));

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

		if ($this->get('module.loader')->exists('Message\\Mothership\\Mailing')
		 && isset($data['opt_in']) && $data['opt_in']) {
			$editSubscriber = $this->get('mailing.subscription.edit');
			$editSubscriber->setTransaction($trans);
			$editSubscriber->subscribe($user->email);
		}

		$trans->commit();
		$user = $this->get('user.loader')->getByID($trans->getIDVariable('USER_ID'));

		$this->get('event.dispatcher')->dispatch(
			Event\Event::CREATE,
			new Event\Event($user)
		);

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
		$url      = $this->generateUrl('ms.user.register.action');
		$redirect = $this->generateUrl('ms.user.register');
		$translator = $this->get('translator');

		$data = array();

		if ($this->get('http.session')->has('user.register.form')) {
			$data = $this->get('http.session')->get('user.register.form');
		}

		$locale = explode("_", $this->get('locale')->getId())[0];
		$titles = $this->get('cfg')->titles->{$locale};

		$form = $userForm->buildForm($url, $redirect, $titles, $data, $translator);

		if ($this->get('module.loader')->exists('Message\\Mothership\\Mailing')) {
			$form->add('opt_in', 'checkbox', $this->trans('ms.mailing.subscribe.option'))
				->val()->optional();
		}

		return $form;
	}
}
