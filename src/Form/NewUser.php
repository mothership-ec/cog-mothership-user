<?php

namespace Message\Mothership\User\Form;

use Message\Cog\Form\Handler;
use Message\Cog\Service\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Message\User\UserInterface;
use Message\Mothership\Commerce\Address\Address;

class NewUser extends Handler
{

	public function __construct(Container $container)
	{
		parent::__construct($container);
	}

	public function buildForm($action = '')
	{

		$this->setAction($action);

		// get the current locale that mothership is using, then pass the correct
		// titles/honoraries to the form builder.
		$locale = explode("_", $this->_container['locale']->getId())[0];
		$titles = $this->_container['cfg']->titles->{$locale};

		$this->add('title','choice','', array(
			'choices'  => $titles
		));

		$this->add('forename','text','');
		$this->add('surname','text','');
		$this->add('email','text','');
		$this->add('password','text','');

		return $this;
	}

}
