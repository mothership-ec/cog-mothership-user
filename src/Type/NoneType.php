<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Field\Factory;

class NoneType implements UserTypeInterface
{
	public function getName()
	{
		return 'none';
	}

	public function getDisplayName()
	{
		return 'None';
	}

	public function getDescription()
	{
		return 'No user type';
	}

	public function setFields(Factory $factory)
	{

	}
}