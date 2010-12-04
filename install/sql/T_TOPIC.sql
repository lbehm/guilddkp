CREATE TABLE `dkp_topic` (
`topic_id`  smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT ,
`topic_title`  varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
`forum_id`  smallint(6) UNSIGNED NOT NULL ,
`topic_author`  smallint(5),
`topic_first_post_id`  smallint(6) UNSIGNED NOT NULL ,
`topic_last_post_id`  smallint(6) UNSIGNED NOT NULL ,
`topic_last_poster`  smallint(5),
`topic_edit_timestamp`  int(11) UNSIGNED NOT NULL ,
`topic_hidden`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,
`topic_closed`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,
`topic_sticky`  bit(1) NOT NULL DEFAULT b'0' ,
`topic_delete`  bit(1) NOT NULL DEFAULT b'0' ,
PRIMARY KEY (`topic_id`),
UNIQUE INDEX `topic_id` USING BTREE (`topic_id`),
FOREIGN KEY (`forum_id`) REFERENCES `dkp_forum` (`forum_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`topic_author`) REFERENCES `dkp_user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
FOREIGN KEY (`topic_last_poster`) REFERENCES `dkp_user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
ROW_FORMAT=COMPACT
;
