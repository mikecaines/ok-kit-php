<?php
namespace Solarfield\Ok;

abstract class HtmlUtils {
	static public function squishHtml($aText) {
		$markup = $aText;

		//trim
		$markup = trim($markup);

		//remove whitespace before and after tags
		$markup = preg_replace('/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/', '', $markup);

		return $markup;
	}

	static public function convertHtmlToText($aHtml, $aOptions = []) {
		$options = array_replace([
			'tagsToRemove' => ['style', 'script', 'noscript', 'head'],
		], $aOptions);
		
		$t = $aHtml;
		
		foreach ($options['tagsToRemove'] as $tag) {
			$quotedTag = preg_quote($tag, '/');
			$t = preg_replace('/<\s*' . $quotedTag . '[^>]*>.*(?!<\s*' . $quotedTag .'[^>]*>).+<\/\s*' . $quotedTag . '\s*>/imsU', '', $t);
		}
		
		$t = strip_tags($t);
		$t = html_entity_decode($t, ENT_QUOTES);

		return $t;
	}
	
	static public function summarize($aHtml, int $aLength, string $aSuffix = '...') {
		return htmlspecialchars(StringUtils::summarize(static::convertHtmlToText($aHtml), $aLength, $aSuffix));
	}
}
