<?php

namespace Message\Mothership\User\Avatar;

/**
 * Interface ProviderInterface
 * @package Message\Mothership\User\Avatar
 */
interface ProviderInterface
{
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $email
	 * @param int $size
	 * @param string | null $default
	 *
	 * @return Avatar
	 */
	public function getAvatar($email, $size = 100, $default = null);
}