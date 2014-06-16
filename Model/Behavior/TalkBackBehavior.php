<?php
class TalkBackBehavior extends ModelBehavior {
/**
 * Ensures that all TalkBack Behaviors with this function are being called
 *
 **/
	protected $_currentCommenterId = null;
	
	// Keeps track of recursive calling between Behaviors
	private $_recursiveCall = array();
	
	public function getCurrentCommenterId(Model $Model) {
		return $this->_currentCommenterId;
	}
	
	public function setCurrentCommenter(Model $Model, $commenterId) {
		$this->_currentCommenterId = $commenterId;
		return $this->_recursiveCall($Model, 'setCurrentCommenter', [$commenterId]);
	}
	
	/**
	 * If a function is repeated between behaviors, make sure it's able to call the same method for each Behavior
	 *
	 **/
	private function _recursiveCall($Model, $method, $args = []) {
		$behaviorNames = array_keys($Model->actsAs);
		
		// Tracks that we're calling this method recursively so we only do it once
		if (empty($this->_recursiveCall[$method])) {
			$this->setRecursiveCall($method, $this->name);
			foreach ($behaviorNames as $name) {
				// Only finds other TalkBack models
				if (strpos($name, 'TalkBack.') === 0) {
					list($plugin, $alias) = pluginSplit($name, true);
					// Makes sure we haven't called it already
					if (
						!$this->hasRecursiveCall($method, $alias) && 
						!$Model->Behaviors->{$alias}->hasRecursiveCall($method, $alias)
					) {
						// Makes sure the method exists
						if (method_exists($Model->Behaviors->{$alias}, $method)) {
							array_unshift($args, $Model);
							call_user_func_array(array($Model->Behaviors->{$alias}, $method), $args);
						}
						$Model->Behaviors->{$alias}->setRecursiveCall($method, $this->name);
						$Model->Behaviors->{$alias}->setRecursiveCall($method, $alias);
						$this->setRecursiveCall($method, $alias);
					}
				}
			}
			$this->resetRecursiveCall($method, $Model);
			unset($this->_recursiveCall[$method]);
		}
	}
	
/**
 * Checks if a method is currently being called recursively in the behavior
 **/
	public function hasRecursiveCall($method, $alias) {
		return !empty($this->_recursiveCall[$method][$alias]);
	}
	
/**
 * Marks a method as being currently called recursively
 **/
	public function setRecursiveCall($method, $name) {
		$this->_recursiveCall[$method][$name] = true;
	}
	
/**
 * Once a method has completed, removes flags that the method is being recursively called
 **/
	public function resetRecursiveCall($method, $Model) {
		if (!empty($this->_recursiveCall[$method])) {
			$behaviors = $this->_recursiveCall[$method];
			unset($this->_recursiveCall[$method]);
			foreach ($behaviors as $alias => $set) {
				$Model->Behaviors->{$alias}->resetRecursiveCall($method, $Model);
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