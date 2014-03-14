<?php


namespace FHJ\Framework;

use Silex\Route;
use Silex\Route\SecurityTrait;

/**
 * SecuredRoute
 * @package FHJ\Framework
 */
class SecuredRoute extends Route {

	use SecurityTrait;

} 