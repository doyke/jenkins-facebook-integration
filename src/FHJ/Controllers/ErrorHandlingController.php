<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Response;

/**
 * ErrorHandlingController
 * @package FHJ\Controllers
 */
class ErrorHandlingController extends BaseController {

	public function handleErrorAction(\Exception $e, $code) {
		$this->getLogger()->addError(sprintf('An exception occured: %s', $e->getMessage()), array('exception' => $e));

		if ($this->isDebug()) {
			return;
		}

		switch ($code) {
			case 404:
				$message = 'The requested page could not be found.';
				break;
			default:
				$message = 'We are sorry, but something went terribly wrong.';
		}

		return new Response($message, $code);
	}

} 