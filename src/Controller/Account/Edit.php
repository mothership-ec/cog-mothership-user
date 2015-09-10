<?php

namespace Message\Mothership\User\Controller\Account;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserAddresses;
use Message\Mothership\User\Form\UserDetails;
use Message\Mothership\Commerce\User\Address\Address;
use Symfony\Component\Validator\Constraints;
use Message\Cog\Localisation\Translator;

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

	public function processDetail()
	{
		$form = $this->_getDetailForm();

		if ($form->isValid() && $data = $form->getFilteredData()) {
			$user = $this->get('user.current');

			$user->title 	= $data['title'];
			$user->forename = $data['forename'];
			$user->surname 	= $data['surname'];
			$user->email 	= $data['email'];

			if ($this->_services->offsetExists('mailing.subscription.edit')){
				$subscriptionEdit = $this->get('mailing.subscription.edit');

				if (isset($data['email_updates']) && $data['email_updates']) {
					$subscriptionEdit->subscribe($data['email']);
				} else {
					$subscriptionEdit->unsubscribe($data['email']);
				}
			}


			if($this->get('user.edit')->save($user)) {
				$this->addFlash('success', $this->get('translator')->trans('ms.user.user.update.success'));
			} else {
				$this->addFlash('error', $this->get('translator')->trans('ms.user.user.update.error'));
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
					$this->addFlash('success', sprintf($this->get('translator')->trans('ms.user.user.address.created-success'), $type));
				}
			} else {
				if($this->get('commerce.user.address.edit')->save($address)) {
					$this->addFlash('success', sprintf($this->get('translator')->trans('ms.user.user.address.updated-success'), $type));
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
				$this->addFlash('error', $this->get('translator')->trans('ms.user.user.password.old-password-error'));
			} elseif(strcmp($data['passwordRepeat'], $newPwd) !== 0) {
				$this->addFlash('error', $this->get('translator')->trans('ms.user.user.password.match-error'));
			} else {
				if($this->get('user.edit')->changePassword($user, $newPwd)) {
					$this->addFlash('success', $this->get('translator')->trans('ms.user.user.password.update.success'));
				} else {
					$this->addFlash('error', $this->get('translator')->trans('ms.user.user.password.update.error'));
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
			->add('title', 'choice', $this->get('translator')->trans('ms.user.user.title'), array(
				'choices' 	=> $titleChoices,
				'data'  	=> $user->title,
				'expanded' 	=> false,
				'multiple'	=> false,
			))
			->val()->optional();

		$form->add('forename', 'text', $this->get('translator')->trans('ms.user.user.firstname'), array('data' => $user->forename))
			->val()->maxLength(255);

		$form->add('surname', 'text', $this->get('translator')->trans('ms.user.user.lastname'), array('data' => $user->surname))
			->val()->maxLength(255);

		$form->add('email', 'email', $this->get('translator')->trans('ms.user.user.email'), array('data' => $user->email))
			->val()->maxLength(255);

		if ($this->_services->offsetExists('mailing.subscription.edit')) {
			$form->add('email_updates', 'checkbox', $this->get('translator')->trans('ms.user.user.email-updates'), array(
				'data' => $this->get('mailing.subscription.loader')->getByUser($user)->isSubscribed(),
			))->val()->optional();
		}

		return $form;
	}

	protected function _getAddressForm(Address $address)
	{
		$form = $this->get('form')
			->setName(sprintf('%s-address-edit', $address->type))
			->setAction($this->generateUrl('ms.user.address.edit.action', array('type' => $address->type)))
			->setMethod('post');

		$linesForm = $this->get('form')
			->setName($this->get('translator')->trans('ms.user.user.address.lines'))
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

		$form->add('town','text', $this->get('translator')->trans('ms.user.user.address.town') , array('data' => $address->town));
		$form->add('postcode','text', $this->get('translator')->trans('ms.user.user.address.postcode') , array('data' => $address->postcode));

		$event = $this->get('country.event');
		$form->add('countryID', 'choice', 'Country', [
			'choices'     => $this->get('event.dispatcher')->dispatch('country.'.$address->type, $event)->getCountries(),
			'empty_value' => 'Please select...',
			'data'        => $address->countryID
		]);
		$form
			->add('stateID','choice', $this->get('translator')->trans('ms.user.user.address.state') , array(
				'choices'     => $this->get('state.list')->all(),
				'data'        => $address->stateID,
				'empty_value' => $this->get('translator')->trans('ms.user.please-select'),
				'attr' => array(
					'data-state-filter-country-id' => $address->type . "-address-edit_countryID"
				),
			))
			->val()->optional();

		$event = $this->get('country.event');
		$form->add('countryID', 'choice', $this->get('translator')->trans('ms.user.user.address.country'), [
			'choices'     => $this->get('event.dispatcher')->dispatch('country.'.$address->type, $event)->getCountries(),
			'empty_value' => $this->get('translator')->trans('ms.user.please-select'),
			'data'        => $address->countryID
		]);
		$form->add('telephone','text', $this->get('translator')->trans('ms.user.user.address.telephone') , array('data' => $address->telephone))->val()->optional();

		return $form;
	}

	protected function _getPasswordForm()
	{
		$form = $this->get('form')
			->setName('password-edit')
			->setAction($this->generateUrl('ms.user.password.edit.action'))
			->setMethod('post');

		$form->add('oldPassword', 'password', $this->get('translator')->trans('ms.user.user.password.old-password'));
		$form->add('newPassword', 'password', $this->get('translator')->trans('ms.user.user.password.new-password'));
		$form->add('passwordRepeat', 'password', $this->get('translator')->trans('ms.user.user.password.confirm'));

		return $form;
	}
}