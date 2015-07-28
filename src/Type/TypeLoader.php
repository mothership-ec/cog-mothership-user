<?php

namespace Message\Mothership\User\Type;

use Message\Cog\DB;
use Message\User;

class TypeLoader
{
	const TYPE_TABLE    = 'user_type';

	/**
	 * @var DB\QueryBuilderFactory
	 */
	private $_queryBuilderFactory;

	/**
	 * @var Collection
	 */
	private $_userTypes;

	public function __construct(DB\QueryBuilderFactory $queryBuilderFactory, Collection $userTypes)
	{
		$this->_queryBuilderFactory = $queryBuilderFactory;
		$this->_userTypes = $userTypes;
	}

	public function getByUserID($userID)
	{
		if (!is_numeric($userID)) {
			throw new \InvalidArgumentException('User ID must be numeric');
		}

		$type = $this->_queryBuilderFactory->getQueryBuilder()
			->select('type')
			->from(self::TYPE_TABLE)
			->where('user_id = ?i', [$userID])
			->getQuery()
			->run()
			->value()
		;

		return $this->_userTypes->get($type ?: 'none');
	}

	public function getByUser(User\User $user)
	{
		return $this->getByUserID($user->id);
	}
}