<?php

namespace FHJ\Repositories;

use Symfony\Component\Security\Core\User\UserInterface;

class DbRepository extends BaseRepository implements DbRepositoryInterface {

    /**
     * @return UserInterface The created user
     */
    public function createUser($facebookUserId) {
        
        
    }
    
    public function findUserByFacebookUserId($facebookUserId) {
        
    }
    
    public function updateUser(UserInterface $user) {
        
    }

}
