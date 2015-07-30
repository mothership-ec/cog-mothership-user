<?php

namespace Message\Mothership\User\Type;

use Message\Cog\DB;
use Message\User;

/**
 * Class TypeLoader
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Class for loading the user type from the database
 */
class TypeLoader
{
	const TYPE_TABLE = 'user_type';

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

		$result = $this->_queryBuilderFactory->getQueryBuilder()
			->select('type')
			->from(self::TYPE_TABLE)
			->where('user_id = ?i', [$userID])
			->getQuery()
			->run()
		;

		$type = $result->count() ? $result->value() : 'none';

		return $this->_userTypes->get($type);
	}

	public function getByUser(User\User $user)
	{
		return $this->getByUserID($user->id);
	}
}