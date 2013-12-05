<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;
use Message\Mothership\User\Form\UserGroups;

class GroupsEdit extends Controller
{

	public function index($userID)
	{
		$user = $this->get('user.loader')->getByID($userID);
		$groupsForm = $this->getGroupsForm($userID);

		return $this->render('Message:Mothership:User::user:groups', array(
			'userID'     => $userID,
			'groupsForm' => $groupsForm,
			'user'       => $user,
		));
	}

	public function groupsUpdate($userID)
	{
		$groupsForm = $this->getGroupsForm($userID);

		if ($groupsForm->isValid() && $data = $groupsForm->getFilteredData()) {

			$user = $this->get('user.loader')->getById($userID);

			if ($this->get('user.edit')->setGroups($user, $data['groups'])) {
				$this->addFlash('success', 'Successfully updated user groups');
			}
			else {
				$this->addFlash('error', 'User groups could not be updated');
			}

			return $this->redirect($this->generateUrl('ms.cp.user.admin.groups.edit', array(
				'userID' => $userID
			)));
		}

		return $this->redirectToReferer();
	}

	public function getGroupsForm($userID)
	{
		$form = $this->get('form');
		$form->setAction($this->generateUrl('ms.cp.user.admin.groups.edit.action', array(
			'userID' => $userID
		)));

		$groups = $this->get('user.groups');
		$groupChoices = array();
		foreach ($groups as $group) {
			$groupChoices[$group->getName()] = $group->getDisplayName();
		}

		$user = $this->get('user.loader')->getById($userID);
		$userGroups = $this->get('user.group.loader')->getByUser($user);
		$userGroups = array_keys($userGroups);

		$form->add('groups', 'choice', 'Groups', array(
			'choices'           => $groupChoices,
			'expanded'          => true,
			'multiple'          => true,
			'preferred_choices' => $userGroups,
			'data'              => $userGroups,
		));

		return $form;
	}

}