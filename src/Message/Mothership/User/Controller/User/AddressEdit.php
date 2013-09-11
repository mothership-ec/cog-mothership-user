<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserAddresses;

/**
 * Class Account
 *
 * Controller for editing user account details
 */
class AddressEdit extends Controller
{
	public function index()
	{
		$billingform = $this->addressForm('billing');
		$deliveryform = $this->addressForm('delivery');

		return $this->render('Message:Mothership:User::User:addresses', array(
			'billingform'	  => $billingform,
			'deliveryform'	  => $deliveryform,
		));
	}

	public function addressForm($type)
	{
		$user = $this->get('user.current');
		$address = $this->get('commerce.user.address.loader')->getByUserAndType($user, $type);

		//de($user);

		$form = new UserAddresses($this->_services);
		$form = $form->buildForm($user, $address, $type, $this->generateUrl('ms.user.admin.address.edit.action', array('userID' => $user->id)));

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

}