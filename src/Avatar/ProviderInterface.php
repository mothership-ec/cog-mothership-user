<?php

namespace Message\Mothership\User\Avatar;

interface ProviderInterface
{
	public function getName();

	public function getAvatarUrl($email, $size = 100, $default = null);
}