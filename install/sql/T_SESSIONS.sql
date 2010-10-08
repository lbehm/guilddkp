CREATE TABLE `dkp_sessions` (
`session_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
`session_user_id`  smallint(5) NOT NULL DEFAULT '-1' ,
`session_last_visit`  int(11) NOT NULL DEFAULT 0 ,
`session_start`  int(11) NOT NULL ,
`session_current`  int(11) NOT NULL ,
`session_page`  varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0' ,
`session_ip`  varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
PRIMARY KEY (`session_id`),
UNIQUE INDEX `session_id` USING BTREE (`session_id`), 
INDEX `session_current` USING BTREE (`session_current`), 
INDEX `session_user_id` USING BTREE (`session_user_id`) 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
ROW_FORMAT=DYNAMIC
;
