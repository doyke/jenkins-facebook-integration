<?php

namespace FHJ\Controllers;

use FHJ\Facebook\CachingFacebookDataRetriever;

/**
 * HomepageController
 * @package FHJ\Controllers
 */
class HomepageController extends BaseController {

    const ROUTE_HOMEPAGE = 'homepage';

	public function indexAction() {
	    $facebookData = new CachingFacebookDataRetriever($this->getFacebookObject(), $this->getCache(),
	        $this->getLogger());
	    $realname = $facebookData->getRealname();
	    $isProjectCreationAllowed = $this->getSecurity()->getUser()->isProjectCreationAllowed();
	    
		return $this->getTemplateEngine()->render('index.html.twig', array(
		    'realname' => $realname,
		    'mayCreateProjects' => $isProjectCreationAllowed,
		));
	}

}
