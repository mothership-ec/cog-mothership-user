<?php

namespace Message\Mothership\User\Form;

use Message\Cog\Form\Handler;
use Message\Cog\Service\Container;
use Message\User\UserInterface;
use Message\Mothership\User\Event\ImpersonateFormEvent;

class Impersonate extends Handler {

	public function __construct(Container $container)
	{
		parent::__construct($container);
	}

	public function buildForm(UserInterface $user, $action = '')
	{
		$this->setMethod('POST')
			 ->setAction($action);

		$event = new ImpersonateFormEvent($this);

		// Fire build event
		$event = $this->_container['event.dispatcher']->dispatch('ms.cp.user.impersonate.form.build', $event);

		return $event->getForm();
	}

}