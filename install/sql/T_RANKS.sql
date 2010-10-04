CREATE TABLE `dkp_ranks` (
`rank_id`  smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT ,
`rank_name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
`rank_hide`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`rank_prefix`  varchar(75) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' ,
`rank_suffix`  varchar(75) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' ,
`rank_icon`  varchar(75) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' ,
PRIMARY KEY (`rank_id`),
UNIQUE INDEX `rank_id` USING BTREE (`rank_id`) ,
UNIQUE INDEX `rank_name` USING BTREE (`rank_name`) 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT
;

INSERT INTO dkp_ranks (rank_name, rank_hide) VALUES('Administrator', 0), ('User', 0), ('Guest', 1);
