<?php

namespace Message\Mothership\User\Subscription;

use Message\Cog\DB;

class Delete
{
	protected $_query;

	public function __construct(DB\Query $query)
	{
		$this->_query = $query;
	}

	public function setTransaction(DB\Transation $query)
	{
		$this->_query = $query;
	}

	public function delete($email)
	{
		$date = new \DateTime;

		$result = $this->_query->run('
			DELETE FROM
				email_subscription
			WHERE
				email = ?s
			', array(
				'email'      => $email,
		));

		if ($this->_query instanceof DB\Transaction) {
			return $email;
		}

		return $result;
	}

}