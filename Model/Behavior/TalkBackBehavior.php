<?php
class TalkBackBehavior extends ModelBehavior {
/**
 * Ensures that all TalkBack Behaviors with this function are being called
 *
 **/
	private $_setCurrentCommenterCalled = array();
	private $_loaded = array();
	
	public function setCurrentCommenter(Model $Model, $commenterId) {
		$method = 'setCurrentCommenter';
		$behaviorNames = array_keys($Model->actsAs);

		$this->_loaded[$Model->alias][$this->name] = true;
		foreach ($behaviorNames as $name) {
			if (strpos($name, 'TalkBack.') === 0) {
				list($plugin, $alias) = pluginSplit($name, true);
				$class = $alias . 'Behavior';
				if ($class != get_class($this)) {
					// Loads Behavior
					App::uses($class, $plugin . 'Model/Behavior');
					if (!isset($this->_loaded[$Model->alias][$alias])) {
						if (ClassRegistry::isKeySet($name)) {
							$this->_loaded[$Model->alias][$alias] = ClassRegistry::getObject($name);
						} else {
							$this->_loaded[$Model->alias][$alias] = new $class();
							ClassRegistry::addObject($class, $this->_loaded[$Model->alias][$alias]);
							if (!empty($plugin)) {
								ClassRegistry::addObject($plugin . '.' . $class, $this->_loaded[$Model->alias][$alias]);
							}
						}
						$this->_loaded[$Model->alias][$alias]->_loaded = $this->_loaded;
						call_user_func_array(
							array($this->_loaded[$Model->alias][$alias], $method), 
							array($Model, $commenterId)
						);
					} 
				}
			}
		}
	}
	
	// Returns the name and plugin
	public function getPluginClassName($Model) {
		$className = $Model->alias;
		if (!empty($Model->plugin)) {
			$className = $Model->plugin . '.' . $className;
		}
		return $className;
	}	
}