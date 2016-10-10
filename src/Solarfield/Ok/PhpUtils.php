<?php
namespace Solarfield\Ok;

abstract class PhpUtils {
	static public function parseShorthandBytes($aValue) {
		$value = trim($aValue);

		if ($value === '') return null;

		$suffix = strtoupper(substr($value, -1));
		if ($suffix == 'B') {}
		else if ($suffix == 'K') $value *= 1024;
		else if ($suffix == 'M') $value *= 1048576;
		else if ($suffix == 'G') $value *= 1073741824;
		else if (!is_numeric($suffix)) throw new \Exception("Could not parse PHP shorthand byte value '$aValue'.");

		return (int)$value;
	}
}