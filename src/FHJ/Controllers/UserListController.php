<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * UserListController
 * @package FHJ\Controllers
 */
class UserListController extends BaseController {

    public function listAllAction(Request $request) {
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