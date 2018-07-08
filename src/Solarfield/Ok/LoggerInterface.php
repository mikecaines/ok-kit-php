<?php
namespace Solarfield\Ok;

interface LoggerInterface extends \Psr\Log\LoggerInterface {
	function name(): string;
	
	function cloneWithName(string $aName): LoggerInterface;
}
