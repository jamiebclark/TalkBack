<?php
App::uses('TalkBackAppHelper', 'TalkBack.View/Helper');
App::uses('PluginConfig', 'TalkBack.Utilities');



class CommenterHelper extends TalkBackAppHelper {
	public $name = 'Commenter';
	public $helpers = ['Html', 'Form'];
	
	public function urlArray($url = []) {
		return parent::urlArray($url + ['controller' => 'commenters']);
	}
	
	public function image($commenter = [], $options = []) {
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
	
	public function link($commenter = [], $options = []) {
		$options = array_merge([
			'prefix' => false,
		], $options);

		$url = ['controller' => 'commenters', 'action' => 'view', $commenter['id'], 'plugin' => 'talk_back'];
		if ($options['prefix'] === false) {
			$url += $this->urlPrefixReset();
		} else if ($options['prefix'] !== true) {
			$url[$options['prefix']] = true;
		}

		$commenter = !empty($commenter['Commenter']) ? $commenter['Commenter'] : $commenter;
		return $this->Html->link($this->name($commenter), $url, ['class' => 'tb-commenter-link']);
	}
	
	public function name($commenter = []) {
		$displayField = Configure::read('TalkBack.Commenter.displayField');
		$commenter = !empty($commenter['Commenter']) ? $commenter['Commenter'] : $commenter;
		return $commenter[$displayField];	
	}
	
	// Creates an autocomplete to add commenters into a form
	public function addInput($options = []) {
		$default = array(
			'type' => 'text',
			'data-source' => Router::url(
				$this->urlArray(['controller' => 'commenters',	'action' => 'search'])
			),
			'data-target' => null, // Set this if something other than data[Commenter][Commenter][]
		);
		$options = array_merge($default, $options);
		return $this->Form->input('add_commenter', $options);
	}
}