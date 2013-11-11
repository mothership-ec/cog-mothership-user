<?php

namespace Message\Mothership\User\Controller\Account;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserAddresses;
use Message\Mothership\User\Form\UserDetails;
use Message\Mothership\Commerce\User\Address\Address;

/**
 * Class Account
 *
 * Controller for editing user account details
 */
class Edit extends Controller
{
	public function index()
	{
		$user            = $this->get('user.current');
		$billingAddress  = $this->get('commerce.user.address.loader')->getByUserAndType($user, 'billing');
		$deliveryAddress = $this->get('commerce.user.address.loader')->getByUserAndType($user, 'delivery');

		if (!$billingAddress) {
			$billingAddress = new Address;
			$billingAddress->type = 'billing';
		}

		if (!$deliveryAddress) {
			$deliveryAddress = new Address;
			$deliveryAddress->type = 'delivery';
		}

		$billingForm  = $this->_getAddressForm($billingAddress);
		$deliveryForm = $this->_getAddressForm($deliveryAddress);
		$detailForm   = $this->_getDetailForm();
		$passwordForm = $this->_getPasswordForm();

		return $this->render('Message:Mothership:User::account:edit', array(
			'billingForm' 	=> $billingForm,
			'deliveryForm'	=> $deliveryForm,
			'detailForm' 	=> $detailForm,
			'passwordForm'  => $passwordForm,
		));
	}

	public function detail()
	{
		return $this->render('Message:Mothership:User::account:edit-detail', array(
			'form' => $this->_getDetailForm(),
		));
	}

	public function address($type)
	{
		$user = $this->get('user.current');
		$address = $this->get('commerce.user.address.loader')->getByUserAndType($user, $type);
		if(!$address) {
			$address = new Address;
			$address->type = $type;
		}

		return $this->render('Message:Mothership:User::account:edit-address', array(
			'form' => $this->_getAddressForm($address),
		));
	}

	/*
	public function deleteAddress($type)
	{
		if ($delete = $this->get('request')->get('delete')) {
			$user = $this->get('user.current');
			$address = $this->get('commerce.user.address.loader')->getByUserAndType($user, $type);

			if ($address = $this->get('commerce.user.address.delete')->delete($address)) {
				$this->addFlash('success', 'You successfully deleted an address');
			} else {
				$this->addFlash('error', 'Your address could not be deleted.');
			}
		}

		return $this->redirectToReferer();
	}
	*/

	public function processDetail()
	{
		$form = $this->_getDetailForm();

		if ($form->isValid() && $data = $form->getFilteredData()) {
			$user = $this->get('user.current');

			$user->title 	= $data['title'];
			$user->forename = $data['forename'];
			$user->surname 	= $data['surname'];
			$user->email 	= $data['email'];

			if (isset($data['email_updates']) && $data['email_updates']) {
				$this->get('user.subscription.create')->create($data['email']);
			} else {
				$this->get('user.subscription.delete')->delete($data['email']);
			}

			if($this->get('user.edit')->save($user)) {
				$this->addFlash('success', 'You successfully updated your account detail');
			} else {
				$this->addFlash('error', 'Your account detail could not be updated');
			}
		}

		return $this->redirectToReferer();
	}


	public function processAddress($type)
	{
		$user = $this->get('user.current');
		$address = $this->get('commerce.user.address.loader')->getByUserAndType($user, $type);
		$created = false;
		if(!$address) {
			$address = new Address;
			$address->type = $type;
			$created = true;
		}
		$form = $this->_getAddressForm($address);

		if ($form->isValid() && $data = $form->getFilteredData()) {
			for($i = 1; $i <= Address::AMOUNT_LINES; ++$i) {
				$address->lines[$i] = $data['lines'][$i];
			}

			$address->town 		= $data['town'];
			$address->stateID	= $data['stateID'];
			$address->countryID = $data['countryID'];
			$address->country 	= $this->get('country.list')->getByID($address->countryID);
			$address->postcode 	= $data['postcode'];
			$address->telephone = $data['telephone'];
			$address->userID	= $user->id;

			if($created) {
				if($this->get('commerce.user.address.create')->create($address)) {
					$this->addFlash('success', sprintf('You successfully created a %s address.', $type));
				}
			} else {
				if($this->get('commerce.user.address.edit')->save($address)) {
					$this->addFlash('success', sprintf('You successfully updated you %s address detail.', $type));
				}
			}
		}

		return $this->redirectToReferer();
	}

	public function processPassword()
	{
		$form = $this->_getPasswordForm();

		if ($form->isValid() && $data = $form->getFilteredData()) {
			$user = $this->get('user.current');

			$newPwd = $data['newPassword'];

			$currentPwd = $this->get('user.loader')->getUserPassword($user);
			if(!$this->get('user.password_hash')->check(
				$data['oldPassword'],
				$this->get('user.loader')->getUserPassword($user)
			)) {
				de('wrong pwd');
				$this->addFlash('error', 'Your old password is not correct');
			} elseif(strcmp($data['passwordRepeat'], $newPwd) !== 0) {
				$this->addFlash('error', 'The entered passwords do not match');
			} else {
				if($this->get('user.edit')->changePassword($user, $newPwd)) {
					$this->addFlash('success', 'You successfully changed your password');
				} else {
					$this->addFlash('error', 'Your password could not be changed');
				}
			}
		}

		return $this->redirectToReferer();
	}

	protected function _getDetailForm()
	{
		$user = $this->get('user.current');

		$form = $this->get('form')
			->setName('detail-edit')
			->setAction($this->generateUrl('ms.user.detail.edit.action'))
			->setMethod('post');

		$titleChoices = $this->get('title.list');

		$form
			->add('title', 'choice', 'Title', array(
				'choices' 	=> $titleChoices,
				'data'  	=> $user->title,
				'expanded' 	=> false,
				'multiple'	=> false,
			))
			->val()->optional();

		$form->add('forename', 'text', 'Forename', array('data' => $user->forename))
			->val()->maxLength(255);

		$form->add('surname', 'text', 'Surname', array('data' => $user->surname))
			->val()->maxLength(255);

		$form->add('email', 'email', 'E-Mail', array('data' => $user->email))
			->val()->maxLength(255);

		$form->add('email_updates', 'checkbox', 'Send me e-mail updates', array(
			'data' => $this->get('user.subscription.loader')->getByUser($user),
		))->val()->optional();

		return $form;
	}


	protected function _getAddressForm(Address $address)
	{
		$form = $this->get('form')
			->setName(sprintf('%s-address-edit', $address->type))
			->setAction($this->generateUrl('ms.user.address.edit.action', array('type' => $address->type)))
			->setMethod('post');

		$linesForm = $this->get('form')
			->setName('lines')
			->addOptions(array(
				'auto_initialize' => false,
			));

		$linesForm->add('1', 'text', ' ', array(
			'data' => $address->lines[1],
		));

		for($i = 2; $i <= Address::AMOUNT_LINES; ++$i) {
			$linesForm->add(sprintf('%s', $i), 'text', ' ', array(
				'data' => $address->lines[$i],
			))->val()->optional();
		}

		$form->add($linesForm->getForm(), 'form', 'Address Lines');

		$form->add('town','text','Town', array('data' => $address->town));
		$form->add('postcode','text','Postcode', array('data' => $address->postcode));

		$form
			->add('stateID','choice','State', array(
				'choices'     => $this->get('state.list')->all(),
				'data'        => $address->stateID,
				'empty_value' => 'Select state...',
				'attr' => array(
					'data-state-filter-country-selector' => "#" . $address->type . "-address-edit_countryID"
				),
			))
			->val()->optional();

		$form->add('countryID','choice','Country', array(
			'choices'     => $this->get('country.list')->all(),
			'data'        => $address->countryID,
			'empty_value' => 'Select country...'
		));

		$form->add('telephone','text','Telephone', array('data' => $address->telephone))->val()->optional();

		return $form;
	}

	protected function _getPasswordForm()
	{
		$form = $this->get('form')
			->setName('password-edit')
			->setAction($this->generateUrl('ms.user.password.edit.action'))
			->setMethod('post');

		$form->add('oldPassword', 'password', 'Old Password');
		$form->add('newPassword', 'password', 'New Password');
		$form->add('passwordRepeat', 'password', 'Repeat Password');

		return $form;
	}
}