<?php
namespace Solarfield\Ok;

abstract class MiscUtils {
	/**
	 * Simple wrapper to return the output of var_dump().
	 * @param $aValue
	 * @return string
	 */
	static public function varInfo($aValue) {
		ob_start();
		var_dump($aValue);
		return trim(ob_get_clean());
	}

	/**
	 * Recursively 'simplifies' the specified value down to either an array or scalar/null.
	 *
	 * This can be used to provide an easily serializable data structure (i.e. for JSON).
	 *
	 * Objects will be checked for the following, in order to control the conversion to a simple type:
	 * - __debugInfo() (magic method)
	 * - ToArrayInterface
	 *
	 * If an object does not implement any of the above, the string value returned by ::varInfo() will be used.
	 *
	 * If an object simplifies to an array, and does not contain an element named 'class', it will be given one to hold
	 * the object's class name (provided by get_class_name()).
	 *
	 * @param $aValue
	 * @return array|string
	 */
	static public function varData($aValue) {
		$isObject = is_object($aValue);


		if ($isObject && method_exists($aValue, '__debugInfo')) {
			$data = $aValue->__debugInfo();

			if (is_array($data) && !array_key_exists('class', $data)) {
				$data['class'] = get_class($aValue);
			}
		}

		else if ($isObject && $aValue instanceof ToArrayInterface) {
			$data = $aValue->toArray();

			if (is_array($data) && !array_key_exists('class', $data)) {
				$data['class'] = get_class($aValue);
			}
		}
		
		//else if object implements __toString()
		else if ($isObject && method_exists($aValue, '__toString')) {
			//get the string representation
			$data = $aValue->__toString();
		}

		else {
			$data = $aValue;
		}


		//if we have an array value
		if (is_array($data)) {
			//recursively call varData() on its elements
			foreach ($data as $k => $v) {
				$data[$k] = static::varData($v);
			}
		}

		//else we have a null/scalar value
		else if (is_scalar($data) || is_null($data)) {
			//leave as is
		}

		//else get a debug-string representation of the value
		else {
			$data = static::varInfo($data);
		}


		return $data;
	}

	/**
	 * Generates a GUID.
	 * Based upon: http://stackoverflow.com/a/15874518
	 * @return string
	 */
	static public function guid() {
		$randomString = openssl_random_pseudo_bytes(16);
		$time_low = bin2hex(substr($randomString, 0, 4));
		$time_mid = bin2hex(substr($randomString, 4, 2));
		$time_hi_and_version = bin2hex(substr($randomString, 6, 2));
		$clock_seq_hi_and_reserved = bin2hex(substr($randomString, 8, 2));
		$node = bin2hex(substr($randomString, 10, 6));

		/**
		 * Set the four most significant bits (bits 12 through 15) of the
		 * time_hi_and_version field to the 4-bit version number from
		 * Section 4.1.3.
		 * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
		 */
		$time_hi_and_version = hexdec($time_hi_and_version);
		$time_hi_and_version = $time_hi_and_version >> 4;
		$time_hi_and_version = $time_hi_and_version | 0x4000;

		/**
		 * Set the two most significant bits (bits 6 and 7) of the
		 * clock_seq_hi_and_reserved to zero and one, respectively.
		 */
		$clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
		$clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
		$clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

		return sprintf('%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
	}

	static public function extractInclude($aPath) {
		/** @noinspection PhpIncludeInspection */
		return include $aPath;
	}
}
