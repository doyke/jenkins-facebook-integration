<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use FHJ\Entities\User;

/**
 * UserEditController
 * @package FHJ\Controllers
 */
class UserEditController extends BaseController {

    const ROUTE_USER_EDIT = 'userEdit';

    public function editAction(Request $request, User $user) {
        $form = $this->defineForm($user);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            return $this->processEdit($form, $user);
        }
        
        return $this->getTemplateEngine()->render('userEdit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
            'cancelPath' => $this->generateRoute(UserListController::ROUTE_USER_LIST)
        )); 
    }
    
    private function defineForm(User $user) {
        
    }
    
    private function processEdit(Form $form, User $originalUser) {
        try {
            
            
            $this->getSession()->getFlashBag()->add('success', sprintf('The user "%s" has been successfully edited!',
                $originalUser->getEmail()));
            return $this->doRedirect(UserListController::ROUTE_USER_LIST);
        } catch (\Exception $e) {
            $this->getLogger()->addError(sprintf('error when saving edited user with id "%d"', $originalUser->getId()),
                array('exception' => $e));
            
            $this->getSession()->getFlashBag()->add('error', sprintf(
                'The edited user "%s" could not be saved to the database!', $originalUser->getEmail()));
            return $this->doRedirect(UserListController::ROUTE_USER_LIST);
        }
    }
}