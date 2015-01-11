<?php
require_once 'ok_ToArrayInterface.php';

function ok_toJson($aThing) {
	if (gettype($aThing) == 'object' && $aThing instanceof ok_ToArrayInterface) {
		return ok_toJson($aThing->toArray());
	}

	return json_encode($aThing);
}
