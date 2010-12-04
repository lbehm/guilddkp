CREATE TABLE `dkp_forum` (
`forum_id`  smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT ,
`forum_name`  varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`forum_desc`  varchar(127) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`forum_hidden`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,
`forum_closed`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,
`forum_delete`  bit(1) NOT NULL DEFAULT b'0' ,
PRIMARY KEY (`forum_id`),
UNIQUE INDEX `forum_id` USING BTREE (`forum_id`) 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=2
ROW_FORMAT=COMPACT
;

