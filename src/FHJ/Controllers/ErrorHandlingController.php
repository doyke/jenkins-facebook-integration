<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ErrorHandlingController
 * @package FHJ\Controllers
 */
class ErrorHandlingController extends BaseController {

    /**
     * @var \Twig_Environment
     */
    private $templateEngine;
    
    public function __construct(\Twig_Environment $templateEngine) {
        $this->templateEngine = $templateEngine;
    }

	public function handleErrorAction(\Exception $e, $code, Request $request) {
		$this->getLogger()->addError(sprintf('An exception occured: %s', $e->getMessage()), array('exception' => $e));

        // In debug mode we do not want to see the error screen. We want to see the REAL things instead!
		if ($this->isDebug()) {
			return;
		}

        // check for AJAX or JSON call
        $isSlimCall = $request->isXmlHttpRequest();

        $messageTitle = $messageContent = $pageTitle = null;
        $httpStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

		switch (true) {
			case $e instanceof AccessDeniedHttpException: // access denied
				$messageTitle = 'Access is denied';
				$messageContent = 'You are not allowed to view the requested resource.';
				$pageTitle = 'Access is denied';
				$httpStatusCode = Response::HTTP_FORBIDDEN;
				break;
				
			case $e instanceof NotFoundHttpException: // not found
				$messageTitle = 'The requested page could not be found.';
				$pageTitle = 'Not found';
				$httpStatusCode = Response::HTTP_NOT_FOUND;
				break;
				
			case $e instanceof NotFoundHttpException: // internal server error
				$messageTitle = 'Internal error';
				$messageContent = 'The application encountered an internal error. Please try again later.';
				$pageTitle = 'Internal error';
				// $httpStatusCode is already set to Response::HTTP_INTERNAL_SERVER_ERROR
				break;
				
			default: // unknown error
			    $messageTitle = 'Unknown error';
				$messageContent = 'We are sorry, but something went terribly wrong.';
				// $httpStatusCode is already set to Response::HTTP_INTERNAL_SERVER_ERROR
				break;
		}

		if ($isSlimCall) {
		    return $this->renderSlimMessage($httpStatusCode, $messageTitle);
		}
		
		return $this->renderFullMessage($httpStatusCode, $messageTitle, $messageContent, $pageTitle);
	}
	
	private function renderSlimMessage($httpStatusCode, $messageTitle) {
	    return new Response($messageTitle, $httpStatusCode);
	}
	
	private function renderFullMessage($httpStatusCode, $messageTitle, $messageContent = null, $pageTitle = null) {
	    $data = array('messageTitle' => $messageTitle);
	    
	    if ($messageContent !== null) {
	        $data['messageContent'] = $messageContent;
	    }
	    
	    if ($pageTitle !== null) {
	        $data['pageTitle'] = $pageTitle;
	    }
	    
	    $pageHtmlCode = $this->templateEngine->render('messageSpecial.html.twig', $data);
	    return new Response($pageHtmlCode, $httpStatusCode);
	}

} 