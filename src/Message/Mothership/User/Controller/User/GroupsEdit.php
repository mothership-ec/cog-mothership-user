<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserGroups;

class GroupsEdit extends Controller
{

	public function index($userID)
	{
		return $this->render('Message:Mothership:User::User:groups', array(
			'userID'    => $userID,
		));
	}

}