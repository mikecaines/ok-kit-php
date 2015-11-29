<?php
namespace Solarfield\Ok;

class Datetime {
	private $datetime;
	
	public function modify($aModifier) {
		$this->datetime->modify($aModifier);
		return $this;
	}
	
	public function format($aFormat) {
		return $this->datetime->format($aFormat);
	}
	
	public function __construct($aDateString) {
		$this->datetime = new DateTime($aDateString);
	}
}
