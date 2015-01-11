<?php
function ok_squishHtml($aText) {
	$markup = $aText;
	
	//remove whitespace before and after tags
	$markup = preg_replace('/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/', '', $markup);
	
	return $markup;
}

function ok_convertHtmlToText($aHtml) {
	$t = $aHtml;

	$t = strip_tags($t);
	$t = html_entity_decode($t);

	return $t;
}