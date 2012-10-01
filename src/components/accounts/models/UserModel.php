<?php
namespace Blocks;

/**
 * User model class
 */
class UserModel extends BaseModel
{
	public function defineAttributes()
	{
		return array(
			'id'                           => AttributeType::Number,
			'username'                     => AttributeType::String,
			'email'                        => AttributeType::Email,
			'password'                     => AttributeType::String,
			'encType'                      => AttributeType::String,
			'language'                     => AttributeType::Language,
			'emailFormat'                  => array(AttributeType::String, 'default' => 'text'),
			'admin'                        => AttributeType::Bool,
			'status'                       => AttributeType::Enum,
			//'authSessionToken'           => AttributeType::String,
			'lastLoginDate'                => AttributeType::DateTime,
			//'lastLoginAttemptIPAddress'  => AttributeType::String,
			//'invalidLoginWindowStart'    => AttributeType::DateTime,
			'invalidLoginCount'            => AttributeType::Number,
			'lastInvalidLoginDate'         => AttributeType::DateTime,
			'lockoutDate'                  => AttributeType::DateTime,
			//'verificationCode'           => AttributeType::String,
			//'verificationCodeIssuedDate' => AttributeType::DateTime,
			'passwordResetRequired'        => AttributeType::Bool,
			//'lastPasswordChangeDate'     => AttributeType::DateTime,
			//'archivedUsername'           => AttributeType::String,
			//'archivedEmail'              => AttributeType::Email,

			'newPassword'                  => AttributeType::String,
		);
	}

	public function save()
	{
		return blx()->account->saveUser($this);
	}

	/**
	 * Returns the user's profile.
	 *
	 * @return UserProfileModel|null
	 */
	public function getProfile()
	{
		if (Blocks::hasPackage(BlocksPackage::Users))
		{
			return blx()->userProfiles->getProfileByUserId($this->id);
		}
	}

	/**
	 * Returns the user's groups.
	 *
	 * @return array|null
	 */
	public function getGroups()
	{
		if (Blocks::hasPackage(BlocksPackage::Users))
		{
			return blx()->userGroups->getGroupsByUserId($this->id);
		}
	}

	/**
	 * Gets the user's full name.
	 *
	 * @return string|null
	 */
	public function getFullName()
	{
		if (Blocks::hasPackage(BlocksPackage::Users))
		{
			$profile = $this->getProfile();
			if ($profile)
			{
				return $profile->getFullName();
			}
		}
	}

	/**
	 * Gets the user's first name or username.
	 *
	 * @return string|null
	 */
	public function getFriendlyName()
	{
		if (Blocks::hasPackage(BlocksPackage::Users))
		{
			$profile = $this->getProfile();
			if ($profile && $profile->firstName)
			{
				return $profile->firstName;
			}
		}

		return $this->username;
	}

	/**
	 * Returns whether this is the current logged-in user.
	 *
	 * @return bool
	 */
	function isCurrent()
	{
		if ($this->id)
		{
			$currentUser = blx()->account->getCurrentUser();
			if ($currentUser)
			{
				return ($this->id == $currentUser->id);
			}
		}

		return false;
	}

	/**
	 * Returns the remaining cooldown time for this user, if they've entered their password incorrectly too many times.
	 *
	 * @return mixed
	 */
	public function getRemainingCooldownTime()
	{
		if ($this->status == UserStatus::Locked)
		{
			$cooldownEnd = $this->lockoutDate + blx()->config->getCooldownDuration();
			$cooldownRemaining = $cooldownEnd - DateTimeHelper::currentTime();

			if ($cooldownRemaining > 0)
			{
				return $cooldownRemaining;
			}
		}
	}
}
