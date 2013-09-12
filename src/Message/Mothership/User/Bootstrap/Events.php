<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Mothership\User\Controller\User;
use Message\Mothership\ControlPanel\Event\Event;
use Message\Cog\Bootstrap\EventsInterface;

class Events implements EventsInterface
{
	public function registerEvents($dispatcher)
	{

		$dispatcher->addListener(Event::BUILD_MAIN_MENU, function($event) {
			$event->addItem('ms.cp.user.user', 'Users', array(
				'ms.cp.user.user'
			));
		});
	}
}