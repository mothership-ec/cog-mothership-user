<?php

namespace Message\Mothership\User\Avatar;

class Gravatar implements ProviderInterface
{
	const PREFIX = 'http://gravatar.com/avatar';

	public function getName()
	{
		return 'gravatar';
	}

	public function getAvatar($email, $size = 100, $default = null)
	{
		if (!is_int($size)) {
			throw new \InvalidArgumentException('Size must be an integer');
		}

		$default = $this->_parseDefault($default);
		$hash    = $this->_getHash($email);
		$url     =  $this->_buildUrl($hash, $size, $default);

		$avatar = new Avatar;
		$avatar->setUrl($url);
		$avatar->setSize($size);
		$avatar->setDefault($default);

		return $avatar;
	}

	private function _parseDefault($default)
	{
		if (null !== $default && !is_string($default)) {
			throw new \InvalidArgumentException('Default path must be a string');
		}

		if (null !== $default) {
			$default = urldecode($default);
			$default = urlencode($default);
		}

		return ($default) ?: urlencode('/cogules/Message:Mothership:User/images/avatar.png');
	}

	private function _buildUrl($hash,  $size, $default)
	{
		return self::PREFIX . '/' . $hash . '?s=' . $size . '&amp;d='. $default . '"&amp;r=g';
	}

	private function _getHash($email)
	{
		if (!is_string($email)) {
			throw new \InvalidArgumentException('Email address must be a string, ' . gettype($email) . ' given');
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new \LogicException('`' . $email . '` is not a valid email address');
		}

		return  md5(strtolower(trim($email)));
	}
}