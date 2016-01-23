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
			return json_encode(StructUtils::toArray($aThing, true));
		}

		return json_encode($aThing);
	}
}
