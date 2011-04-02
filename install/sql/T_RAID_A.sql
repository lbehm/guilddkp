CREATE TABLE `dkp_raid_attendees` (
`ra_id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Raidanmeldung' ,
`raid_id`  mediumint(8) UNSIGNED NOT NULL COMMENT 'Raid' ,
`char_id`  smallint(5) UNSIGNED NOT NULL COMMENT 'Character' ,
`ra_status`  enum('sign','agree','disagree','maybe') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'maybe' COMMENT 'Status' ,
`ra_category`  enum('melee','range','tank','heal') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Aufgabe des Chars: Tank, Heal, DD-Melee/Range' ,
`ra_note`  text CHARACTER SET utf8 COLLATE utf8_bin NULL ,
`ra_time`  int(10) UNSIGNED NOT NULL COMMENT 'Raidanmeldung' ,
PRIMARY KEY (`ra_id`),
FOREIGN KEY (`raid_id`) REFERENCES `dkp_raid` (`raid_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`char_id`) REFERENCES `dkp_char` (`char_id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT
;
