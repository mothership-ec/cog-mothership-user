<?php

namespace Message\Mothership\User\Subscription;

use Message\Cog\DB;
use Message\Cog\ValueObject\DateTimeImmutable;
use Message\User\UserInterface;

use DateTimeZone;

class Delete
{
	protected $_query;
	protected $_currentUser;

	public function __construct(DB\Query $query, UserInterface $currentUser)
	{
		$this->_query       = $query;
		$this->_currentUser = $currentUser;
	}

	public function setTransaction(DB\Transation $query)
	{
		$this->_query = $query;
	}

	public function delete($email)
	{
		$updatedAt = new DateTimeImmutable('now');
		$updatedAt->setTimezone(new DateTimeZone(date_default_timezone_get()));

		$result = $this->_query->run('
			UPDATE
				email_subscription
			SET
				subscribed = 0,
				updated_at = :updatedAt?d,
				updated_by = :updatedBy?in
			WHERE
				email = :email?s
		', array(
			'updatedAt' => $updatedAt,
			'updatedBy' => ($this->_currentUser) ? $this->_currentUser->id : null,
			'email' => $email,
		));

		if ($this->_query instanceof DB\Transaction) {
			return $email;
		}

		return $result;
	}

}