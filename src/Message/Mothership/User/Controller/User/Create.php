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
		$form = $form->buildForm($this->generateUrl('ms.user.admin.create.action'));

		return $form;
	}

	public function newUserFormProcess()
	{
		$form = $this->newUserForm();

		if ($form->isValid() && $data = $form->getFilteredData()) {

			$user->title  	 = $data['title'];
			$user->forename  = $data['forename'];
			$user->surname   = $data['surname'];
			$user->email  	 = $data['email'];

			$updateUser = $this->get('user.edit');
			$updateUser->save($user);

			$address->lines[1] 	  = $data['address_line_1'];
			$address->lines[2] 	  = $data['address_line_2'];
			$address->lines[3] 	  = $data['address_line_3'];
			$address->lines[4] 	  = $data['address_line_4'];
			$address->town 		  = $data['town'];
			$address->stateID 	  = $data['state_id'];
			$address->countryID   = $data['country_id'];
			$address->postcode 	  = $data['postcode'];
			$address->telephone   = $data['telephone'];

			$addressEdit = $this->get('commerce.user.address.edit');
			$addressEdit->save($address);

		}
	}

}