<?php

namespace FHJ\Repositories;

use Symfony\Component\Security\Core\User\UserInterface;

class DbRepository extends BaseRepository implements DbRepositoryInterface {

	/**
	 * @param $facebookUserId Facebook user id
	 *
	 * @return UserInterface The created user
	 */
    public function createUser($facebookUserId) {
        
        
    }
    
    public function findUserByFacebookUserId($facebookUserId) {
        
    }
    
    public function updateUser(UserInterface $user) {
        
    }

}
