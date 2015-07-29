<?php

namespace Message\Mothership\User\Type;

use Message\User;
use Message\Cog\DB;
use Message\Cog\Field;
use Message\Cog\Event\DispatcherInterface;
use Message\Cog\ValueObject\DateTimeImmutable;

class ProfileEdit implements DB\TransactionalInterface
{
	/**
	 * @var DB\Transaction
	 */
	private $_transaction;

	/**
	 * @var User\Edit
	 */
	private $_userEdit;

	/**
	 * @var DispatcherInterface
	 */
	private $_dispatcher;

	/**
	 * @var User\UserInterface
	 */
	private $_currentUser;

	/**
	 * @var array
	 */
	private $_fieldKeys = [
		'field',
		'value',
		'group',
		'sequence',
		'data_name',
	];

	/**
	 * @var bool
	 */
	private $_transOverride = false;

	/**
	 * @param DB\Transaction $transaction
	 * @param User\Edit $userEdit
	 * @param DispatcherInterface $dispatcher
	 * @param User\UserInterface $user
	 */
	public function __construct(
		DB\Transaction $transaction,
		User\Edit $userEdit,
		DispatcherInterface $dispatcher,
		User\UserInterface $user
	)
	{
		$this->_transaction = $transaction;
		$this->_userEdit    = $userEdit;
		$this->_dispatcher  = $dispatcher;
		$this->_currentUser = $user;
	}

	/**
	 * Save changes to the user profile to the database
	 *
	 * @param User\User $user
	 * @param Profile $profile
	 *
	 * @return User\User
	 */
	public function save(User\User $user, Profile $profile)
	{
		$data = $this->_flatten($profile);

		// Update user type
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
			'type' => $profile->getType()->getName(),
		]);

		// Delete existing fields in repeatable groups
		foreach ($profile as $key => $part) {
			if ($part instanceof Field\RepeatableContainer) {
				$this->_transaction->add("
					DELETE FROM
						user_profile
					WHERE
						user_id = :id?i
					AND
						group_name = :group?s
				", [
					'id'    => $user->id,
					'group' => $part->getName(),
				]);
			}
		}

		// Delete any values that have been set to empty
		foreach ($data as $row) {
			if ($row['value'] === 'none') {
				$this->_transaction->add("
					DELETE FROM
						user_profile
					WHERE
						user_id = :id?i,
					AND
						field_name = :field?s
					AND
						group_name = :group?s
					AND
						data_name = :dataName?s
				", [
					'id' => $user->id,
					'field' => $row['field'],
					'group' => $row['group'],
					'dataName' => $row['data_name'],
				]);

				continue;
			}

			// Update values
			$this->_transaction->add("
				REPLACE INTO
					user_profile
					(
						user_id,
						field_name,
						group_name,
						value_string,
						value_int,
						sequence,
						data_name
					)
				VALUES
					(
						:id?i,
						:field?s,
						:group?s,
						:value?s,
						:value?i,
						:sequence?i,
						:dataName?s
					)
			", [
				'id' => $user->id,
				'field' => $row['field'],
				'group' => $row['group'],
				'value' => $row['value'],
				'sequence' => $row['sequence'],
				'dataName' => $row['data_name'],
			]);
		}

		// Flag the user object as having been edited
		$user->authorship->update(new DateTimeImmutable, $this->_currentUser->id);

		$this->_commitTransaction();

		$user = $this->_userEdit->save($user);

		$this->_dispatcher->dispatch(
			Events::PROFILE_UPDATE,
			new Event($user, $profile)
		);

		return $user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setTransaction(DB\Transaction $transaction)
	{
		$this->_transaction = $transaction;
	}

	/**
	 * Create an array of data stored in the profile
	 *
	 * @param Profile $profile
	 *
	 * @return array
	 */
	private function _flatten(Profile $profile)
	{
		$fields = [];

		foreach ($profile as $part) {
			if ($part instanceof Field\RepeatableContainer) {
				$this->_appendRepeatable($fields, $part);
			} elseif ($part instanceof Field\Group) {
				$this->_appendGroup($fields, $part);
			} else {
				$this->_appendField($fields, $part);
			}
		}

		return $fields;
	}

	/**
	 * Loop through groups in a repeatable container and add them to the array
	 *
	 * @param array $fields
	 * @param Field\RepeatableContainer $repeatable
	 */
	private function _appendRepeatable(array &$fields, Field\RepeatableContainer $repeatable)
	{
		foreach ($repeatable as $sequence => $group) {
			$this->_appendGroup($fields, $group, $sequence);
		}
	}

	/**
	 * Loop through fields in a group and add them to the array
	 *
	 * @param array $fields
	 * @param Field\Group $group
	 * @param null $sequence
	 */
	private function _appendGroup(array &$fields, Field\Group $group, $sequence = null)
	{
		foreach ($group->getFields() as $field) {
			$this->_appendField($fields, $field, $group->getName(), $sequence);
		}
	}

	/**
	 * Add the data in the field to the array, looping through the field value if it is an array
	 *
	 * @param array $fields
	 * @param Field\BaseField $field
	 * @param null $group
	 * @param null $sequence
	 */
	private function _appendField(array &$fields, Field\BaseField $field, $group = null, $sequence = null)
	{
		if (is_array($field->getValue())) {
			foreach ($field->getValue() as $key => $value) {
				$fields[] = array_combine($this->_fieldKeys, [
					$field->getName(),
					$value,
					$group,
					$sequence,
					$key,
				]);
			}
		} else {
			$fields[] = array_combine($this->_fieldKeys, [
				$field->getName(),
				$field->getValue(),
				$group,
				$sequence,
				null,
			]);
		}
	}

	/**
	 * Commit transaction if it is not overridden
	 */
	private function _commitTransaction()
	{
		if (false === $this->_transOverride) {
			$this->_transaction->commit();
		}
	}
}