<?php

namespace Message\Mothership\User\Event;

use Message\Cog\Event\Event;

/**
 * Event for building the impersonate form.
 *
 * @author Laurence Roberts <laurence@message.co.uk>
 */
class ImpersonateFormEvent extends Event
{
	protected $_form;

	public function __construct($form)
	{
		$this->setForm($form);
	}

	public function setForm($form)
	{
		$this->_form = $form;
	}

	public function getForm()
	{
		return $this->_form;
	}
}