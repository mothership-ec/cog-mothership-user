<?php

namespace Message\Mothership\User\Type;

use Message\User;
use Message\Cog\DB;
use Message\Cog\Field;
use Message\Cog\Event\DispatcherInterface;
use Message\Cog\ValueObject\DateTimeImmutable;

class ProfileEdit implements DB\TransactionalInterface
{
	private $_transaction;
	private $_userEdit;
	private $_dispatcher;
	private $_currentUser;

	private $_fieldKeys = [
		'field',
		'value',
		'group',
		'sequence',
		'data_name',
	];

	private $_transOverride = false;

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

	public function save(User\User $user, Profile $profile)
	{
		$data = $this->_flatten($profile);

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

		$user->authorship->update(new DateTimeImmutable, $this->_currentUser->id);

		$this->_commitTransaction();

		$user = $this->_userEdit->save($user);

		$this->_dispatcher->dispatch(
			Events::PROFILE_UPDATE,
			new Event($user, $profile)
		);

		return $user;
	}

	public function setTransaction(DB\Transaction $transaction)
	{
		$this->_transaction = $transaction;
	}

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

	private function _appendRepeatable(array &$fields, Field\RepeatableContainer $repeatable)
	{
		foreach ($repeatable as $sequence => $group) {
			$this->_appendGroup($fields, $group, $sequence);
		}
	}

	private function _appendGroup(array &$fields, Field\Group $group, $sequence = null)
	{
		foreach ($group->getFields() as $field) {
			$this->_appendField($fields, $field, $group->getName(), $sequence);
		}
	}

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

	private function _commitTransaction()
	{
		if (false === $this->_transOverride) {
			$this->_transaction->commit();
		}
	}
}