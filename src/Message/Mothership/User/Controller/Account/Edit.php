<?php

namespace Message\Mothership\User\Controller\Account;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserAddresses;
use Message\Mothership\User\Form\UserDetails;

/**
 * Class Account
 *
 * Controller for editing user account details
 */
class Edit extends Controller
{
	public function index()
	{
		$billingform = $this->addressForm('billing');
		$deliveryform = $this->addressForm('delivery');
		$accountdetails = $this->detailsForm();

		return $this->render('Message:Mothership:User::Account:edit', array(
			'billingform'	  => $billingform,
			'deliveryform'	  => $deliveryform,
			'accountdetails'  => $accountdetails,
		));
	}

	public function addressForm($type)
	{
		$user = $this->get('user.current');
		$address = $this->get('commerce.user.address.loader')->getByUserAndType($user, $type);

		//de($address);

		$form = new UserAddresses($this->_services);
		$form = $form->buildForm($user, $address, $type, $this->generateUrl('ms.user.edit.action'));

		return $form;
	}

	public function addressFormProcess()
	{
		$form = $this->addressForm();

		if ($form->isValid() && $data = $form->getFilteredData()) {

			$user = $this->get('user.current');

			$address = $this->get('commerce.user.address.loader')->getByUserAndType($user, $type);
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

			$this->addFlash('notice', 'Updated User Details');
		}

		return $this->redirectToReferer();;
	}

	public function detailsForm()
	{
		$user = $this->get('user.current');

		//de($user);

		$form = new UserDetails($this->_services);
		$form = $form->buildForm($user, $this->generateUrl('ms.user.edit.action'));

		return $form;
	}


	public function detailsFormProcess() 
	{	
		$form = $this->detailsForm();

		$user = $this->get('user.current');

		$user->title  	 = $data['title'];
		$user->forename  = $data['forename'];
		$user->surname   = $data['surname'];
		$user->email     = $data['email'];

		$updateUser = $this->get('user.edit');
		$updateUser->save($user);

		$this->addFlash('notice', 'Updated User Details');
	}

}