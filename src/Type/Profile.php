<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Field;
use Message\Cog\ValueObject\Collection as BaseCollection;

class Profile extends BaseCollection
{
	/**
	 * @var UserTypeInterface
	 */
	private $_type;

	public function __construct(UserTypeInterface $type)
	{
		$this->_type = $type;

		parent::__construct();
	}

	public function getType()
	{
		return $this->_type;
	}

	public function __set($var, Field\FieldInterface $value)
	{
		if ($this->exists($var)) {
			$this->remove($var);
		}

		$this->add($value);
	}

	public function __get($var)
	{
		if ($this->exists($var)) {
			return $this->get($var);
		}

		return null;
	}

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