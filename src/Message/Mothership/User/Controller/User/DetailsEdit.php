<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserDetails;

class DetailsEdit extends Controller
{

	public function index($userID)
	{
		$user = $this->get('user.loader')->getByID($userID);

		$accountdetails = $this->detailsForm($userID);

		return $this->render('Message:Mothership:User::User:details', array(
			'accountdetails'  => $accountdetails,
			'userID'   		  => $userID,
		));
	}

	public function detailsForm($userID)
	{
		$user = $this->get('user.loader')->getByID($userID);

		$form = new UserDetails($this->_services);
		$form = $form->buildForm($user, $this->generateUrl('ms.cp.user.admin.detail.edit.action', array('userID' => $user->id)));

		return $form;
	}


	public function detailsFormProcess($userID)
	{
		$form = $this->detailsForm($userID);

		if ($form->isValid() && $data = $form->getFilteredData()) {
			$user = $this->get('user.loader')->getByID($userID);
			$user->title  	 = $data['title'];
			$user->forename  = $data['forename'];
			$user->surname   = $data['surname'];
			$user->email     = $data['email'];

			if($this->get('user.edit')->save($user)) {
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
				$message->setSubject(sprintf('%s password reset request', $this->get('cfg')->merchant->companyName));
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
}