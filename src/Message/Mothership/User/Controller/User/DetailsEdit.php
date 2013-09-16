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
		}

		return $this->redirect($this->generateUrl('ms.cp.user.admin.detail.edit', array(
			'userID' => $userID
		)));
	}
}