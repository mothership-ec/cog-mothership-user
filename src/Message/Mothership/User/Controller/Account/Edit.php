<?php

namespace Message\Mothership\User\Controller\Account;

use Message\Cog\Controller\Controller;
use Message\Mothership\Ecommerce\Form\UserDetails;
use Message\Mothership\Ecommerce\Form\UserRegister;

/**
 * Class Account
 *
 * Controller for processing orders in Fulfillment
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
			de($data);
		}

	}
}