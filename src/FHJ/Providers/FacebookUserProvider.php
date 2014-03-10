<?php

namespace FHJ\Providers;

use FOS\FacebookBundle\Security\User\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use FHJ\Repositories\DbRepositoryInterface;

/**
 * FacebookUserProvider
 */
class FacebookUserProvider implements UserManagerInterface
{
	/**
	 * @var DbRepositoryInterface
	 */
	private $dbRepository;

	public function __construct(DbRepositoryInterface $dbRepository)
	{
		$this->dbRepository = $dbRepository;
	}

    /**
     * Creates an user.
     * 
     * @param string $uid The user id.
     * 
     * @return UserInterface
     */ 
	public function createUserFromUid($uid)
	{
		// TODO: Implement createUserFromUid() method.
	}

	/**
	 * Loads the user for the given username.
	 * This method must throw UsernameNotFoundException if the user is not
	 * found.
	 *
	 * @param string $uid The user id
	 *
	 * @return UserInterface
	 * @see UsernameNotFoundException
	 * @throws UsernameNotFoundException if the user is not found
	 */
	public function loadUserByUsername($uid)
	{
		// TODO: Implement loadUserByUsername() method.
	}

	/**
	 * Refreshes the user for the account interface.
	 * It is up to the implementation to decide if the user data should be
	 * totally reloaded (e.g. from the database), or if the UserInterface
	 * object can just be merged into some internal array of users / identity
	 * map.
	 *
	 * @param UserInterface $user
	 *
	 * @return UserInterface
	 * @throws UnsupportedUserException if the account is not supported
	 */
	public function refreshUser(UserInterface $user)
	{
		if ( !$user instanceof User )
		{
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
		}

		return $this->loadUserByUsername($user->getUsername());
	}

	/**
	 * Whether this provider supports the given user class
	 *
	 * @param string $class
	 *
	 * @return Boolean
	 */
	public function supportsClass($class)
	{
		return $class === 'Symfony\Component\Security\Core\User\User';
	}
}