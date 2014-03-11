<?php

namespace FHJ\Repositories;

use Symfony\Component\Security\Core\User\UserInterface;
use FHJ\Entities\User;

interface UserDbRepositoryInterface {

	/**
	 * @param $facebookUserId string Facebook user id
	 *
	 * @return UserInterface The created user
	 */
    public function createUser($facebookUserId);
    
    public function findUserByFacebookUserId($facebookUserId);
    
    public function findUserById($id);
    
    public function updateUser(User $user);
    
    public function deleteUser(User $user);

} 