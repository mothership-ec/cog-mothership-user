<?php

namespace Message\Mothership\User\Report;

use Message\Cog\Location\CountryList;
use Message\Cog\Location\StateList;
use Message\Cog\DB;
use Message\Cog\Routing\UrlGenerator;

use Message\Mothership\Report\Report\AbstractReport;
use Message\Mothership\Report\Chart\TableChart;

class UserSummary extends AbstractReport
{
//	private $_filters;
	private $_countryList;
	private $_stateList;

	/**
	 * Constructor.
	 *
	 * @param DB\QueryBuilderFactory   $builderFactory
	 * @param UrlGenerator          $routingGenerator
	 * @param CountryList           $countryList
	 * @param StateList             $stateList
	 */
	public function __construct(
		DB\QueryBuilderFactory $builderFactory,
		UrlGenerator $routingGenerator,
		CountryList $countryList,
		StateList $stateList
	)
	{
		parent::__construct($builderFactory, $routingGenerator);
		$this->_setName('user_summary');
		$this->_setDisplayName('User Summary');
		$this->_setReportGroup('Users');
		$this->_charts = [new TableChart];
		$this->_countryList = $countryList;
		$this->_stateList = $stateList;
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
			'Email'   => 'string',
			'Name'    => 'string',
			'Address line 1' => 'string',
			'Address line 2' => 'string',
			'Address line 3' => 'string',
			'Address line 4' => 'string',
			'Town' => 'string',
			'Postcode' => 'string',
			'State' => 'string',
			'Country' => 'string',
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

//		$addressType = $this->_filters->exists('address_type') ? $this->_filters->get('address_type')->getChoices() : 'delivery';
		$addressType = 'delivery';

		if (is_array($addressType)) {
			$addressType = array_shift($addressType);
		}

		$queryBuilder
			->select('user.user_id AS "ID"')
			->select('user.created_at AS "Created"')
			->select('CONCAT(user.surname,", ",user.forename) AS "User"')
			->select('user.email AS "Email"')
			->select('address.line_1 AS "Line1"')
			->select('address.line_2 AS "Line2"')
			->select('address.line_3 AS "Line3"')
			->select('address.line_4 AS "Line4"')
			->select('address.town AS "Town"')
			->select('address.postcode AS "Postcode"')
			->select('address.state_id AS "State"')
			->select('address.country_id AS "Country"')
			->from('user')
			->leftJoin('address', 'user.user_id = address.user_id AND address.type = :addressType?s', 'user_address')
			->addParams(['addressType' => $addressType])
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
				foreach ($row as $name => $value) {
					$row->$name = is_string($value) ? trim($value) : $value;
				}

				$this->_getLocations($row);
				$result[] = [
					$row->Email,
					$row->User ? [
						'v' => utf8_encode(trim($row->User)),
						'f' => (string) '<a href ="'.$this->generateUrl('ms.cp.user.admin.detail.edit', [
								'userID' => $row->ID
							]).'">'.ucwords(utf8_encode(trim($row->User))).'</a>'
					] : $row->User,
					$row->Line1,
					$row->Line2,
					$row->Line3,
					$row->Line4,
					$row->Town,
					$row->Postcode,
					$row->State ?: '',
					$row->Country ?: '',
					[ 'v' => (int) $row->Created, 'f' => date('Y-m-d H:i', $row->Created)],
				];
			}

			return json_encode($result);
		} else {

			foreach ($data as $row) {
				$this->_getLocations($row);
				$result[] = [
					utf8_encode($row->Email),
					utf8_encode($row->User),
					utf8_encode($row->Line1),
					utf8_encode($row->Line2),
					utf8_encode($row->Line3),
					utf8_encode($row->Line4),
					utf8_encode($row->Town),
					utf8_encode($row->Postcode),
					utf8_encode($row->State),
					utf8_encode($row->Country),
					date('Y-m-d H:i', $row->Created),
				];
			}

			return $result;
		}
	}

	private function _getLocations($row)
	{
		$validCountries = array_keys($this->_stateList->all());

		if ($row->State && in_array($row->Country, $validCountries)) {
			if (array_key_exists($row->Country, $this->_stateList->all())) {
				$row->State = $this->_stateList->getByID($row->Country, $row->State);
			}
		}

		$row->Country = $this->_countryList->getByID($row->Country);
	}
}
