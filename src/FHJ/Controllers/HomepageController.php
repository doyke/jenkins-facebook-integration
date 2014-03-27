<?php

namespace FHJ\Controllers;

use FHJ\Entities\User;
use FHJ\Facebook\Data\CachingFacebookDataRetriever;

/**
 * HomepageController
 * @package FHJ\Controllers
 */
class HomepageController extends BaseController {

    const ROUTE_HOMEPAGE = 'homepage';

	public function indexAction() {
		$realname = '';
		$isProjectCreationAllowed = false;

		if ($this->getSecurity()->getToken() !== null && $this->getSecurity()->getToken()->getUser() instanceof User) {
	        $facebookData = new CachingFacebookDataRetriever($this->getFacebookObject(), $this->getCache(),
	                $this->getLogger());
	        $realname = $facebookData->getRealname();
			$isProjectCreationAllowed = $this->getSecurity()->getToken()->getUser()->isProjectCreationAllowed();
		}
	    
		return $this->getTemplateEngine()->render('index.html.twig', array(
		    'realname' => $realname,
		    'mayCreateProjects' => $isProjectCreationAllowed,
		));
	}

}
