<?php

namespace FHJ\Repositories;

use Symfony\Component\Security\Core\User\UserInterface;

interface DbRepositoryInterface {
    
    /**
     * @return UserInterface The created user
     */
    public createUser($facebookUserId);
    
    public findUserByFacebookUserId($facebookUserId);
    
    public updateUser(UserInterface $user);

} 