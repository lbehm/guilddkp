CREATE TABLE `dkp_user` (
`user_id`  smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_name`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
`user_displayname`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`user_password`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
`user_decrypt_password`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
`user_email`  varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
`user_rank`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 ,
`user_style`  tinyint(4) UNSIGNED NULL DEFAULT 1 ,
`user_lang`  varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT 'de_de' ,
`user_key`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`user_lastvisit`  tinyint(11) UNSIGNED NULL DEFAULT 0 ,
`user_lastip`  varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`user_lastpage`  varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`user_active`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`user_newpassword`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`first_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`last_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`country`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`town`  varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`state`  varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`ZIP_code`  tinyint(11) UNSIGNED NULL DEFAULT NULL ,
`phone`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`cellphone`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`address`  text CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`facebook_name`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`game_acc`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`icq`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`skype`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`msn`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`irq`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`gender`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`birthday`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
`privacy_settings`  blob NOT NULL ,
PRIMARY KEY (`user_id`, `user_name`),
UNIQUE INDEX `user_name` USING BTREE (`user_name`) ,
UNIQUE INDEX `user_id` USING BTREE (`user_id`) 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT
;
