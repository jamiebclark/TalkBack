<?php 
class TalkBackSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $tb_channel_commenter_admins = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'channel_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'commenter_id_2' => array('column' => array('commenter_id', 'channel_id'), 'unique' => 1),
			'commenter_id' => array('column' => 'commenter_id', 'unique' => 0),
			'channel_id' => array('column' => 'channel_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_channels = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'prefix' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'key' => 'index', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'allow_forums' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'allow_topics' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'forum_count' => array('type' => 'integer', 'null' => true, 'default' => null),
		'topic_count' => array('type' => 'integer', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'prefix' => array('column' => 'prefix', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_channels_commenter_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'channel_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'commenter_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'channel_id_2' => array('column' => array('channel_id', 'commenter_type_id'), 'unique' => 1),
			'channel_id' => array('column' => 'channel_id', 'unique' => 0),
			'commenter_type_id' => array('column' => 'commenter_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_channels_commenters = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'channel_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'channel_id_2' => array('column' => array('channel_id', 'commenter_id'), 'unique' => 1),
			'channel_id' => array('column' => 'channel_id', 'unique' => 0),
			'commenter_id' => array('column' => 'commenter_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_commenter_email_controls = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'comment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'email_on_reply' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'commenter_id_2' => array('column' => array('commenter_id', 'comment_id'), 'unique' => 1),
			'commenter_id' => array('column' => 'commenter_id', 'unique' => 0),
			'comment_id' => array('column' => 'comment_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_commenter_has_reads = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'id' => array('column' => array('id', 'foreign_key'), 'unique' => 0),
			'id_2' => array('column' => array('id', 'foreign_key', 'commenter_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_commenter_types_commenters = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'commenter_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'commenter_type_id_2' => array('column' => array('commenter_type_id', 'commenter_id'), 'unique' => 1),
			'commenter_type_id' => array('column' => 'commenter_type_id', 'unique' => 0),
			'commenter_id' => array('column' => 'commenter_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_commenters_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'message_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'commenter_id_2' => array('column' => array('commenter_id', 'message_id'), 'unique' => 1),
			'commenter_id' => array('column' => 'commenter_id', 'unique' => 0),
			'message_id' => array('column' => 'message_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_comments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null),
		'depth' => array('type' => 'integer', 'null' => true, 'default' => null),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null),
		'prefix' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'model' => array('column' => 'model', 'unique' => 0),
			'model_2' => array('column' => array('model', 'foreign_key'), 'unique' => 0),
			'model_3' => array('column' => array('model', 'foreign_key', 'commenter_id'), 'unique' => 0),
			'parent_id' => array('column' => 'parent_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_forums = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'channel_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'topic_count' => array('type' => 'integer', 'null' => true, 'default' => null),
		'comment_count' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'channel_id' => array('column' => 'channel_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'from_commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'last_commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'subject' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'comment_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 6),
		'first_comment_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'last_comment_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'viewed' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_topics = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'forum_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'first_comment_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'last_comment_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'comment_count' => array('type' => 'integer', 'null' => true, 'default' => null),
		'locked' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'sticky' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'forum_id' => array('column' => 'forum_id', 'unique' => 0),
			'user_id' => array('column' => 'commenter_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);
}
