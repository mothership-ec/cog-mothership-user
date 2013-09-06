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
		$user = $this->get('user.current');
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

		$billingForm  = $this->_getAddressForm($billingAddress);
		$deliveryForm = $this->_getAddressForm($deliveryAddress);
		$detailForm   = $this->_getDetailForm();

		return $this->render('Message:Mothership:User::Account:edit', array(
			'billingForm' 	=> $billingForm,
			'deliveryForm'	=> $deliveryForm,
			'detailForm' 	=> $detailForm,
		));
	}

	public function detail()
	{
		return $this->render('Message:Mothership:User::account:detail-edit', array(
			'form' => $this->_getDetailForm(),
		));
	}

	public function address($type)
	{
		$user = $this->get('user.current');
		$address = $this->get('commerce.user.address.loader')->getByUserAndType($user, $type);

		return $this->render('Message:Mothership:User::account:address-edit', array(
			'form' => $this->_getAddressForm($address),
		));
	}

	public function processDetail()
	{
		$form = $this->_getDetailForm();

		if ($form->isValid() && $data = $form->getFilteredData()) {
			$user = $this->get('user.current');

			$user->title 	= $data['title'];
			$user->forename = $data['forename'];
			$user->surname 	= $data['surname'];

			if($this->get('user.edit')->save($user)) {
				$this->addFlash('success', 'You successfully updated your account detail');
				return $this->redirectToRoute('ms.user.account');
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

	protected function _getDetailForm()
	{
		$user = $this->get('user.current');

		$form = $this->get('form')
			->setName('detail-edit')
			->setAction($this->generateUrl('ms.user.detail.edit.action'))
			->setMethod('post');

		// TODO: Get choices from somewhere else!!
		$titleChoices = array(
			'Mr'   => 'Mr',
			'Mrs'  => 'Mrs',
			'Ms'   => 'Ms',
			'Miss' => 'Miss',
		);

		$form->add('title', 'choice', 'Title', array(
			'choices' 	=> $titleChoices,
			'data'  	=> $user->title,
			'expanded' 	=> false,
			'multiple'	=> false,
		))->val()->optional();

		$form->add('forename', 'text', 'Forename', array('data' => $user->forename))
			->val()->maxLength(255);

		$form->add('surname', 'text', 'Surname', array('data' => $user->surname))
			->val()->maxLength(255);

		$form->add('email-updates', 'checkbox', 'Send me e-mail updates')->val()->optional();

		return $form;
	}


	protected function _getAddressForm(Address $address)
	{
		d($address->type);
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
		$form->add('stateID','text','State ID', array('data' => $address->stateID))
			->val()->optional();

		$form->add('countryID','choice','Country', array(
			'choices' => $this->get('country.list')->all(),
			'data'    => $address->countryID,
		));

		$form->add('telephone','text','Telephone', array('data' => $address->telephone))->val()->optional();

		return $form;
	}
}