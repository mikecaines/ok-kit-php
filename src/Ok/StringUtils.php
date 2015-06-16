<?php
namespace Ok;

abstract class StringUtils {
	/**
	 * Converts a string from dash-separated to camel-case.
	 * @param $aString string Dash-separated string.
	 * @return string Camel-case string.
	 */
	static public function dashToCamel($aString) {
		$str = '';

		$matches = array();
		preg_match_all('/-?([^\-]+)/i', $aString, $matches);

		foreach ($matches[1] as $match) {
			$c = substr($match, 0, 1);

			if ($str != '') {
				$c = strtoupper($c);
			}

			$str .= $c . substr($match, 1);
		}

		return $str;
	}

	/**
	 * Converts a string from camel-case to dash-separated.
	 * @param $aString string Camel-case string.
	 * @return string Dash-separated string.
	 */
	static public function camelToDash($aString) {
		$matches = array();
		preg_match_all('/((?:[A-Z]?[a-z]+)|(?:[0-9]+))/', $aString, $matches);

		$str = implode('-', $matches[1]);
		$str = strtolower($str);

		return $str;
	}
}
