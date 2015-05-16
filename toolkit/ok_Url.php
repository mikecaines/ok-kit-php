<?php
class ok_Url {
	private $parts;

	private function parseUrl($aString) {
		$string = trim($aString);

		if (preg_match('/^[^\/:]:\/\//', $string) == 0 && preg_match('/^[^\/]/', $string) == 1) {
			$string = '//' . $string;
		}

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

		$parts['query'] = $this->parseQuery($parts['query']);

		return $parts;
	}

	private function parseQuery($aQueryString) {
		$queryString = $aQueryString;

		if ($queryString != '') {
			$query = array();

			$queryString = explode('&', $queryString);
			foreach ($queryString as $pair) {
				$pairParts = explode('=', $pair);
				$paramName = $pairParts[0];
				$paramValue = count($pairParts) > 1 ? $pairParts[1] : '';
				$key = rawurldecode($paramName);

				if (array_key_exists($key, $query) == false) {
					$query[$key] = array();
				}

				$query[$key][] = rawurldecode($paramValue);
			}
		}

		else {
			$query = array();
		}

		return $query;
	}

	private function serializeQuery($aQueryParams) {
		$str = '';

		if (count($aQueryParams) > 0) {
			foreach ($aQueryParams as $paramName => $values) {
				foreach ($values as $v) {
					if ($str != '') {
						$str .= '&';
					}

					$str .= rawurlencode($paramName) . '=' . rawurlencode($v);
				}
			}
		}

		return $str;
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
			$str .= $this->parts['path'];
		}

		$queryString = $this->serializeQuery($this->parts['query']);
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

	public function getQueryParams() {
		$params = array();

		foreach ($this->parts['query'] as $k => $values) {
			if (substr($k, -2) == '[]') $params[$k] = $values;
			else $params[$k] = $values[0];
		}

		return $params;
	}

	public function getQueryParam($aName) {
		$value = '';

		foreach ($this->parts['query'] as $k => $v) {
			if ($aName == $k) {
				$value = $v[0];
			}
		}

		return $value;
	}

	public function getQueryParamAsArray($aName) {
		$value = array();

		foreach ($this->parts['query'] as $k => $v) {
			if ($aName == $k) {
				$value = $v;
			}
		}

		return $value;
	}

	public function setQueryParam($aName, $aValue = '', $aReplace = true) {
		$values = is_array($aValue) ? $aValue : array($aValue);

		foreach ($values as $value) {
			if ($aReplace) {
				$this->parts['query'][$aName] = array($value);
			}
			else {
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

	public function getFileName() {
		$fileName = null;

		if (preg_match('/([^\/]+)$/', $this->parts['path'], $matches) == 1) {
			$fileName = $matches[1];
		}

		return $fileName;
	}

	public function __toString() {
		return $this->toString();
	}

	public function __construct($aString) {
		$this->parts = $this->parseUrl($aString);
	}
}
