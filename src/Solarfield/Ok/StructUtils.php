<?php
/**
 * @link https://github.com/solarfield/ok-kit-php
 * @license https://github.com/solarfield/ok-kit-php/blob/master/LICENSE
 */

namespace Solarfield\Ok;

use Exception;

abstract class StructUtils {
	static public function has($aArray, $aPath, $aSeparator = '.') {
		return static::scout($aArray, $aPath, $aSeparator)[0];
	}

	/**
	 * Gets the item located at the specified path in the source array.
	 * If the item is not found, null is returned.
	 * @param array $aArray Source array.
	 * @param string $aPath Path to item.
	 * @param string $aSeparator Separator used by path.
	 * @return mixed Retrieved item or null.
	 */
	static public function get($aArray, $aPath, $aSeparator = '.') {
		return StructUtils::scout($aArray, $aPath, $aSeparator)[1];
	}

	/**
	 * Sets the item to the location at the specified path in the destination array.
	 * If any steps along the path do not exist, or are not an array, they will be set to an array.
	 * @param array $aArray Destination array.
	 * @param string $aPath Path to item.
	 * @param mixed $aValue Value of item.
	 * @param string $aSeparator Separator used by path.
	 */
	static public function set(&$aArray, $aPath, $aValue, $aSeparator = '.') {
		$steps = explode($aSeparator, $aPath);

		$node = &$aArray;

		for ($i = 0; $i < count($steps) - 1; $i++) {
			if (array_key_exists($steps[$i], $node) == false || !is_array($node[$steps[$i]])) {
				$node[$steps[$i]] = array();
			}

			$node = &$node[$steps[$i]];
		}

		$node[$steps[$i]] = $aValue;
	}

	/**
	 * Similar to StructUtils::arraySet(), except the value is pushed onto the item at the path.
	 * If the item at the path is not an array, it will be set to one.
	 * @param $aArray
	 * @param $aPath
	 * @param $aValue
	 * @param string $aSeparator
	 */
	static public function pushSet(&$aArray, $aPath, $aValue, $aSeparator = '.') {
		$steps = explode($aSeparator, $aPath);

		$node = &$aArray;

		for ($i = 0; $i < count($steps) - 1; $i++) {
			if (array_key_exists($steps[$i], $node) == false || !is_array($node[$steps[$i]])) {
				$node[$steps[$i]] = array();
			}

			$node = &$node[$steps[$i]];
		}

		$node[$steps[$i]][] = $aValue;
	}

	/**
	 * Inspects the source array and determines whether an item exists at the specified path.
	 * @param array|\ArrayAccess|ToArrayInterface $aArray Source array.
	 * @param string $aPath Path to item.
	 * @param string $aSeparator Separator used by path.
	 * @return array Array with integer keys containing result information.
	 *   The first element contains a boolean value as to whether the item exists.
	 *   The second element is the value of the element if it exists, or null if it does not.
	 */
	static public function scout($aArray, $aPath, $aSeparator = '.') {
		$result = array(false, null);

		$steps = explode($aSeparator, $aPath);

		$node = $aArray;
		for ($i = 0; $i < count($steps); $i++) {
			if (is_array($node) && array_key_exists($steps[$i], $node)) {
				$node = $node[$steps[$i]];
				continue;
			}

			else if (is_object($node)) {
				if ($node instanceof \ArrayAccess && $node->offsetExists($steps[$i])) {
					$node = $node->offsetGet($steps[$i]);
					continue;
				}

				else if ($node instanceof ToArrayInterface) {
					$t = $node->toArray();

					if (array_key_exists($steps[$i], $t)) {
						$node = $t[$steps[$i]];
						continue;
					}
				}
			}

			break;
		}

		if ($i == count($steps)) {
			$result = array(true, $node);
		}

		return $result;
	}

	/**
	 * Gets items specified by paths from the source array, and sets them in the returned array.
	 * @param array $aArray Source array.
	 * @param array $aPaths Array of paths to items.
	 * @param bool $aFill If true, non-existent items will be set to null.
	 * @return array Array containing extracted items.
	 */
	static public function extract($aArray, $aPaths, $aFill = false) {
		$arr = array();

		foreach ($aPaths as $k) {
			$p = StructUtils::scout($aArray, $k);

			if ($p[0]) {
				StructUtils::set($arr, $k, $p[1]);
			}
			else if ($aFill) {
				StructUtils::set($arr, $k, null);
			}
		}

		return $arr;
	}

	/**
	 * Trims elements in an array, optionally recursively.
	 * Only values which are scalar or null, will be modified.
	 * @param array $aArray Array of elements to be trimmed.
	 * @param bool $aDeep If true, will trim recursively.
	 * @return array Array of trimmed elements.
	 */
	static public function trim($aArray, $aDeep = false) {
		$arr = array();

		foreach ($aArray as $k => $v) {
			if (is_null($v) || is_scalar($v)) {
				$arr[$k] = trim($v);
			}
			else if ($aDeep) {
				$arr[$k] = StructUtils::trim($v, $aDeep);
			}
			else {
				$arr[$k] = $v;
			}
		}

		return $arr;
	}

	/**
	 * Creates a new array which can be used as a lookup of the original array.
	 * @param array $aArray Reference to a 2D array.
	 * @param string $aPrimaryKey The 2nd level key of the source array,
	 *   whose corresponding value will be used as the 1st level key in the destination array.
	 * @return array The generated lookup.
	 * @throws Exception if a duplicate primary key value is encountered.
	 */
	static public function delegate(&$aArray, $aPrimaryKey) {
		$arr = array();

		foreach ($aArray as &$v) {
			if (array_key_exists($v[$aPrimaryKey], $arr)) {
				throw new Exception("Duplicate value found for primary key: '" . $aPrimaryKey . "'. Value: '" . $v[$aPrimaryKey] . "'.");
			}

			$arr[$v[$aPrimaryKey]] = &$v;
		}

		return $arr;
	}

	/**
	 * Creates a tree structure from a 2D, self-referencing source.
	 * @param array $aArray Reference to a 2D array.
	 * @param string $aPrimaryKey Key of 2nd level element whose value is its identifier.
	 * @param string $aForeignKey Key of 2nd level element whose value is the identifier of its parent.
	 * @param string $aChildrenKey Key of new element to be created, which stores an array of child elements.
	 * @param string $aParentKey Key of new element to be created, which stores a reference to the parent element.
	 * @throws Exception if a reference to a non-existent element is encountered.
	 * @return array Nested structure of lookups.
	 */
	static public function tree(&$aArray, $aPrimaryKey, $aForeignKey, $aChildrenKey, $aParentKey) {
		$arr = array();

		$lookup = StructUtils::delegate($aArray, $aPrimaryKey);

		//init each child array to empty
		foreach ($lookup as &$v) {
			$v[$aChildrenKey] = array();
		}
		unset($v);

		foreach ($lookup as &$v) {
			if ($v[$aForeignKey] != null && !array_key_exists($v[$aForeignKey], $lookup)) {
				throw new Exception("Reference to non-existent element with identifier '" . $v[$aForeignKey] . "'.");
			}

			//if the current item references a parent
			if ($v[$aForeignKey] != null) {
				//set the parent key to a reference to the parent
				$v[$aParentKey] = &$lookup[$v[$aForeignKey]];

				//add the current item to its parent's list of children
				$lookup[$v[$aForeignKey]][$aChildrenKey][$v[$aPrimaryKey]] = &$v;
			}

			//else the current item is top-level
			else {
				//set the parent key to null
				$v[$aParentKey] = null;

				//add the current item to the top level
				$arr[$v[$aPrimaryKey]] = &$v;
			}
		}

		return $arr;
	}

	/**
	 * Checks the elements specified by $aKeys, and returns the first non-null value.
	 * @param array $aArray Array to check.
	 * @param array $aKeys Array of keys to check.
	 * @return mixed First non-null value encountered or null.
	 */
	static public function coalesce($aArray, $aKeys) {
		foreach ($aKeys as $key) {
			if (array_key_exists($key, $aArray)) {
				$value = $aArray[$key];
				if ($value !== null) return $value;
			}
		}

		return null;
	}

	/**
	 * @param array $aArray Source array.
	 * @param string $aPath Path of the item to retrieve.
	 * @param string $aSeparator Separator used by path.
	 * @return array Array of retrieved items.
	 * @see StructUtils::get()
	 */
	static public function getEach($aArray, $aPath, $aSeparator = '.') {
		$values = array();

		foreach ($aArray as $v) {
			$values[] = StructUtils::get($v, $aPath, $aSeparator);
		}

		return $values;
	}

	/**
	 * @param array $aArray Source array.
	 * @param string $aPath Path of the item to check.
	 * @param mixed $aValue Value to compare to.
	 * @param string $aSeparator Separator used by path.
	 * @return bool|int|string Integer or string key if a match was found, boolean false if not.
	 */
	static public function search($aArray, $aPath, $aValue, $aSeparator = '.') {
		foreach ($aArray as $k => $v) {
			if (StructUtils::get($v, $aPath, $aSeparator) == $aValue) {
				return $k;
			}
		}

		return false;
	}
	
	static public function find($aArray, $aPath, $aValue, $aSeparator = '.') {
		$k = static::search($aArray, $aPath, $aValue, $aSeparator);
		return $k !== false ? $aArray[$k] : null;
	}

	/**
	 * Recursively merges $aArray2 into $aArray1 according to specific criteria.
	 * Associative arrays are recursively merged.
	 * Vector arrays are replaced, not merged or appended to.
	 * @param $aArray1 array Array to merge into.
	 * @param $aArray2 array Array to merge from.
	 * @return array The new merged array.
	 * @throws Exception if a vector & associative array would be merged.
	 */
	static public function merge($aArray1, $aArray2) {
		$v1 = StructUtils::isVector($aArray1);
		$v2 = StructUtils::isVector($aArray2);

		if ((($v1 && !$v2) || ($v2 && !$v1)) && count($aArray1) > 0 && count($aArray2) > 0) {
			throw new Exception("Cannot merge associative array with vector array.");
		}

		//if $aArray2 is a vector, replace aArray1 with aArray2
		if ($v2) {
			$arr = $aArray2;
		}

		else {
			$arr = $aArray1;

			foreach ($aArray2 as $k => $v) {
				if (array_key_exists($k, $arr) && is_array($arr[$k]) && is_array($v)) {
					$arr[$k] = StructUtils::merge($arr[$k], $v);
				}

				else {
					$arr[$k] = $v;
				}
			}
		}

		return $arr;
	}

	/**
	 * Same as merge(), but modifies $aArray1 instead of returning a new array.
	 * @param $aArray1
	 * @param $aArray2
	 * @throws Exception
	 */
	static public function mergeInto(&$aArray1, $aArray2) {
		$v1 = StructUtils::isVector($aArray1);
		$v2 = StructUtils::isVector($aArray2);

		if ((($v1 && !$v2) || ($v2 && !$v1)) && count($aArray1) > 0 && count($aArray2) > 0) {
			throw new Exception("Cannot merge associative array with vector array.");
		}

		//if $aArray2 is a vector, replace aArray1 with aArray2
		if ($v2) {
			array_splice($aArray1, 0);
			foreach ($aArray2 as $k => $v) {
				$aArray1[$k] = $v;
			}
		}

		else {
			foreach ($aArray2 as $k => $v) {
				if (array_key_exists($k, $aArray1) && is_array($aArray1[$k]) && is_array($v)) {
					StructUtils::mergeInto($aArray1[$k], $v);
				}

				else {
					$aArray1[$k] = $v;
				}
			}
		}
	}

	static public function flatten($aArray, $aSeparator = '.') {
		$arr = array();

		foreach ($aArray as $k => $v) {
			if (is_null($v) || is_scalar($v)) {
				$arr[$k] = $v;
			}

			else {
				$arrarr = StructUtils::flatten($v, $aSeparator);

				foreach ($arrarr as $kk => $vv) {
					$arr[$k . $aSeparator . $kk] = $vv;
				}
			}
		}

		return $arr;
	}

	static public function unflatten($aArray, $aSeparator = '.') {
		$arr = array();

		foreach ($aArray as $k => $v) {
			StructUtils::set($arr, $k, $v, $aSeparator);
		}

		return $arr;
	}

	/**
	 * Inserts a new element, before an existing element.
	 * @param array $aArray The source array.
	 * @param string|int $aKey Key of existing element to insert before.
	 * @param string|int $aNewKey Key of new element.
	 * @param mixed $aNewValue Value of new element.
	 * @return array The new array.
	 * @throws Exception if the specified key does not exist in the array.
	 */
	static public function insertBefore($aArray, $aKey, $aNewKey, $aNewValue) {
		$keys = array_keys($aArray);
		$values = array_values($aArray);

		$index = array_search($aKey, $keys);

		if ($index === false) {
			throw new Exception("Key does not exist: '" . $aKey . "'.");
		}

		array_splice($keys, $index, 0, $aNewKey);
		array_splice($values, $index, 0, array($aNewValue));

		return array_combine($keys, $values);
	}

	/**
	 * Checks elements in an array against a regexp, and if they match, replaces the value with the specified value.
	 * Boolean and null values are unmodified.
	 * Other scalar values are matched against the regexp.
	 * If $aDeep is true, arrays are processed recursively, otherwise they are unmodified.
	 * All other values are unmodified.
	 * @param array $aArray Source array.
	 * @param string $aRegexp A regular expression to match against.
	 * @param mixed $aSubstitute The substitute value which will replace matched values.
	 * @param bool $aDeep If true, arrays are processed recursively.
	 * @return array The new array.
	 */
	static public function substitute($aArray, $aRegexp, $aSubstitute, $aDeep = false) {
		$arr = array();

		foreach ($aArray as $k => $v) {
			if (is_bool($v) || is_null($v)) {
				$arr[$k] = $v;
			}
			else if (is_scalar($v) && preg_match($aRegexp, $v) == 1) {
				$arr[$k] = $aSubstitute;
			}
			else if (is_array($v) && $aDeep == true) {
				$arr[$k] = StructUtils::substitute($v, $aRegexp, $aSubstitute, true);
			}
			else {
				$arr[$k] = $v;
			}
		}

		return $arr;
	}

	static public function import($aSource, $aMap, $aScalarOnly = false) {
		$dest = [];

		foreach ($aMap as $translation) {
			if (is_string($translation)) $translation = [$translation, $translation];
			else if (count($translation) == 1) $translation[] = $translation[0];

			$srcInfo = static::scout($aSource, $translation[0]);
			if ($srcInfo[0]) {
				if ($aScalarOnly && !(is_null($srcInfo[1]) || is_scalar($srcInfo[1]))) throw new Exception(
					"Encountered non-scalar/null value at '$translation[0]'."
				);

				static::set($dest, $translation[1], $srcInfo[1]);
			}
		}

		return $dest;
	}

	/**
	 * Converts an object to an array according to specific criteria.
	 * @param mixed $aThing An array or an object which implements ToArrayInterface.
	 * @param bool $aDeep If true, conversion is done recursively.
	 * @return array The array created from the object.
	 * @throws Exception if the object cannot be converted to an array.
	 */
	static public function toArray($aThing, $aDeep = false) {
		if (is_array($aThing)) {
			$arr = $aThing;
		}
		else if (is_object($aThing) && $aThing instanceof ToArrayInterface) {
			$arr = $aThing->toArray();
		}
		else {
			throw new Exception("Could not convert to array: '" . (is_object($aThing) ? get_class($aThing) : $aThing) . "'");
		}

		if ($aDeep) {
			foreach ($arr as $k => $v) {
				if (is_null($v) || is_scalar($v)) {
					$arr[$k] = $v;
				}
				else {
					$arr[$k] = StructUtils::toArray($v, $aDeep);
				}
			}
		}

		return $arr;
	}

	/**
	 * Checks if an array has sequential integer keys.
	 * @param array $aArray The array to check
	 * @return bool If the array is a vector or not.
	 */
	static public function isVector($aArray) {
		$i = 0;

		if (is_array($aArray)) {
			foreach ($aArray as $k => $v) {
				if ((string)$k != (string)$i) return false;
				$i++;
			}
		}

		return $i > 0;
	}

}
