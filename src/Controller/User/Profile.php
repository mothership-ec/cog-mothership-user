<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Field;
use Message\Cog\Controller\Controller;

/**
 * Class Profile
 * @package Message\Mothership\User\Controller\User
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Controller for editing the profile from the admin panel
 */
class Profile extends Controller
{
	/**
	 * Render the profile edit form
	 *
	 * @param $userID
	 *
	 * @return \Message\Cog\HTTP\Response
	 */
	public function index($userID)
	{
		$user    = $this->get('user.loader')->getByID($userID);
		$profile = $this->get('user.profile.loader')->getByUser($user);

		$repeatables = [];

		foreach ($profile as $name => $part) {
			if ($part instanceof Field\RepeatableContainer) {
				$repeatables[$name] = [];
				foreach ($part->getFields() as $field) {
					$repeatables[$name][] = $field;
				}
			}
		}

		$groups = array_reduce($this->get('user.group.loader')->getByUser($user), function($result, $group) {
			return ((null === $result) ? '' : $result . ', ') . $group->getDisplayName();
		});

		$form = $this->get('field.form')->generate($profile);

		return $this->render('Message:Mothership:User::user:profile', [
			'profile'     => $profile,
			'form'        => $form,
			'repeatables' => $repeatables,
			'user'        => $user,
			'groups'      => $groups,
		]);
	}

	/**
	 * Validate the form data and save the profile to the database
	 *
	 * @param $userID
	 *
	 * @return \Message\Cog\HTTP\RedirectResponse
	 */
	public function editProfile($userID)
	{
		$user = $this->get('user.loader')->getByID($userID);
		$profile = $this->get('user.profile.loader')->getByUser($user);

		$form = $this->get('field.form')->generate($profile);
		$form->handleRequest();

		if ($form->isValid()) {
			$data = $form->getData();
			$profile->update($data);
			$this->get('user.profile.edit')->save($user, $profile);

			$this->addFlash('success', 'Profile updated successfully');
		}

		return $this->redirectToReferer();
	}
}