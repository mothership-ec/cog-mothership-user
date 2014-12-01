<?php

namespace Message\Mothership\User\Task;

use Message\User\User;
use Message\Cog\Console\Task\Task;

use Colors\Color;

class CreateAdminUser extends Task
{
	const ADMIN = 'ms-super-admin';

	private $_details = [
		'forename',
		'surname',
		'email',
		'password',
	];

	public function process()
	{
		$user = new User;

		$c = new Color;
		$asking = true;

		while ($asking) {
			echo $c('Please enter your user details:')->fg('black')->bg('green');

			foreach ($this->_details as $detail) {
				$user->$detail = $this->_ask($detail);
			}
			$asking = false;
		}

		$user->emailConfirmed = true;

		$user = $this->get('user.create')->create($user);
		$group = $this->_getAdminGroup();

		$this->get('user.edit')->addToGroup($user, $group);
	}

	private function _ask($detail)
	{
		$c = new Color;
		$wait = true;
		echo $c($detail . ':')->fg('green');

		while ($wait) {
			$fh = fopen('php://stdin', 'r');
			$value = trim(fgets($fh, 1024));

			if (empty($value)) {
				echo $c('Please enter ' . $detail)->fg('red');
				return $this->_ask($detail);
			} elseif ($this->_invalidEmail($detail, $value)) {
				echo $c('`' . $detail . '` is not a valid email address')->fg('red');
				return $this->_ask($detail);
			}

			return $value;
		}
	}

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

	private function _getAdminGroup()
	{
		return $this->get('user.group.loader')->getByName(self::ADMIN);
	}

}