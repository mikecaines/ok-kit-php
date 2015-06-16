<?php
namespace Ok;

abstract class UrlUtils {
	/**
	 * Creates a URL slug.
	 * i.e. Creates "foo-bar" from "Foo Bar" for use in "http://host.com/foo-bar/".
	 * @param string $aText Text to be slugified.
	 * @param array $aOptions Additional options.
	 * @return string Slug.
	 */
	static public function createSlug($aText, $aOptions = array()) {
		$options = array_merge(array(
			'preserveCase' => false,
		), $aOptions);

		$text = $aText;

		if ($options['preserveCase'] == false) {
			$text = strtolower($text);
		}

		$text = str_replace(' ', '-', $text);
		$text = preg_replace('/[^a-z0-9\-]/i', '', $text);
		$text = preg_replace('/-{2,}/', '-', $text);
		$text = preg_replace('/(^-)|(-$)/', '', $text);

		return $text;
	}
}
