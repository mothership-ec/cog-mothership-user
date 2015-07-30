<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Field\Factory;

/**
 * Class NoneType
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * 'Null' type for a user. If the user has this type, the 'Profile' tab will not appear.
 */
class NoneType implements UserTypeInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'none';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDisplayName()
	{
		return 'None';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDescription()
	{
		return 'No user type';
	}

	/**
	 * {@inheritDoc}
	 */
	public function setFields(Factory $factory)
	{

	}
}