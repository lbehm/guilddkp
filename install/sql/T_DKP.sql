CREATE TABLE `dkp_dkp` (
`dkp_id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id`  smallint(5) NOT NULL ,
`dkp_ref`  enum('boss','raid','loot','other') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'other' ,
`dkp_ref_id`  int(10) UNSIGNED NULL ,
`dkp`  smallint(5) NULL DEFAULT 0 ,
`dkp_note`  text CHARACTER SET utf8 COLLATE utf8_bin NULL ,
`dkp_time`  int(10) UNSIGNED NOT NULL,
PRIMARY KEY (`dkp_id`),
CONSTRAINT `fk_dkp_user_id` FOREIGN KEY (`user_id`) REFERENCES `dkp_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
;
