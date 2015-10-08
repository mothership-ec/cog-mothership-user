<?php

namespace Message\Mothership\User\Report\Filter;

use Message\Cog\DB;
use Message\Cog\Location\CountryList;
use Message\Mothership\Report\Filter\Choices;
use Message\Mothership\Report\Filter\ModifyQueryInterface;

class CountryFilter extends Choices implements ModifyQueryInterface
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

	public function apply(DB\QueryBuilder $queryBuilder)
	{
		if ($this->getChoices()) {
			$queryBuilder->where('address.country_id IN (?js)', [$this->getChoices()]);
		}
	}

	private function _getCountryChoices()
	{
		$eu = ['EU' => 'EU'];

		return $eu + $this->_countryList->all();
	}
}