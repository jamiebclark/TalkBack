<?php
App::uses('TalkBackAppHelper', 'TalkBack.View/Helper');
class MessageHelper extends TalkBackAppHelper {
	public $name = 'Message';
	public $helpers = array('Html', 'TalkBack.Commenter');
	
	public function thumb($message, $options) {
		$out = '';
		$class = 'tb-message-thumb';
		if (empty($message['OtherCommenter'])){
			$out = $this->Html->tag('em', 'Empty');
		} elseif (count($message['OtherCommenter']) == 1) {
			$out = $this->Commenter->image($message['OtherCommenter'][0], $options);
		} else {
			$out = count($message['OtherCommenter']);
		}
		return $this->Html->tag('span', $out, compact('class'));
	}
}