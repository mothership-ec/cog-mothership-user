<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserAddresses;
use Message\Mothership\Commerce\User\Address\Address;

class AddressEdit extends Controller
{
	public function index($userID)
	{
		$user = $this->get('user.loader')->getByID($userID);
		$billingAddress = $this->get('commerce.user.address.loader')->getByUserAndType($user, 'billing');
		$deliveryAddress = $this->get('commerce.user.address.loader')->getByUserAndType($user, 'delivery');

		if(!$billingAddress) {
			$billingAddress = new Address;
			$billingAddress->type = 'billing';
		}
		if(!$deliveryAddress) {
			$deliveryAddress = new Address;
			$deliveryAddress->type = 'delivery';
		}

		$billingform = $this->addressForm('billing', $userID);
		$deliveryform = $this->addressForm('delivery', $userID);

		return $this->render('Message:Mothership:User::user:addresses', array(
			'billingform'     => $billingform,
			'deliveryform'    => $deliveryform,
			'userID'          => $userID,
			'user'            => $user,
		));
	}

	public function addressForm($type,$userID)
	{
		$user = $this->get('user.loader')->getByID($userID);
		$address = $this->get('commerce.user.address.loader')->getByUserAndType($user, $type);
		if(!$address) {
			$address = new Address;
			$address->type = $type;
		}

		$form = new UserAddresses($this->_services);
		$form = $form->buildForm($user, $address, $type, $this->generateUrl('ms.cp.user.admin.address.edit.action', array(
			'userID' => $user->id,
			'type' => $type,
		)));

		return $form;
	}

	public function addressFormProcess($type,$userID)
	{
		$user = $this->get('user.loader')->getByID($userID);
		$address = $this->get('commerce.user.address.loader')->getByUserAndType($user, $type);
		$created = false;
		if(!$address) {
			$address = new Address;
			$address->type = $type;
			$created = true;
		}

		$form = $this->addressForm($type,$userID);

		if ($form->isValid() && $data = $form->getFilteredData()) {
			$user = $this->get('user.loader')->getByID($userID);

			$address->lines[1] 	  = $data['address_line_1'];
			$address->lines[2] 	  = $data['address_line_2'];
			$address->lines[3] 	  = $data['address_line_3'];
			$address->lines[4] 	  = $data['address_line_4'];
			$address->town 		  = $data['town'];
			$address->stateID 	  = $data['state_id'];
			$address->countryID   = $data['country_id'];
			$address->postcode 	  = $data['postcode'];
			$address->telephone   = $data['telephone'];
			$address->userID	  = $user->id;

			if($created) {
				if($this->get('commerce.user.address.create')->create($address)) {
					$this->addFlash('success', sprintf('You successfully created a %s address.', $type));
				} else {
					$this->addFlash('error', 'Account details could not be updated');
				}
			} else {
				if($this->get('commerce.user.address.edit')->save($address)) {
					$this->addFlash('success', sprintf('You successfully updated the %s address.', $type));
				} else {
					$this->addFlash('error', 'Account details could not be updated');
				}
			}
		}
		return $this->redirectToReferer();;
	}

}