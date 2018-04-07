<?php
namespace Solarfield\Ok;

use Exception;

class Url {
	static public function parseQuery($aQueryString) {
		if ($aQueryString != '') {
			$query = array();

			$queryString = explode('&', $aQueryString);
			foreach ($queryString as $pair) {
				$pairParts = explode('=', $pair);
				$paramName = $pairParts[0];
				$key = rawurldecode($paramName);

				if (array_key_exists($key, $query) == false) {
					$query[$key] = array();
				}

				if ( count($pairParts) > 1) {
					$query[$key][] = rawurldecode($pairParts[1]);
				}
			}
		}

		else {
			$query = array();
		}

		return $query;
	}

	static public function serializeQuery($aQueryParams) {
		$str = '';

		if (count($aQueryParams) > 0) {
			foreach ($aQueryParams as $paramName => $values) {
				if (!is_array($values)) $values = [$values];

				if (count($values) > 0) {
					foreach ($values as $v) {
						if ($str != '') {
							$str .= '&';
						}

						$str .= rawurlencode($paramName);
						if ($v !== null) $str .= '=' . rawurlencode($v);
					}
				}

				else {
					if ($str != '') {
						$str .= '&';
					}

					$str .= rawurlencode($paramName);
				}
			}
		}

		return $str;
	}

	private $parts;

	private function parseUrl($aString) {
		$string = trim($aString);

		$parts = parse_url($string);

		if ($parts === false) {
			throw new Exception("Could not parse URL from string: '" . $aString . "'");
		}

		$parts = array_merge(array(
			'scheme' => '',
			'host' => '',
			'port' => '',
			'user' => '',
			'pass' => '',
			'path' => '',
			'query' => '',
			'fragment' => '',
		), $parts);

		$parts['query'] = static::parseQuery($parts['query']);

		return $parts;
	}

	public function toString() {
		$str = '';

		if ($this->parts['host'] != '') {
			if ($this->parts['scheme'] != '') {
				$str .= $this->parts['scheme'] . '://';
			}
			else {
				$str .= '//';
			}
		}

		if ($this->parts['user'] != '' || $this->parts['pass'] != '') {
			$str .= $this->parts['user'] . ':' . $this->parts['pass'] . '@';
		}

		if ($this->parts['host'] != '') {
			$str .= $this->parts['host'];
		}

		if ($this->parts['port'] != '') {
			$str .= ':' . $this->parts['port'];
		}

		if ($this->parts['path'] != '') {
			if ($str != '') {
				if (!preg_match('/^\\//', $this->parts['path'])) {
					$str .= '/';
				}
			}

			$str .= $this->parts['path'];
		}

		$queryString = static::serializeQuery($this->parts['query']);
		if ($queryString != '') {
			$str .= '?' . $queryString;
		}

		if ($this->parts['fragment'] != '') {
			$str .= '#' . $this->parts['fragment'];
		}

		return $str;
	}

	public function setScheme($aScheme) {
		$this->parts['scheme'] = (string)$aScheme;
	}

	public function getScheme() {
		return $this->parts['scheme'];
	}

	public function getHost() {
		return $this->parts['host'];
	}

	public function setHost($aHost) {
		$this->parts['host'] = (string)$aHost;
	}

	public function getPort() {
		return $this->parts['port'];
	}

	public function setPort($aPort) {
		$this->parts['port'] = (string)$aPort;
	}

	public function getQueryParamsAll() {
		$params = array();

		foreach ($this->parts['query'] as $k => $values) {
			$params[$k] = $values;
		}

		return $params;
	}

	public function getQueryParams() {
		$params = array();

		foreach ($this->parts['query'] as $k => $values) {
			$params[$k] = $values[0];
		}

		return $params;
	}

	public function getQueryParamAll($aName) {
		if (array_key_exists($aName, $this->parts['query'])) {
			return $this->parts['query'][$aName];
		}

		return [];
	}

	public function getQueryParam($aName) {
		if (array_key_exists($aName, $this->parts['query'])) {
			return $this->parts['query'][$aName][0];
		}

		return '';
	}

	public function setQueryParam($aName, $aValue = '', $aReplace = true) {
		$values = is_array($aValue) ? array_values($aValue) : array($aValue);

		//normalize all values to string type
		foreach ($values as &$value) {
			$value = (string)$value;
		}
		unset($value);

		if ($aReplace) {
			$this->parts['query'][$aName] = $values;
		}

		else {
			foreach ($values as $value) {
				$this->parts['query'][$aName][] = $value;
			}
		}
	}

	public function removeQueryParam($aName) {
		unset($this->parts['query'][$aName]);
	}

	public function getPath() {
		return $this->parts['path'];
	}

	public function setPath($aPath) {
		$this->parts['path'] = (string)$aPath;
	}
	
	public function pushPath($aPath) {
		$this->parts['path'] .= $aPath;
	}
	
	public function pushDir($aDir) {
		if (!preg_match('/\/$/', $this->parts['path'])) {
			$this->parts['path'] .= '/';
		}
	
		$this->parts['path'] .= rawurlencode($aDir) . '/';
	}

	public function getFileName() {
		if (preg_match('/([^\/]+)$/', $this->parts['path'], $matches) == 1) {
			return $matches[1];
		}

		return '';
	}
	
	public function setFileName($aFileName) {
		$this->parts['path'] = preg_replace('/\/[^\/]+$/', '/', $this->parts['path']);
		
		if (!preg_match('/\/$/', $this->parts['path'])) {
			$this->parts['path'] .= '/';
		}

		$this->parts['path'] .= rawurlencode($aFileName);
	}

	public function __toString() {
		return $this->toString();
	}

	public function __construct($aString) {
		$this->parts = $this->parseUrl($aString);
	}
}
