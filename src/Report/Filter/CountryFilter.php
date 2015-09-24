<?php

namespace Message\Mothership\User\Report\Filter;

use Message\Mothership\Report\Filter\Choices;
use Message\Cog\Location\CountryList;

class CountryFilter extends Choices
{
	const NAME = 'country';

	public function __construct(CountryList $countryList, $label = 'Country')
	{
		$this->_countryList = $countryList;
		$choices = $this->_getCountryChoices();
		$this->setFormChoices($choices);

		parent::__construct(self::NAME, $label, null, true);
	}

	public function getChoices()
	{
		$choices = parent::getChoices();

		if (null === $choices) {
			return $choices;
		}

		if (!is_array($choices)) {
			$choices = [$choices];
		}

		if (in_array('EU', $choices)) {
			$choices = $choices + array_keys($this->_countryList->getEU());
			foreach ($choices as $key => $choice) {
				if ($choice === 'EU') {
					unset($choices[$key]);
				}
			}
		}

		return array_values($choices);
	}

	private function _getCountryChoices()
	{
		$eu = ['EU' => 'EU'];

		return $eu + $this->_countryList->all();
	}
}