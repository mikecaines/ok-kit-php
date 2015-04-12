<?php
require_once 'ok_ToArrayInterface.php';

function ok_toJson($aThing) {
	if (
		(is_object($aThing) && $aThing instanceof ok_ToArrayInterface)
		|| is_array($aThing)
	) {
		return json_encode(ok_toArray($aThing));
	}

	return json_encode($aThing);
}
