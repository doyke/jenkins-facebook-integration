<?php

namespace FHJ\Repositories;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserDbRepository
 * @package FHJ\Repositories
 */
class UserDbRepository extends BaseRepository implements UserDbRepositoryInterface {

	/**
	 * @param $facebookUserId Facebook user id
	 *
	 * @return UserInterface The created user
	 */
    public function createUser($facebookUserId) {
        $this->getLogger()->addInfo('creating new user from facebook id', array('facebook_id' => $facebookUserId));


    }
    
    public function findUserByFacebookUserId($facebookUserId) {
	    $this->getLogger()->addInfo('looking up user by facebook id', array('facebook_id' => $facebookUserId));


    }
    
    public function updateUser(UserInterface $user) {
	    $this->getLogger()->addInfo('updating user', array('facebook_id' => $user->getUsername()));

    }

}
