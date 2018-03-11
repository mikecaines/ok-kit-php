<?php
namespace Solarfield\Ok;

interface EventTargetInterface {
	public function addEventListener(string $aType, callable $aHandler);
}