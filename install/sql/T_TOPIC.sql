CREATE TABLE `dkp_topic` (
`topic_id`  smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT ,
`topic_title`  varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
`forum_id`  smallint(5) UNSIGNED NOT NULL ,
`topic_author`  smallint(5) NOT NULL ,
`topic_first_post_id`  smallint(5) UNSIGNED NOT NULL ,
`topic_last_post_id`  smallint(5) UNSIGNED NOT NULL ,
`topic_last_poster`  smallint(5) NOT NULL ,
`topic_edit_timestamp`  int(11) UNSIGNED NOT NULL ,
`topic_hidden`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,
`topic_closed`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,
`topic_sticky`  bit(1) NOT NULL DEFAULT b'0' ,
`topic_delete`  bit(1) NOT NULL DEFAULT b'0' ,
PRIMARY KEY (`topic_id`),
FOREIGN KEY (`forum_id`) REFERENCES `dkp_forum` (`forum_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`topic_author`) REFERENCES `dkp_user` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
FOREIGN KEY (`topic_last_poster`) REFERENCES `dkp_user` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
UNIQUE INDEX `topic_id` USING BTREE (`topic_id`) ,
INDEX `forum_id` USING BTREE (`forum_id`) ,
INDEX `topic_author` USING BTREE (`topic_author`) ,
INDEX `topic_last_poster` USING BTREE (`topic_last_poster`) 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT
;
