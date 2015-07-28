<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Field;

class ProfileFactory
{
	private $_userTypes;
	private $_fieldFactory;

	public function __construct(Collection $userTypes, Field\Factory $fieldFactory)
	{
		$this->_userTypes = $userTypes;
		$this->_fieldFactory = $fieldFactory;
	}

	public function getProfile($type)
	{
		if (is_string($type)) {
			$type = $this->_userTypes->get($type);
		} elseif (!$type instanceof UserTypeInterface) {
			$varType = gettype($type) === 'object' ? get_class($type) : gettype($type);
			throw new \InvalidArgumentException('Type must be either a string or an instance of UserTypeInterface, ' . $varType . ' given');
		}

		$profile = new Profile($type);

		$factory = $this->_getFieldFactory();
		$factory->build($type);

		foreach ($factory as $name => $field) {
			$profile->$name = ($field instanceof Field\Group && $field->isRepeatable())
				? new Field\RepeatableContainer($field)
				: $field;
		}

		return $profile;
	}

	private function _getFieldFactory()
	{
		return clone $this->_fieldFactory;
	}
}