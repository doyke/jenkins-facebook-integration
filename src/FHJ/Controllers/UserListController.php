<?php

namespace FHJ\Controllers;

/**
 * UserListController
 * @package FHJ\Controllers
 */
class UserListController extends BaseController {

    public function listAllAction() {
        $currentUser = $this->getSecurity()->getUser();
        $users = $this->getUserRepository()->findAllUsers();
        
        // The current user may not delete his own user to prevent strange errors
        foreach ($users as &$user) {
            $user['deletable'] = !$currentUser->equals($user);
        }
        
        return $this->getTemplateEngine()->render('users.html.twig', array(
            'users'  => $users
        ));
    }
    
}