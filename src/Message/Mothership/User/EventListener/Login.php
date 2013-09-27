<?php

namespace Message\Mothership\User\EventListener;

use Message\User\Event\Event;

use Message\Cog\Event\SubscriberInterface;
use Message\Cog\Event\EventListener;

/**
 * Event listener for when a user logs in to the system.
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
class Login extends EventListener implements SubscriberInterface
{
	/**
	 * {@inheritDoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(Event::LOGIN => array(
			array('checkUserSubscription')
		));
	}

	public function checkUserSubscription(Event $event)
	{
		$user = $this->_services['user.current']
		$user->emailSubscription = $this->_services['user.subscription.loader']->getByUser($user);

		return $user;
	}
}