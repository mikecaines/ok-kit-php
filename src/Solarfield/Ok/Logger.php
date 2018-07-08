<?php
namespace Solarfield\Ok;

use Psr\Log\AbstractLogger;

/**
 * A simple logger which just logs the message and context via error_log().
 */
class Logger extends AbstractLogger implements LoggerInterface {
	private $name;
	
	public function log($level, $message, array $context = []) {
		$msg = $message;
		
		if ($context) $msg .= "\n\n[context] ". MiscUtils::varInfo(MiscUtils::varData($context));
		
		error_log($msg);
	}
	
	function name(): string {
		return $this->name;
	}
	
	function cloneWithName(string $aName): LoggerInterface {
		return new static([
			'name' => $aName,
		]);
	}
	
	public function __construct(array $aOptions = null) {
		$options = array_replace([
			'name' => '',
		], $aOptions ?: []);
		
		$this->name = (string)$options['name'];
	}
}
