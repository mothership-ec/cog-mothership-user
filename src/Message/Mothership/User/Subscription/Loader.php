<?php

namespace Message\Mothership\User\Subscription;

use Message\User\User;
use Message\Cog\DB;

class Loader
{
	protected $_query;

	public function __construct(DB\Query $query)
	{
		$this->_query = $query;
	}

	public function getByEmail($email)
	{
		return $this->_load($email);
	}

	public function getByUser(User $user)
	{
		return $this->_load($user->email);
	}

	public function _load($email)
	{
		$result = $this->_query->run('
			SELECT
				email
			FROM
				email_subscription
			WHERE
				email = ?s AND
				subscribed = 1
		', array(
			$email
		));

		return (bool) count($result);
	}
}