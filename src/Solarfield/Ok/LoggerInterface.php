<?php
namespace Solarfield\Ok;

interface LoggerInterface extends \Psr\Log\LoggerInterface {
	function getName(): string;
	
	/**
	 * Clones this logger and gives it the specified name.
	 * @param string $aName
	 * @return LoggerInterface
	 */
	function withName(string $aName): LoggerInterface;
}
