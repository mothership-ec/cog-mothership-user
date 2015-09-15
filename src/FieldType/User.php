<?php

namespace Message\Mothership\User\FieldType;

use Message\Cog\Field;
use Message\Cog\DB;
use Message\User as BaseUser;
use Message\Mothership\User\Type;
use Message\Mothership\User\Loader as UserLoader;

class User extends Field\Field
{
	/**
	 * @var Type\Collection
	 */
	private $_userTypes;

	/**
	 * @var UserLoader
	 */
	private $_userLoader;

	/**
	 * @var BaseUser\Loader
	 */
	private $_baseUserLoader;

	/**
	 * @var Type\ProfileLoader
	 */
	private $_profileLoader;

	/**
	 * @var Type\UserTypeInterface
	 */
	private $_type;

	/**
	 * @var BaseUser\User
	 */
	private $_user;

	/**
	 * @var Type\Profile
	 */
	private $_profile;

	public function __construct(
		Type\Collection $userTypes,
		UserLoader $userLoader,
		Type\ProfileLoader $profileLoader,
		BaseUser\Loader $baseUserLoader
	)
	{
		$this->_userTypes = $userTypes;
		$this->_userLoader = $userLoader;
		$this->_baseUserLoader = $baseUserLoader;
		$this->_profileLoader = $profileLoader;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFieldType()
	{
		return 'user';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormType()
	{
		$this->_setFieldOptions();

		return 'choice';
	}

	/**
	 * Set the user type to determine which user choices will be set.
	 * An exception will be thrown if this method is not called when using this field.
	 *
	 * @param string | Type\UserTypeInterface $type
	 *
	 * @return User
	 */
	public function setUserType($type)
	{
		if (!is_string($type) && !$type instanceof Type\UserTypeInterface) {
			throw new \InvalidArgumentException('User type must be a string or an instance of UserTypeInterface');
		}

		$this->_type = is_string($type) ? $this->_userTypes->get($type) : $type;

		return $this;
	}

	/**
	 * Get the user assigned to the field
	 *
	 * @return BaseUser\User | null
	 */
	public function getUser()
	{
		if (null === $this->_user) {
			if (!$this->getValue()) {
				return null;
			}

			$user = $this->_baseUserLoader->getByID($this->getValue());

			if (is_array($user)) {
				$user = array_shift($user);
			}

			if ($user) {
				$this->_user = $user;
			}
		}

		return $this->_user;
	}

	/**
	 * Get the profile of the user assigned to the field
	 *
	 * @return Type\Profile | null
	 */
	public function getUserProfile()
	{
		if (null === $this->_profile) {
			if (!$this->getUser()) {
				return null;
			}

			$this->_profile = $this->_profileLoader->getByUser($this->getUser());
		}

		return $this->_profile;
	}

	/**
	 * Shorthand alias for getUserProfile()
	 *
	 * @return Type\Profile | null
	 */
	public function getProfile()
	{
		return $this->getUserProfile();
	}

	/**
	 * Load all users by type and set them as the choices for the form field
	 */
	private function _setFieldOptions()
	{
		$options = $this->getFieldOptions();

		if (empty($options['choices'])) {

			if (!$this->_type) {
				throw new \LogicException('User type must be set');
			}

			$users = $this->_userLoader->getByType($this->_type);
			$choices = [];

			if ($users) {
				foreach ($users as $user) {
					$choices[$user->id] = $user->getName();
				}
			}

			$options = array_merge($options, ['choices' => $choices]);

			$this->setFieldOptions($options);
		}
	}
}