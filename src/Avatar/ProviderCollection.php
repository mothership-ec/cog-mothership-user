<?php

namespace Message\Mothership\User\Avatar;

use Message\Cog\ValueObject\Collection as BaseCollection;

/**
 * Class ProviderCollection
 * @package Message\Mothership\User\Avatar
 */
class ProviderCollection extends BaseCollection
{
	protected function _configure()
	{
		$this->setKey(function ($item) {
			return $item->getName();
		});

		$this->addValidator(function ($item) {
			if (!$item instanceof ProviderInterface) {
				throw new \InvalidArgumentException('Avatar provider must be an instance of ProviderInterface');
			}
		});
	}
}