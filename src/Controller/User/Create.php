<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\NewUser;

use Message\Mothership\User\User;


/**
 * Class Account
 *
 * Controller for adding a new user
 */
class Create extends Controller
{

	public function index(NewUser $newuser = null)
	{
		$newuser = ($newuser) ?: $this->newUserForm();

		return $this->render('Message:Mothership:User::user:create', array(
			'newuser'	  => $newuser,
		));
	}

	public function newUserForm()
	{
		$form = new NewUser($this->_services);
		$form = $form->buildForm($this->generateUrl('ms.cp.user.admin.create.action'));

		return $form;
	}

	public function newUserFormProcess()
	{
		$form = $this->newUserForm();

		// Check if the form is valid and attempt to get the data
		if (false === $form->isValid() || false == $data = $form->getFilteredData()) {
			return $this->index($form);
		}

		// Check if the user email already exists
		if (null !== $this->get('user.loader')->getByEmail($data['email'])) {
			$this->addFlash('error', sprintf('A user already exists with the email address "%s"', $data['email']));
			return $this->index($form);
		}

		// Create the user
		$user = $this->get('user');
		$user->title  	 = $data['title'];
		$user->forename  = $data['forename'];
		$user->surname   = $data['surname'];
		$user->email  	 = $data['email'];
		$user->password  = $data['password'];

		// Attempt to save the user
		if (false === $user = $this->get('user.create')->create($user)) {
			$this->addFlash('error', 'Account could not be added');
			return $this->index($form);
		}

		$this->addFlash('success', 'Successfully added new account');

		return $this->redirectToRoute('ms.cp.user.admin.detail.edit', array('userID' => $user->id));
	}

}