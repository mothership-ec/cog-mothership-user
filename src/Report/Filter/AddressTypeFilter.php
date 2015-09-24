<?php

namespace Message\Mothership\User\Report\Filter;

use Message\Mothership\Report\Filter\Choices;

class AddressTypeFilter extends Choices
{
	const NAME = 'address_type';

	public function __construct($label = null, array $choices = null)
	{
		if (null === $choices) {
			$choices = [
				'delivery' => 'Delivery',
				'billing' => 'Billing',
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
}