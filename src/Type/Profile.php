<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Field;
use Message\Cog\ValueObject\Collection as BaseCollection;

/**
 * Class Profile
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 */
class Profile extends BaseCollection
{
	/**
	 * @var UserTypeInterface
	 */
	private $_type;

	/**
	 * @param UserTypeInterface $type
	 */
	public function __construct(UserTypeInterface $type)
	{
		$this->_type = $type;

		parent::__construct();
	}

	/**
	 * Get the user type
	 *
	 * @return UserTypeInterface
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param $var
	 * @param Field\FieldInterface $value
	 */
	public function __set($var, Field\FieldInterface $value)
	{
		if ($this->exists($var)) {
			$this->remove($var);
		}

		$this->add($value);
	}

	/**
	 * @param $var
	 *
	 * @return Field\FieldInterface | null
	 */
	public function __get($var)
	{
		if ($this->exists($var)) {
			return $this->get($var);
		}

		return null;
	}

	/**
	 * Populate the profile with data from an array
	 *
	 * @param array $data
	 */
	public function update(array $data)
	{
		foreach ($data as $name => $value) {
			$part = $this->$name;

			if (!$part) {
				continue;
			}

			if ($part instanceof Field\RepeatableContainer) {
				$part->clear();
				$key = 0;
				foreach ($value as $row) {
					foreach ($row as $fieldName => $fieldValue) {
						$part->get($key)
							->$fieldName
							->setValue($fieldValue)
						;

						++$key;
					}
				}
			} elseif ($part instanceof Field\Group) {
				foreach ($value as $fieldName => $fieldValue) {
					$part->$fieldName->setValue($fieldValue);
				}
			} else {
				$part->setValue($value);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _configure()
	{
		$this->addValidator(function ($item) {
			if (!$item instanceof Field\FieldInterface) {
				$type = gettype($item) === 'object' ? get_class($item) : gettype($item);
				throw new \InvalidArgumentException('Objects passed to user profile must be instances of Message\\Cog\\Field\\FieldInterface, ' . $type . ' given');
			}
		});

		$this->setKey(function ($item) {
			return $item->getName();
		});
	}
}