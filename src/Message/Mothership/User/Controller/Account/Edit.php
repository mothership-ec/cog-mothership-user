<?php

namespace Message\Mothership\User\Controller\Account;

use Message\Cog\Controller\Controller;
use Message\Mothership\Ecommerce\Form\UserDetails;
use Message\Mothership\Ecommerce\Form\UserRegister;

/**
 * Class Account
 *
 * Controller for editing user account details
 */
class Edit extends Controller
{
	public function index()
	{
		// Load the user addresses
		$form = $this->addressForm();

		return $this->render('Message:Mothership:User::Account:edit', array(
			'form'    => $form,
		));
	}

	public function addressForm()
	{
		$user = $this->get('user.current');
		$addresses = $this->get('commerce.user.collection')->getByProperty('type', 'billing');
		$address = array_pop($addresses);
		$address->forename = $user->forename;
		$address->surname = $user->surname;

		$form = new UserDetails($this->_services);
		$form = $form->buildForm($user, $address, 'billing', $this->generateUrl('ms.user.edit.action'));

		return $form;
	}

	public function addressFormProcess()
	{
		$form = $this->addressForm();

		if ($form->isValid() && $data = $form->getFilteredData()) {

			$user = $this->get('user.current');
			
			// Update the user here
			$user->title  	 = $data['title'];
			$user->forename  = $data['forename'];
			$user->surname   = $data['surname'];
			//$user->email  = $data['email'];

			$updateUser = $this->get('user.edit');
			$updateUser->save($user);

			// Update the address
			$addresses = $this->get('commerce.user.collection')->getByProperty('type', 'billing');
			$address = array_pop($addresses);
			$address->lines[1] 	  = $data['address_line_1'];
			$address->lines[2] 	  = $data['address_line_2'];
			$address->lines[3] 	  = $data['address_line_3'];
			$address->lines[4] 	  = $data['address_line_4'];
			$address->town 		  = $data['town'];
			$address->stateID 	  = $data['state_id'];
			$address->countryID   = $data['country_id'];
			$address->postcode 	  = $data['postcode'];
			//$address->telephone = $data['telephone']

			$addressEdit = $this->get('commerce.user.address.edit');
			$addressEdit->save($address);

			$this->addFlash('notice', 'Updated Used Details');
		}

		return $this->redirectToReferer();;
	}
}