<?php

namespace Message\Mothership\User\Subscription;

use Message\Cog\DB;
use Message\Cog\ValueObject\DateTimeImmutable;
use Message\User\UserInterface;

class Create
{
	protected $_query;
	protected $_currentUser;

	public function __construct(DB\Query $query, UserInterface $currentUser)
	{
		$this->_query       = $query;
		$this->_currentUser = $currentUser;
	}

	public function setTransaction(DB\Transaction $query)
	{
		$this->_query = $query;
	}

	public function create($email)
	{
		$result = $this->_query->run('
			REPLACE INTO
				email_subscription
			SET
				email      = :email?s,
				subscribed = 1,
				updated_at = :updatedAt?d,
				updated_by = :updatedBy?in
		', array(
			'updatedAt' => new \DateTime('now'),
			'updatedBy' => $this->_currentUser->id,
			'email'     => $email,
		));

		if ($this->_query instanceof DB\Transaction) {
			return $email;
		}

		return $result->id();
	}

}