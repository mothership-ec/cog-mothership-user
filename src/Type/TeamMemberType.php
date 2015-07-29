<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Field\Factory;
use Message\Mothership\FileManager\File;
use Symfony\Component\Validator\Constraints;

/**
 * Class TeamMemberType
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Pre-packaged user type. This type represents a member of the team and contains basic profile information.
 */
class TeamMemberType implements UserTypeInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'team-member';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDisplayName()
	{
		return 'Team member';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDescription()
	{
		return 'A member of the company, this profile type has basic information regarding their role';
	}

	/**
	 * {@inheritDoc}
	 */
	public function setFields(Factory $factory)
	{
		$factory->add($factory->getField('text', 'job_title', 'Job title'));

		$factory->add($factory->getField('richtext', 'biography', 'Biography'));

		$factory->add($factory->getField('file', 'profile_picture', 'Profile picture')
			->setAllowedTypes(File\Type::IMAGE))
		;
	}
}