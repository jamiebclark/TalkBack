<?php 
class TalkBackSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $tb_channel_commenter_admins = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'channel_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'commenter_id_2' => array('column' => array('commenter_id', 'channel_id'), 'unique' => 1),
			'commenter_id' => array('column' => 'commenter_id', 'unique' => 0),
			'channel_id' => array('column' => 'channel_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_channels = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'prefix' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'key' => 'index', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'allow_forums' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'allow_topics' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'forum_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'topic_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'prefix' => array('column' => 'prefix', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_channels_commenter_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'channel_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'commenter_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'channel_id_2' => array('column' => array('channel_id', 'commenter_type_id'), 'unique' => 1),
			'channel_id' => array('column' => 'channel_id', 'unique' => 0),
			'commenter_type_id' => array('column' => 'commenter_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_channels_commenters = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'channel_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'channel_id_2' => array('column' => array('channel_id', 'commenter_id'), 'unique' => 1),
			'channel_id' => array('column' => 'channel_id', 'unique' => 0),
			'commenter_id' => array('column' => 'commenter_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_commenter_email_controls = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'comment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'id' => array('column' => array('id', 'foreign_key'), 'unique' => 0),
			'id_2' => array('column' => array('id', 'foreign_key', 'commenter_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_commenter_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_commenter_types_commenters = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'commenter_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'commenter_id' => array('column' => array('commenter_id', 'commenter_type_id'), 'unique' => 1),
			'commenter_id_2' => array('column' => 'commenter_id', 'unique' => 0),
			'commenter_type_id' => array('column' => 'commenter_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_commenters_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'message_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'commenter_id_2' => array('column' => array('commenter_id', 'message_id'), 'unique' => 1),
			'commenter_id' => array('column' => 'commenter_id', 'unique' => 0),
			'message_id' => array('column' => 'message_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_comments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'depth' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'prefix' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'channel_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'private' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'topic_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'comment_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'channel_id' => array('column' => 'channel_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_forums_commenter_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'forum_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'commenter_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'forum_id' => array('column' => array('forum_id', 'commenter_type_id'), 'unique' => 1),
			'forum_id_2' => array('column' => 'forum_id', 'unique' => 0),
			'commenter_type_id' => array('column' => 'commenter_type_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_forums_commenters = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'forum_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'forum_id' => array('column' => array('forum_id', 'commenter_id'), 'unique' => 1),
			'forum_id_2' => array('column' => 'forum_id', 'unique' => 0),
			'commenter_id' => array('column' => 'commenter_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'from_commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'last_commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'subject' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'comment_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 6, 'unsigned' => false),
		'first_comment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'last_comment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'viewed' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $tb_topics = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'commenter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'forum_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'first_comment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'last_comment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'comment_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'locked' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'sticky' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'forum_id' => array('column' => 'forum_id', 'unique' => 0),
			'user_id' => array('column' => 'commenter_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'key' => 'unique', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'password' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'name_limited' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'avatar' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'first_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'addline2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'last_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'business_title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'location_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'addline1' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'city' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'state' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 16, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'zip_distance' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 6, 'unsigned' => false),
		'lng' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '9,6', 'unsigned' => false),
		'lat' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '8,6', 'unsigned' => false),
		'gender' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 25, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'website' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'is_admin' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'is_business' => array('type' => 'boolean', 'null' => false, 'default' => null),
		'is_school_admin' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'event_review_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'event_review_score_avg' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '4,2', 'unsigned' => false),
		'is_test' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'user_notification_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'max_user_child_birthday' => array('type' => 'date', 'null' => true, 'default' => null),
		'min_user_child_birthday' => array('type' => 'date', 'null' => true, 'default' => null),
		'user_child_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false),
		'public_info_permission' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 1, 'unsigned' => false),
		'public_attending_permission' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 1, 'unsigned' => false),
		'friends_info_permission' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 1, 'unsigned' => false),
		'last_event_review_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'email' => array('column' => 'email', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

}
