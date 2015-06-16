<?php
namespace Ok;

use Exception;

abstract class HttpUtils {
	static public function createStatusHeader($aHttpStatusCode) {
		$headers = array(
			200 => '200 OK',
			304 => '304 Not Modified',
			403 => '403 Forbidden',
			404 => '404 Not Found',
			408 => '408 Request Time-out',
			500 => '500 Internal Server Error',
			501 => '501 Not Implemented',
			503 => '503 Service Unavailable',
		);

		if (!array_key_exists($aHttpStatusCode, $headers)) {
			throw new Exception("Cannot create HTTP status header with code '" . $aHttpStatusCode . "'.");
		}

		$protocol = null;
		if (isset($_SERVER) && array_key_exists('SERVER_PROTOCOL', $_SERVER) && stripos($_SERVER['SERVER_PROTOCOL'], 'HTTP') !== false) {
			$protocol = $_SERVER['SERVER_PROTOCOL'];
		}

		if (!$protocol) {
			throw new Exception("Cannot create HTTP status header because the protocol cannot be determined.");
		}

		$header = $protocol . ' ' . $headers[$aHttpStatusCode];

		return $header;
	}
}
