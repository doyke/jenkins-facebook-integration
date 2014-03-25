<?php

namespace FHJ\Controllers;

use FHJ\Facebook\Data\CachingFacebookDataRetriever;

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
	    $isProjectCreationAllowed = false;
		if ($this->getSecurity()->getToken() !== null) {
			$isProjectCreationAllowed= $this->getSecurity()->getToken()->getUser()->isProjectCreationAllowed();
		}
	    
		return $this->getTemplateEngine()->render('index.html.twig', array(
		    'realname' => $realname,
		    'mayCreateProjects' => $isProjectCreationAllowed,
		));
	}

}
