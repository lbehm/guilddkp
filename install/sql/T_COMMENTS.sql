CREATE TABLE `dkp_comments` (
`comment_id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id`  smallint(5) NOT NULL ,
`user_name`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' ,
`comment_date`  int(11) NOT NULL ,
`comment_text`  varchar(511) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`comment_ranking`  int(11) NULL DEFAULT 0 ,
`comment_page`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`comment_attach_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`comment_respond_to_id`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`comment_id`),
FOREIGN KEY (`user_id`) REFERENCES `dkp_user` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
UNIQUE INDEX `comment_id` USING BTREE (`comment_id`) ,
INDEX `user_id` USING BTREE (`user_id`) ,
INDEX `comment_date` USING BTREE (`comment_date`) 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT
;
