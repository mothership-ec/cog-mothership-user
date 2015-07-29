<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Event\Event as BaseEvent;
use Message\User;

/**
 * Class Event
 * @package Message\Mothership\User\Type
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Event to be fired when a profile is created or updated.
 */
class Event extends BaseEvent
{
	/**
	 * @var User\User
	 */
	private $_user;

	/**
	 * @var Profile
	 */
	private $_profile;

	/**
	 * @param User\User $user
	 * @param Profile $profile
	 */
	public function __construct(User\User $user, Profile $profile)
	{
		$this->_user = $user;
		$this->_profile = $profile;
	}

	/**
	 * @return User\User
	 */
	public function getUser()
	{
		return $this->_user;
	}

	/**
	 * @param User\User $user
	 */
	public function setUser(User\User $user)
	{
		$this->_user = $user;
	}

	/**
	 * @return Profile
	 */
	public function getProfile()
	{
		return $this->_profile;
	}

	/**
	 * @param Profile $profile
	 */
	public function setProfile(Profile $profile)
	{
		$this->_profile = $profile;
	}
}