<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form;
use Message\Mothership\User\Type\Profile;

class DetailsEdit extends Controller
{

	public function index($userID)
	{
		$user = $this->get('user.loader')->getByID($userID);
		$groups = array_reduce($this->get('user.group.loader')->getByUser($user), function($result, $group) {
			return ((null === $result) ? '' : $result . ', ') . $group->getDisplayName();
		});

		$accountdetails = $this->_getDetailsForm($user);
		$impersonateForm = $this->_getImpersonateForm($user);

		return $this->render('Message:Mothership:User::user:details', array(
			'accountdetails'  => $accountdetails,
			'impersonateForm' => $impersonateForm,
			'userID'          => $userID,
			'user'            => $user,
			'groups'		  => $groups,
		));
	}

	public function detailsFormProcess($userID)
	{
		$user = $this->get('user.loader')->getByID($userID);

		$form = $this->_getDetailsForm($user);

		if ($form->isValid() && $data = $form->getFilteredData()) {
			$user->title  	 = $data['title'];
			$user->forename  = $data['forename'];
			$user->surname   = $data['surname'];
			$user->email     = $data['email'];

			$type = $this->get('user.profile.type.loader')->getByUser($user)->getName();
			$profile = null;

			if ($data['type'] !== $type) {
				$profile = $this->get('user.profile.factory')->getProfile($data['type']);
			}

			if($this->get('user.edit')->save($user) && (null === $profile || $this->get('user.profile.edit')->save($user, $profile))) {
				$this->addFlash('success', 'Successfully updated account details');
			} else {
				$this->addFlash('error', 'Account detail could not be updated');
			}

			if (isset($data['reset_password']) and $data['reset_password']) {
				// Update the "password requested at" timestamp
				$this->get('user.edit')->updatePasswordRequestTime($user);

				// Generate the hash
				$hash = $this->_generateHash($user);

				$message = $this->get('mail.message');
				$message->setTo('laurence@message.co.uk');
				$message->setFrom('laurence@message.co.uk');
				$message->setSubject(sprintf('%s password reset request', $this->get('cfg')->app->defaultEmailFrom->name));
				$message->setView('Message:User::mail:reset-password', array(
					'user'     => $user,
					'domain'   => null,
					'resetUrl' => null,
				));

				$dispatcher = $this->get('mail.dispatcher');

				if ($dispatcher->send($message)) {
					$this->addFlash('success', sprintf('Reset password email sent to %s', '_emailaddress_'));
				}
				else {
					$this->addFlash('error', sprintf('Could not send reset password email to %s', '_emailaddress_'));
				}

				// Dispatch password request event
				$this->get('event.dispatcher')->dispatch(
					Event\Event::PASSWORD_REQUEST,
					new Event\Event($user)
				);
			}
		}

		return $this->redirect($this->generateUrl('ms.cp.user.admin.detail.edit', array(
			'userID' => $userID
		)));
	}

	protected function _getDetailsForm($user)
	{
		$form = new Form\UserDetails($this->_services);
		$form = $form->buildForm($user, $this->generateUrl('ms.cp.user.admin.detail.edit.action', array('userID' => $user->id)));

		return $form;
	}

	protected function _getImpersonateForm($user)
	{
		$form = new Form\Impersonate($this->_services);
		$form = $form->buildForm($user, $this->generateUrl('ms.cp.user.admin.impersonate.action', array('userID' => $user->id)));

		return $form;
	}
}