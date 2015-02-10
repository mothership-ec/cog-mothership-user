<?php

namespace Message\Mothership\User\Address;

use Message\Cog\ValueObject\Authorship;

class Address
{
	const AMOUNT_LINES = 4;

	public $id;
	public $userID;
	public $type;
	public $lines;
	public $town;
	public $stateID;
	public $state;
	public $postcode;
	public $country;
	public $countryID;
	public $telephone;

	public $authorship;

	public function __construct()
	{
		$this->authorship = new Authorship;
		$this->authorship->enableUpdate();

		for($i = 1; $i <= self::AMOUNT_LINES; ++$i) {
			$this->lines[$i] = null;
		}
	}

	public function setLines(array $lines)
	{
		if (count($lines) > self::AMOUNT_LINES) {
			throw new \InvalidArgumentException(sprintf('An Address can only have %d lines, `%s` passed', self::AMOUNT_LINES, count($lines)));
		}

		$i = 1;

		foreach ($lines as $line) {
			$this->lines[$i] = $line;

			$i++;
		}
	}

	/**
	 * Flatten the address into a single array. Any falsey values are not added
	 * as elements. This is handy for showing the address with line breaks.
	 *
	 * The following fields are included:
	 *
	 * * All lines
	 * * Town
	 * * State
	 * * Postcode
	 * * Country
	 * * Telephone
	 *
	 * @return array
	 */
	public function flatten()
	{
		$lines = array();

		foreach ($this->lines as $line) {
			if ($line) {
				$lines[] = $line;
			}
		}

		if ($this->town) {
			$lines[] = $this->town;
		}

		if ($this->state) {
			$lines[] = $this->state;
		}

		if ($this->postcode) {
			$lines[] = $this->postcode;
		}

		if ($this->country) {
			$lines[] = $this->country;
		}

		if ($this->telephone) {
			$lines[] = $this->telephone;
		}

		return $lines;
	}
}