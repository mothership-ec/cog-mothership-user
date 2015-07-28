<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Field\Factory;
use Message\Mothership\FileManager\File;
use Symfony\Component\Validator\Constraints;

class TeamMemberType implements UserTypeInterface
{
	public function getName()
	{
		return 'team-member';
	}

	public function getDisplayName()
	{
		return 'Team member';
	}

	public function getDescription()
	{
		return 'A member of the company, this profile type has basic information regarding their role';
	}

	public function setFields(Factory $factory)
	{
		$factory->add($factory->getField('text', 'job_title', 'Job title'));

		$factory->add($factory->getField('richtext', 'biography', 'Biography'));

		$factory->add($factory->getField('file', 'profile_picture', 'Profile picture')
			->setAllowedTypes(File\Type::IMAGE))
		;
	}
}