<?php

namespace Message\Mothership\User\Type;

use Message\Cog\Event\Event as BaseEvent;
use Message\User;

class Event extends BaseEvent
{
	private $_user;
	private $_profile;

	public function __construct(User\User $user, Profile $profile)
	{
		$this->_user = $user;
		$this->_profile = $profile;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getProfile()
	{
		return $this->_profile;
	}
}