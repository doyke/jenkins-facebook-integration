<?php


namespace FHJ\Framework;

use Silex\Route;
use Silex\Route\SecurityTrait;

/**
 * SecuredRoute
 * @package FHJ\Framework
 */
class SecuredRoute extends Route {

	// this provides us the secure() method for restricting a controller method to certain user roles
	use SecurityTrait;

} 