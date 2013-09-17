<?php

namespace Message\Mothership\User\Form;

use Message\Cog\Form\Handler;
use Message\Cog\Service\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Message\User\UserInterface;
use Message\Mothership\User\User;


class UserDetails extends Handler
{

	public function __construct(Container $container)
	{
		parent::__construct($container);
	}

	public function buildForm(UserInterface $user, $action = '')
	{

		$defaults = array();
		if (!is_null($user)) {
			$defaults = array(
				'title'    => $user->title,
				'forename' => $user->forename,
				'surname'  => $user->surname,
				'email'    => $user->email,
			);
		}

		$this->setMethod('POST')
			->setDefaultValues($defaults)
			->setAction($action);

		$this->add('title','choice','', array(
			'choices'  => array(
				'mr'   => 'Mr',
				'miss' => 'Miss',
				'mrs'  => 'Mrs',
			)
		));

		$this->add('forename','text','');
		$this->add('surname','text','');
		$this->add('email','text','');

		$this->add('reset_password', 'checkbox', 'Send Reset Password Email?');

		return $this;

	}

}