<?php
namespace Solarfield\Ok;

abstract class HtmlUtils {
	static public function squishHtml($aText) {
		$markup = $aText;

		//remove whitespace before and after tags
		$markup = preg_replace('/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/', '', $markup);

		return $markup;
	}

	static public function convertHtmlToText($aHtml) {
		$t = $aHtml;

		$t = strip_tags($t);
		$t = html_entity_decode($t);

		return $t;
	}
}
