<?php

namespace Message\Mothership\User;

use Message\Mothership\User\Type\UserTypeInterface;
use Message\User;
use Message\Cog\DB;

/**
 * Class UserLoader
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Class for loading users with additional methods for Mothership specific data.
 *
 * @todo This method currently aliases the base User\Loader class. Eventually that class will be refactored to use the
 *       QueryBuilder and at that point this method should extend the base User\Loader class
 */
class Loader
{
	/**
	 * @var DB\QueryBuilderFactory
	 */
	private $_queryBuilderFactory;

	/**
	 * @var User\Loader
	 */
	private $_baseLoader;

	/**
	 * @param DB\QueryBuilderFactory $queryBuilderFactory
	 * @param User\Loader $baseLoader
	 */
	public function __construct(DB\QueryBuilderFactory $queryBuilderFactory, User\Loader $baseLoader)
	{
		$this->_queryBuilderFactory = $queryBuilderFactory;
		$this->_baseLoader = $baseLoader;
	}

	/**
	 * @param UserTypeInterface $type
	 *
	 * @return array | User\User
	 */
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

	/**
	 * Magic method to alias base user loader.
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call($name, array $arguments)
	{
		if (!method_exists($this->_baseLoader, $name)) {
			throw new \BadMethodCallException('Method `' . $name . '()` does not exist on User\Loader');
		}

		$reflection = new \ReflectionMethod($this->_baseLoader, $name);
		if (!$reflection->isPublic()) {
			throw new \BadMethodCallException('Method `' . $name . '()` on User\Loader is not public.');
		}

		return call_user_func_array([$this->_baseLoader, $name], $arguments);
	}
}