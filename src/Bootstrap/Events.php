<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Mothership\ControlPanel\Event\Event;
use Message\Cog\Bootstrap\EventsInterface;
use Message\Mothership\User;

class Events implements EventsInterface
{
	public function registerEvents($dispatcher)
	{
		$dispatcher->addListener(Event::BUILD_MAIN_MENU, function($event) {
			$event->addItem('ms.cp.user.user', 'Users', array(
				'ms.cp.user.user'
			));
		});

		$dispatcher->addSubscriber(new User\EventListener);
	}
}