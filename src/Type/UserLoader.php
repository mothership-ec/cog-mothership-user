<?php

namespace Message\Mothership\User\Type;

use Message\Cog\DB;
use Message\User;

/**
 * Class UserLoader
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Class for loading users by their type
 */
class UserLoader
{
	private $_queryBuilderFactory;
	private $_baseLoader;

	public function __construct(DB\QueryBuilderFactory $queryBuilderFactory, User\Loader $baseLoader)
	{
		$this->_queryBuilderFactory = $queryBuilderFactory;
		$this->_baseLoader = $baseLoader;
	}

	public function getByType(UserTypeInterface $type)
	{
		$ids = $this->_queryBuilderFactory
			->getQueryBuilder()
			->select('user_id')
			->from('user_type')
			->where('type = ?s', [$type->getName()])
			->run()
			->flatten()
		;

		return $this->_baseLoader->getByID($ids);
	}
}