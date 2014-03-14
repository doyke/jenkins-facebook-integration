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

    public function deleteAction(Request $request, User $user) {
        // prevent deletion of currently logged-in user
    }
    
}