<?php
App::uses('PluginConfig', 'TalkBack.Utilities');
PluginConfig::init('TalkBack');

class TalkBackAppModel extends AppModel {
	public $tablePrefix = 'tb_';
	
/**
 * Certain models are designed to be customized using the Config file stored in /Config/talk_back.php
 *
 * @return True if configure is found, false if not
 **/
	protected function constructFromConfigure() {
		//Loads custom Commenter model information
		if ($config = Configure::read('TalkBack.' . $this->name)) {
			// Uses an existing model / table
			if (!empty($config['className'])) {
				// Properties to copy from the existing model
				$_copyProperties = array('useTable', 'tablePrefix', 'displayField', 'primaryKey', 'actsAs');
				if ($Copy = ClassRegistry::init($config['className'], true)) {
					foreach ($_copyProperties as $property) {
						$val = isset($Copy->$property) ? $Copy->$property : null;
						$this->$property = $val;
						// Stores them for accessing later
						Configure::write('TalkBack.' . $this->name . '.' . $property, $val);
					}
				}
			} else {
				foreach ($config as $property => $value) {
					$this->$property = $value;
				}
			}
			return true;
		}
		return false;
	}
	
	// Returns the table name including any existing prefixes
	protected function getTable() {
		$table = $this->useTable;
		if (!empty($this->tablePrefix)) {
			$table = $this->tablePrefix . $table;
		}
		return $table;
	}
}