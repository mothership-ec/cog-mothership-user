<?php

namespace Message\Mothership\User\Type;

use Message\User;
use Message\Cog\DB;

class TypeEdit implements DB\TransactionalInterface
{
	/**
	 * @var DB\Transaction
	 */
	private $_transaction;

	/**
	 * @var bool
	 */
	private $_transOverride = false;

	public function __construct(DB\Transaction $transaction)
	{
		$this->_transaction = $transaction;
	}

	public function save(User\User $user, UserTypeInterface $type)
	{
		$this->_transaction->add("
			REPLACE INTO
				user_type
				(
					user_id,
					`type`
				)
			VALUES
				(
					:id?i,
					:type?s
				)
		", [
			'id'   => $user->id,
			'type' => $type->getName(),
		]);

		$this->_commitTransaction();

		return $type;
	}

	public function setTransaction(DB\Transaction $transaction)
	{
		$this->_transaction = $transaction;
		$this->_transOverride = true;
	}

	private function _commitTransaction()
	{
		if (false === $this->_transOverride) {
			$this->_transaction->commit();
		}
	}
}