<?php
namespace Solarfield\Ok;

abstract class JsonUtils {
	static public function parse($aText) {
		$json = json_decode($aText, true);

		if (($error = json_last_error()) != JSON_ERROR_NONE) {
			throw new \Exception(
				"Parsing JSON failed with code $error."
			);
		}

		return $json;
	}

	static public function toJson($aThing) {
		if (
			(is_object($aThing) && $aThing instanceof ToArrayInterface)
			|| is_array($aThing)
		) {
			$thing = StructUtils::toArray($aThing, true);
		}

		else {
			$thing = $aThing;
		}

		$json = json_encode($thing);

		if (($error = json_last_error()) != JSON_ERROR_NONE) {
			throw new \Exception(
				"Serializing JSON failed with code $error."
			);
		}

		return $json;
	}
}
