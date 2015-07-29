<?php

namespace Message\Mothership\User\Type;

use Message\Cog\ValueObject\Collection as BaseCollection;

/**
 * Class Collection
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Collection of user types for accessing from the service container
 */
class Collection extends BaseCollection
{
	protected function _configure()
	{
		$this->addValidator(function ($item) {
			if (!$item instanceof UserTypeInterface) {
				$type = gettype($item) === 'object' ? get_class($item) : gettype($item);
				throw new \InvalidArgumentException('User type in collection expected to be instance of UserTypeInterface, ' . $type . ' given');
			}
		});
		$this->setKey(function ($item) {
			return $item->getName();
		});
	}
}