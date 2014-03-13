<?php

namespace FHJ\Controllers;

/**
 * HomepageController
 * @package FHJ\Controllers
 */
class HomepageController extends BaseController {

	public function indexAction() {
		return $this->getTemplateEngine()->render('index.html.twig');
	}

}
