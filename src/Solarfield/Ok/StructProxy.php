<?php
namespace Solarfield\Ok;


class StructProxy {
	private $data;

	public function &getData() {
		return $this->data;
	}
	
	public function get($aPath) {
		return StructUtils::get($this->data, $aPath);
	}
	
	public function getAsString($aPath) {
		return (string)$this->get($aPath);
	}

	public function getAsArray($aPath) {
		$value = $this->get($aPath);

		if (is_array($value) || $value instanceof ToArrayInterface) {
			return $value;
		}

		$arr = [];

		if (is_object($value)) {
			foreach ($value as $k => $v) {
				$arr[$k] = $v;
			}
		}

		return $arr;
	}

	public function getAsBool($aPath) {
		return (bool)$this->get($aPath);
	}

	public function set($aPath, $aValue) {
		StructUtils::set($this->data, $aPath, $aValue);
	}

	public function pushSet($aPath, $aValue) {
		StructUtils::pushSet($this->data, $aPath, $aValue);
	}

	public function mergeInto($aValue) {
		StructUtils::mergeInto($this->data, $aValue);
	}

	public function __construct(array &$aData = null) {
		if ($aData != null) {
			$this->data = &$aData;
		}
		else {
			$this->data = [];
		}
	}
}
