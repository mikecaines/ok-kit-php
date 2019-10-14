<?php
namespace Solarfield\Ok;

abstract class LogUtils {
	/**
	 * Returns true, if message with level b, should be included when logging at level a.
	 * @param $a
	 * @param $b
	 * @return bool
	 * @throws \Exception
	 */
	static public function includes($a, $b): bool {
		return static::toRfc5424($a) >= static::toRfc5424($b);
	}

	/**
	 * Maps the specified level identifier to the corresponding RFC5424 integer.
	 * @param $aLevel
	 * @return int
	 * @throws \Exception
	 */
	static public function toRfc5424($aLevel): int {
		if ((string)(int)$aLevel === (string)$aLevel) {
			$level = (int)$aLevel;
			
			if (!($level >= 0 && $level <= 7)) throw new \Exception(
				"Unknown log level '{$aLevel}'."
			);
			
			return (int)$aLevel;
		}
		
		$map = [
			'emergency' => 0,
			'alert' => 1,
			'critical' => 2,
			'error' => 3,
			'warning' => 4,
			'warn' => 4,
			'notice' => 5,
			'info' => 6,
			'informational' => 6,
			'debug' => 7,
		];
		
		$level = strtolower($aLevel);
		
		if (!array_key_exists($level, $map)) throw new \Exception(
			"Unknown log level '{$aLevel}'."
		);
		
		return $map[$level];
	}
}
