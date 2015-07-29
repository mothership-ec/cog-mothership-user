<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Field;

/**
 * Class ProfileFactory
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Class for creating new instances of Profile by loading the type and setting up the fields with the field factory
 */
class ProfileFactory
{
	/**
	 * @var Collection
	 */
	private $_userTypes;

	/**
	 * @var Field\Factory
	 */
	private $_fieldFactory;

	/**
	 * @param Collection $userTypes
	 * @param Field\Factory $fieldFactory
	 */
	public function __construct(Collection $userTypes, Field\Factory $fieldFactory)
	{
		$this->_userTypes = $userTypes;
		$this->_fieldFactory = $fieldFactory;
	}

	/**
	 * Get a new instance of Profile with the fields set up
	 *
	 * @param $type
	 *
	 * @return Profile
	 */
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

	/**
	 * Clone the field factory and return it
	 *
	 * @return Field\Factory
	 */
	private function _getFieldFactory()
	{
		return clone $this->_fieldFactory;
	}
}