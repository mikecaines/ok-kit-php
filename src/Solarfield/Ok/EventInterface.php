<?php
namespace Solarfield\Ok;

interface EventInterface {
	public function getType();
	public function getTarget();
}
