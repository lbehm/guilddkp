CREATE TABLE `dkp_post` (
`post_id`  smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT ,
`topic_id`  smallint(6) UNSIGNED NOT NULL ,
`post_user_id`  smallint(5),
`post_user_name`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' ,
`post_timestamp`  int(11) NOT NULL ,
`post_text`  longtext CHARACTER SET utf8 COLLATE utf8_bin NULL ,
`post_sticky`  bit(1) NOT NULL DEFAULT b'0' ,
`post_delete`  bit(1) NOT NULL DEFAULT b'0' ,
`post_edit_user_id`  smallint(5) NULL DEFAULT NULL ,
`post_edit_count`  smallint(8) UNSIGNED NULL DEFAULT 0 ,
`post_edit_time`  int(11) UNSIGNED NULL DEFAULT NULL ,
PRIMARY KEY (`post_id`),
FOREIGN KEY (`topic_id`) REFERENCES `dkp_topic` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`post_user_id`) REFERENCES `dkp_user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
FOREIGN KEY (`post_user_name`) REFERENCES `dkp_user` (`user_displayname`) ON DELETE NO ACTION ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT
;
