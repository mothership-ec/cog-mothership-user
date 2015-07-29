<?php

namespace Message\Mothership\User\Type;

use Message\User;
use Message\Cog\DB;

/**
 * Class TypeEdit
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Class for updating the type set against a user
 */
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

	/**
	 * @param DB\Transaction $transaction
	 */
	public function __construct(DB\Transaction $transaction)
	{
		$this->_transaction = $transaction;
	}

	/**
	 * Save the type against the user
	 *
	 * @param User\User $user
	 * @param UserTypeInterface $type
	 *
	 * @return UserTypeInterface
	 */
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

	/**
	 * {@inheritDoc}
	 */
	public function setTransaction(DB\Transaction $transaction)
	{
		$this->_transaction = $transaction;
		$this->_transOverride = true;
	}

	/**
	 * Commit the transaction if it has not been overridden
	 */
	private function _commitTransaction()
	{
		if (false === $this->_transOverride) {
			$this->_transaction->commit();
		}
	}
}