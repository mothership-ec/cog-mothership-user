<?php

namespace Message\Mothership\User\Subscription;

use Message\Cog\DB;

class Create
{
	protected $_query;

	public function __construct(DB\Query $query)
	{
		$this->_query = $query;
	}

	public function setTransaction(DB\Transaction $query)
	{
		$this->_query = $query;
	}

	public function create($email)
	{
		$date = new \DateTime;

		$result = $this->_query->run('
			REPLACE INTO
				email_subscription
			SET
				email = :email?s
			', array(
				'email'      => $email,
		));

		if ($this->_query instanceof DB\Transaction) {
			return $email;
		}

		return $result->id();
	}

}