<?php

namespace FHJ\Repositories;

use Symfony\Component\Security\Core\User\UserInterface;
use FHJ\Entities\User;

interface UserDbRepositoryInterface {

	/**
	 * @param $facebookUserId string Facebook user id
	 * @param $email string Email address of user
	 * @param $accessToken string The facebook access token
	 *
	 * @return UserInterface The created user
	 */
    public function createUser($facebookUserId, $email, $accessToken);
    
    public function updateUser(User $user);
    
    public function deleteUser(User $user);
    
    public function findAllUsers();
    
    public function findUserByFacebookUserId($facebookUserId);
    
    public function findUserById($id);
    
    public function findAllUsersCount();

} 