<?php

namespace Message\Mothership\User\Report;

use Message\Cog\DB\QueryBuilderInterface;
use Message\Cog\DB\QueryBuilderFactory;
use Message\Cog\Routing\UrlGenerator;

use Message\Mothership\Report\Report\AbstractReport;
use Message\Mothership\Report\Chart\TableChart;

class UserSummary extends AbstractReport
{
	/**
	 * Constructor.
	 *
	 * @param QueryBuilderFactory   $builderFactory
	 * @param UrlGenerator          $routingGenerator
	 */
	public function __construct(QueryBuilderFactory $builderFactory, UrlGenerator $routingGenerator)
	{
		parent::__construct($builderFactory, $routingGenerator);
		$this->_setName('user_summary');
		$this->_setDisplayName('User Summary');
		$this->_setReportGroup('Users');
		$this->_charts = [new TableChart];
	}

	/**
	 * Retrieves JSON representation of the data and columns.
	 * Applies data to chart types set on report.
	 *
	 * @return array  Returns all types of chart set on report with appropriate data.
	 */
	public function getCharts()
	{
		$data = $this->_dataTransform($this->_getQuery()->run(), "json");
		$columns = $this->_parseColumns($this->getColumns());

		foreach ($this->_charts as $chart) {
			$chart->setColumns($columns);
			$chart->setData($data);
		}

		return $this->_charts;
	}

	/**
	 * Set columns for use in reports.
	 *
	 * @return array  Returns array of columns as keys with format for Google Charts as the value.
	 */
	public function getColumns()
	{
		return [
			'Name'    => 'string',
			'Email'   => 'string',
			'Created' => 'number',
		];
	}

	/**
	 * Gets all user data.
	 *
	 * @return Query
	 */
	protected function _getQuery()
	{
		$queryBuilder = $this->_builderFactory->getQueryBuilder();

		$queryBuilder
			->select('user.user_id AS "ID"')
			->select('created_at AS "Created"')
			->select('CONCAT(surname,", ",forename) AS "User"')
			->select('email AS "Email"')
			->from('user')
			->orderBy('surname')
		;

		return $queryBuilder->getQuery();
	}

	/**
	 * Takes the data and transforms it into a useable format.
	 *
	 * @param  $data    DB\Result  The data from the report query.
	 * @param  $output  string     The type of output required.
	 *
	 * @return string|array  Returns data as string in JSON format or array.
	 */
	protected function _dataTransform($data, $output = null)
	{
		$result = [];

		if ($output === "json") {

			foreach ($data as $row) {

				$result[] = [
					$row->User ? [ 'v' => utf8_encode($row->User), 'f' => (string) '<a href ="'.$this->generateUrl('ms.cp.user.admin.detail.edit', ['userID' => $row->ID]).'">'.ucwords(utf8_encode($row->User)).'</a>' ] : $row->User,
					$row->Email,
					[ 'v' => $row->Created, 'f' => date('Y-m-d H:i', $row->Created)],
				];

			}
			return json_encode($result);

		} else {

			foreach ($data as $row) {
				$result[] = [
					utf8_encode($row->User),
					$row->Email,
					date('Y-m-d H:i', $row->Created),
				];
			}
			return $result;

		}
	}
}
