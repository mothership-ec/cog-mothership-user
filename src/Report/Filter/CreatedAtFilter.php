<?php

namespace Message\Mothership\User\Report\Filter;

use Message\Cog\DB;
use Message\Mothership\Report\Filter\DateRange;
use Message\Mothership\Report\Filter\ModifyQueryInterface;

class CreatedAtFilter extends DateRange implements ModifyQueryInterface
{
	public function getForm()
	{
		$this->setFormType('date');

		return parent::getForm();
	}

	public function apply(DB\QueryBuilder $queryBuilder)
	{
		if ($this->getStartDate()) {
			$queryBuilder->where('user.created_at >= ?d', [$this->getStartDate()]);
		}

		if ($this->getEndDate()) {
			$queryBuilder->where('user.created_At <= ?d', [$this->getEndDate()]);
		}
	}

}