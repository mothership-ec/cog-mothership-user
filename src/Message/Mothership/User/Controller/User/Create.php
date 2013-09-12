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

	public function index()
	{
		$newuser = $this->newUserForm();

		return $this->render('Message:Mothership:User::User:create', array(
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

		if ($form->isValid() && $data = $form->getFilteredData()) {
			$user = $this->get('user');
			$user->title  	 = $data['title'];
			$user->forename  = $data['forename'];
			$user->surname   = $data['surname'];
			$user->email  	 = $data['email'];
			$user->password  = $data['password'];

			if($user = $this->get('user.create')->save($user)) {

				$this->addFlash('success', 'Successfully added new account');

				return $this->redirectToRoute('ms.cp.user.admin.detail.edit', array('userID' => $user->id));

			} else {
				$this->addFlash('error', 'Account could not be added');
			}

		}

		return $this->render('Message:Mothership:User::User:create', array(
			'newuser'	  => $form,
		));
	}

}