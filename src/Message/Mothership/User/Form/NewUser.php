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

		$this->add('title','choice','', array(
			'choices' => array(
				'mr'   => 'Mr',
				'miss' => 'Miss',
				'mrs'  => 'Mrs',
			)
		));

		$this->add('forename','text','');
		$this->add('surname','text','');
		$this->add('email','text','');

		$this->add('address_line_1','text','');
		$this->add('address_line_2','text','')
			->val()->optional();
		$this->add('address_line_3','text','')
			->val()->optional();
		$this->add('address_line_4','text','')
			->val()->optional();
		$this->add('town','text','');
		$this->add('postcode','text','');
		$this->add('state_id','text','')
			->val()->optional();
		$this->add('country_id','choice','', array(
			'choices' => $this->_container['country.list']->all()
		));
		$this->add('telephone','text','');


		$this->add('address_line_1','text','');
		$this->add('address_line_2','text','')
			->val()->optional();
		$this->add('address_line_3','text','')
			->val()->optional();
		$this->add('address_line_4','text','')
			->val()->optional();
		$this->add('town','text','');
		$this->add('postcode','text','');
		$this->add('state_id','text','')
			->val()->optional();
		$this->add('country_id','choice','', array(
			'choices' => $this->_container['country.list']->all()
		));
		$this->add('telephone','text','');

		return $this;
	}


/*			INSERT INTO
				user
			SET
				title      = :title?s,
				forename   = :forename?s,
				surname    = :surname?s,
				email      = :email?s,
				password   = :password?s,
				created_by = :created_by?in,
				created_at = :created_at?i',
			array(
				'title'      => $user->title,
				'forename'   => $user->forename,
				'surname'    => $user->surname,
				'email'      => $user->email,
				'password'   => $user->password,
				'created_by' => $user->authorship->createdBy()->id,
				'created_at' => $user->authorship->createdAt(),
			)
	*/

/*			INSERT INTO
				user_address
			SET
				user_id    	= :user_id?in,
				type   	   	= :type?s,
				line_1     	= :line_1?s,
				line_2     	= :line_2?s,
				line_3     	= :line_3?s,
				line_4     	= :line_4?s,
				town	   	= :town?s,
				postcode   	= :postcode?s,
				state_id	= :state_id?s,
				country_id	= :country_id?s,
				telephone	= :telephone?s,
				created_by 	= :created_by?in,
				created_at 	= :created_at?i',
			array(
				'user_id'    	=> $user->user_id,
				'type'   	   	=> $user->type,
				'line_1'     	=> $user->line_1,
				'line_2'     	=> $user->line_2,
				'line_3'     	=> $user->line_3,
				'line_4'     	=> $user->line_4,
				'town'	   		=> $user->town,
				'postcode'   	=> $user->postcode,
				'state_id'		=> $user->state_id,
				'country_id'	=> $user->country_id,
				'telephone'		=> $user->telephone,
				'created_by'	=> $user->authorship->createdBy()->id,
				'created_at' 	=> $user->authorship->createdAt(),
			)
	*/


}