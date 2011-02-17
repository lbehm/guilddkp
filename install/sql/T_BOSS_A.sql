CREATE TABLE `dkp_boss_attendees` (
`ba_id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`boss_id`  mediumint(8) UNSIGNED NOT NULL COMMENT 'Bosskill' ,
`char_id`  smallint(5) UNSIGNED NOT NULL COMMENT 'Character' ,
`ba_category`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT 'Aufgabe des Chars: Tank, Heal, DD' ,
`ba_note`  text CHARACTER SET utf8 COLLATE utf8_bin NULL ,
PRIMARY KEY (`ba_id`),
CONSTRAINT `fk_boss_id` FOREIGN KEY (`boss_id`) REFERENCES `dkp_boss` (`boss_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `fk_char_id` FOREIGN KEY (`char_id`) REFERENCES `dkp_char` (`char_id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT
;
