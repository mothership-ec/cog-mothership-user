<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserAddresses;
use Message\Mothership\User\Address\Address;

class AddressEdit extends Controller
{
	private $_loadedUsers = [];

	public function index($userID)
	{
		$user = $this->_getUser($userID);

		$groups = array_reduce($this->get('user.group.loader')->getByUser($user), function($result, $group) {
			return ((null === $result) ? '' : $result . ', ') . $group->getDisplayName();
		});

		$addressForms = [];

		foreach ($this->get('user.address.types') as $type) {
			$addressForms[$type] = $this->addressForm($user, $type);
		}

		return $this->render('Message:Mothership:User::user:addresses', array(
			'addressForms'    => $addressForms,
			'userID'          => $userID,
			'user'            => $user,
			'groups'          => $groups,
		));
	}

	public function addressForm($type,$userID)
	{
		$user = $this->_getUser($userID);
		$address = $this->get('user.address.loader')->getByUserAndType($user, $type);

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
		$user = $this->_getUser($userID);
		$address = $this->get('user.address.loader')->getByUserAndType($user, $type);
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
				if($this->get('user.address.create')->create($address)) {
					$this->addFlash('success', sprintf('You successfully created a %s address.', $type));
				} else {
					$this->addFlash('error', 'Account details could not be updated');
				}
			} else {
				if($this->get('user.address.edit')->save($address)) {
					$this->addFlash('success', sprintf('You successfully updated the %s address.', $type));
				} else {
					$this->addFlash('error', 'Account details could not be updated');
				}
			}
		}

		return $this->redirectToReferer();
	}

	private function _getUser($userID)
	{
		if (!array_key_exists($userID, $this->_loadedUsers)) {
			$this->_loadedUsers[$userID] = $this->get('user.loader')->getByID($userID);
		}

		return $this->_loadedUsers[$userID];
	}

}