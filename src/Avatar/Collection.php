<?php

namespace Message\Mothership\User\Avatar;

use Message\Cog\ValueObject\Collection as BaseCollection;

class Collection extends BaseCollection
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