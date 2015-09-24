<?php

namespace Message\Mothership\User\Report\Filter;

use Message\Cog\DB;
use Message\Mothership\Report\Filter\Choices;
use Message\Mothership\Report\Filter\ModifyQueryInterface;

class AddressTypeFilter extends Choices implements ModifyQueryInterface
{
	const NAME = 'address_type';

	public function __construct($label = 'Address type', array $choices = null)
	{
		if (null === $choices) {
			$choices = [
				'delivery' => 'Delivery',
				'billing'  => 'Billing',
			];
		}

		$this->setFormChoices($choices);

		parent::__construct(self::NAME, $label, null, true);
	}

	public function addChoice($choice)
	{
		if (!is_string($choice) && !is_array($choice)) {
			throw new \InvalidArgumentException('Choice must be a string or an array');
		}

		if (is_string($choice)) {
			$choice = [$choice => $choice];
		}

		$this->setChoices($choice + $this->getChoices());

		return $this;
	}

	public function apply(DB\QueryBuilder $queryBuilder)
	{
		$addressType = $this->getChoices() ?
			$this->getChoices() :
			'delivery';

		if (is_array($addressType)) {
			$addressType = array_shift($addressType);
		}

		$queryBuilder->where('address.type = ?s', [$addressType]);
	}
}