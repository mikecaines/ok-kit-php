<?php
namespace Ok;

abstract class JsonUtils {
	static public function toJson($aThing) {
		if (
			(is_object($aThing) && $aThing instanceof ToArrayInterface)
			|| is_array($aThing)
		) {
			return json_encode(StructUtils::toArray($aThing));
		}

		return json_encode($aThing);
	}
}
