<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FHJ\Entities\User;

/**
 * UserDeleteController
 * @package FHJ\Controllers
 */
class UserDeleteController extends BaseController {

    const ROUTE_USER_DELETE = 'userDelete';
    
    public function deleteAction(Request $request, User $user) {
        // prevent deletion of currently logged-in user
        $currentLoggedUser = $this->getSecurity()->getToken()->getUser();
        if ($user->equals($currentLoggedUser)) {
            $this->getSession()->getFlashBag()->add('error', 'You cannot delete your own user!');
            return $this->doRedirect(ProjectListController::ROUTE_PROJECT_LIST_OWN);
        }
        
        if ($request->getMethod() === 'POST') {
            $this->doDelete($user);
        } else {
            return $this->getTemplateEngine()->render('message.html.twig', array(
                'active' => 'users',
                'message'  => sprintf('Do you really want to delete the user "%s" and all of his projects?',
                    $user->getEmail()),
                'mode' => 'deleteCancel',
                'deletePath' => $this->generateRoute(self::ROUTE_USER_DELETE, array('user' => $user->getId())),
                'cancelPath' => $this->generateRoute(UserListController::ROUTE_USER_LIST)
            )); 
        }
    }
    
    private function doDelete(User $user) {
        try {
            $this->getUserRepository()->deleteUser($user);
            
            $this->getSession()->getFlashBag()->add('success', sprintf(
                'The user "%s" and all of his projects have been successfully deleted!', $user->getEmail()));
            $this->doRedirect(ProjectListController::ROUTE_PROJECT_LIST_OWN);
        } catch (\Exception  $e) {
            $this->getLogger()->addError(sprintf('error when deleting user with id "%d"', $user->getId()),
                array('exception' => $e));
            
            $this->getSession()->getFlashBag()->add('error', sprintf(
                'The deletion of user "%s" and all of his projects failed!', $user->getEmail()));
            $this->doRedirect(UserListController::ROUTE_USER_LIST);
        }
    }
}