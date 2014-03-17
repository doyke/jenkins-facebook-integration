<?php

namespace FHJ\Converters;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Monolog\Logger;

/**
 * AbstractConverter
 * @package FHJ\Converters
 */
abstract class AbstractConverter {

	/**
	 * Calls a converter method with value for conversion. Does all the error handling if something goes wrong. The
	 * callable must take one parameter which is the value that has to be converted.
	 *
	 * @param $value
	 * @param callable $conversionCallback
	 *
	 * @return mixed
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function handleConversion($value, Callable $conversionCallback) {
		$returnedObject = null;

		try {
			$returnedObject = $conversionCallback($value);
		} catch (\Exception $e) {
			throw new HttpException(500, sprintf('Conversion of value "%s" to class "%s" failed', $value,
				get_class($this)), $e);
		}

		if ($returnedObject === null) {
			throw new NotFoundHttpException(sprintf('Conversion of value "%s" to class "%s" failed', $value,
				get_class($this)));
		}

		return $returnedObject;
	}

	abstract public function convert($value);

} 