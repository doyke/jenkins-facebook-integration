<?php

namespace FHJ\Controllers;

use FHJ\Models\UserModel;

/**
 * UserListController
 * @package FHJ\Controllers
 */
class UserListController extends BaseController {

    public function listAllAction() {
        $currentUser = $this->getSecurity()->getUser();
        $rawUsers = $this->getUserRepository()->findAllUsers();
        
        // The current user may not delete his own user to prevent strange errors
        $users = array();
        foreach ($rawUsers as $rawUser) {
            $currentUserModel = new UserModel($rawUser);
            $currentUserModel->setDeletable(!$currentUser->equals($rawUser));
            $currentUserModel->setProjectCount($this->getProjectRepository()->findProjectCountByUser($rawUser));
            
            $users[] = $currentUserModel;
        }
        
        return $this->getTemplateEngine()->render('users.html.twig', array(
            'users'  => $users
        ));
    }
    
}