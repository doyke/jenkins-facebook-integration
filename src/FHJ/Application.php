<?php


namespace FHJ;

use Silex\Application as BaseApplication;
use Silex\Route\SecurityTrait;

/**
 * Application
 * @package FHJ
 */
class Application extends BaseApplication {

	// includes ->secure(...) method: provides an authentication check before the controller is called
	use SecurityTrait;

} 