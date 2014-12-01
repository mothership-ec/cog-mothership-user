<?php

namespace Message\Mothership\User\Task;

use Message\User\User;
use Message\User\Group\GroupInterface;
use Message\Cog\Console\Task\Task;

use Colors\Color;

/**
 * Class CreateAdminUser
 * @package Message\Mothership\User\Task
 *
 * @author Thomas Marchant <thomas@message.co.uk>
 *
 * Task for creating an admin user from the command line
 */
class CreateAdminUser extends Task
{
	const ADMIN = 'ms-super-admin';

	/**
	 * @var Color
	 */
	private $_colour;

	/**
	 * Details to ask for when creating a new user
	 *
	 * @var array
	 */
	private $_details = [
		'forename'        => 'Forename',
		'surname'         => 'Surname',
		'email'           => 'Email',
		'password'        => 'Password',
	];

	/**
	 * Instanciate a new User object and a Color object to prettify the output.
	 *
	 * Loop through the required details and assign them to the user object, then save the user against the `ms-super-admin`
	 * usergroup.
	 */
	public function process()
	{
		$user = new User;

		$c = new Color;
		$asking = true;

		while ($asking) {
			$this->writeln($c('Please enter your user details:')->fg('black')->bg('green'));

			foreach ($this->_details as $detail => $detailName) {
				$user->$detail = $this->_ask($detail, $detailName);
			}
			$asking = false;
		}

		$user->emailConfirmed = true;

		$user = $this->get('user.create')->create($user);
		$group = $this->_getAdminGroup();

		$this->get('user.edit')->addToGroup($user, $group);
		$this->writeln($c('User `' . $user->getName() . '` (' . $user->id . ') created!')->fg('cyan'));
	}

	/**
	 * Method to handle asking the user to input data to assign to the user object.
	 * If invalid, it will return a call to itself until it validates successfully.
	 *
	 * @param $detail
	 * @param $detailName
	 *
	 * @return string
	 */
	private function _ask($detail, $detailName)
	{
		$c = new Color;
		$this->writeln($c($detailName . ':')->fg('green'));

		while (true) {
			$fh = fopen('php://stdin', 'r');
			$value = trim(fgets($fh, 1024));

			if (empty($value)) {
				$this->writeln($c('Please enter ' . strtolower($detail))->fg('red'));
				return $this->_ask($detail, $detailName);
			} elseif ($this->_invalidEmail($detail, $value)) {
				$this->writeln($c('`' . $value . '` is not a valid email address')->fg('red'));
				return $this->_ask($detail, $detailName);
			}

			return $value;
		}
	}

	/**
	 * Check if a detail is supposed to be an email and if it is valid
	 *
	 * @param $detail
	 * @param $value
	 *
	 * @return bool
	 */
	private function _invalidEmail($detail, $value)
	{
		if ($detail !== 'email') {
			return false;
		}

		if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return false;
		}

		return true;
	}

	/**
	 * Get an instance of the super admin group
	 *
	 * @return GroupInterface
	 */
	private function _getAdminGroup()
	{
		return $this->get('user.group.loader')->getByName(self::ADMIN);
	}

}