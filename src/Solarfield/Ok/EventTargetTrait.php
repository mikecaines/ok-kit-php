<?php
namespace Solarfield\Ok;

trait EventTargetTrait {
	private $listeners = [];

	protected function addedEventListener($aEventType, $aListener) {
		if (array_key_exists($aEventType, $this->listeners)) {
			foreach ($this->listeners[$aEventType] as $k => $listener) {
				if ($listener === $aListener) {
					return $k;
				}
			}
		}

		return null;
	}

	protected function hasEventListeners($aEventType) {
		return array_key_exists($aEventType, $this->listeners) && count($this->listeners[$aEventType]) > 0;
	}

	protected function dispatchEvent(EventInterface $aEvent) {
		$type = $aEvent->getType();

		if (array_key_exists($type, $this->listeners)) {
			foreach ($this->listeners[$type] as $listener) {
				$listener($aEvent);
			}
		}
	}

	public function addEventListener($aEventType, $aListener) {
		if (!$this->addedEventListener($aEventType, $aListener)) {
			if (!array_key_exists($aEventType, $this->listeners)) {
				$this->listeners[$aEventType] = [];
			}

			$this->listeners[$aEventType][] = $aListener;
		}
	}
}
