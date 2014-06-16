<?php
App::uses('TalkBackAppHelper', 'TalkBack.View/Helper');
class CommenterHelper extends TalkBackAppHelper {
	public $name = 'Commenter';
	public $helpers = array('Html', 'Form');
	
	public function urlArray($url = array()) {
		return parent::urlArray($url + array('controller' => 'commenters'));
	}
	
	public function image($commenter = array(), $options = array()) {
		$dir = Configure::read('TalkBack.Commenter.img.dir');
		$field = Configure::read('TalkBack.Commenter.img.field');
		if (substr($dir, -1) != '/' || substr($field, 0) != '/') {
			$dir .= '/';
		}
		if ($dir && $field && !empty($commenter[$field])) {
			return $this->Html->image($dir . $commenter[$field], $options);
		}
		return '';
	}
	
	public function link($commenter = array()) {
		$commenter = !empty($commenter['Commenter']) ? $commenter['Commenter'] : $commenter;
		return $this->Html->link(
			$this->name($commenter),
			array('controller' => 'commenters', 'action' => 'view', $commenter['id'], 'plugin' => 'talk_back') + Prefix::reset(),
			array('class' => 'tb-commenter-link')
		);
	}
	
	public function name($commenter = []) {
		$commenter = !empty($commenter['Commenter']) ? $commenter['Commenter'] : $commenter;
		return $commenter[Configure::read('TalkBack.Commenter.displayField')];	
	}
	
	// Creates an autocomplete to add commenters into a form
	public function addInput($options = array()) {
		$default = array(
			'type' => 'text',
			'data-source' => Router::url(
				$this->urlArray(array('controller' => 'commenters',	'action' => 'search'))
			),
			'data-target' => null, // Set this if something other than data[Commenter][Commenter][]
		);
		$options = array_merge($default, $options);
		return $this->Form->input('add_commenter', $options);
	}	
}