<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FHJ\Entities\User;

/**
 * UserEditController
 * @package FHJ\Controllers
 */
class UserEditController extends BaseController {

    const ROUTE_USER_EDIT = 'userEdit';

    public function editAction(Request $request, User $user) {
        
    }
    
}