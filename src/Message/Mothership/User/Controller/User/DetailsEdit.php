<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserDetails;

/**
 * Class Account
 *
 * Controller for editing user account details
 */
class DetailsEdit extends Controller
{

	public function index()
	{
		$accountdetails = $this->detailsForm();

		return $this->render('Message:Mothership:User::User:details', array(
			'accountdetails'	  => $accountdetails,
		));
	}

	public function detailsForm()
	{
		$user = $this->get('user.current');

		//de($user);

		$form = new UserDetails($this->_services);
		$form = $form->buildForm($user, $this->generateUrl('ms.user.admin.detail.edit.action', array('userID' => $user->id)));

		return $form;
	}


	public function detailsFormProcess()
	{
		$form = $this->detailsForm();

		$user = $this->get('user.current');
de($data['title']);
		$user->title  	 = $data['title'];
		$user->forename  = $data['forename'];
		$user->surname   = $data['surname'];
		$user->email     = $data['email'];


		$updateUser = $this->get('user.edit');
		$updateUser->save($user);
		//	if($this->get('user.edit')->save($user)) {
		//		$this->addFlash('success', 'You successfully updated your account detail');
		//	} else {
		//		$this->addFlash('error', 'Your account detail could not be updated');
		//	}

		$this->addFlash('notice', 'Updated User Details');
	}


}