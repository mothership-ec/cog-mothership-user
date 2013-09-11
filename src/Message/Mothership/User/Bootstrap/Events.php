<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Mothership\User\Controller\User;
use Message\Mothership\ControlPanel\Event\Event;
use Message\Cog\Bootstrap\EventsInterface;

class Events implements EventsInterface
{
	public function registerEvents($dispatcher)
	{
		//$dispatcher->addSubscriber(new User\EventListener);

		$dispatcher->addListener(Event::BUILD_MAIN_MENU, function($event) {
			$event->addItem('ms.user.user', 'Users', array(
				'ms.user.user'
			));
		});
	}
}