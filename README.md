= Message Board Plugin =
== Layout ==
* Channel
** Forum
*** Topic
**** Reply
* Message

MYSQL
DROP TABLE IF EXISTS tb_channels;
CREATE TABLE tb_channels (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255),
	prefix VARCHAR (64) NULL,
	created DATETIME NULL,
	modified DATETIME NULL,
	forum_count INT(11) NULL,
	topic_count INT(11) NULL,
	INDEX (prefix)
);

DROP TABLE IF EXISTS tb_forums;	
CREATE TABLE tb_forums (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	channel_id INT(11),
	title VARCHAR(255),
	description TEXT NULL,
	INDEX (channel_id),
	topic_count INT(11) NULL,
	comment_count INT(11) NULL,
	created DATETIME NULL,
	modified DATETIME NULL
);

DROP TABLE IF EXISTS tb_topics;
CREATE TABLE tb_topics (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	commenter_id INT(11),
	forum_id INT(11),
	first_comment_id INT(11),
	last_comment_id INT(11),
	title VARCHAR(255),
	created DATETIME NULL,
	modified DATETIME NULL,
	comment_count INT(11),
	locked BOOL DEFAULT 0,
	sticky BOOL DEFAULT 0,
	INDEX (forum_id),
	INDEX (commenter_id)
);
DROP TABLE IF EXISTS tb_comments;
CREATE TABLE tb_comments (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	commenter_id INT(11),
	topic_id INT(11),
	body TEXT,
	created DATETIME NULL,
	modified DATETIME NULL,
	INDEX (topic_id),
	INDEX (commenter_id)
);
DROP TABLE IF EXISTS tb_messages;
CREATE TABLE tb_messages (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	from_commenter_id INT(11),
	commenter_id INT(11),
	title VARCHAR(255),
	body TEXT,
	viewed DATETIME NULL,
	created DATETIME NULL,
	modified DATETIME NULL,
	INDEX (from_commenter_id),
	INDEX (commenter_id)
);

DROP TABLE IF EXISTS tb_commenters;
CREATE TABLE tb_commenters (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(128) NULL,
	email VARCHAR(128),
	password VARCHAR(128),
	created DATETIME NULL,
	modified DATETIME NULL
);	

DROP TABLE IF EXISTS tb_comment_reads;
DROP TABLE IF EXISTS tb_read_comments;
CREATE TABLE tb_read_comments (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	commenter_id INT(11),
	comment_id INT(11),
	INDEX (commenter_id),
	INDEX (comment_id),
	UNIQUE INDEX (commenter_id, comment_id)
);

DROP TABLE IF EXISTS tb_topic_reads;
DROP TABLE IF EXISTS tb_read_topics;
CREATE TABLE tb_read_topics (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	commenter_id INT(11),
	topic_id INT(11),
	INDEX (commenter_id),
	INDEX (topic_id),
	UNIQUE INDEX (commenter_id, topic_id)
);