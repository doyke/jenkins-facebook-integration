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
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 * @return mixed
	 */
	protected function handleConversion($value, callable $conversionCallback) {
		$returnedObject = null;

		try {
			$returnedObject = $conversionCallback($value);
		} catch (\Exception $e) {
			// Prevent additional exceptions if $value is a class (it may not be convertible to a string)
			$logValue = is_object($value) ? gettype($value) : $value;

			throw new HttpException(500, sprintf('Conversion of value "%s" with converter "%s" failed: internal error',
				$logValue, get_class($this)), $e);
		}

		if ($returnedObject === null) {
			// Prevent additional exceptions if $value is a class (it may not be convertible to a string)
			$logValue = is_object($value) ? gettype($value) : $value;

			throw new NotFoundHttpException(sprintf(
				'Conversion of value "%s" with converter "%s" failed: return value is null', $logValue,
				get_class($this)));
		}

		return $returnedObject;
	}

	abstract public function convert($value);

} 